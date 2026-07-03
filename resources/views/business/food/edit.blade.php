@extends('layouts.app')

@section('title', 'Edit Food Listing')

@section('content')
<div class="d-flex">
    @include('business.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Edit Food Listing: {{ $food->name }}</h4>
            <a href="{{ route('business.food.index') }}" class="btn btn-outline-danger btn-sm px-3">Cancel</a>
        </div>

        <div class="content-area text-start" style="max-width: 800px;">
            <div class="card border-0 shadow-sm bg-white p-4 rounded-3">
                <h5 class="fw-bold mb-4 text-dark">Update Surplus Listing</h5>
                <hr>

                <form action="{{ route('business.food.update', $food->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-semibold">Food Title</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $food->name) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label fw-semibold">Food Category</label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $food->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="original_price" class="form-label fw-semibold">Original Price (Rs.)</label>
                            <input type="number" name="original_price" id="original_price" class="form-control" value="{{ old('original_price', $food->original_price) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="discount_price" class="form-label fw-semibold">Surplus Discounted Price (Rs.)</label>
                            <input type="number" name="discount_price" id="discount_price" class="form-control" value="{{ old('discount_price', $food->discount_price) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label fw-semibold">Portions Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" value="{{ old('quantity', $food->quantity) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="expiry_datetime" class="form-label fw-semibold">Expiry Date & Time</label>
                            <input type="datetime-local" name="expiry_datetime" id="expiry_datetime" class="form-control" value="{{ old('expiry_datetime', $food->expiry_datetime->format('Y-m-d\TH:i')) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label fw-semibold">Food Listing Image</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            @if($food->image)
                                <div class="mt-2 small text-muted">Current image: <a href="{{ asset('storage/' . $food->image) }}" target="_blank">View image</a></div>
                            @endif
                        </div>

                        <div class="col-12 mb-4">
                            <label for="description" class="form-label fw-semibold">Listing Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $food->description) }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success px-5 py-2">Update Listing</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
