<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sitemap extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'frequency',
        'priority',
        'filepath',
        'last_generated_at',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'last_generated_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}