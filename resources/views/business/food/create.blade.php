@extends('layouts.app')

@section('title', 'Add Food Listing')

@section('content')
<div class="d-flex">
    @include('business.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Add Surplus Food Listing</h4>
            <a href="{{ route('business.food.index') }}" class="btn btn-outline-danger btn-sm px-3">Cancel</a>
        </div>

        <div class="content-area text-start" style="max-width: 800px;">
            <div class="card border-0 shadow-sm bg-white p-4 rounded-3">
                <h5 class="fw-bold mb-4 text-dark">List Surplus Food Item</h5>
                <hr>

                <form action="{{ route('business.food.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Please fix the following before submitting:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Template Selection Grid Section -->
                    <div class="mb-5">
                        <label class="form-label fw-bold text-dark d-block mb-3">
                            <i class="fa fa-magic text-success me-1"></i> Quick List: Select a Pre-defined Food Template (Optional)
                        </label>
                        <div class="row g-3">
                            @foreach($templates as $index => $tpl)
                                <div class="col-md-4 col-sm-6">
                                    <div class="card h-100 border-2 template-card cursor-pointer" 
                                         data-index="{{ $index }}"
                                         data-name="{{ $tpl['name'] }}"
                                         data-category="{{ $tpl['category_id'] }}"
                                         data-original-price="{{ $tpl['original_price'] }}"
                                         data-discount-price="{{ $tpl['discount_price'] }}"
                                         data-image-url="{{ $tpl['image_url'] }}"
                                         data-description="{{ $tpl['description'] }}"
                                         style="cursor: pointer; border-radius: var(--radius); overflow: hidden; transition: var(--transition); border-color: var(--border);">
                                        <div style="height: 110px; overflow: hidden; background: #eee; position: relative;">
                                            <img src="{{ asset($tpl['image_url']) }}" alt="{{ $tpl['name'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            <span class="badge bg-success position-absolute top-2 end-2 d-none checked-badge">
                                                <i class="fa fa-check"></i> Selected
                                            </span>
                                        </div>
                                        <div class="card-body p-2 text-center">
                                            <h6 class="fw-bold mb-1 small text-dark">{{ $tpl['name'] }}</h6>
                                            <div class="small text-muted" style="font-size: 0.75rem;">
                                                Rs. {{ $tpl['original_price'] }} &rarr; <span class="text-success fw-bold">Rs. {{ $tpl['discount_price'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="template_image" id="template_image" value="">
                    </div>

                    <h5 class="fw-bold mb-3 text-dark mt-4">Custom Food Details</h5>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label fw-semibold">Food Title</label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="e.g., Fish Biryani, Chocolate Pastry" value="{{ old('title') }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label fw-semibold">Food Category</label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="original_price" class="form-label fw-semibold">Original Price (Rs.)</label>
                            <input type="number" name="original_price" id="original_price" class="form-control @error('original_price') is-invalid @enderror" placeholder="Original price" value="{{ old('original_price') }}" required>
                            @error('original_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="discounted_price" class="form-label fw-semibold">Surplus Discounted Price (Rs.)</label>
                            <input type="number" name="discounted_price" id="discounted_price" class="form-control @error('discounted_price') is-invalid @enderror" placeholder="Surplus rescue price" value="{{ old('discounted_price') }}" required>
                            @error('discounted_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Must be lower than the original price.</small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label fw-semibold">Available Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" placeholder="Number of portions" value="{{ old('quantity') }}" required>
                            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="expiry_time" class="form-label fw-semibold">Expiry Date & Time</label>
                            <input type="datetime-local" name="expiry_time" id="expiry_time" class="form-control @error('expiry_time') is-invalid @enderror" value="{{ old('expiry_time') }}" required>
                            @error('expiry_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Set when the food items should be taken down or expire. Must be in the future.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label fw-semibold">Food Listing Image (Custom Upload)</label>
                            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Or upload your own custom photo. Selecting a template photo above will be cleared.</small>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="description" class="form-label fw-semibold">Listing Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Portion size, dietary ingredients, allergen advice...">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success px-5 py-2">Create Listing</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Expiry time: give it a sensible minimum (10 mins from now) and default
    // (3 hours from now), and re-check the minimum on load — datetime-local
    // inputs don't do this by themselves, so a business owner could otherwise
    // pick a time that looks fine but silently fails the backend's
    // "must be after now" validation with no visible error.
    const expiryInput = document.getElementById('expiry_time');
    if (expiryInput && !expiryInput.value) {
        function toLocalDatetimeValue(d) {
            const pad = n => String(n).padStart(2, '0');
            return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate())
                 + 'T' + pad(d.getHours()) + ':' + pad(d.getMinutes());
        }
        const now = new Date();
        expiryInput.min = toLocalDatetimeValue(new Date(now.getTime() + 10 * 60000));
        expiryInput.value = toLocalDatetimeValue(new Date(now.getTime() + 3 * 60 * 60000));
    }

    const templateCards = document.querySelectorAll('.template-card');
    const titleInput = document.getElementById('title');
    const categorySelect = document.getElementById('category_id');
    const originalPriceInput = document.getElementById('original_price');
    const discountedPriceInput = document.getElementById('discounted_price');
    const descriptionTextarea = document.getElementById('description');
    const templateImageHidden = document.getElementById('template_image');
    const fileInput = document.getElementById('image');

    templateCards.forEach(card => {
        card.addEventListener('click', function() {
            // Clear active border styles and hide checked badges on all cards
            templateCards.forEach(c => {
                c.style.borderColor = 'var(--border)';
                c.querySelector('.checked-badge').classList.add('d-none');
            });

            // Set active border and show checkmark badge for selected card
            this.style.borderColor = 'var(--green)';
            this.querySelector('.checked-badge').classList.remove('d-none');

            // Autofill form inputs with data attributes from the selected template card
            titleInput.value = this.dataset.name;
            categorySelect.value = this.dataset.category;
            originalPriceInput.value = this.dataset.originalPrice;
            discountedPriceInput.value = this.dataset.discountPrice;
            descriptionTextarea.value = this.dataset.description;
            templateImageHidden.value = this.dataset.imageUrl;

            // Clear the file upload input since template is selected
            fileInput.value = '';
        });
    });

    // Clear template selections when a custom image file is selected manually
    fileInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            templateCards.forEach(c => {
                c.style.borderColor = 'var(--border)';
                c.querySelector('.checked-badge').classList.add('d-none');
            });
            templateImageHidden.value = '';
        }
    });
});
</script>
@endsection
