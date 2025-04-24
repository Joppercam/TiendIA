<?php

namespace App\View\Components;

use App\Models\Product;
use App\Services\PromotionService;
use Illuminate\View\Component;

class ProductPrice extends Component
{
    public $product;
    public $priceInfo;
    
    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->priceInfo = app(PromotionService::class)->calculateDiscountedPrice($product);
    }
    
    public function render()
    {
        return view('components.product-price');
    }
}