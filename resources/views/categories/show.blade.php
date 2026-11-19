@extends('layouts.app')

@section('title', $category->name)

@section('content')
    <h2 class="mb-4">{{ $category->name }}</h2>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Description:</strong> {{ $category->description ?: '—' }}</p>
            <p class="mb-0"><strong>Total Products:</strong> {{ $category->products_count }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Products Under This Category</div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Name</th><th>SKU</th><th>Brand</th><th>Quantity</th></tr></thead>
                <tbody>
                    @forelse ($products as $p)
                        <tr>
                            <td><a href="{{ route('products.show', $p) }}">{{ $p->name }}</a></td>
                            <td>{{ $p->sku }}</td>
                            <td>{{ $p->brand->name ?? '—' }}</td>
                            <td>{{ $p->quantity }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">No products linked to this category yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $products->links() }}

    <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary">Edit</a>
    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Back to List</a>
@endsection
