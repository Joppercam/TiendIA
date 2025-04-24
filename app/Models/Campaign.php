<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'starts_at',
        'ends_at',
        'is_active',
        'banner_image',
        'banner_link',
        'slug',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($campaign) {
            if (empty($campaign->slug)) {
                $campaign->slug = Str::slug($campaign->name);
            }
        });
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    public function isActive()
    {
        $now = now();
        
        return $this->is_active && 
            $this->starts_at->lessThanOrEqualTo($now) && 
            $this->ends_at->greaterThanOrEqualTo($now);
    }
}