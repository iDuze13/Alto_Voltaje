/**
 * Sistema de Ventas - JavaScript
 * Maneja todo el frontend del sistema de ventas
 */

/**
 * Sistema de Ventas - JavaScript
 * Maneja todo el frontend del sistema de ventas
 */

// ===== VARIABLES GLOBALES =====
let cart = [];
// Nota: BASE_URL se define en la vista desde PHP antes de cargar este script

// ===== FUNCIONES DE UTILIDAD =====

/**
 * Muestra alertas personalizadas
 */
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#007bff'};
        color: white;
        border-radius: 8px;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 300);
    }, 4000);
}

/**
 * Formatea un precio en formato argentino
 */
function formatPrice(price) {
    return parseFloat(price).toLocaleString('es-AR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// ===== FUNCIONES DEL CARRITO =====

/**
 * Agrega un producto al carrito
 */
function addToCart(producto) {
    console.log('Agregando producto:', producto);
    
    if (!producto || !producto.idProducto) {
        showAlert('Error: Producto no válido', 'error');
        return;
    }

    const existingItem = cart.find(item => item.idProducto == producto.idProducto);
    
    if (existingItem) {
        if (existingItem.cantidad < producto.stock) {
            existingItem.cantidad++;
            existingItem.subtotal = existingItem.cantidad * existingItem.precio;
            showAlert(`Cantidad actualizada: ${producto.nombre}`, 'success');
        } else {
            showAlert('No hay suficiente stock disponible', 'error');
            return;
        }
    } else {
        cart.push({
            idProducto: producto.idProducto,
            nombre: producto.nombre,
            precio: parseFloat(producto.precio),
            cantidad: 1,
            subtotal: parseFloat(producto.precio),
            stock: producto.stock,
            categoria: producto.categoria || 'N/A'
        });
        showAlert(`Producto agregado: ${producto.nombre}`, 'success');
    }

    updateCartDisplay();
}

/**
 * Elimina un producto del carrito
 */
function removeFromCart(productId) {
    const item = cart.find(item => item.idProducto == productId);
    cart = cart.filter(item => item.idProducto != productId);
    if (item) {
        showAlert(`Producto removido: ${item.nombre}`, 'info');
    }
    updateCartDisplay();
}

/**
 * Actualiza la cantidad de un producto en el carrito
 */
function updateQuantity(productId, newQuantity) {
    const item = cart.find(item => item.idProducto == productId);
    if (!item) return;

    if (newQuantity <= 0) {
        removeFromCart(productId);
        return;
    }
    
    if (newQuantity > item.stock) {
        showAlert('No hay suficiente stock disponible', 'error');
        return;
    }

    item.cantidad = newQuantity;
    item.subtotal = item.cantidad * item.precio;
    updateCartDisplay();
}

/**
 * Actualiza la visualización del carrito
 */
function updateCartDisplay() {
    const cartItemsContainer = document.getElementById('cartItems');
    const cartSummary = document.getElementById('cartSummary');
    const paymentSection = document.getElementById('paymentSection');

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = `
            <div class="empty-cart">
                <div>🛒</div>
                <p>El carrito está vacío</p>
                <small>Agrega productos para comenzar la venta</small>
            </div>
        `;
        cartSummary.style.display = 'none';
        paymentSection.style.display = 'none';
        return;
    }

    // Generar HTML de items
    cartItemsContainer.innerHTML = cart.map(item => `
        <div class="cart-item">
            <div class="cart-item-header">
                <div class="cart-item-name">${item.nombre}</div>
                <button class="remove-btn" onclick="removeFromCart(${item.idProducto})">×</button>
            </div>
            <div style="font-size: 11px; color: #888; margin-bottom: 8px;">
                ${item.categoria} | Stock: ${item.stock}
            </div>
            <div class="cart-item-controls">
                <div class="quantity-controls">
                    <button class="qty-btn" onclick="updateQuantity(${item.idProducto}, ${item.cantidad - 1})">-</button>
                    <span class="quantity">${item.cantidad}</span>
                    <button class="qty-btn" onclick="updateQuantity(${item.idProducto}, ${item.cantidad + 1})">+</button>
                </div>
                <div class="item-total">$${formatPrice(item.subtotal)}</div>
            </div>
            <div style="font-size: 12px; color: #888; margin-top: 5px;">
                $${formatPrice(item.precio)} c/u
            </div>
        </div>
    `).join('');

    // Calcular totales
    const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
    const iva = subtotal * 0.21;
    const total = subtotal + iva;

    // Actualizar resumen
    document.getElementById('subtotal').textContent = formatPrice(subtotal);
    document.getElementById('iva').textContent = formatPrice(iva);
    document.getElementById('total').textContent = formatPrice(total);

    cartSummary.style.display = 'block';
    paymentSection.style.display = 'block';
}

/**
 * Vacía el carrito
 */
function clearCart() {
    if (cart.length > 0) {
        if (confirm('¿Está seguro de que desea vaciar el carrito?')) {
            cart = [];
            updateCartDisplay();
            showAlert('Carrito vaciado', 'info');
        }
    }
}

// ===== FUNCIONES DE VENTA =====

/**
 * Procesa la venta
 */
function processSale() {
    if (cart.length === 0) {
        showAlert('El carrito está vacío', 'error');
        return;
    }
    
    const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
    const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
    const iva = subtotal * 0.21;
    const total = subtotal + iva;
    
    // Recopilar datos del cliente si es transferencia
    let datosCliente = {};
    if (paymentMethod === 'Transferencia') {
        const nombre = document.getElementById('clienteNombre')?.value || '';
        const alias = document.getElementById('clienteAlias')?.value || '';
        const cbu = document.getElementById('clienteCBU')?.value || '';
        
        if (!nombre || !alias) {
            showAlert('Complete los datos del cliente para transferencia', 'error');
            return;
        }
        
        datosCliente = { nombre, alias, cbu };
    }
    
    const saleData = {
        productos: cart.map(item => ({
            id: item.idProducto,
            nombre: item.nombre,
            cantidad: item.cantidad,
            precio: item.precio,
            subtotal: item.subtotal
        })),
        subtotal: subtotal,
        iva: iva,
        total: total,
        metodo_pago: paymentMethod,
        datos_cliente: datosCliente
    };
    
    // Deshabilitar botón
    const processBtn = document.getElementById('processBtn');
    processBtn.disabled = true;
    processBtn.textContent = 'Procesando...';
    
    // Enviar al servidor
    fetch(BASE_URL + '/ventas/procesarVenta', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(saleData)
    })
    .then(response => response.json())
    .then(data => {
        processBtn.disabled = false;
        processBtn.textContent = 'Procesar Venta';
        
        if (data.success) {
            showAlert('¡Venta procesada exitosamente!', 'success');
            showReceipt(data, saleData);
            cart = [];
            updateCartDisplay();
        } else {
            showAlert('Error: ' + (data.error || 'Error desconocido'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        processBtn.disabled = false;
        processBtn.textContent = 'Procesar Venta';
        showAlert('Error de conexión al procesar la venta', 'error');
    });
}

/**
 * Muestra el recibo de la venta
 */
function showReceipt(ventaData, saleData) {
    const modal = document.getElementById('receiptModal');
    
    // Información básica
    document.getElementById('receiptNumber').textContent = ventaData.numero_venta;
    document.getElementById('receiptDate').textContent = new Date().toLocaleString('es-AR');
    document.getElementById('receiptEmployee').textContent = ventaData.empleado_nombre || 'Empleado';
    
    // Items
    let itemsHTML = '';
    saleData.productos.forEach(item => {
        itemsHTML += `
            <div class="receipt-item">
                <div>
                    <strong>${item.nombre}</strong><br>
                    <small>${item.cantidad} x $${formatPrice(item.precio)}</small>
                </div>
                <span>$${formatPrice(item.subtotal)}</span>
            </div>
        `;
    });
    document.getElementById('receiptItems').innerHTML = itemsHTML;
    
    // Totales
    document.getElementById('receiptSubtotal').textContent = formatPrice(saleData.subtotal);
    document.getElementById('receiptIVA').textContent = formatPrice(saleData.iva);
    document.getElementById('receiptTotal').textContent = formatPrice(saleData.total);
    document.getElementById('receiptPayment').textContent = saleData.metodo_pago.toUpperCase().replace('_', ' ');
    
    // Datos del cliente (si es transferencia)
    const receiptClientData = document.getElementById('receiptClientData');
    if (saleData.metodo_pago === 'Transferencia' && saleData.datos_cliente) {
        receiptClientData.innerHTML = `
            <div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                <strong>Datos del Cliente:</strong><br>
                <strong>Nombre:</strong> ${saleData.datos_cliente.nombre}<br>
                <strong>Alias:</strong> ${saleData.datos_cliente.alias}
                ${saleData.datos_cliente.cbu ? `<br><strong>CBU:</strong> ${saleData.datos_cliente.cbu}` : ''}
            </div>
        `;
    } else {
        receiptClientData.innerHTML = '';
    }
    
    modal.style.display = 'flex';
}

/**
 * Cierra el modal del recibo
 */
function closeReceipt() {
    document.getElementById('receiptModal').style.display = 'none';
}

/**
 * Imprime el recibo
 */
function printReceipt() {
    window.print();
}

// ===== FUNCIONES DE BÚSQUEDA =====

/**
 * Filtra productos en la tabla
 */
function filtrarProductos() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#productosTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

// ===== ESCANEO DE CÓDIGO DE BARRAS =====

let codigoBuffer = '';
let ultimaTecla = Date.now();
let timerEscaneo = null;
let contadorTeclas = 0;

/**
 * Procesa un código escaneado
 */
function procesarCodigoEscaneado(codigo) {
    console.log('Código escaneado:', codigo);
    
    const rows = document.querySelectorAll('#productosTable tbody tr');
    let encontrado = false;
    
    rows.forEach(row => {
        const codigoBarrasCell = row.cells[6];
        if (codigoBarrasCell) {
            const textoBarras = codigoBarrasCell.textContent.replace(/\s/g, '');
            const codigoLimpio = codigo.replace(/\s/g, '');
            
            if (textoBarras.includes(codigoLimpio)) {
                encontrado = true;
                const btnAgregar = row.querySelector('.add-btn');
                
                if (btnAgregar && !btnAgregar.disabled) {
                    btnAgregar.click();
                    row.style.backgroundColor = 'rgba(81, 207, 102, 0.3)';
                    setTimeout(() => { row.style.backgroundColor = ''; }, 500);
                    
                    const nombreProducto = row.cells[1].querySelector('.prod-name').textContent.trim();
                    showAlert(`Escaneado: ${nombreProducto}`, 'success');
                } else {
                    showAlert('Producto sin stock disponible', 'error');
                }
            }
        }
    });
    
    if (!encontrado) {
        showAlert('Código no encontrado: ' + codigo, 'error');
    }
}

// ===== EVENT LISTENERS =====

document.addEventListener('DOMContentLoaded', function() {
    
    // Botones de agregar al carrito
    document.querySelectorAll('.add-btn').forEach(button => {
        button.addEventListener('click', function() {
            const producto = {
                idProducto: this.dataset.id,
                nombre: this.dataset.nombre,
                precio: parseFloat(this.dataset.precio),
                stock: parseInt(this.dataset.stock),
                categoria: this.dataset.categoria
            };
            addToCart(producto);
        });
    });
    
    // Búsqueda en tiempo real
    document.getElementById('searchInput').addEventListener('keyup', filtrarProductos);
    
    // Detección de escáner de código de barras
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const ahora = Date.now();
        const intervalo = ahora - ultimaTecla;
        
        if (intervalo < 30) {
            contadorTeclas++;
        } else {
            contadorTeclas = 0;
            codigoBuffer = '';
        }
        
        ultimaTecla = ahora;
        codigoBuffer = this.value;
        
        if (timerEscaneo) clearTimeout(timerEscaneo);
        
        if (contadorTeclas >= 5) {
            timerEscaneo = setTimeout(() => {
                if (codigoBuffer.length >= 8) {
                    procesarCodigoEscaneado(codigoBuffer);
                    this.value = '';
                    codigoBuffer = '';
                    contadorTeclas = 0;
                }
            }, 100);
        }
    });
    
    // Mostrar/ocultar campos de transferencia
    document.querySelectorAll('input[name="payment"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const transferData = document.getElementById('transferData');
            if (transferData) {
                transferData.style.display = this.value === 'Transferencia' ? 'block' : 'none';
            }
        });
    });
    
    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeReceipt();
        }
    });
    
    console.log('Sistema de ventas cargado correctamente');
});

// Añadir estilos de animación
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(100px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100px); }
    }
`;
document.head.appendChild(style);