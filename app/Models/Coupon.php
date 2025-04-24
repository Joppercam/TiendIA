<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_purchase',
        'usage_limit',
        'usage_count',
        'starts_at',
        'expires_at',
        'is_active',
        'description',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'min_purchase' => 'decimal:2',
        'value' => 'decimal:2',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
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
        
        if ($this->expires_at && $this->expires_at->lessThan($now)) {
            return false;
        }
        
        // Verificar límite de uso
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }
        
        return true;
    }
}