@extends('layouts.app')

@section('title', 'Edit Brand')

@section('content')
    <h2 class="mb-4">Edit Brand</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('brands.update', $brand) }}">
                @method('PUT')
                @include('brands._form')
            </form>
        </div>
    </div>
@endsection
