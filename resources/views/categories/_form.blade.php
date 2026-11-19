@csrf

<div class="mb-3">
    <label class="form-label">Category Name</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name ?? '') }}">
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description ?? '') }}</textarea>
</div>

<button type="submit" class="btn btn-primary">Save Category</button>
<a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
