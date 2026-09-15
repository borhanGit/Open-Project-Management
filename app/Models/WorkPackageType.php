<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkPackageType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'icon',
        'is_milestone',
        'position',
    ];

    protected $casts = [
        'is_milestone' => 'boolean',
        'position' => 'integer',
    ];

    public function workPackages(): HasMany
    {
        return $this->hasMany(WorkPackage::class, 'type_id');
    }
}
