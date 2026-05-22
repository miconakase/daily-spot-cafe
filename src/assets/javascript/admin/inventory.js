const productImage = document.getElementById('productImage');
const imagePreview = document.getElementById('image-preview');

if (productImage) {
    productImage.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.innerHTML = `<i class="bi bi-image text-muted fs-3"></i>`;
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const addProductForm = document.getElementById('addProductForm');
    const productImage = document.getElementById('productImage');
    const imagePreview = document.getElementById('image-preview');

    productImage.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
            };
            reader.readAsDataURL(file);
        }
    });

    addProductForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const longDescription = quill.root.innerHTML;
        const formData = new FormData();
        formData.append('action', 'create');
        formData.append('product_name', document.querySelector('input[placeholder="Enter product name"]').value);
        formData.append('category_id', document.querySelector('select[name="category_id"]').value);
        formData.append('price', document.querySelector('input[placeholder="0.00"]').value);
        formData.append('short_description', document.querySelector('input[type="text"].form-control.bg-light.border-0').value);
        formData.append('long_description', longDescription);
        formData.append('featured_image', productImage.files[0]);

        fetch('api/ProductsService.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                modal.hide();
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 3000);
                addProductForm.reset();
                imagePreview.innerHTML = '<i class="bi bi-image text-muted fs-3"></i>';
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Something went wrong!', 'error');
        });
    });
});

document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-product-id]');
    if (!btn) return;

    document.getElementById('editProductId').value = btn.getAttribute('data-product-id');
    document.getElementById('editProductDisplayId').value = btn.getAttribute('data-product-id');
    document.getElementById('editProductName').value = btn.getAttribute('data-product-name');
    document.getElementById('editCategoryId').value = btn.getAttribute('data-category-id');
    document.getElementById('editPrice').value = btn.getAttribute('data-price');
    document.getElementById('editShortDescription').value = btn.getAttribute('data-short-description');
    document.getElementById('editStatus').value = btn.getAttribute('data-status');

    new bootstrap.Modal(document.getElementById('editProductModal')).show();
});

document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('product_id', document.getElementById('editProductId').value);
    formData.append('product_name', document.getElementById('editProductName').value);
    formData.append('category_id', document.getElementById('editCategoryId').value);
    formData.append('price', document.getElementById('editPrice').value);
    formData.append('short_description', document.getElementById('editShortDescription').value);
    formData.append('is_published', document.getElementById('editStatus').value);

    fetch('api/ProductsService.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 3000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Something went wrong!', 'error');
    });
});

function deleteProduct(id) {
    document.getElementById('deleteProductId').value = id;
    new bootstrap.Modal(document.getElementById('deleteProductModal')).show();
}

document.getElementById('confirmDeleteProductBtn').addEventListener('click', function() {
    const id = document.getElementById('deleteProductId').value;
    bootstrap.Modal.getInstance(document.getElementById('deleteProductModal')).hide();

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('product_id', id);

    fetch('api/ProductsService.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 3000);
        } else {
            showToast(data.message, 'error');
        }
    });
});
