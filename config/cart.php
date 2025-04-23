<?php
// config/cart.php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración del Módulo de Carrito de Compras
    |--------------------------------------------------------------------------
    */

    // Tiempo de vida (en días) para los carritos de invitados abandonados
    'guest_cart_lifetime' => 30,
    
    // Tiempo de vida (en días) para los carritos de usuarios autenticados abandonados
    'user_cart_lifetime' => 60,
    
    // Permitir carritos simultáneos para un mismo usuario
    'allow_multiple_carts' => false,
    
    // Permitir guardar precios especiales en el carrito
    'save_special_prices' => true,
    
    // Cantidad máxima de un mismo producto que se puede añadir al carrito
    'max_quantity_per_item' => 99,
    
    // Máximo número de productos diferentes en el carrito
    'max_items' => 50,
    
    // Mantener el carrito después de un pedido completado
    'keep_cart_after_order' => false,
];