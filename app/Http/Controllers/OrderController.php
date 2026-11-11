<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * GET /api/orders
     * Display all orders with clients and products.
     */
    public function index()
    {
        $orders = Order::with([
            'client',
            'items.product'
        ])->paginate(10);

        return OrderResource::collection($orders);
    }

    /**
     * POST /api/orders
     * Create an order with multiple items using a transaction.
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();

        $order = DB::transaction(function () use ($data) {

            // Create the order
            $order = Order::create([
                'client_id' => $data['client_id'],
                'total_amount' => 0,
                'status' => 'pending',
            ]);

            $total = 0;

            // Create order items
            foreach ($data['items'] as $item) {

                $product = Product::findOrFail($item['product_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ]);

                // Calculate total amount
                $total += $product->price * $item['quantity'];
            }

            // Update order total
            $order->update([
                'total_amount' => $total,
            ]);

            return $order;
        });

        // Return formatted resource with message and HTTP 201
        return response()->json([
            'message' => 'Order created successfully',
            'data' => new OrderResource(
                $order->load(['client', 'items.product'])
            )
        ], 201);
    }

    /**
     * GET /api/orders/{order}
     * Display one order with its client and products.
     */
    public function show(Order $order)
    {
        $order->load([
            'client',
            'items.product'
        ]);

        return new OrderResource($order);
    }

    /**
     * PUT/PATCH /api/orders/{order}
     * Update an order.
     */
    public function update()
    {
        return response()->json([
            'message' => 'Order update is not implemented yet.'
        ], 501);
    }

    /**
     * DELETE /api/orders/{order}
     * Delete an order.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->noContent();
    }
}
