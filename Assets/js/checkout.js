/**
 * Checkout - JavaScript
 * Sistema completo de checkout paso a paso
 */

class CheckoutManager {
    constructor() {
        this.currentStep = 1;
        this.cartItems = [];
        this.shippingData = {};
        this.paymentData = {
            method: 'vexor' // Establecer Vexor como método por defecto
        };
        this.shippingCost = 0;
        
        this.init();
    }

    init() {
        console.log('🚀 Inicializando Checkout Manager...');
        console.log('🔍 Verificando disponibilidad de carritoLateral:', !!window.carritoLateral);
        console.log('🔍 LocalStorage disponible:', !!localStorage);
        
        // Cargar datos del carrito
        this.loadCartData();
        
        // Solo continuar si hay items en el carrito
        if (this.cartItems && this.cartItems.length > 0) {
            // Renderizar paso inicial
            this.renderCartReview();
            this.renderSummary();
            
            // Setup event listeners
            this.setupEventListeners();
            
            // Seleccionar método de pago por defecto (Vexor)
            this.selectPaymentMethod('vexor');
        }
    }

    setupEventListeners() {
        // Navegación entre pasos
        document.getElementById('continueToShipping')?.addEventListener('click', () => this.goToStep(2));
        document.getElementById('backToCart')?.addEventListener('click', () => this.goToStep(1));
        document.getElementById('continueToPayment')?.addEventListener('click', () => this.validateShippingAndContinue());
        document.getElementById('backToShipping')?.addEventListener('click', () => this.goToStep(2));
        document.getElementById('finalizePurchase')?.addEventListener('click', () => this.processPurchase());

        // Cambio de provincia para calcular envío
        document.getElementById('provincia')?.addEventListener('change', (e) => {
            this.updateShippingCost(e.target.value);
        });

        // Métodos de pago
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.selectPaymentMethod(e.target.value);
            });
        });

        // Formateo de campos de tarjeta
        this.setupCardFormatting();
    }

    loadCartData() {
        console.log('🛒 Cargando datos del carrito...');
        
        // Obtener datos del carrito lateral si existe
        if (window.carritoLateral) {
            this.cartItems = window.carritoLateral.getItems();
            console.log('🛒 Datos cargados desde carritoLateral:', this.cartItems);
        } else {
            // Fallback: cargar desde localStorage
            const stored = localStorage.getItem('altoVoltajeCart');
            console.log('🛒 localStorage altoVoltajeCart:', stored);
            
            if (stored) {
                try {
                    this.cartItems = JSON.parse(stored);
                    console.log('🛒 Datos parseados desde localStorage:', this.cartItems);
                } catch (e) {
                    console.error('❌ Error cargando carrito:', e);
                    this.cartItems = [];
                }
            } else {
                console.log('🛒 No hay datos en localStorage');
                this.cartItems = [];
            }
        }

        console.log('🛒 CartItems final:', this.cartItems);
        console.log('🛒 Cantidad de items:', this.cartItems.length);

        // Si no hay items, redirigir a la tienda
        if (this.cartItems.length === 0) {
            alert('Tu carrito está vacío. Te redirigimos a la tienda.');
            window.location.href = window.BASE_URL + '/tienda';
            return;
        }
    }
    

    


    goToStep(stepNumber) {
        // Ocultar paso actual
        document.getElementById(`step-${this.currentStep}`).style.display = 'none';
        document.querySelector(`[data-step="${this.currentStep}"]`).classList.remove('active');
        
        // Marcar paso completado
        if (stepNumber > this.currentStep) {
            document.querySelector(`[data-step="${this.currentStep}"]`).classList.add('completed');
        }
        
        // Mostrar nuevo paso
        this.currentStep = stepNumber;
        document.getElementById(`step-${this.currentStep}`).style.display = 'block';
        document.querySelector(`[data-step="${this.currentStep}"]`).classList.add('active');
        
        // Scroll al top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    renderCartReview() {
        const container = document.getElementById('cartReviewItems');
        if (!container) return;

        container.innerHTML = '';

        this.cartItems.forEach(item => {
            const itemEl = document.createElement('div');
            itemEl.className = 'review-item';
            
            const currentPrice = item.price * item.quantity;
            const originalPrice = item.originalPrice ? item.originalPrice * item.quantity : null;
            
            itemEl.innerHTML = `
                <div class="review-item-image">
                    <img src="${item.image || window.BASE_URL + '/Assets/images/product-not-available.svg'}" 
                         alt="${item.name}">
                </div>
                <div class="review-item-details">
                    <div class="review-item-name">${item.name}</div>
                    <div class="review-item-brand">${item.brand}</div>
                    <div class="review-item-quantity">Cantidad: ${item.quantity}</div>
                </div>
                <div class="review-item-price">
                    <div class="review-item-current">${this.formatPrice(currentPrice)}</div>
                    ${originalPrice && originalPrice !== currentPrice ? 
                        `<div class="review-item-original">${this.formatPrice(originalPrice)}</div>` : ''
                    }
                </div>
            `;
            
            container.appendChild(itemEl);
        });
    }

    renderSummary() {
        const summaryItems = document.getElementById('summaryItems');
        const totalItems = document.getElementById('totalItems');
        const summarySubtotal = document.getElementById('summarySubtotal');
        const summaryShipping = document.getElementById('summaryShipping');
        const summaryTotal = document.getElementById('summaryTotal');

        if (!summaryItems) return;

        // Items resumidos
        summaryItems.innerHTML = '';
        this.cartItems.forEach(item => {
            const itemEl = document.createElement('div');
            itemEl.className = 'summary-item';
            
            itemEl.innerHTML = `
                <div class="summary-item-image">
                    <img src="${item.image || window.BASE_URL + '/Assets/images/product-not-available.svg'}" 
                         alt="${item.name}">
                </div>
                <div class="summary-item-info">
                    <span class="summary-item-name">${item.name.length > 30 ? item.name.substring(0, 30) + '...' : item.name}</span>
                    <span class="summary-item-quantity">Cantidad: ${item.quantity}</span>
                </div>
            `;
            
            summaryItems.appendChild(itemEl);
        });

        // Totales
        const itemCount = this.cartItems.reduce((total, item) => total + item.quantity, 0);
        const subtotal = this.cartItems.reduce((total, item) => total + (item.price * item.quantity), 0);
        const total = subtotal + this.shippingCost;

        if (totalItems) totalItems.textContent = itemCount;
        if (summarySubtotal) summarySubtotal.textContent = this.formatPrice(subtotal);
        if (summaryShipping) summaryShipping.textContent = this.shippingCost === 0 ? 'Gratis' : this.formatPrice(this.shippingCost);
        if (summaryTotal) summaryTotal.textContent = this.formatPrice(total);
    }

    updateShippingCost(provincia) {
        const shippingCostEl = document.getElementById('shippingCost');
        const summaryShipping = document.getElementById('summaryShipping');
        
        // Envío gratis en Formosa, $2500 en otras provincias
        this.shippingCost = provincia === 'Formosa' ? 0 : 2500;
        
        const shippingText = this.shippingCost === 0 ? 'Gratis' : this.formatPrice(this.shippingCost);
        
        if (shippingCostEl) shippingCostEl.textContent = shippingText;
        if (summaryShipping) summaryShipping.textContent = shippingText;
        
        // Actualizar total
        this.renderSummary();
    }

    validateShippingAndContinue() {
        const form = document.getElementById('shippingForm');
        const requiredFields = form.querySelectorAll('input[required], select[required]');
        let isValid = true;

        // Limpiar errores previos
        requiredFields.forEach(field => field.classList.remove('error'));

        // Validar campos requeridos
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('error');
                isValid = false;
            }
        });

        // Validar email
        const email = document.getElementById('email');
        if (email.value && !this.isValidEmail(email.value)) {
            email.classList.add('error');
            isValid = false;
        }

        if (!isValid) {
            alert('Por favor, complete todos los campos obligatorios correctamente.');
            return;
        }

        // Guardar datos de envío
        this.shippingData = {
            nombre: document.getElementById('nombre').value,
            apellido: document.getElementById('apellido').value,
            email: document.getElementById('email').value,
            telefono: document.getElementById('telefono').value,
            direccion: document.getElementById('direccion').value,
            ciudad: document.getElementById('ciudad').value,
            provincia: document.getElementById('provincia').value,
            codigo_postal: document.getElementById('codigo_postal').value
        };

        this.goToStep(3);
    }

    selectPaymentMethod(method) {
        // Remover selección previa
        document.querySelectorAll('.payment-option').forEach(option => {
            option.classList.remove('selected');
            option.querySelector('.payment-details').style.display = 'none';
        });

        // Seleccionar nuevo método
        const selectedOption = document.querySelector(`[data-method="${method}"]`);
        selectedOption.classList.add('selected');
        selectedOption.querySelector('.payment-details').style.display = 'block';

        // Validar disponibilidad de efectivo
        if (method === 'cash' && this.shippingData.provincia !== 'Formosa') {
            alert('El pago en efectivo solo está disponible para envíos en Formosa Capital.');
            document.getElementById('payment_vexor').checked = true;
            this.selectPaymentMethod('vexor');
            return;
        }

        this.paymentData.method = method;
    }

    setupCardFormatting() {
        const cardNumber = document.getElementById('card_number');
        const cardExpiry = document.getElementById('card_expiry');
        const cardCvc = document.getElementById('card_cvc');

        if (cardNumber) {
            cardNumber.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
                let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                e.target.value = formattedValue;
            });
        }

        if (cardExpiry) {
            cardExpiry.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                e.target.value = value;
            });
        }

        if (cardCvc) {
            cardCvc.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });
        }
    }

    async processPurchase() {
        const finalizePurchaseBtn = document.getElementById('finalizePurchase');
        
        // Validar método de pago seleccionado
        const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
        if (!selectedPayment) {
            alert('Por favor, selecciona un método de pago.');
            return;
        }

        // Validar datos de tarjeta si es necesario
        if (selectedPayment.value === 'card') {
            const cardNumber = document.getElementById('card_number').value;
            const cardName = document.getElementById('card_name').value;
            const cardExpiry = document.getElementById('card_expiry').value;
            const cardCvc = document.getElementById('card_cvc').value;

            if (!cardNumber || !cardName || !cardExpiry || !cardCvc) {
                alert('Por favor, complete todos los datos de la tarjeta.');
                return;
            }

            this.paymentData.details = {
                number: cardNumber,
                name: cardName,
                expiry: cardExpiry,
                cvc: cardCvc
            };
        }

        // Mostrar loading
        finalizePurchaseBtn.classList.add('loading');
        finalizePurchaseBtn.disabled = true;

        try {
            // Preparar datos para enviar
            const orderData = {
                productos: JSON.stringify(this.cartItems), // Convertir a JSON string
                ...this.shippingData,
                metodo_pago: this.paymentData.method,
                detalles_pago: JSON.stringify(this.paymentData.details || {})
            };

            console.log('📤 CartItems antes de enviar:', this.cartItems);
            console.log('📤 CartItems como JSON:', JSON.stringify(this.cartItems));
            console.log('📤 PaymentData:', this.paymentData);
            console.log('📤 ShippingData:', this.shippingData);
            console.log('📤 Datos completos del pedido:', orderData);
            console.log('📤 URLSearchParams:', new URLSearchParams(orderData).toString());
            
            // Enviar pedido al servidor
            const response = await fetch(window.BASE_URL + '/checkout/procesar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(orderData)
            });

            console.log('📥 Respuesta del servidor - Status:', response.status);
            console.log('📥 Respuesta del servidor - Headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const responseText = await response.text();
            console.log('📥 Respuesta raw del servidor:', responseText);
            
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (parseError) {
                console.error('❌ Error parseando JSON:', parseError);
                console.error('❌ Respuesta que causó el error:', responseText);
                throw new Error('Respuesta del servidor no es JSON válido: ' + responseText.substring(0, 100));
            }
            
            console.log('📥 Resultado parseado:', result);

            if (result.status) {

                console.log('🔍 Método de pago actual:', this.paymentData.method);
                console.log('🔍 Datos de respuesta:', result.data);
                console.log('🔍 ¿Tiene datos de pago?', !!(result.data.mercadopago || result.data.vexor));
                
                // Si es MercadoPago, redirigir a la plataforma de pago
                if (this.paymentData.method === 'vexor' && (result.data.mercadopago || result.data.vexor)) {
                    const paymentData = result.data.mercadopago || result.data.vexor;
                    console.log('🚀 Redirigiendo a MercadoPago...');
                    console.log('🔗 URL de pago:', paymentData.payment_url);
                    
                    // Guardar datos del pedido antes de redirigir
                    sessionStorage.setItem('pendingOrder', JSON.stringify({
                        numero_pedido: result.data.numero_pedido,
                        total: result.data.total,
                        payment_id: paymentData.payment_id
                    }));
                    
                    // Redirigir a MercadoPago
                    window.location.href = paymentData.payment_url;
                    return;
                } else {
                    console.log('⚠️ No se redirige a MercadoPago');
                    console.log('⚠️ Razón - Método:', this.paymentData.method);
                    console.log('⚠️ Razón - Datos pago:', result.data.mercadopago || result.data.vexor);
                }
                
                // Para otros métodos de pago
                // Limpiar carrito
                localStorage.removeItem('altoVoltajeCart');
                if (window.carritoLateral) {
                    window.carritoLateral.clear();
                }

                // Mostrar confirmación
                this.showOrderConfirmation(result.data);
                this.goToStep(4);
            } else {
                throw new Error(result.msg || 'Error procesando el pedido');
            }

        } catch (error) {
            console.error('Error completo:', error);
            console.error('Error message:', error.message);
            console.error('Error stack:', error.stack);
            
            // Mostrar más información del error
            let errorMsg = 'Hubo un error procesando tu pedido.';
            if (error.message) {
                errorMsg += '\nDetalle: ' + error.message;
            }
            
            alert(errorMsg + '\n\nPor favor, revisa la consola para más detalles.');
        } finally {
            // Remover loading
            finalizePurchaseBtn.classList.remove('loading');
            finalizePurchaseBtn.disabled = false;
        }
    }

    showOrderConfirmation(orderData) {
        const container = document.getElementById('orderConfirmation');
        if (!container) return;

        const subtotal = this.cartItems.reduce((total, item) => total + (item.price * item.quantity), 0);
        const total = subtotal + this.shippingCost;

        container.innerHTML = `
            <div class="confirmation-detail">
                <span class="confirmation-label">Número de pedido:</span>
                <span class="confirmation-value order-number">${orderData.numero_pedido}</span>
            </div>
            
            <div class="confirmation-detail">
                <span class="confirmation-label">Total pagado:</span>
                <span class="confirmation-value">${this.formatPrice(total)}</span>
            </div>
            
            <div class="confirmation-detail">
                <span class="confirmation-label">Método de pago:</span>
                <span class="confirmation-value">${this.getPaymentMethodName(orderData.metodo_pago)}</span>
            </div>
            
            <div class="confirmation-detail">
                <span class="confirmation-label">Envío a:</span>
                <span class="confirmation-value">${this.shippingData.direccion}, ${this.shippingData.ciudad}, ${this.shippingData.provincia}</span>
            </div>
            
            <div class="alert alert-success">
                <i class="fa fa-check-circle"></i>
                Te contactaremos pronto para coordinar la entrega. 
                También puedes comunicarte con nosotros al +54 3704-804704.
            </div>
        `;
    }

    getPaymentMethodName(method) {
        const methods = {
            'card': 'Tarjeta de crédito/débito',
            'transfer': 'Transferencia bancaria',
            'cash': 'Efectivo contra entrega'
        };
        return methods[method] || method;
    }

    formatPrice(price) {
        return `$${Number(price).toLocaleString('es-AR', { minimumFractionDigits: 0 })}`;
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.checkoutManager = new CheckoutManager();
    console.log('🛒 Checkout inicializado correctamente');
});