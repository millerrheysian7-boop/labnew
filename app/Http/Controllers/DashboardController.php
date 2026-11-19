<?php

namespace App\Http\Controllers;

use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $lowStockCount = Product::whereColumn('quantity', '<=', 'reorder_level')
            ->where('quantity', '>', 0)
            ->count();
        $outOfStockCount = Product::where('quantity', '<=', 0)->count();
        $totalValue = Product::selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0;

        $recentProducts = Product::latest()->take(5)->get();

        $categorySummary = Product::selectRaw('category, COUNT(*) as count, SUM(quantity * unit_price) as value')
            ->groupBy('category')
            ->get();

        $brandSummary = Product::leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->selectRaw("COALESCE(brands.name, 'No Brand') as brand, COUNT(*) as count, SUM(products.quantity) as units, SUM(products.quantity * products.unit_price) as value")
            ->groupBy('brands.id', 'brands.name')
            ->orderByRaw('brands.name IS NULL, brands.name')
            ->get();

        $categoryDetails = Product::selectRaw('category, COUNT(*) as count, SUM(quantity) as units, SUM(quantity * unit_price) as value')
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        $overviewProducts = Product::with('brand')->orderBy('category')->orderBy('name')->get();

        $topProducts = Product::selectRaw('name, quantity * unit_price as value')
            ->orderByDesc('value')
            ->take(5)
            ->get();

        $chartData = [
            'overview' => $overviewProducts->map(function ($p) {
                return [
                    'label' => $p->name,
                    'value' => round($p->quantity * $p->unit_price, 2),
                    'quantity' => $p->quantity,
                    'detail' => $p->category . ', ' . ($p->brand->name ?? 'No Brand') . ', ' . $p->stockStatus(),
                ];
            })->values(),
            'categories' => $categoryDetails->map(function ($row) {
                return ['label' => $row->category, 'value' => (float) $row->value];
            })->values(),
            'status' => [
                'inStock' => max(0, $totalProducts - $lowStockCount - $outOfStockCount),
                'lowStock' => $lowStockCount,
                'outOfStock' => $outOfStockCount,
            ],
            'top' => $topProducts->map(function ($row) {
                return ['label' => $row->name, 'value' => (float) $row->value];
            })->values(),
        ];

        return view('dashboard', compact(
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'totalValue',
            'recentProducts',
            'categorySummary',
            'brandSummary',
            'categoryDetails',
            'chartData'
        ));
    }
}
