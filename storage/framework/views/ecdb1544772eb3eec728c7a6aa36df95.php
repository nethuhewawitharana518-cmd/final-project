<?php $__env->startSection('title', 'Add Food Listing'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex">
    <?php echo $__env->make('business.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Add Surplus Food Listing</h4>
            <a href="<?php echo e(route('business.food.index')); ?>" class="btn btn-outline-danger btn-sm px-3">Cancel</a>
        </div>

        <div class="content-area text-start" style="max-width: 800px;">
            <div class="card border-0 shadow-sm bg-white p-4 rounded-3">
                <h5 class="fw-bold mb-4 text-dark">List Surplus Food Item</h5>
                <hr>

                <form action="<?php echo e(route('business.food.store')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <!-- Template Selection Grid Section -->
                    <div class="mb-5">
                        <label class="form-label fw-bold text-dark d-block mb-3">
                            <i class="fa fa-magic text-success me-1"></i> Quick List: Select a Pre-defined Food Template (Optional)
                        </label>
                        <div class="row g-3">
                            <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-4 col-sm-6">
                                    <div class="card h-100 border-2 template-card cursor-pointer" 
                                         data-index="<?php echo e($index); ?>"
                                         data-name="<?php echo e($tpl['name']); ?>"
                                         data-category="<?php echo e($tpl['category_id']); ?>"
                                         data-original-price="<?php echo e($tpl['original_price']); ?>"
                                         data-discount-price="<?php echo e($tpl['discount_price']); ?>"
                                         data-image-url="<?php echo e($tpl['image_url']); ?>"
                                         data-description="<?php echo e($tpl['description']); ?>"
                                         style="cursor: pointer; border-radius: var(--radius); overflow: hidden; transition: var(--transition); border-color: var(--border);">
                                        <div style="height: 110px; overflow: hidden; background: #eee; position: relative;">
                                            <img src="<?php echo e(asset($tpl['image_url'])); ?>" alt="<?php echo e($tpl['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            <span class="badge bg-success position-absolute top-2 end-2 d-none checked-badge">
                                                <i class="fa fa-check"></i> Selected
                                            </span>
                                        </div>
                                        <div class="card-body p-2 text-center">
                                            <h6 class="fw-bold mb-1 small text-dark"><?php echo e($tpl['name']); ?></h6>
                                            <div class="small text-muted" style="font-size: 0.75rem;">
                                                Rs. <?php echo e($tpl['original_price']); ?> &rarr; <span class="text-success fw-bold">Rs. <?php echo e($tpl['discount_price']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <input type="hidden" name="template_image" id="template_image" value="">
                    </div>

                    <h5 class="fw-bold mb-3 text-dark mt-4">Custom Food Details</h5>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label fw-semibold">Food Title</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="e.g., Fish Biryani, Chocolate Pastry" value="<?php echo e(old('title')); ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label fw-semibold">Food Category</label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="original_price" class="form-label fw-semibold">Original Price (Rs.)</label>
                            <input type="number" name="original_price" id="original_price" class="form-control" placeholder="Original price" value="<?php echo e(old('original_price')); ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="discounted_price" class="form-label fw-semibold">Surplus Discounted Price (Rs.)</label>
                            <input type="number" name="discounted_price" id="discounted_price" class="form-control" placeholder="Surplus rescue price" value="<?php echo e(old('discounted_price')); ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label fw-semibold">Available Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Number of portions" value="<?php echo e(old('quantity')); ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="expiry_time" class="form-label fw-semibold">Expiry Date & Time</label>
                            <input type="datetime-local" name="expiry_time" id="expiry_time" class="form-control" value="<?php echo e(old('expiry_time')); ?>" required>
                            <small class="text-muted">Set when the food items should be taken down or expire.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label fw-semibold">Food Listing Image (Custom Upload)</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            <small class="text-muted">Or upload your own custom photo. Selecting a template photo above will be cleared.</small>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="description" class="form-label fw-semibold">Listing Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control" placeholder="Portion size, dietary ingredients, allergen advice..."><?php echo e(old('description')); ?></textarea>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/business/food/create.blade.php ENDPATH**/ ?>