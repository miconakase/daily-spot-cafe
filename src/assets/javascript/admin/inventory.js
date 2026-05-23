// --- Reusable helpers ---

function buildVariantTable(headerEl, bodyEl, hasVariants, hasSizes, existingVariants, inputClass) {
    inputClass = inputClass || 'variant-price';
    const variantOpts = hasVariants ? ['Hot', 'Iced'] : [''];
    const sizeOpts = hasSizes ? ['Grande', 'Venti'] : [''];

    let headerHtml = '';
    if (hasVariants) headerHtml += '<th>Variant</th>';
    if (hasSizes) headerHtml += '<th>Size</th>';
    headerHtml += '<th>Price (PHP)</th>';
    headerEl.innerHTML = headerHtml;

    let bodyHtml = '';
    variantOpts.forEach(v => {
        sizeOpts.forEach(s => {
            const key = (hasVariants ? v : '') + '|' + (hasSizes ? s : '');
            const existing = existingVariants ? existingVariants.find(x =>
                (x.variant_type || '') === (v || '') && (x.size_label || '') === (s || '')
            ) : null;
            const val = existing ? existing.price : '';
            bodyHtml += '<tr>';
            if (hasVariants) bodyHtml += `<td>${v}</td>`;
            if (hasSizes) bodyHtml += `<td>${s}</td>`;
            bodyHtml += `<td><input type="number" step="0.01" class="form-control form-control-sm ${inputClass}" data-variant="${v}" data-size="${s}" placeholder="0.00" value="${val}" required></td>`;
            bodyHtml += '</tr>';
        });
    });
    bodyEl.innerHTML = bodyHtml;
}

