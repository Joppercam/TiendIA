<?php

namespace App\Interfaces;

interface PaymentGatewayInterface
{
    /**
     * Inicializar una transacción de pago
     */
    public function initializePayment(array $data);
    
    /**
     * Procesar una transacción de pago
     */
    public function processPayment(array $data);
    
    /**
     * Verificar el estado de un pago
     */
    public function verifyPayment($reference);
    
    /**
     * Procesar un reembolso
     */
    public function processRefund(array $data);
    
    /**
     * Procesar una notificación (webhook) desde la pasarela
     */
    public function handleWebhook(array $data);
}