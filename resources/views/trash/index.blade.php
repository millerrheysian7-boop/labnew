@extends('layouts.app')

@section('title', 'Trash')

@section('content')
    <h2 class="mb-4">Trash</h2>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Deleted Products</div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr><th>Name</th><th>SKU</th><th>Category</th><th>Brand</th><th>Deleted</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td><strong>{{ $product->name }}</strong></td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->category }}</td>
                            <td>{{ $product->brand->name ?? 'No Brand' }}</td>
                            <td>{{ $product->deleted_at->diffForHumans() }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form method="POST" action="{{ route('trash.restore', ['type' => 'products', 'id' => $product->id]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">Restore</button>
                                    </form>
                                    <form method="POST" action="{{ route('trash.force-delete', ['type' => 'products', 'id' => $product->id]) }}" onsubmit="return confirm('Permanently delete this product? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete Permanently</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">Nothing here.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (auth()->user()->role === 'admin')
    <div class="card mb-4">
        <div class="card-header">Deleted Users</div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Role</th><th>Deleted</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>{{ $user->deleted_at->diffForHumans() }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form method="POST" action="{{ route('trash.restore', ['type' => 'users', 'id' => $user->id]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">Restore</button>
                                    </form>
                                    <form method="POST" action="{{ route('trash.force-delete', ['type' => 'users', 'id' => $user->id]) }}" onsubmit="return confirm('Permanently delete this user? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete Permanently</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Nothing here.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection
