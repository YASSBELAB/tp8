<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    /**
     * GET /api/products
     * Display all products with filtering, sorting and pagination.
     */
    public function index(Request $request)
    {
        $query = Product::query();

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

        // Sorting
        $sort = $request->get('sort', 'id');
        $direction = strtolower($request->get('direction', 'asc'));

        $allowedSorts = ['id', 'name', 'price', 'quantity'];

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        // Multi-column sorting
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

        foreach ($validSorts as $field) {
            $query->orderBy($field, $direction);
        }

        // Pagination: 10 products per page
        $products = $query->paginate(10)->withQueryString();

        return ProductResource::collection($products);
    }

    /**
     * POST /api/products
     * Create a new product.
     */
    public function store(ProductStoreRequest $request)
    {
        $product = Product::create($request->validated());

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/products/{product}
     * Display one product.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * PUT/PATCH /api/products/{product}
     * Update a product.
     */
    public function update(
        ProductUpdateRequest $request,
        Product $product
    ) {
        $product->update($request->validated());

        return new ProductResource($product->fresh());
    }

    /**
     * DELETE /api/products/{product}
     * Delete a product.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ], 200);
    }
}
