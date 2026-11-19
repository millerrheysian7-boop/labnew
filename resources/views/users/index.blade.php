@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>User Management</h2>
        <a href="{{ route('users.create') }}" class="btn btn-primary">+ Add User</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-hover">
        <thead>
            <tr><th>#</th><th>Name</th><th>Email Address</th><th>Role</th><th>Email Status</th><th>Date Registered</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                    <td>
                        <strong>{{ $user->name }}</strong>
                        @if ($user->id === auth()->id())
                            <span class="badge bg-success ms-1">You</span>
                        @endif
                    </td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-secondary' }}">{{ ucfirst($user->role) }}</span></td>
                    <td>
                        @if ($user->hasVerifiedEmail())
                            <span class="badge bg-success">Verified</span>
                        @else
                            <span class="badge bg-warning text-dark">Unverified</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        @if (! $user->hasVerifiedEmail() && (auth()->user()->role === 'admin' || $user->id === auth()->id()))
                            <form method="POST" action="{{ route('users.send-verification', $user) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-success">Verify Email</button>
                            </form>
                        @endif
                        @if (auth()->user()->role === 'admin' && $user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Delete this user account?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->links() }}
@endsection
