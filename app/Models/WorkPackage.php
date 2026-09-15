<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'parent_id',
        'type_id',
        'status_id',
        'priority_id',
        'author_id',
        'assignee_id',
        'subject',
        'description',
        'start_date',
        'due_date',
        'estimated_hours',
        'done_ratio',
        'position',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'estimated_hours' => 'decimal:2',
        'done_ratio' => 'integer',
        'position' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(WorkPackage::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(WorkPackage::class, 'parent_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(WorkPackageType::class, 'type_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(WorkPackageStatus::class, 'status_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(WorkPackagePriority::class, 'priority_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(WorkPackageComment::class)->latest();
    }

    public function getTotalSpentHoursAttribute(): float
    {
        return (float) $this->timeEntries()->sum('hours');
    }
}
