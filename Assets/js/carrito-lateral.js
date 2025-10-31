/**
 * Carrito Lateral - JavaScript
 * Manejo completo del carrito de compras
 */

class CarritoLateral {
    constructor() {
        this.items = [];
        this.isOpen = false;
        this.init();
        this.loadFromStorage();
    }

    init() {
        // Elementos del DOM
        this.cartSidebar = document.getElementById('cartSidebar');
        this.cartOverlay = document.getElementById('cartOverlay');
        this.cartClose = document.getElementById('cartClose');
        this.cartEmpty = document.getElementById('cartEmpty');
        this.cartItems = document.getElementById('cartItems');
        this.cartSummary = document.getElementById('cartSummary');
        this.cartHeaderCount = document.getElementById('cartHeaderCount');
        this.cartItemCount = document.getElementById('cartItemCount');
        this.cartSubtotal = document.getElementById('cartSubtotal');
        this.cartTotal = document.getElementById('cartTotal');
        this.cartCheckout = document.getElementById('cartCheckout');
        this.cartItemTemplate = document.getElementById('cartItemTemplate');

        // Event listeners
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Cerrar carrito
        this.cartClose.addEventListener('click', () => this.close());
        this.cartOverlay.addEventListener('click', () => this.close());

        // Checkout
        this.cartCheckout.addEventListener('click', () => this.checkout());

        // Escape key para cerrar
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });

        // Event delegation para items del carrito
        this.cartItems.addEventListener('click', (e) => {
            const cartItem = e.target.closest('.cart-item');
            if (!cartItem) return;

            const productId = parseInt(cartItem.dataset.productId);

            if (e.target.closest('.quantity-decrease')) {
                this.decreaseQuantity(productId);
            } else if (e.target.closest('.quantity-increase')) {
                this.increaseQuantity(productId);
            } else if (e.target.closest('.cart-item-remove')) {
                this.removeItem(productId);
            }
        });

        // Input de cantidad
        this.cartItems.addEventListener('change', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                const cartItem = e.target.closest('.cart-item');
                const productId = parseInt(cartItem.dataset.productId);
                const newQuantity = parseInt(e.target.value);
                
                if (newQuantity > 0 && newQuantity <= 99) {
                    this.updateQuantity(productId, newQuantity);
                } else {
                    e.target.value = this.getItem(productId).quantity;
                }
            }
        });
    }

    // Abrir carrito
    open() {
        this.isOpen = true;
        this.cartSidebar.classList.add('open');
        this.cartOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Trigger event personalizado
        window.dispatchEvent(new CustomEvent('cartOpened'));
    }

    // Cerrar carrito
    close() {
        this.isOpen = false;
        this.cartSidebar.classList.remove('open');
        this.cartOverlay.classList.remove('active');
        document.body.style.overflow = '';
        
        // Trigger event personalizado
        window.dispatchEvent(new CustomEvent('cartClosed'));
    }

    // Toggle carrito
    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    // Agregar producto al carrito
    addItem(product) {
        const existingItem = this.items.find(item => item.id === product.id);
        
        if (existingItem) {
            existingItem.quantity += product.quantity || 1;
        } else {
            this.items.push({
                id: product.id,
                name: product.name,
                brand: product.brand || 'Alto Voltaje',
                price: product.price,
                originalPrice: product.originalPrice || product.price,
                image: product.image,
                quantity: product.quantity || 1
            });
        }

        this.saveToStorage();
        this.render();
        this.updateCartCount();
        
        // Mostrar notificación
        this.showNotification(`${product.name} agregado al carrito`);
        
        // Trigger event personalizado
        window.dispatchEvent(new CustomEvent('cartUpdated', { 
            detail: { items: this.items } 
        }));
    }

    // Eliminar producto del carrito
    removeItem(productId) {
        const itemIndex = this.items.findIndex(item => item.id === productId);
        if (itemIndex > -1) {
            const removedItem = this.items.splice(itemIndex, 1)[0];
            this.saveToStorage();
            this.render();
            this.updateCartCount();
            
            this.showNotification(`${removedItem.name} eliminado del carrito`, 'warning');
            
            window.dispatchEvent(new CustomEvent('cartUpdated', { 
                detail: { items: this.items } 
            }));
        }
    }

    // Obtener item del carrito
    getItem(productId) {
        return this.items.find(item => item.id === productId);
    }

    // Actualizar cantidad
    updateQuantity(productId, newQuantity) {
        const item = this.getItem(productId);
        if (item) {
            item.quantity = Math.max(1, Math.min(99, newQuantity));
            this.saveToStorage();
            this.render();
            this.updateCartCount();
            
            window.dispatchEvent(new CustomEvent('cartUpdated', { 
                detail: { items: this.items } 
            }));
        }
    }

    // Aumentar cantidad
    increaseQuantity(productId) {
        const item = this.getItem(productId);
        if (item && item.quantity < 99) {
            this.updateQuantity(productId, item.quantity + 1);
        }
    }

    // Disminuir cantidad
    decreaseQuantity(productId) {
        const item = this.getItem(productId);
        if (item) {
            if (item.quantity > 1) {
                this.updateQuantity(productId, item.quantity - 1);
            } else {
                this.removeItem(productId);
            }
        }
    }

    // Limpiar carrito
    clear() {
        this.items = [];
        this.saveToStorage();
        this.render();
        this.updateCartCount();
        
        this.showNotification('Carrito vaciado', 'info');
        
        window.dispatchEvent(new CustomEvent('cartCleared'));
    }

    // Renderizar carrito
    render() {
        if (this.items.length === 0) {
            this.cartEmpty.style.display = 'block';
            this.cartItems.style.display = 'none';
            this.cartSummary.style.display = 'none';
        } else {
            this.cartEmpty.style.display = 'none';
            this.cartItems.style.display = 'block';
            this.cartSummary.style.display = 'block';
            
            this.renderItems();
            this.renderSummary();
        }
    }

    // Renderizar items
    renderItems() {
        this.cartItems.innerHTML = '';
        
        this.items.forEach(item => {
            const cartItemEl = this.createCartItemElement(item);
            this.cartItems.appendChild(cartItemEl);
        });
    }

    // Crear elemento de item del carrito
    createCartItemElement(item) {
        const template = this.cartItemTemplate.content.cloneNode(true);
        const cartItem = template.querySelector('.cart-item');
        
        cartItem.dataset.productId = item.id;
        
        // Imagen
        const img = template.querySelector('img');
        img.src = item.image || window.BASE_URL + '/Assets/images/product-not-available.svg';
        img.alt = item.name;
        
        // Detalles
        template.querySelector('.cart-item-brand').textContent = item.brand;
        template.querySelector('.cart-item-name').textContent = item.name;
        template.querySelector('.quantity-input').value = item.quantity;
        
        // Precio
        const priceEl = template.querySelector('.cart-item-price');
        const currentPrice = this.formatPrice(item.price * item.quantity);
        
        if (item.originalPrice && item.originalPrice !== item.price) {
            const originalPrice = this.formatPrice(item.originalPrice * item.quantity);
            priceEl.innerHTML = `${currentPrice} <span class="cart-item-price-old">${originalPrice}</span>`;
        } else {
            priceEl.textContent = currentPrice;
        }
        
        return template;
    }

    // Renderizar resumen
    renderSummary() {
        const itemCount = this.getTotalItems();
        const subtotal = this.getSubtotal();
        
        this.cartItemCount.textContent = itemCount;
        this.cartSubtotal.textContent = this.formatPrice(subtotal);
        this.cartTotal.textContent = this.formatPrice(subtotal); // Sin shipping por ahora
    }

    // Obtener total de items
    getTotalItems() {
        return this.items.reduce((total, item) => total + item.quantity, 0);
    }

    // Obtener subtotal
    getSubtotal() {
        return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    // Actualizar contador del carrito
    updateCartCount() {
        const count = this.getTotalItems();
        this.cartHeaderCount.textContent = count;
        
        // Actualizar contador en el botón del carrito principal si existe
        const mainCartCount = document.querySelector('.cart-count-main');
        if (mainCartCount) {
            mainCartCount.textContent = count;
            mainCartCount.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }

    // Formatear precio
    formatPrice(price) {
        return `$${Number(price).toLocaleString('es-AR', { minimumFractionDigits: 0 })}`;
    }

    // Mostrar notificación
    showNotification(message, type = 'success') {
        // Crear notificación toast simple
        const notification = document.createElement('div');
        notification.className = `cart-notification cart-notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#28a745' : type === 'warning' ? '#ffc107' : '#17a2b8'};
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animar entrada
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remover después de 3 segundos
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Guardar en localStorage
    saveToStorage() {
        localStorage.setItem('altoVoltajeCart', JSON.stringify(this.items));
    }

    // Cargar desde localStorage
    loadFromStorage() {
        const stored = localStorage.getItem('altoVoltajeCart');
        if (stored) {
            try {
                this.items = JSON.parse(stored);
                this.render();
                this.updateCartCount();
            } catch (e) {
                console.error('Error cargando carrito desde localStorage:', e);
                this.items = [];
            }
        }
    }

    // Checkout
    checkout() {
        if (this.items.length === 0) {
            this.showNotification('El carrito está vacío', 'warning');
            return;
        }

        // Redirigir a la página de checkout
        window.location.href = window.BASE_URL + '/checkout';
    }

    // Métodos públicos para integración
    getItems() {
        return [...this.items];
    }

    getItemCount() {
        return this.getTotalItems();
    }

    getTotal() {
        return this.getSubtotal();
    }
}

// Inicializar carrito cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Crear instancia global del carrito
    window.carritoLateral = new CarritoLateral();
    
    // Función global para agregar al carrito (para usar desde los botones)
    window.addToCart = function(productId, productName, productPrice, productImage, productBrand) {
        const product = {
            id: parseInt(productId),
            name: productName || 'Producto sin nombre',
            price: parseFloat(productPrice) || 0,
            image: productImage || '',
            brand: productBrand || 'Alto Voltaje',
            quantity: 1
        };
        
        window.carritoLateral.addItem(product);
        
        // Abrir carrito automáticamente al agregar producto
        setTimeout(() => {
            window.carritoLateral.open();
        }, 300);
    };
    
    // Función global para abrir carrito
    window.openCart = function() {
        window.carritoLateral.open();
    };
    
    // Función global para cerrar carrito
    window.closeCart = function() {
        window.carritoLateral.close();
    };
    
    // Función global para toggle carrito
    window.toggleCart = function() {
        window.carritoLateral.toggle();
    };
    
    console.log('🛒 Carrito lateral inicializado correctamente');
});