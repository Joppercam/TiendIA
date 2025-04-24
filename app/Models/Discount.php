<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'value',
        'scope',
        'scope_id',
        'starts_at',
        'ends_at',
        'is_active',
        'description',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
    ];

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

    public function scopeItem()
    {
        if ($this->scope === 'product') {
            return $this->belongsTo(Product::class, 'scope_id');
        } elseif ($this->scope === 'category') {
            return $this->belongsTo(Category::class, 'scope_id');
        } elseif ($this->scope === 'brand') {
            return $this->belongsTo(Brand::class, 'scope_id');
        }
        
        return null;
    }
}