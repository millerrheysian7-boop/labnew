@extends('layouts.app')

@section('title', $user->name)

@section('content')
    <h2 class="mb-4">{{ $user->name }}</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
            <p><strong>Date Registered:</strong> {{ $user->created_at->format('M d, Y h:i A') }}</p>
            <p class="mb-0"><strong>Email Verified:</strong> {{ $user->email_verified_at ? 'Yes' : 'No' }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Back to List</a>
    </div>
@endsection
