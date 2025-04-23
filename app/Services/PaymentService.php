<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;

class PaymentService
{
    public function processPayment(Order $order, PaymentMethod $paymentMethod)
    {
        // Based on payment method, process the payment
        switch ($paymentMethod->code) {
            case 'credit_card':
                return $this->processCreditCardPayment($order);
            
            case 'paypal':
                return $this->processPaypalPayment($order);
                
            case 'bank_transfer':
                return $this->processBankTransferPayment($order);
                
            case 'cash_on_delivery':
                return $this->processCashOnDeliveryPayment($order);
                
            default:
                throw new \Exception('Método de pago no soportado');
        }
    }
    
    protected function processCreditCardPayment(Order $order)
    {
        // Implement credit card payment logic
        // This would typically integrate with a payment gateway
        
        // For now, we'll create a payment record with pending status
        return $this->createPaymentRecord($order, 'pending', 'credit_card');
    }
    
    protected function processPaypalPayment(Order $order)
    {
        // Implement PayPal payment logic
        
        return $this->createPaymentRecord($order, 'pending', 'paypal');
    }
    
    protected function processBankTransferPayment(Order $order)
    {
        // Implement bank transfer payment logic
        
        return $this->createPaymentRecord($order, 'pending', 'bank_transfer');
    }
    
    protected function processCashOnDeliveryPayment(Order $order)
    {
        // Implement cash on delivery payment logic
        
        return $this->createPaymentRecord($order, 'pending', 'cash_on_delivery');
    }
    
    protected function createPaymentRecord(Order $order, string $status, string $paymentType)
    {
        return Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total,
            'payment_type' => $paymentType,
            'status' => $status,
            'transaction_id' => null, // Would be filled with real transaction ID from payment gateway
        ]);
    }
    
    public function confirmPayment(Order $order, $transactionId)
    {
        $payment = $order->payment;
        
        if (!$payment) {
            throw new \Exception('No existe un pago para esta orden');
        }
        
        $payment->update([
            'status' => 'completed',
            'transaction_id' => $transactionId,
            'paid_at' => now(),
        ]);
        
        $order->update([
            'status' => 'processing',
        ]);
        
        return $payment;
    }
    
    public function cancelPayment(Order $order, $reason)
    {
        $payment = $order->payment;
        
        if (!$payment) {
            throw new \Exception('No existe un pago para esta orden');
        }
        
        $payment->update([
            'status' => 'cancelled',
            'notes' => $reason,
        ]);
        
        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
        
        return $payment;
    }
}