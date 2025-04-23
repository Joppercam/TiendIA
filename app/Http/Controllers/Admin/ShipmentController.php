<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Shipment;
use App\Notifications\ShipmentUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{
    /**
     * Display a listing of shipments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Shipment::with(['order.user', 'order.status']);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tracking number if provided
        if ($request->has('tracking_number')) {
            $query->where('tracking_number', 'like', '%' . $request->tracking_number . '%');
        }

        // Filter by order number if provided
        if ($request->has('order_number')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->order_number . '%');
            });
        }

        // Sort shipments
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $shipments = $query->paginate(15)->withQueryString();

        return view('admin.shipments.index', compact('shipments'));
    }

    /**
     * Show the form for creating a new shipment.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function create(Order $order)
    {
        // Check if order already has a shipment
        if ($order->shipment) {
            return redirect()->route('admin.shipments.edit', $order->shipment)
                ->with('info', 'Este pedido ya tiene un envío asociado. Puedes editarlo aquí.');
        }

        return view('admin.shipments.create', compact('order'));
    }

    /**
     * Store a newly created shipment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Order $order)
    {
        $request->validate([
            'tracking_number' => 'nullable|string|max:255',
            'shipping_company' => 'required|string|max:255',
            'status' => 'required|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($request, $order) {
            // Create shipment
            $shipment = new Shipment([
                'tracking_number' => $request->tracking_number,
                'shipping_company' => $request->shipping_company,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // Set shipped_at if status is shipped
            if ($request->status === 'shipped') {
                $shipment->shipped_at = now();
            }

            // Set delivered_at if status is delivered
            if ($request->status === 'delivered') {
                $shipment->delivered_at = now();
            }

            // Save shipment
            $order->shipment()->save($shipment);

            // Update order status if necessary
            if ($request->status === 'shipped' && $order->shipping_status !== 'shipped') {
                $shippedStatus = OrderStatus::where('slug', 'shipped')->first();
                if ($shippedStatus) {
                    $order->order_status_id = $shippedStatus->id;
                }
                $order->shipping_status = 'shipped';
                $order->shipped_at = now();
                $order->save();
            } elseif ($request->status === 'delivered' && $order->shipping_status !== 'delivered') {
                $deliveredStatus = OrderStatus::where('slug', 'delivered')->first();
                if ($deliveredStatus) {
                    $order->order_status_id = $deliveredStatus->id;
                }
                $order->shipping_status = 'delivered';
                $order->delivered_at = now();
                $order->save();
            }

            // Notify customer
            if ($order->user) {
                $order->user->notify(new ShipmentUpdate($shipment));
            }
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Envío creado correctamente.');
    }

    /**
     * Display the specified shipment.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function show(Shipment $shipment)
    {
        $shipment->load('order.user', 'order.items.product');
        
        return view('admin.shipments.show', compact('shipment'));
    }

    /**
     * Show the form for editing the specified shipment.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function edit(Shipment $shipment)
    {
        $shipment->load('order');
        
        return view('admin.shipments.edit', compact('shipment'));
    }

    /**
     * Update the specified shipment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shipment $shipment)
    {
        $request->validate([
            'tracking_number' => 'nullable|string|max:255',
            'shipping_company' => 'required|string|max:255',
            'status' => 'required|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($request, $shipment) {
            $oldStatus = $shipment->status;
            
            // Update shipment details
            $shipment->tracking_number = $request->tracking_number;
            $shipment->shipping_company = $request->shipping_company;
            $shipment->status = $request->status;
            $shipment->notes = $request->notes;

            // Update timestamps based on status
            if ($request->status === 'shipped' && is_null($shipment->shipped_at)) {
                $shipment->shipped_at = now();
            } elseif ($request->status === 'delivered' && is_null($shipment->delivered_at)) {
                $shipment->delivered_at = now();
            }

            $shipment->save();

            // Update related order status if necessary
            $order = $shipment->order;
            
            if ($request->status === 'shipped' && $order->shipping_status !== 'shipped') {
                $shippedStatus = OrderStatus::where('slug', 'shipped')->first();
                if ($shippedStatus) {
                    $order->order_status_id = $shippedStatus->id;
                }
                $order->shipping_status = 'shipped';
                $order->shipped_at = now();
                $order->save();
            } elseif ($request->status === 'delivered' && $order->shipping_status !== 'delivered') {
                $deliveredStatus = OrderStatus::where('slug', 'delivered')->first();
                if ($deliveredStatus) {
                    $order->order_status_id = $deliveredStatus->id;
                }
                $order->shipping_status = 'delivered';
                $order->delivered_at = now();
                $order->save();
            }

            // Notify customer only if status has changed
            if ($oldStatus !== $request->status && $order->user) {
                $order->user->notify(new ShipmentUpdate($shipment));
            }
        });

        return redirect()->route('admin.shipments.show', $shipment)
            ->with('success', 'Envío actualizado correctamente.');
    }

    /**
     * Update the shipment tracking information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function updateTracking(Request $request, Shipment $shipment)
    {
        $request->validate([
            'tracking_details' => 'required|array',
            'tracking_details.*.date' => 'required|date',
            'tracking_details.*.status' => 'required|string',
            'tracking_details.*.location' => 'nullable|string',
            'tracking_details.*.description' => 'nullable|string',
        ]);

        // Update tracking details as a JSON array
        $shipment->tracking_details = $request->tracking_details;
        $shipment->save();

        // Update the status to the latest tracking status
        $latestStatus = end($request->tracking_details);
        if ($latestStatus) {
            $statusName = $latestStatus['status'];
            
            // Update if not already updated
            if ($shipment->status !== $statusName) {
                $shipment->status = $statusName;
                
                // Update timestamps if necessary
                if ($statusName === 'delivered' && is_null($shipment->delivered_at)) {
                    $shipment->delivered_at = now();
                }
                
                $shipment->save();
                
                // Notify customer of the update
                if ($shipment->order->user) {
                    $shipment->order->user->notify(new ShipmentUpdate($shipment));
                }
            }
        }

        return redirect()->route('admin.shipments.show', $shipment)
            ->with('success', 'Información de seguimiento actualizada correctamente.');
    }

    /**
     * Mark shipment as delivered.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function markAsDelivered(Shipment $shipment)
    {
        DB::transaction(function () use ($shipment) {
            // Update shipment
            $shipment->status = 'delivered';
            $shipment->delivered_at = now();
            $shipment->save();

            // Update order
            $order = $shipment->order;
            $deliveredStatus = OrderStatus::where('slug', 'delivered')->first();
            
            if ($deliveredStatus) {
                $order->order_status_id = $deliveredStatus->id;
            }
            
            $order->shipping_status = 'delivered';
            $order->delivered_at = now();
            $order->save();

            // Notify customer
            if ($order->user) {
                $order->user->notify(new ShipmentUpdate($shipment));
            }
        });

        return redirect()->route('admin.shipments.show', $shipment)
            ->with('success', 'Envío marcado como entregado correctamente.');
    }

    /**
     * Add a tracking update to the shipment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function addTrackingUpdate(Request $request, Shipment $shipment)
    {
        $request->validate([
            'status' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        // Get current tracking details or initialize an empty array
        $trackingDetails = $shipment->tracking_details ?? [];

        // Add new update
        $trackingDetails[] = [
            'date' => now()->toDateTimeString(),
            'status' => $request->status,
            'location' => $request->location,
            'description' => $request->description,
        ];

        // Save updated tracking details
        $shipment->tracking_details = $trackingDetails;
        $shipment->status = $request->status; // Update current status
        
        // Update timestamps if necessary
        if ($request->status === 'shipped' && is_null($shipment->shipped_at)) {
            $shipment->shipped_at = now();
        } elseif ($request->status === 'delivered' && is_null($shipment->delivered_at)) {
            $shipment->delivered_at = now();
        }
        
        $shipment->save();

        // Update order if status has changed
        $order = $shipment->order;
        if ($request->status === 'shipped' && $order->shipping_status !== 'shipped') {
            $shippedStatus = OrderStatus::where('slug', 'shipped')->first();
            if ($shippedStatus) {
                $order->order_status_id = $shippedStatus->id;
            }
            $order->shipping_status = 'shipped';
            $order->shipped_at = now();
            $order->save();
        } elseif ($request->status === 'delivered' && $order->shipping_status !== 'delivered') {
            $deliveredStatus = OrderStatus::where('slug', 'delivered')->first();
            if ($deliveredStatus) {
                $order->order_status_id = $deliveredStatus->id;
            }
            $order->shipping_status = 'delivered';
            $order->delivered_at = now();
            $order->save();
        }

        // Notify customer
        if ($order->user) {
            $order->user->notify(new ShipmentUpdate($shipment));
        }

        return redirect()->route('admin.shipments.show', $shipment)
            ->with('success', 'Actualización de seguimiento añadida correctamente.');
    }

    /**
     * Print shipping label.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function printLabel(Shipment $shipment)
    {
        $shipment->load('order.user', 'order.address');
        
        // Logic to generate and print shipping label
        // ... [Implementación pendiente según la librería de PDF que se use] ...
        
        return view('admin.shipments.print-label', compact('shipment'));
    }
}