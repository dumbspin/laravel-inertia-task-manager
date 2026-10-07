<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    /**
     * Allowed task statuses.
     *
     * @var array<string, string>
     */
    public const STATUSES = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
    ];

    /**
     * Allowed task priorities.
     *
     * @var array<string, string>
     */
    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'status_label',
        'priority_label',
        'is_completed',
        'created_by',
        'formatted_due_date',
        'formatted_created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date:Y-m-d',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * The user that created the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope the query to the given search term (task title).
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where('title', 'like', '%'.$search.'%');
    }

    /**
     * Scope the query to the given status.
     */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (! isset(self::STATUSES[$status ?? ''])) {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Scope the query to the given priority.
     */
    public function scopePriority(Builder $query, ?string $priority): Builder
    {
        if (! isset(self::PRIORITIES[$priority ?? ''])) {
            return $query;
        }

        return $query->where('priority', $priority);
    }

    /**
     * Human readable status.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Human readable priority.
     */
    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }

    /**
     * Whether the task is completed.
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * The name of the user that created the task.
     */
    public function getCreatedByAttribute(): ?string
    {
        return $this->relationLoaded('user') ? $this->user?->name : null;
    }

    /**
     * Due date formatted for the UI (Y-m-d).
     */
    public function getFormattedDueDateAttribute(): ?string
    {
        return $this->due_date?->format('Y-m-d');
    }

    /**
     * Created at formatted for the UI.
     */
    public function getFormattedCreatedAtAttribute(): ?string
    {
        return $this->created_at?->format('Y-m-d H:i');
    }
}
