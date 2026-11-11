<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * GET /api/products
     * Display products with search, filtering, sorting and pagination.
     */
    public function index(Request $request)
    {
        // Allowed columns for sorting
        $allowedSorts = ['id', 'name', 'price', 'stock'];

        // Get sorting parameters
        $sort = $request->query('sort', 'id');
        $direction = strtolower($request->query('direction', 'asc'));

        // Validate sorting direction
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        // Support multiple sorting columns
        $validSorts = [];

        foreach (explode(',', $sort) as $field) {
            $field = trim($field);

            if (in_array($field, $allowedSorts)) {
                $validSorts[] = $field;
            }
        }

        if (empty($validSorts)) {
            $validSorts = ['id'];
        }

        // Pagination: default 15, minimum 1, maximum 100
        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        // Eager loading + search scope
        $query = Product::with('category')
            ->search($request->query('q'));

        // Filter by name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by minimum price
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        // Filter by maximum price
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Apply sorting
        foreach ($validSorts as $field) {
            $query->orderBy($field, $direction);
        }

        // Paginate and preserve query parameters
        $products = $query->paginate($perPage)->withQueryString();

        return ProductResource::collection($products);
    }

    /**
     * POST /api/products
     * Create a new product.
     * Authorization is handled by route middleware.
     */
    public function store(ProductStoreRequest $request)
    {
        $product = Product::create($request->validated());

        return (new ProductResource($product->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/products/{product}
     * Display one product with its category.
     */
    public function show(Product $product)
    {
        $product->load('category');

        return new ProductResource($product);
    }

    /**
     * PUT/PATCH /api/products/{product}
     * Update an existing product.
     * Authorization is handled by route middleware.
     */
    public function update(
        ProductUpdateRequest $request,
        Product $product
    ) {
        $product->update($request->validated());

        return new ProductResource(
            $product->fresh()->load('category')
        );
    }

    /**
     * DELETE /api/products/{product}
     * Delete a product.
     * Authorization is handled by route middleware.
     */
   public function destroy(Product $product)
{
    // Check whether this product is used in an order
    $isUsed = DB::table('order_items')
        ->where('product_id', $product->id)
        ->exists();

    if ($isUsed) {
        return response()->json([
            'message' => 'Cannot delete this product because it is already used in an order.',
            'product_id' => $product->id
        ], 409);
    }

    $product->delete();

    return response()->json([
        'message' => 'Product deleted successfully.'
    ], 200);
}
}
