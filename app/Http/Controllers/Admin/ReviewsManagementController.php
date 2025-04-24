<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Question;
use App\Models\Answer;
use App\Services\ReviewService;

class ReviewsManagementController extends Controller
{
    protected $reviewService;
    
    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
        $this->middleware(['auth', 'role:admin|editor']);
    }
    
    /**
     * Lista de reseñas pendientes de aprobación
     */
    public function pendingReviews()
    {
        $reviews = Review::where('is_approved', false)
            ->with(['user:id,name', 'product:id,name,slug'])
            ->latest()
            ->paginate(15);
            
        return view('admin.reviews.pending', compact('reviews'));
    }
    
    /**
     * Aprobar una reseña
     */
    public function approveReview(Review $review)
    {
        $this->authorize('approve', $review);
        
        $this->reviewService->approveReview($review);
        
        return redirect()->back()
            ->with('success', 'Reseña aprobada correctamente.');
    }
    
    /**
     * Rechazar (eliminar) una reseña
     */
    public function rejectReview(Review $review)
    {
        $this->authorize('delete', $review);
        
        $review->delete();
        
        return redirect()->back()
            ->with('success', 'Reseña rechazada correctamente.');
    }
    
    /**
     * Destacar o quitar destacado de una reseña
     */
    public function toggleFeatureReview(Review $review)
    {
        $this->authorize('feature', $review);
        
        $review->update([
            'is_featured' => !$review->is_featured,
        ]);
        
        $message = $review->is_featured 
            ? 'Reseña destacada correctamente.' 
            : 'Se ha quitado el destacado de la reseña.';
            
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * Lista de preguntas pendientes de aprobación
     */
    public function pendingQuestions()
    {
        $questions = Question::where('is_approved', false)
            ->with(['user:id,name', 'product:id,name,slug'])
            ->latest()
            ->paginate(15);
            
        return view('admin.reviews.pending-questions', compact('questions'));
    }
    
    /**
     * Aprobar una pregunta
     */
    public function approveQuestion(Question $question)
    {
        $this->authorize('approve', $question);
        
        $this->reviewService->approveQuestion($question);
        
        return redirect()->back()
            ->with('success', 'Pregunta aprobada correctamente.');
    }
    
    /**
     * Rechazar (eliminar) una pregunta
     */
    public function rejectQuestion(Question $question)
    {
        $this->authorize('delete', $question);
        
        $question->delete();
        
        return redirect()->back()
            ->with('success', 'Pregunta rechazada correctamente.');
    }
    
    /**
     * Lista de respuestas pendientes de aprobación
     */
    public function pendingAnswers()
    {
        $answers = Answer::where('is_approved', false)
            ->with(['user:id,name', 'question.product:id,name,slug'])
            ->latest()
            ->paginate(15);
            
        return view('admin.reviews.pending-answers', compact('answers'));
    }
    
    /**
     * Aprobar una respuesta
     */
    public function approveAnswer(Answer $answer)
    {
        $this->authorize('approve', $answer);
        
        $this->reviewService->approveAnswer($answer);
        
        return redirect()->back()
            ->with('success', 'Respuesta aprobada correctamente.');
    }
    
    /**
     * Rechazar (eliminar) una respuesta
     */
    public function rejectAnswer(Answer $answer)
    {
        $this->authorize('delete', $answer);
        
        $answer->delete();
        
        return redirect()->back()
            ->with('success', 'Respuesta rechazada correctamente.');
    }
    
    /**
     * Dashboard de estadísticas de reseñas
     */
    public function dashboard()
    {
        $stats = [
            'total_reviews' => Review::count(),
            'approved_reviews' => Review::where('is_approved', true)->count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
            'average_rating' => Review::whereHas('rating')->with('rating')->get()->avg('rating.score'),
            'total_questions' => Question::count(),
            'pending_questions' => Question::where('is_approved', false)->count(),
            'total_answers' => Answer::count(),
            'pending_answers' => Answer::where('is_approved', false)->count(),
        ];
        
        // Obtener productos con mejores/peores valoraciones
        $topRatedProducts = \App\Models\Product::withCount('ratings')
            ->having('ratings_count', '>=', 5)
            ->withAvg('ratings as average_rating', 'score')
            ->orderByDesc('average_rating')
            ->take(5)
            ->get();
            
        $lowRatedProducts = \App\Models\Product::withCount('ratings')
            ->having('ratings_count', '>=', 5)
            ->withAvg('ratings as average_rating', 'score')
            ->orderBy('average_rating')
            ->take(5)
            ->get();
            
        return view('admin.reviews.dashboard', compact('stats', 'topRatedProducts', 'lowRatedProducts'));
    }
}