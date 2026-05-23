let quillAdd = null;
let quillEdit = null;

function initQuill(containerId) {
    const toolbarOptions = [
        ['bold', 'italic', 'underline'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['clean']
    ];
    return new Quill('#' + containerId, {
        theme: 'snow',
        placeholder: 'Enter detailed product description...',
        modules: { toolbar: toolbarOptions }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const addModal = document.getElementById('addProductModal');
    if (addModal) {
        addModal.addEventListener('shown.bs.modal', function () {
            if (!quillAdd) {
                const container = document.getElementById('editor-container');
                if (container) quillAdd = initQuill('editor-container');
            }
        });
    }
    const addForm = document.getElementById('addProductForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            const input = document.getElementById('long_description');
            if (quillAdd && input) input.value = quillAdd.root.innerHTML;
        });
    }

    const editModal = document.getElementById('editProductModal');
    if (editModal) {
        editModal.addEventListener('shown.bs.modal', function () {
            if (!quillEdit) {
                const container = document.getElementById('edit-editor-container');
                if (container) quillEdit = initQuill('edit-editor-container');
            }
            const longDesc = editModal.getAttribute('data-long-description');
            if (quillEdit && longDesc) {
                quillEdit.root.innerHTML = longDesc;
            }
        });
    }
    const editForm = document.getElementById('editProductForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            const input = document.getElementById('edit_long_description');
            if (quillEdit && input) input.value = quillEdit.root.innerHTML;
        });
    }
});
