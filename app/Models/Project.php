<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'identifier',
        'description',
        'parent_id',
        'status',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'parent_id');
    }

    public function subprojects(): HasMany
    {
        return $this->hasMany(Project::class, 'parent_id');
    }

    public function workPackages(): HasMany
    {
        return $this->hasMany(WorkPackage::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function getRouteKeyName(): string
    {
        return 'identifier';
    }

    /**
     * Scope a query to only include projects visible to the given user.
     */
    public function scopeVisibleTo($query, ?User $user)
    {
        if (! $user) {
            return $query->where('is_public', true);
        }

        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('is_public', true)
                ->orWhereHas('members', function ($m) use ($user) {
                    $m->where('user_id', $user->id);
                });
        });
    }

    /**
     * Determine whether the project is visible to the given user.
     */
    public function isVisibleTo(?User $user): bool
    {
        if (! $user) {
            return (bool) $this->is_public;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($this->is_public) {
            return true;
        }

        return $this->members()->where('user_id', $user->id)->exists();
    }
}
