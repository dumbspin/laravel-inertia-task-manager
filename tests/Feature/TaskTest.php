<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    public function test_guests_are_redirected_from_the_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_the_dashboard_shows_task_statistics(): void
    {
        $user = $this->user();

        $user->tasks()->create(['title' => 'Pending task', 'status' => 'pending', 'priority' => 'low', 'due_date' => '2026-12-01']);
        $user->tasks()->create(['title' => 'Running task', 'status' => 'in_progress', 'priority' => 'medium', 'due_date' => '2026-12-02']);
        $user->tasks()->create(['title' => 'Finished task', 'status' => 'completed', 'priority' => 'high', 'due_date' => '2026-12-03']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Tasks/Index')
                ->where('stats', [
                    'total' => 3,
                    'pending' => 1,
                    'in_progress' => 1,
                    'completed' => 1,
                ])
                ->has('tasks.data', 3)
            );
    }

    public function test_a_user_can_create_a_task(): void
    {
        $user = $this->user();

        $this->actingAs($user)->post('/tasks', [
            'title' => 'Write the report',
            'description' => 'Quarterly numbers',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => '2026-11-30',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Write the report',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => '2026-11-30',
        ]);
    }

    public function test_the_task_title_cannot_be_empty(): void
    {
        $user = $this->user();

        $this->actingAs($user)->from('/tasks/create')->post('/tasks', [
            'title' => '',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => '2026-11-30',
        ])->assertRedirect('/tasks/create')
            ->assertSessionHasErrors('title');

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_the_task_title_may_not_exceed_255_characters(): void
    {
        $user = $this->user();

        $this->actingAs($user)->post('/tasks', [
            'title' => str_repeat('a', 256),
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => '2026-11-30',
        ])->assertSessionHasErrors('title');
    }

    public function test_status_and_priority_must_be_allowed_values(): void
    {
        $user = $this->user();

        $this->actingAs($user)->post('/tasks', [
            'title' => 'Valid title',
            'status' => 'not-a-status',
            'priority' => 'urgent',
            'due_date' => '2026-11-30',
        ])->assertSessionHasErrors(['status', 'priority']);
    }

    public function test_the_due_date_must_be_a_valid_date(): void
    {
        $user = $this->user();

        $this->actingAs($user)->post('/tasks', [
            'title' => 'Valid title',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => 'next tuesday-ish',
        ])->assertSessionHasErrors('due_date');
    }

    public function test_a_user_can_update_their_task(): void
    {
        $user = $this->user();
        $task = $user->tasks()->create([
            'title' => 'Old title',
            'status' => 'pending',
            'priority' => 'low',
            'due_date' => '2026-11-30',
        ]);

        $this->actingAs($user)->put('/tasks/'.$task->id, [
            'title' => 'New title',
            'description' => 'Updated description',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => '2026-12-15',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'New title', 'status' => 'in_progress']);
    }

    public function test_a_task_can_be_marked_as_completed(): void
    {
        $user = $this->user();
        $task = $user->tasks()->create([
            'title' => 'Finish it',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => '2026-11-30',
        ]);

        $this->actingAs($user)->patch('/tasks/'.$task->id.'/toggle')->assertRedirect();

        $this->assertSame('completed', $task->fresh()->status);
    }

    public function test_a_user_can_delete_their_task(): void
    {
        $user = $this->user();
        $task = $user->tasks()->create([
            'title' => 'Delete me',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => '2026-11-30',
        ]);

        $this->actingAs($user)->delete('/tasks/'.$task->id)->assertRedirect('/dashboard');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_tasks_can_be_searched_by_title_and_filtered(): void
    {
        $user = $this->user();

        $user->tasks()->create(['title' => 'Prepare budget', 'status' => 'pending', 'priority' => 'high', 'due_date' => '2026-11-30']);
        $user->tasks()->create(['title' => 'Prepare slides', 'status' => 'completed', 'priority' => 'low', 'due_date' => '2026-11-30']);
        $user->tasks()->create(['title' => 'Unrelated task', 'status' => 'pending', 'priority' => 'high', 'due_date' => '2026-11-30']);

        $this->actingAs($user)
            ->get('/dashboard?search=prepare&status=pending&priority=high')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Tasks/Index')
                ->has('tasks.data', 1)
                ->where('tasks.data.0.title', 'Prepare budget')
                ->where('filters.search', 'prepare')
                ->where('filters.status', 'pending')
                ->where('filters.priority', 'high')
            );
    }

    public function test_a_user_cannot_edit_another_users_task(): void
    {
        $owner = $this->user();
        $intruder = $this->user();

        $task = $owner->tasks()->create([
            'title' => 'Not yours',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => '2026-11-30',
        ]);

        $this->actingAs($intruder)->get('/tasks/'.$task->id.'/edit')->assertNotFound();
        $this->actingAs($intruder)->delete('/tasks/'.$task->id)->assertNotFound();
    }

    public function test_a_user_only_sees_their_own_tasks(): void
    {
        $mine = $this->user();
        $other = $this->user();

        $mine->tasks()->create(['title' => 'Mine', 'status' => 'pending', 'priority' => 'low', 'due_date' => '2026-11-30']);
        $other->tasks()->create(['title' => 'Theirs', 'status' => 'pending', 'priority' => 'low', 'due_date' => '2026-11-30']);

        $this->actingAs($mine)
            ->get('/dashboard')
            ->assertInertia(fn ($page) => $page
                ->has('tasks.data', 1)
                ->where('tasks.data.0.title', 'Mine')
                ->where('stats.total', 1)
            );
    }
}
