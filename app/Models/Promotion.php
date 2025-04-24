<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'campaign_id',
        'promotion_type',
        'rules',
        'starts_at',
        'ends_at',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'rules' => 'json',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function isValid()
    {
        $now = now();
        
        // Verificar si está activo
        if (!$this->is_active) {
            return false;
        }
        
        // Verificar fechas de validez
        if ($this->starts_at && $this->starts_at->greaterThan($now)) {
            return false;
        }
        
        if ($this->ends_at && $this->ends_at->lessThan($now)) {
            return false;
        }
        
        return true;
    }

    public static function getActivePromotions()
    {
        $now = now();
        
        return self::where('is_active', true)
            ->where(function($query) use ($now) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function($query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            })
            ->orderBy('priority', 'desc')
            ->get();
    }
}