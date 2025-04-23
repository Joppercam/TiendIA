<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    /**
     * Obtener el usuario asociado.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener el producto asociado.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}