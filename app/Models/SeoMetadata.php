<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoMetadata extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'structured_data',
        'additional_tags',
        'canonical_url',
        'no_index',
        'no_follow',
    ];

    protected $casts = [
        'structured_data' => 'array',
        'no_index' => 'boolean',
        'no_follow' => 'boolean',
    ];

    /**
     * Get the parent seoable model (Product, Category, Page, etc.).
     */
    public function seoable()
    {
        return $this->morphTo();
    }
}