<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Client;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * GET /api/orders
     * Display all orders for admin, or only own orders for normal users.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Order::with([
            'client',
            'items.product'
        ]);

        if ($user && ! $user->hasRole('ADMIN')) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('client_id', $user->id)
                  ->orWhereHas('client', function ($clientQuery) use ($user) {
                      $clientQuery->where('email', $user->email);
                  });
            });
        }

        $orders = $query->paginate(10);

        return OrderResource::collection($orders);
    }

    /**
     * POST /api/orders
     * Create an order with multiple items using a transaction.
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        $order = DB::transaction(function () use ($data, $user) {

            $clientId = $data['client_id'] ?? null;

            if (! $clientId && $user) {
                $client = Client::firstOrCreate(
                    ['email' => $user->email],
                    [
                        'first_name' => explode(' ', $user->name)[0] ?? $user->name,
                        'last_name' => explode(' ', $user->name, 2)[1] ?? 'User',
                    ]
                );
                $clientId = $client->id;
            }

            // Create the order
            $order = Order::create([
                'user_id' => $user?->id,
                'client_id' => $clientId,
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
     * Update an order (status, client_id, and/or items).
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $data = $request->validated();

        DB::transaction(function () use ($order, $data) {
            if (isset($data['status'])) {
                $order->status = $data['status'];
            }

            if (isset($data['client_id'])) {
                $order->client_id = $data['client_id'];
            }

            if (isset($data['items'])) {
                $order->items()->delete();

                $total = 0;
                foreach ($data['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                    ]);

                    $total += $product->price * $item['quantity'];
                }

                $order->total_amount = $total;
            }

            $order->save();
        });

        return new OrderResource(
            $order->fresh()->load(['client', 'items.product'])
        );
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
