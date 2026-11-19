@extends('layouts.app')

@section('title', 'Add Brand')

@section('content')
    <h2 class="mb-4">Add Brand</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('brands.store') }}">
                @include('brands._form')
            </form>
        </div>
    </div>
@endsection
