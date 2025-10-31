<!-- Carrito Lateral -->
<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-sidebar" id="cartSidebar">
    <!-- Header del carrito -->
    <div class="cart-header">
        <h3 class="cart-title">
            Mi Carrito
            <span class="cart-count" id="cartHeaderCount">0</span>
            <button class="cart-close" id="cartClose">
                <i class="fa fa-times"></i>
            </button>
        </h3>
    </div>

    <!-- Contenido del carrito -->
    <div class="cart-content">
        <!-- Estado vacío -->
        <div class="cart-empty" id="cartEmpty">
            <i class="fa fa-shopping-cart"></i>
            <h4>Tu carrito está vacío</h4>
            <p>Agrega algunos productos y aparecerán aquí</p>
        </div>

        <!-- Items del carrito -->
        <div class="cart-items" id="cartItems">
            <!-- Los items se cargarán dinámicamente aquí -->
        </div>
    </div>

    <!-- Resumen del carrito -->
    <div class="cart-summary" id="cartSummary" style="display: none;">
        <div class="cart-summary-row">
            <span class="cart-summary-label">Subtotal (<span id="cartItemCount">0</span> productos)</span>
            <span class="cart-summary-value" id="cartSubtotal">$0</span>
        </div>
        <div class="cart-summary-row">
            <span class="cart-summary-label">Envío</span>
            <span class="cart-summary-value" id="cartShipping">Gratis</span>
        </div>
        <div class="cart-total">
            <div class="cart-summary-row">
                <span class="cart-summary-label">Total</span>
                <span class="cart-summary-value" id="cartTotal">$0</span>
            </div>
            <button class="cart-checkout-btn" id="cartCheckout">
                Finalizar Compra
            </button>
        </div>
    </div>
</div>

<!-- Template para items del carrito -->
<template id="cartItemTemplate">
    <div class="cart-item" data-product-id="">
        <div class="cart-item-image">
            <img src="" alt="">
        </div>
        <div class="cart-item-details">
            <div class="cart-item-brand"></div>
            <div class="cart-item-name"></div>
            <div class="cart-item-controls">
                <div class="quantity-controls">
                    <button class="quantity-btn quantity-decrease">
                        <i class="fa fa-minus"></i>
                    </button>
                    <input type="number" class="quantity-input" value="1" min="1" max="99">
                    <button class="quantity-btn quantity-increase">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
                <div style="display: flex; align-items: center;">
                    <div class="cart-item-price"></div>
                    <button class="cart-item-remove" title="Eliminar producto">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>