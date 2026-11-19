<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->with('brand')->orderBy('name')->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->pluck('name');

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->pluck('name');
        $brands = Brand::orderBy('name')->get();

        return view('products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product added successfully.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
        $product->load('brand');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->pluck('name');
        $brands = Brand::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request, $product->id);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product moved to trash.');
    }

    private function validateProduct(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'required', 'string', 'max:50',
                'unique:products,sku' . ($ignoreId ? ",{$ignoreId}" : ''),
            ],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'exists:categories,name'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'brand_id' => ['nullable', 'exists:brands,id'],
        ], [
            'sku.unique' => 'This SKU is already in use by another product.',
            'quantity.min' => 'Quantity cannot be negative.',
            'unit_price.min' => 'Unit price cannot be negative.',
        ]);
    }
}
