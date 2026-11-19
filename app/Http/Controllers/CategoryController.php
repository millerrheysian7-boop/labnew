<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->paginate(10);

        $brandsByCategory = Product::join('brands', 'products.brand_id', '=', 'brands.id')
            ->select('products.category', 'brands.name')
            ->distinct()
            ->orderBy('brands.name')
            ->get()
            ->groupBy('category')
            ->map(function ($rows) {
                return $rows->pluck('name');
            });

        return view('categories.index', compact('categories', 'brandsByCategory'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCategory($request);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category added successfully.');
    }

    public function show(Category $category)
    {
        $category->loadCount('products');
        $products = $category->products()->with('brand')->orderBy('name')->paginate(10);

        return view('categories.show', compact('category', 'products'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $this->validateCategory($request, $category->id);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        return back()->with('error', 'Categories cannot be deleted.');
    }

    private function validateCategory(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name' . ($ignoreId ? ",{$ignoreId}" : '')],
            'description' => ['nullable', 'string'],
        ], [
            'name.unique' => 'This category already exists.',
        ]);
    }
}
