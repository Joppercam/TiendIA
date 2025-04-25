<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PDF;

class InvoiceService
{
    /**
     * Genera una factura para una orden
     */
    public function generateInvoice(Order $order, Payment $payment = null)
    {
        try {
            // Verificar si ya existe una factura para esta orden
            if ($order->invoice) {
                return $order->invoice;
            }
            
            // Generar número de factura único
            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
            
            // Preparar datos del cliente
            $customerDetails = [
                'name' => $order->billing_name ?? $order->user->name,
                'email' => $order->billing_email ?? $order->user->email,
                'address' => $order->billing_address,
                'city' => $order->billing_city,
                'state' => $order->billing_state,
                'postal_code' => $order->billing_postal_code,
                'country' => $order->billing_country,
                'rut' => $order->billing_rut ?? '',
                'phone' => $order->billing_phone ?? $order->user->profile->phone ?? ''
            ];
            
            // Preparar items de la factura
            $items = [];
            foreach ($order->items as $item) {
                $items[] = [
                    'name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->quantity * $item->price
                ];
            }
            
            // Calcular subtotal y total
            $subtotal = $order->subtotal;
            $tax = $order->tax;
            $total = $order->total;
            
            // Crear la factura en la base de datos
            $invoice = Invoice::create([
                'order_id' => $order->id,
                'payment_id' => $payment ? $payment->id : null,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => now(),
                'due_date' => now()->addDays(15),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => 'paid',
                'customer_details' => $customerDetails,
                'items' => $items
            ]);
            
            // Generar PDF de la factura
            $pdf = PDF::loadView('invoices.template', [
                'invoice' => $invoice,
                'order' => $order,
                'payment' => $payment
            ]);
            
            // Guardar el PDF
            $pdfPath = 'invoices/' . $invoice->invoice_number . '.pdf';
            Storage::disk('public')->put($pdfPath, $pdf->output());
            
            // Actualizar la factura con la ruta del PDF
            $invoice->update(['pdf_path' => $pdfPath]);
            
            return $invoice;
        } catch (Exception $e) {
            Log::error('Error al generar factura: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Obtiene el PDF de una factura
     */
    public function getInvoicePdf(Invoice $invoice)
    {
        try {
            if ($invoice->pdf_path) {
                return Storage::disk('public')->get($invoice->pdf_path);
            }
            
            // Si no existe el PDF, regenerarlo
            $pdf = PDF::loadView('invoices.template', [
                'invoice' => $invoice,
                'order' => $invoice->order,
                'payment' => $invoice->payment
            ]);
            
            // Guardar el PDF
            $pdfPath = 'invoices/' . $invoice->invoice_number . '.pdf';
            Storage::disk('public')->put($pdfPath, $pdf->output());
            
            // Actualizar la factura con la ruta del PDF
            $invoice->update(['pdf_path' => $pdfPath]);
            
            return $pdf->output();
        } catch (Exception $e) {
            Log::error('Error al obtener PDF de factura: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
}