<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Promotion;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Collection;

class MarketingService
{
    /**
     * Obtener campañas activas
     */
    public function getActiveCampaigns(): Collection
    {
        $now = now();
        
        return Campaign::where('is_active', true)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now)
            ->orderBy('starts_at', 'desc')
            ->get();
    }
    
    /**
     * Obtener detalles de una campaña específica
     */
    public function getCampaignDetails(string $slug): array
    {
        $campaign = Campaign::where('slug', $slug)
            ->with('promotions.products')
            ->first();
            
        if (!$campaign) {
            return [
                'found' => false,
            ];
        }
        
        // Verificar si la campaña está activa
        $isActive = $campaign->isActive();
        
        // Obtener productos destacados de la campaña
        $featuredProducts = $this->getCampaignFeaturedProducts($campaign);
        
        // Obtener categorías destacadas
        $featuredCategories = $this->getCampaignFeaturedCategories($campaign);
        
        return [
            'found' => true,
            'campaign' => $campaign,
            'is_active' => $isActive,
            'featured_products' => $featuredProducts,
            'featured_categories' => $featuredCategories,
        ];
    }
    
    /**
     * Obtener productos destacados de una campaña
     */
    private function getCampaignFeaturedProducts(Campaign $campaign): Collection
    {
        // Obtener todos los IDs de productos en promociones de la campaña
        $productIds = [];
        
        foreach ($campaign->promotions as $promotion) {
            if ($promotion->isValid()) {
                $productIds = array_merge($productIds, $promotion->products->pluck('id')->toArray());
            }
        }
        
        $productIds = array_unique($productIds);
        
        return Product::whereIn('id', $productIds)
            ->where('is_active', true)
            ->take(8)
            ->get();
    }
    
    /**
     * Obtener categorías destacadas de una campaña
     */
    private function getCampaignFeaturedCategories(Campaign $campaign): Collection
    {
        // Obtenemos los productos de la campaña
        $products = $this->getCampaignFeaturedProducts($campaign);
        
        // Extraemos las categorías únicas
        $categoryIds = $products->pluck('category_id')->unique()->toArray();
        
        return Category::whereIn('id', $categoryIds)
            ->where('is_active', true)
            ->take(4)
            ->get();
    }
    
    /**
     * Obtener banners activos para mostrar en home o categorías
     */
    public function getActiveBanners(string $section = 'home', int $limit = 3): Collection
    {
        $now = now();
        
        $campaigns = Campaign::where('is_active', true)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now)
            ->whereNotNull('banner_image')
            ->orderBy('starts_at', 'desc')
            ->take($limit)
            ->get();
            
        return $campaigns;
    }
    
    /**
     * Registrar visualización de campaña (para análisis)
     */
    public function registerCampaignView(Campaign $campaign): void
    {
        // Aquí se podría implementar lógica para registrar vistas y análisis
        // Por ejemplo, incrementar un contador en la base de datos o enviar a un sistema de analítica
    }
    
    /**
     * Generar recomendaciones personalizadas para un usuario
     */
    public function getPersonalizedRecommendations(int $userId, int $limit = 4): Collection
    {
        // Esta sería una implementación básica
        // En un sistema real, se utilizaría machine learning o algoritmos más avanzados
        
        // Por ahora, simplemente devolvemos productos en promoción
        $activePromotions = Promotion::getActivePromotions();
        $productIds = [];
        
        foreach ($activePromotions as $promotion) {
            $productIds = array_merge($productIds, $promotion->products->pluck('id')->toArray());
        }
        
        $productIds = array_slice(array_unique($productIds), 0, $limit);
        
        if (empty($productIds)) {
            // Si no hay promociones, devolver productos populares
            return Product::where('is_active', true)
                ->orderBy('views', 'desc')
                ->take($limit)
                ->get();
        }
        
        return Product::whereIn('id', $productIds)
            ->where('is_active', true)
            ->take($limit)
            ->get();
    }
}