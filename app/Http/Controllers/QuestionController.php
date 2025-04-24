<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Question;
use App\Models\Answer;
use App\Services\ReviewService;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    protected $reviewService;
    
    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
        $this->middleware('auth')->except(['index', 'show']);
    }
    
    /**
     * Mostrar todas las preguntas de un producto
     */
    public function index(Product $product)
    {
        $questions = $this->reviewService->getApprovedQuestions($product);
        
        return view('questions.index', compact('product', 'questions'));
    }
    
    /**
     * Almacenar una nueva pregunta
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'question' => 'required|string|min:10|max:500',
        ]);
        
        $data = [
            'product_id' => $product->id,
            'question' => $request->question,
        ];
        
        $question = $this->reviewService->createQuestion($data, Auth::user());
        
        return redirect()->route('products.show', $product)
            ->with('success', 'Tu pregunta ha sido enviada y está pendiente de aprobación.');
    }
    
    /**
     * Editar una pregunta
     */
    public function edit(Product $product, Question $question)
    {
        $this->authorize('update', $question);
        
        return view('questions.edit', compact('product', 'question'));
    }
    
    /**
     * Actualizar una pregunta existente
     */
    public function update(Request $request, Product $product, Question $question)
    {
        $this->authorize('update', $question);
        
        $request->validate([
            'question' => 'required|string|min:10|max:500',
        ]);
        
        $question->update([
            'question' => $request->question,
            'is_approved' => false, // Requiere re-aprobación
            'approved_at' => null,
        ]);
        
        return redirect()->route('products.questions.index', $product)
            ->with('success', 'Tu pregunta ha sido actualizada y está pendiente de aprobación.');
    }
    
    /**
     * Eliminar una pregunta
     */
    public function destroy(Product $product, Question $question)
    {
        $this->authorize('delete', $question);
        
        $question->delete();
        
        return redirect()->route('products.questions.index', $product)
            ->with('success', 'La pregunta ha sido eliminada correctamente.');
    }
    
    /**
     * Responder a una pregunta
     */
    public function storeAnswer(Request $request, Product $product, Question $question)
    {
        $request->validate([
            'answer' => 'required|string|min:10|max:500',
        ]);
        
        $data = [
            'question_id' => $question->id,
            'answer' => $request->answer,
        ];
        
        $answer = $this->reviewService->createAnswer($data, Auth::user());
        
        return redirect()->route('products.questions.index', $product)
            ->with('success', 'Tu respuesta ha sido enviada correctamente.');
    }
    
    /**
     * Editar una respuesta
     */
    public function editAnswer(Product $product, Question $question, Answer $answer)
    {
        $this->authorize('update', $answer);
        
        return view('questions.edit-answer', compact('product', 'question', 'answer'));
    }
    
    /**
     * Actualizar una respuesta existente
     */
    public function updateAnswer(Request $request, Product $product, Question $question, Answer $answer)
    {
        $this->authorize('update', $answer);
        
        $request->validate([
            'answer' => 'required|string|min:10|max:500',
        ]);
        
        $answer->update([
            'answer' => $request->answer,
            'is_approved' => $answer->is_from_seller, // Solo auto-aprobación para vendedores
            'approved_at' => $answer->is_from_seller ? now() : null,
        ]);
        
        return redirect()->route('products.questions.index', $product)
            ->with('success', 'Tu respuesta ha sido actualizada correctamente.');
    }
    
    /**
     * Eliminar una respuesta
     */
    public function destroyAnswer(Product $product, Question $question, Answer $answer)
    {
        $this->authorize('delete', $answer);
        
        $answer->delete();
        
        return redirect()->route('products.questions.index', $product)
            ->with('success', 'La respuesta ha sido eliminada correctamente.');
    }
}