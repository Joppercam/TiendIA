<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Services\MarketingService;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    protected $marketingService;
    
    public function __construct(MarketingService $marketingService)
    {
        $this->marketingService = $marketingService;
    }
    
    public function index()
    {
        $campaigns = $this->marketingService->getActiveCampaigns();
        
        return view('shop.campaigns.index', compact('campaigns'));
    }
    
    public function show($slug)
    {
        $result = $this->marketingService->getCampaignDetails($slug);
        
        if (!$result['found']) {
            abort(404, 'Campaña no encontrada');
        }
        
        $campaign = $result['campaign'];
        
        // Si la campaña existe pero no está activa, mostrar un mensaje adecuado
        if (!$result['is_active']) {
            return view('shop.campaigns.inactive', compact('campaign'));
        }
        
        // Registrar la visualización para análisis
        $this->marketingService->registerCampaignView($campaign);
        
        $featuredProducts = $result['featured_products'];
        $featuredCategories = $result['featured_categories'];
        
        return view('shop.campaigns.show', compact('campaign', 'featuredProducts', 'featuredCategories'));
    }
}