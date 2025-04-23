<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="flex items-center text-gray-700 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        <span class="ml-2">Carrito</span>
        @if($cartItemCount > 0)
            <span class="ml-1 text-xs text-indigo-600 font-bold">({{ $cartItemCount }})</span>
        @endif
    </button>
    
    <div x-show="open" 
        x-transition:enter="transition ease-out duration-200" 
        x-transition:enter-start="opacity-0 scale-95" 
        x-transition:enter-end="opacity-100 scale-100" 
        x-transition:leave="transition ease-in duration-150" 
        x-transition:leave-start="opacity-100 scale-100" 
        x-transition:leave-end="opacity-0 scale-95" 
        class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg z-50 overflow-hidden"
        style="display: none;">
        
        <a href="{{ route('cart.index') }}" class="block py-2 px-4 text-center text-sm font-medium text-indigo-600 hover:bg-gray-50 border-t">
            Ver Carrito Completo
        </a>
    </div>
</div>