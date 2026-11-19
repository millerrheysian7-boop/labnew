@extends('layouts.app')

@section('title', 'Brands')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Brand Management</h2>
        <a href="{{ route('brands.create') }}" class="btn btn-primary">+ Add Brand</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-hover">
        <thead>
            <tr><th>#</th><th>Brand Name</th><th>Description</th><th>Products</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse ($brands as $brand)
                <tr>
                    <td>{{ $loop->iteration + ($brands->currentPage() - 1) * $brands->perPage() }}</td>
                    <td><strong>{{ $brand->name }}</strong></td>
                    <td>{{ $brand->description ?: '—' }}</td>
                    <td><span class="badge bg-secondary">{{ $brand->products_count }}</span></td>
                    <td>
                        <a href="{{ route('brands.show', $brand) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        <a href="{{ route('brands.edit', $brand) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No brands yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $brands->links() }}
@endsection
