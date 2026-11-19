<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TrashController extends Controller
{
    public function index()
    {
        $products = Product::onlyTrashed()
            ->with('brand')
            ->orderByDesc('deleted_at')
            ->get();

        $users = Auth::user()->role === 'admin'
            ? User::onlyTrashed()->orderByDesc('deleted_at')->get()
            : collect();

        return view('trash.index', compact('products', 'users'));
    }

    public function restore(string $type, int $id)
    {
        $model = $this->modelFor($type);

        if ($type === 'users') {
            abort_unless(Auth::user()->role === 'admin', 403, 'Only administrators can restore user accounts.');
        }

        $model::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('trash.index')
            ->with('success', $type === 'users' ? 'User restored successfully.' : 'Product restored successfully.');
    }

    public function forceDelete(string $type, int $id)
    {
        $model = $this->modelFor($type);

        if ($type === 'users') {
            abort_unless(Auth::user()->role === 'admin', 403, 'Only administrators can permanently delete user accounts.');
        }

        $model::onlyTrashed()->findOrFail($id)->forceDelete();

        return redirect()->route('trash.index')
            ->with('success', $type === 'users' ? 'User permanently deleted.' : 'Product permanently deleted.');
    }

    private function modelFor(string $type): string
    {
        return match ($type) {
            'products' => Product::class,
            'users' => User::class,
            default => abort(404),
        };
    }
}
