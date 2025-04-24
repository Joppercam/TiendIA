<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use App\Models\Rating;
use App\Services\ReviewService;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    protected $reviewService;
    
    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
        $this->middleware('auth')->except(['index', 'show']);
    }
    
    /**
     * Mostrar todas las reseñas de un producto
     */
    public function index(Product $product)
    {
        $reviews = $this->reviewService->getApprovedReviews($product);
        $ratingSummary = $this->reviewService->getRatingsSummary($product);
        
        return view('reviews.index', compact('product', 'reviews', 'ratingSummary'));
    }
    
    /**
     * Mostrar el formulario para crear una reseña
     */
    public function create(Product $product)
    {
        // Verificar si el usuario ya ha escrito una reseña
        $existingReview = Auth::user()->reviews()
            ->where('product_id', $product->id)
            ->first();
            
        if ($existingReview) {
            return redirect()->route('products.reviews.edit', [$product, $existingReview])
                ->with('info', 'Ya has escrito una reseña para este producto. Puedes editarla.');
        }
        
        return view('reviews.create', compact('product'));
    }
    
    /**
     * Almacenar una nueva reseña
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'comment' => 'required|string|min:10',
            'score' => 'required|integer|min:1|max:5',
        ]);
        
        $data = $request->only(['title', 'comment', 'score']);
        $data['product_id'] = $product->id;
        
        $review = $this->reviewService->createReview($data, Auth::user());
        
        return redirect()->route('products.show', $product)
            ->with('success', 'Tu reseña ha sido enviada y está pendiente de aprobación.');
    }
    
    /**
     * Mostrar el formulario para editar una reseña
     */
    public function edit(Product $product, Review $review)
    {
        $this->authorize('update', $review);
        
        return view('reviews.edit', compact('product', 'review'));
    }
    
    /**
     * Actualizar una reseña existente
     */
    public function update(Request $request, Product $product, Review $review)
    {
        $this->authorize('update', $review);
        
        $request->validate([
            'title' => 'required|string|max:100',
            'comment' => 'required|string|min:10',
            'score' => 'required|integer|min:1|max:5',
        ]);
        
        $review->update([
            'title' => $request->title,
            'comment' => $request->comment,
            'is_approved' => false, // Requiere re-aprobación
            'approved_at' => null,
        ]);
        
        // Actualizar la valoración asociada
        if ($review->rating) {
            $review->rating->update(['score' => $request->score]);
        }
        
        return redirect()->route('products.show', $product)
            ->with('success', 'Tu reseña ha sido actualizada y está pendiente de aprobación.');
    }
    
    /**
     * Eliminar una reseña
     */
    public function destroy(Product $product, Review $review)
    {
        $this->authorize('delete', $review);
        
        $review->delete();
        
        return redirect()->route('products.show', $product)
            ->with('success', 'La reseña ha sido eliminada correctamente.');
    }
    
    /**
     * Enviar solo una valoración (sin reseña)
     */
    public function rateProduct(Request $request, Product $product)
    {
        $request->validate([
            'score' => 'required|integer|min:1|max:5',
        ]);
        
        $data = [
            'product_id' => $product->id,
            'score' => $request->score,
        ];
        
        $this->reviewService->createRating($data, Auth::user());
        
        return response()->json([
            'success' => true,
            'message' => 'Valoración guardada correctamente',
            'new_average' => $product->fresh()->average_rating,
            'count' => $product->fresh()->rating_count,
        ]);
    }
}