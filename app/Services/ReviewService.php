<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Rating;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    /**
     * Crear una reseña con su valoración
     */
    public function createReview(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            // Verificar si el usuario ha comprado el producto
            $isVerifiedPurchase = $this->hasUserPurchasedProduct(
                $user->id, 
                $data['product_id']
            );
            
            // Crear la reseña
            $review = Review::create([
                'user_id' => $user->id,
                'product_id' => $data['product_id'],
                'title' => $data['title'],
                'comment' => $data['comment'],
                'is_verified_purchase' => $isVerifiedPurchase,
                'is_approved' => false, // Requiere aprobación
            ]);
            
            // Crear la valoración asociada
            $rating = Rating::create([
                'user_id' => $user->id,
                'product_id' => $data['product_id'],
                'review_id' => $review->id,
                'score' => $data['score'],
            ]);
            
            return $review;
        });
    }
    
    /**
     * Crear una valoración sin reseña
     */
    public function createRating(array $data, User $user)
    {
        // Verificar si ya existe una valoración para este usuario y producto
        $existingRating = Rating::where('user_id', $user->id)
            ->where('product_id', $data['product_id'])
            ->first();
            
        if ($existingRating) {
            $existingRating->update(['score' => $data['score']]);
            return $existingRating;
        }
        
        return Rating::create([
            'user_id' => $user->id,
            'product_id' => $data['product_id'],
            'score' => $data['score'],
        ]);
    }
    
    /**
     * Aprobar una reseña
     */
    public function approveReview(Review $review)
    {
        return $review->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);
    }
    
    /**
     * Destacar una reseña
     */
    public function featureReview(Review $review)
    {
        return $review->update([
            'is_featured' => true,
        ]);
    }
    
    /**
     * Crear una pregunta
     */
    public function createQuestion(array $data, User $user)
    {
        return Question::create([
            'user_id' => $user->id,
            'product_id' => $data['product_id'],
            'question' => $data['question'],
            'is_approved' => false, // Requiere aprobación
        ]);
    }
    
    /**
     * Aprobar una pregunta
     */
    public function approveQuestion(Question $question)
    {
        return $question->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);
    }
    
    /**
     * Crear una respuesta
     */
    public function createAnswer(array $data, User $user)
    {
        $isSeller = $user->hasRole(['seller', 'admin']);
        
        return Answer::create([
            'question_id' => $data['question_id'],
            'user_id' => $user->id,
            'answer' => $data['answer'],
            'is_from_seller' => $isSeller,
            'is_approved' => $isSeller, // Auto-aprobación para vendedores
            'approved_at' => $isSeller ? now() : null,
        ]);
    }
    
    /**
     * Aprobar una respuesta
     */
    public function approveAnswer(Answer $answer)
    {
        return $answer->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);
    }
    
    /**
     * Verificar si un usuario ha comprado un producto
     */
    protected function hasUserPurchasedProduct($userId, $productId)
    {
        return Order::where('user_id', $userId)
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->where('status', 'completed')
            ->exists();
    }
    
    /**
     * Obtener reseñas aprobadas para un producto
     */
    public function getApprovedReviews(Product $product, $perPage = 10)
    {
        return $product->reviews()
            ->with('user:id,name')
            ->with('rating')
            ->approved()
            ->latest()
            ->paginate($perPage);
    }
    
    /**
     * Obtener preguntas aprobadas para un producto
     */
    public function getApprovedQuestions(Product $product, $perPage = 10)
    {
        return $product->questions()
            ->with('user:id,name')
            ->with(['answers' => function ($query) {
                $query->approved()->latest();
            }])
            ->with('answers.user:id,name')
            ->approved()
            ->latest()
            ->paginate($perPage);
    }
    
    /**
     * Obtener resumen de valoraciones para un producto
     */
    public function getRatingsSummary(Product $product)
    {
        $ratings = $product->ratings();
        
        $summary = [
            'average' => $ratings->avg('score') ?: 0,
            'count' => $ratings->count(),
            'distribution' => [],
        ];
        
        // Obtener distribución de valoraciones (1-5)
        for ($i = 5; $i >= 1; $i--) {
            $count = $ratings->where('score', $i)->count();
            $percentage = $summary['count'] > 0 
                ? round(($count / $summary['count']) * 100) 
                : 0;
                
            $summary['distribution'][$i] = [
                'count' => $count,
                'percentage' => $percentage,
            ];
        }
        
        return $summary;
    }
}