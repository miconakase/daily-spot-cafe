const CartManager = {
    STORAGE_KEY: 'dailySpotCart',

    getItems() {
        try {
            return JSON.parse(localStorage.getItem(this.STORAGE_KEY)) || [];
        } catch {
            return [];
        }
    },

    saveItems(items) {
        localStorage.setItem(this.STORAGE_KEY, JSON.stringify(items));
        this.updateBadge();
    },

    _key(item) {
        const v = item.variant_type || '';
        const s = item.size_label || '';
        return `${item.product_id}_${v}_${s}`;
    },

    getCount() {
        return this.getItems().reduce((sum, i) => sum + i.quantity, 0);
    },

    getTotal() {
        return this.getItems().reduce((sum, i) => sum + (i.price * i.quantity), 0);
    },

    addItem(product) {
        const items = this.getItems();
        const key = this._key(product);
        const existing = items.find(i => this._key(i) === key);
        if (existing) {
            existing.quantity += product.quantity || 1;
        } else {
            items.push({
                product_id: product.product_id,
                product_name: product.product_name,
                price: product.price,
                quantity: product.quantity || 1,
                img_url: product.img_url || '',
                variant_type: product.variant_type || null,
                size_label: product.size_label || null
            });
        }
        this.saveItems(items);
        this.renderDropdown();
    },

    removeItem(productId, variantKey) {
        let items = this.getItems();
        const target = `${productId}_${variantKey}`;
        items = items.filter(i => this._key(i) !== target);
        this.saveItems(items);
        this.renderDropdown();
    },

    updateQuantity(productId, variantKey, delta) {
        const items = this.getItems();
        const target = `${productId}_${variantKey}`;
        const item = items.find(i => this._key(i) === target);
        if (item) {
            item.quantity += delta;
            if (item.quantity <= 0) {
                this.removeItem(productId, variantKey);
                return;
            }
            this.saveItems(items);
            this.renderDropdown();
        }
    },

    renderDropdown() {
        const dropdown = document.getElementById('cartDropdown');
        if (!dropdown) return;
        const items = this.getItems();
        const body = dropdown.querySelector('.cart-dropdown-body');
        const footer = dropdown.querySelector('.cart-dropdown-footer');
        const totalEl = footer?.querySelector('.cart-total-amount');

        if (items.length === 0) {
            body.innerHTML = `
                <div class="cart-empty-state">
                    <i class="bi bi-cart3"></i>
                    <p>Your cart is empty</p>
                </div>
            `;
            const btn = footer?.querySelector('.btn-checkout');
            if (btn) btn.disabled = true;
        } else {
            let html = '';
            items.forEach(item => {
                const vk = this._key(item).replace(/^\d+_/, '');
                const img = item.img_url || 'https://placehold.co/60x60/E8D5C4/3D2B1F?text=N';
                const variantText = [item.variant_type, item.size_label].filter(Boolean).join(', ');
                html += `
                    <div class="cart-item" data-pid="${item.product_id}" data-vk="${vk}">
                        <img src="${img}" class="cart-item-img" alt="${item.product_name}">
                        <div class="cart-item-info">
                            <div class="cart-item-name">${item.product_name}</div>
                            ${variantText ? `<div class="cart-item-variant">${variantText}</div>` : ''}
                            <div class="cart-item-price">₱${parseFloat(item.price).toFixed(2)}</div>
                        </div>
                        <div class="cart-item-qty">
                            <button class="qty-btn qty-minus">−</button>
                            <span>${item.quantity}</span>
                            <button class="qty-btn qty-plus">+</button>
                        </div>
                        <button class="cart-item-remove"><i class="bi bi-x"></i></button>
                    </div>
                `;
            });
            body.innerHTML = html;
            const btn = footer?.querySelector('.btn-checkout');
            if (btn) btn.disabled = false;
        }

        if (totalEl) {
            totalEl.textContent = '₱' + this.getTotal().toFixed(2);
        }

        this._bindDropdownEvents();
    },

    _bindDropdownEvents() {
        const dropdown = document.getElementById('cartDropdown');
        if (!dropdown) return;

        dropdown.querySelectorAll('.qty-plus').forEach(btn => {
            btn.onclick = (e) => {
                e.stopPropagation();
                const el = btn.closest('.cart-item');
                if (!el) return;
                this.updateQuantity(parseInt(el.dataset.pid), el.dataset.vk, 1);
            };
        });
        dropdown.querySelectorAll('.qty-minus').forEach(btn => {
            btn.onclick = (e) => {
                e.stopPropagation();
                const el = btn.closest('.cart-item');
                if (!el) return;
                this.updateQuantity(parseInt(el.dataset.pid), el.dataset.vk, -1);
            };
        });
        dropdown.querySelectorAll('.cart-item-remove').forEach(btn => {
            btn.onclick = (e) => {
                e.stopPropagation();
                const el = btn.closest('.cart-item');
                if (!el) return;
                this.removeItem(parseInt(el.dataset.pid), el.dataset.vk);
            };
        });
    },

    updateBadge() {
        const badge = document.getElementById('cartBadge');
        if (!badge) return;
        const count = this.getCount();
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    },

    toggleDropdown() {
        const dropdown = document.getElementById('cartDropdown');
        if (!dropdown) return;
        const visible = dropdown.style.display === 'block';
        document.querySelectorAll('.cart-dropdown').forEach(el => el.style.display = 'none');
        dropdown.style.display = visible ? 'none' : 'block';
        if (!visible) this.renderDropdown();
    },

    setupEventListeners() {
        const cartIcon = document.getElementById('cartIcon');
        if (cartIcon) {
            cartIcon.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown();
                const userDropdown = document.getElementById('userDropdown');
                if (userDropdown) userDropdown.style.display = 'none';
            });
        }

        document.addEventListener('click', (e) => {
            const container = document.querySelector('.cart-container');
            if (container && !container.contains(e.target)) {
                const dd = document.getElementById('cartDropdown');
                if (dd) dd.style.display = 'none';
            }
        });

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-checkout');
            if (btn) {
                e.preventDefault();
                alert('Checkout feature coming soon!');
                const dd = document.getElementById('cartDropdown');
                if (dd) dd.style.display = 'none';
            }
        });

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.add-to-cart-btn');
            if (btn) {
                e.preventDefault();
                e.stopPropagation();
                CartManager.addItem({
                    product_id: parseInt(btn.dataset.id),
                    product_name: btn.dataset.name,
                    price: parseFloat(btn.dataset.price),
                    img_url: btn.dataset.img
                });
                const dd = document.getElementById('cartDropdown');
                if (dd) {
                    dd.style.display = 'block';
                    this.renderDropdown();
                }
            }
        });
    },

    init() {
        if (!document.getElementById('cartDropdown')) {
            const container = document.querySelector('.cart-container');
            if (!container) return;
            const dd = document.createElement('div');
            dd.id = 'cartDropdown';
            dd.className = 'cart-dropdown';
            dd.innerHTML = `
                <div class="cart-dropdown-header">
                    <h6>Your Cart</h6>
                </div>
                <div class="cart-dropdown-body"></div>
                <div class="cart-dropdown-footer">
                    <div class="cart-total-row">
                        <span>Total:</span>
                        <span class="cart-total-amount">₱0.00</span>
                    </div>
                    <button class="btn btn-checkout" disabled>Checkout</button>
                </div>
            `;
            container.appendChild(dd);
        }
        this.updateBadge();
        this.setupEventListeners();
    }
};

document.addEventListener('DOMContentLoaded', () => CartManager.init());