function setupCategoryVariantToggle(catSelect, sectionEl, wrapperEl, priceInput, headerEl, bodyEl, existingVariants, inputClass, displayEl) {
    if (!catSelect) return;
    catSelect.addEventListener('change', function() {
        const catId = this.value;
        if (!catId) {
            sectionEl.style.display = 'none';
            wrapperEl.style.display = 'block';
            priceInput.required = true;
            return;
        }
        fetch(`api/get_category_config.php?category_id=${catId}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                if (data.has_variants || data.has_sizes) {
                    sectionEl.style.display = 'block';
                    wrapperEl.style.display = 'none';
                    priceInput.required = false;
                    const cls = inputClass || (headerEl.id && headerEl.id.startsWith('add') ? 'variant-price' : 'edit-variant-price');
                    buildVariantTable(headerEl, bodyEl, data.has_variants, data.has_sizes, existingVariants || [], cls);
                    setTimeout(function() {
                        updateLowestPriceDisplay('.' + cls, displayEl);
                        setupVariantPriceListeners('.' + cls, displayEl);
                    }, 0);
                } else {
                    sectionEl.style.display = 'none';
                    wrapperEl.style.display = 'block';
                    priceInput.required = true;
                }
            });
    });
}

function collectVariantData(selector) {
    const inputs = document.querySelectorAll(selector);
    if (inputs.length === 0) return null;
    const data = [];
    inputs.forEach(input => {
        data.push({
            variant_type: input.dataset.variant || null,
            size_label: input.dataset.size || null,
            price: input.value
        });
    });
    return data;
}

function getLowestVariantPrice(selector) {
    const inputs = document.querySelectorAll(selector);
    if (inputs.length === 0) return null;
    let lowest = Infinity;
    inputs.forEach(input => {
        const val = parseFloat(input.value);
        if (!isNaN(val) && val < lowest) lowest = val;
    });
    return lowest === Infinity ? null : lowest;
}

function updateLowestPriceDisplay(selector, displayEl) {
    if (!displayEl) return;
    const lowest = getLowestVariantPrice(selector);
    displayEl.textContent = lowest !== null ? '₱' + lowest.toFixed(2) : '₱0.00';
}

function setupVariantPriceListeners(selector, displayEl) {
    const inputs = document.querySelectorAll(selector);
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            updateLowestPriceDisplay(selector, displayEl);
        });
    });
}

function handleImagePreview(fileInput, previewEl) {
    if (!fileInput || !previewEl) return;
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewEl.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
            };
            reader.readAsDataURL(file);
        } else {
            previewEl.innerHTML = `<i class="bi bi-image text-muted fs-3"></i>`;
        }
    });
}

// --- ADD PRODUCT MODAL ---

const addCatSelect = document.querySelector('select[name="category_id"]');
const addVariantsSection = document.getElementById('variantsSection');
const addPriceWrapper = document.getElementById('priceFieldWrapper');
const addBasePrice = document.getElementById('basePrice');
const addVariantHeader = document.getElementById('variantHeader');
const addVariantBody = document.getElementById('variantBody');

setupCategoryVariantToggle(addCatSelect, addVariantsSection, addPriceWrapper, addBasePrice, addVariantHeader, addVariantBody, [], 'variant-price', document.getElementById('addLowestPriceDisplay'));

handleImagePreview(document.getElementById('productImage'), document.getElementById('image-preview'));

document.getElementById('addProductForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('product_name', document.getElementById('productName').value);
    formData.append('category_id', addCatSelect.value);
    formData.append('short_description', document.getElementById('shortDescription').value);
    formData.append('long_description', document.getElementById('long_description').value);
    formData.append('is_published', document.getElementById('addStatus').value);
    formData.append('is_available', document.getElementById('addAvailability').value);
    formData.append('featured_image', document.getElementById('productImage').files[0]);

    const variants = collectVariantData('.variant-price');
    if (variants) {
        formData.append('variants', JSON.stringify(variants));
        const lowest = getLowestVariantPrice('.variant-price');
        formData.append('price', lowest !== null ? lowest : 0);
    } else {
        formData.append('price', addBasePrice.value);
    }

    fetch('api/ProductsService.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 3000);
                this.reset();
                document.getElementById('image-preview').innerHTML = '<i class="bi bi-image text-muted fs-3"></i>';
                addVariantsSection.style.display = 'none';
                addPriceWrapper.style.display = 'block';
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(() => showToast('Something went wrong!', 'error'));
});

// --- EDIT PRODUCT MODAL ---

const editCatSelect = document.getElementById('editCategoryId');
const editVariantsSection = document.getElementById('editVariantsSection');
const editPriceWrapper = document.getElementById('editPriceFieldWrapper');
const editPrice = document.getElementById('editPrice');
const editVariantHeader = document.getElementById('editVariantHeader');
const editVariantBody = document.getElementById('editVariantBody');

handleImagePreview(document.getElementById('editProductImage'), document.getElementById('edit-image-preview'));

// Click handler to populate edit modal from button data attributes
document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-product-id]');
    if (!btn) return;

    document.getElementById('editProductId').value = btn.dataset.productId;
    document.getElementById('editProductDisplayId').value = btn.dataset.productId;
    document.getElementById('editProductName').value = btn.dataset.productName;
    document.getElementById('editCategoryId').value = btn.dataset.categoryId;
    document.getElementById('editPrice').value = btn.dataset.price;
    document.getElementById('editShortDescription').value = btn.dataset.shortDescription;
    document.getElementById('editStatus').value = btn.dataset.status;
    document.getElementById('editAvailability').value = btn.dataset.isAvailable;

    // Store long description on the modal for Quill to pick up
    document.getElementById('editProductModal').setAttribute('data-long-description', btn.dataset.longDescription || '');

    // Show current image preview
    const imgUrl = btn.dataset.imgUrl;
    const editPreview = document.getElementById('edit-image-preview');
    if (imgUrl) {
        editPreview.innerHTML = `<img src="../${imgUrl}" style="width: 100%; height: 100%; object-fit: cover;">`;
    } else {
        editPreview.innerHTML = '<i class="bi bi-image text-muted fs-3"></i>';
    }

    // Set up variant toggle with existing variants
    const existingVariants = btn.dataset.variants ? JSON.parse(btn.dataset.variants) : [];
    const hasVariants = parseInt(btn.dataset.hasVariants);
    const hasSizes = parseInt(btn.dataset.hasSizes);

    // Remove old listener by cloning + replacing
    const newSelect = editCatSelect.cloneNode(true);
    editCatSelect.parentNode.replaceChild(newSelect, editCatSelect);
    // Re-bind the reference
    const newEditCatSelect = document.getElementById('editCategoryId');
    newEditCatSelect.value = btn.dataset.categoryId;

    if (hasVariants || hasSizes) {
        editVariantsSection.style.display = 'block';
        editPriceWrapper.style.display = 'none';
        editPrice.required = false;
        buildVariantTable(editVariantHeader, editVariantBody, hasVariants, hasSizes, existingVariants, 'edit-variant-price');
        setTimeout(function() {
            updateLowestPriceDisplay('.edit-variant-price', document.getElementById('editLowestPriceDisplay'));
            setupVariantPriceListeners('.edit-variant-price', document.getElementById('editLowestPriceDisplay'));
        }, 0);
    } else {
        editVariantsSection.style.display = 'none';
        editPriceWrapper.style.display = 'block';
        editPrice.required = true;
    }

    setupCategoryVariantToggle(newEditCatSelect, editVariantsSection, editPriceWrapper, editPrice, editVariantHeader, editVariantBody, existingVariants, 'edit-variant-price', document.getElementById('editLowestPriceDisplay'));

    new bootstrap.Modal(document.getElementById('editProductModal')).show();
});

document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('product_id', document.getElementById('editProductId').value);
    formData.append('product_name', document.getElementById('editProductName').value);
    formData.append('category_id', document.getElementById('editCategoryId').value);
    formData.append('short_description', document.getElementById('editShortDescription').value);
    formData.append('long_description', document.getElementById('edit_long_description').value);
    formData.append('is_published', document.getElementById('editStatus').value);
    formData.append('is_available', document.getElementById('editAvailability').value);

    const fileInput = document.getElementById('editProductImage');
    if (fileInput.files[0]) {
        formData.append('featured_image', fileInput.files[0]);
    }

    const variants = collectVariantData('.edit-variant-price');
    if (variants) {
        formData.append('variants', JSON.stringify(variants));
        const lowest = getLowestVariantPrice('.edit-variant-price');
        formData.append('price', lowest !== null ? lowest : 0);
    } else {
        formData.append('price', document.getElementById('editPrice').value);
    }

    fetch('api/ProductsService.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 3000);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(() => showToast('Something went wrong!', 'error'));
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
