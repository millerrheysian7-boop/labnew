@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Category Management</h2>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">+ Add Category</a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-hover">
        <thead>
            <tr><th>#</th><th>Category Name</th><th>Description</th><th>Products</th><th>Brands</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                    <td><strong>{{ $category->name }}</strong></td>
                    <td>{{ $category->description ?: '—' }}</td>
                    <td><span class="badge bg-secondary">{{ $category->products_count }}</span></td>
                    <td>
                        @forelse ($brandsByCategory[$category->name] ?? [] as $brandName)
                            <span class="badge text-bg-light border">{{ $brandName }}</span>
                        @empty
                            —
                        @endforelse
                    </td>
                    <td>
                        <a href="{{ route('categories.show', $category) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No categories yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $categories->links() }}
@endsection
