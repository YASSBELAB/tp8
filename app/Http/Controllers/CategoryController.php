<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(
            Category::with('products')->paginate(10)
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($validated);

        return (new CategoryResource($category->load('products')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Category $category)
    {
        $category->load('products');

        return new CategoryResource($category);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
            'description' => 'sometimes|nullable|string',
        ]);

        $category->update($validated);

        return new CategoryResource($category->fresh()->load('products'));
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->noContent();
    }
}
