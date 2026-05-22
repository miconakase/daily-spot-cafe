document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-light.btn-sm[data-category-id]');
    if (!btn) return;

    document.getElementById('editCategoryId').value = btn.getAttribute('data-category-id');
    document.getElementById('editCategoryDisplayId').value = btn.getAttribute('data-category-id');
    document.getElementById('editCategoryName').value = btn.getAttribute('data-category-name');
    document.getElementById('editCategoryDescription').value = btn.getAttribute('data-description');
    document.getElementById('editCategoryStatus').value = btn.getAttribute('data-is-active');

    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
});

document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('category_id', document.getElementById('editCategoryId').value);
    formData.append('category_name', document.getElementById('editCategoryName').value);
    formData.append('description', document.getElementById('editCategoryDescription').value);
    formData.append('is_active', document.getElementById('editCategoryStatus').value);

    fetch('api/CategoriesService.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editCategoryModal')).hide();
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

function deleteCategory(id) {
    document.getElementById('deleteCategoryId').value = id;
    new bootstrap.Modal(document.getElementById('deleteCategoryModal')).show();
}

document.getElementById('confirmDeleteCategoryBtn').addEventListener('click', function() {
    const id = document.getElementById('deleteCategoryId').value;
    bootstrap.Modal.getInstance(document.getElementById('deleteCategoryModal')).hide();

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('category_id', id);

    fetch('api/CategoriesService.php', {
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
