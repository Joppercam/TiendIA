<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'type', 'is_filterable', 'is_required'
    ];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('attribute_value_id', 'custom_value')
            ->withTimestamps();
    }
}
