<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkPackagePriority extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'is_default',
        'position',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'position' => 'integer',
    ];

    public function workPackages(): HasMany
    {
        return $this->hasMany(WorkPackage::class, 'priority_id');
    }
}
