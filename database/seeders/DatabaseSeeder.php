<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
        ]);

        $tasks = [
            ['title' => 'Prepare quarterly sales report', 'description' => 'Compile the sales figures for Q3 and build the summary deck for the review meeting.', 'status' => 'in_progress', 'priority' => 'high', 'due_date' => now()->addDays(3)->toDateString()],
            ['title' => 'Update company website copy', 'description' => 'Refresh the homepage and pricing page text to match the new product positioning.', 'status' => 'pending', 'priority' => 'medium', 'due_date' => now()->addDays(7)->toDateString()],
            ['title' => 'Fix login page styling', 'description' => 'The email field overflows on small screens. Adjust the responsive styles.', 'status' => 'completed', 'priority' => 'high', 'due_date' => now()->subDays(2)->toDateString()],
            ['title' => 'Onboard new designer', 'description' => 'Share the brand guidelines, Figma files and the design system documentation.', 'status' => 'in_progress', 'priority' => 'medium', 'due_date' => now()->addDays(5)->toDateString()],
            ['title' => 'Write API documentation', 'description' => 'Document the public endpoints with request and response examples.', 'status' => 'pending', 'priority' => 'low', 'due_date' => now()->addDays(14)->toDateString()],
            ['title' => 'Client feedback call', 'description' => 'Collect feedback from the pilot customer about the new dashboard.', 'status' => 'completed', 'priority' => 'medium', 'due_date' => now()->subDays(1)->toDateString()],
            ['title' => 'Refactor billing module', 'description' => 'Extract invoice calculation logic into a dedicated service class.', 'status' => 'pending', 'priority' => 'high', 'due_date' => now()->addDays(10)->toDateString()],
            ['title' => 'Set up automated backups', 'description' => 'Schedule nightly database backups and verify the restore procedure.', 'status' => 'in_progress', 'priority' => 'medium', 'due_date' => now()->addDays(6)->toDateString()],
            ['title' => 'Plan team retrospective', 'description' => 'Collect agenda items and book a meeting room for Friday.', 'status' => 'pending', 'priority' => 'low', 'due_date' => now()->addDays(9)->toDateString()],
            ['title' => 'Renew SSL certificates', 'description' => 'Renew the certificates for the API and the staging environment.', 'status' => 'completed', 'priority' => 'high', 'due_date' => now()->subDays(5)->toDateString()],
            ['title' => 'Optimise image assets', 'description' => 'Compress hero images and convert them to WebP to improve page load.', 'status' => 'pending', 'priority' => 'low', 'due_date' => now()->addDays(20)->toDateString()],
            ['title' => 'Review pull requests', 'description' => 'Review the open pull requests from the backend team.', 'status' => 'in_progress', 'priority' => 'medium', 'due_date' => now()->addDays(2)->toDateString()],
        ];

        foreach ($tasks as $task) {
            $user->tasks()->create($task);
        }
    }
}
