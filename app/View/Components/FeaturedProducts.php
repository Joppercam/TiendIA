<?php

namespace App\View\Components;

use App\Services\PromotionService;
use Illuminate\View\Component;

class FeaturedProducts extends Component
{
    public $products;
    public $title;
    
    public function __construct($title = 'Productos Destacados', $limit = 8)
    {
        $this->title = $title;
        $this->products = app(PromotionService::class)->getFeaturedProducts($limit);
    }
    
    public function render()
    {
        return view('components.featured-products');
    }
}