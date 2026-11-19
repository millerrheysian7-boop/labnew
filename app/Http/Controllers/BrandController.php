<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')->orderBy('name')->paginate(10);

        return view('brands.index', compact('brands'));
    }

    public function create()
    {
        return view('brands.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateBrand($request);

        Brand::create($validated);

        return redirect()->route('brands.index')->with('success', 'Brand added successfully.');
    }

    public function show(Brand $brand)
    {
        $brand->loadCount('products');
        $products = $brand->products()->orderBy('name')->paginate(10);

        return view('brands.show', compact('brand', 'products'));
    }

    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $this->validateBrand($request, $brand->id);

        $brand->update($validated);

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        return back()->with('error', 'Brands cannot be deleted.');
    }

    private function validateBrand(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:brands,name' . ($ignoreId ? ",{$ignoreId}" : '')],
            'description' => ['nullable', 'string'],
        ], [
            'name.unique' => 'This brand already exists.',
        ]);
    }
}
