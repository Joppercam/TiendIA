<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question_id',
        'user_id',
        'answer',
        'is_from_seller',
        'is_approved',
        'approved_at',
    ];

    protected $casts = [
        'is_from_seller' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relaciones
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeFromSeller($query)
    {
        return $query->where('is_from_seller', true);
    }
}