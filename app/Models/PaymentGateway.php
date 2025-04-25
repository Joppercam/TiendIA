<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'is_default',
        'config',
        'logo',
        'position',
        'min_amount',
        'max_amount',
        'instructions',
        'credentials'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'config' => 'array',
        'credentials' => 'encrypted:array'
    ];

    // Relaciones
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}