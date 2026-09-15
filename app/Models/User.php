<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function roleInProject(Project $project): ?Role
    {
        $membership = $this->projectMemberships()->where('project_id', $project->id)->first();

        return $membership ? $membership->role : null;
    }

    public function hasProjectPermission(Project $project, string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $role = $this->roleInProject($project);
        if (! $role) {
            return false;
        }

        $perms = $role->permissions ?? [];

        return in_array('all', $perms, true) || in_array($permission, $perms, true);
    }

    public function projectMemberships(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function assignedWorkPackages(): HasMany
    {
        return $this->hasMany(WorkPackage::class, 'assignee_id');
    }

    public function authoredWorkPackages(): HasMany
    {
        return $this->hasMany(WorkPackage::class, 'author_id');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }
}
