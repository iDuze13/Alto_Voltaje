/**
 * Sistema de Ventas - JavaScript v3.0 FINAL
 * Maneja todo el frontend del sistema de ventas
 * 
 * ⭐ VERSIÓN MEJORADA: v3.0 FINAL (31/10/2025 - 22:00)
 * - FIX definitivo para saleData null
 * - Validaciones robustas
 * - Logs detallados en consola
 */

// Verificación de carga del archivo
console.log('%c📦 VENTAS JS v3.0 FINAL CARGADO', 'background: #ff0000; color: white; padding: 10px 20px; border-radius: 5px; font-weight: bold; font-size: 14px;');
console.log('%c✅ FIX PARA saleData null APLICADO', 'background: #00a650; color: white; padding: 5px 10px; border-radius: 3px; font-weight: bold;');

// ===== VARIABLES GLOBALES =====
let cart = [];
let ventaPendienteMercadoPago = null; // Guarda datos de venta mientras espera confirmación
// Nota: BASE_URL se define en la vista desde PHP antes de cargar este script

// ===== FUNCIONES PARA MERCADO PAGO =====

/**
 * Cambia el botón según el método de pago seleccionado
 */
function cambiarBotonProcesar() {
    const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
    const processBtn = document.getElementById('processBtn');
    
    if (paymentMethod === 'MercadoPago') {
        processBtn.innerHTML = '<i class="fa fa-credit-card"></i> Procesar Pago con Mercado Pago';
        processBtn.style.background = 'linear-gradient(135deg, #00a650 0%, #009ee3 100%)';
        processBtn.style.boxShadow = '0 4px 12px rgba(0,166,80,0.4)';
        document.getElementById('transferData').style.display = 'none';
    } else if (paymentMethod === 'Transferencia') {
        processBtn.innerHTML = '<i class="fa fa-check-circle"></i> Procesar Venta';
        processBtn.style.background = '#F5A623';
        processBtn.style.boxShadow = '0 4px 8px rgba(0,0,0,0.3)';
        document.getElementById('transferData').style.display = 'block';
    } else {
        processBtn.innerHTML = '<i class="fa fa-check-circle"></i> Procesar Venta';
        processBtn.style.background = '#F5A623';
        processBtn.style.boxShadow = '0 4px 8px rgba(0,0,0,0.3)';
        document.getElementById('transferData').style.display = 'none';
    }
}

/**
 * Muestra modal de Mercado Pago con CVU y Alias
 */
function mostrarModalMercadoPago(datosVenta, datosBancarios) {
    console.log('=== MOSTRAR MODAL MERCADO PAGO ===');
    console.log('datosVenta:', datosVenta);
    console.log('datosBancarios:', datosBancarios);
    
    // Validar que datosVenta tenga productos
    if (!datosVenta || !datosVenta.productos || datosVenta.productos.length === 0) {
        console.error('ERROR: datosVenta no tiene productos válidos');
        showAlert('Error: No hay productos en la venta', 'error');
        return;
    }
    
    // Guardar datos temporalmente (COPIAR el objeto para evitar referencias)
    ventaPendienteMercadoPago = JSON.parse(JSON.stringify(datosVenta));
    console.log('ventaPendienteMercadoPago guardado:', ventaPendienteMercadoPago);
    
    // Validar datos bancarios
    if (!datosBancarios) {
        console.warn('ADVERTENCIA: No hay datos bancarios, usando valores por defecto');
        datosBancarios = {
            cvu: 'No configurado',
            alias: 'No configurado',
            titular: 'ALTO VOLTAJE S.R.L.',
            banco: 'Mercado Pago'
        };
    }
    
    // Llenar datos en el modal
    const mpModalCVU = document.getElementById('mpModalCVU');
    const mpModalAlias = document.getElementById('mpModalAlias');
    const mpModalMonto = document.getElementById('mpModalMonto');
    
    if (mpModalCVU) mpModalCVU.textContent = datosBancarios.cvu || 'No configurado';
    if (mpModalAlias) mpModalAlias.textContent = datosBancarios.alias || 'No configurado';
    if (mpModalMonto) mpModalMonto.textContent = '$' + formatPrice(datosVenta.total);
    
    // Guardar datos en atributos para copiar
    const btnCopiarCVU = document.getElementById('btnCopiarCVU');
    const btnCopiarAlias = document.getElementById('btnCopiarAlias');
    
    if (btnCopiarCVU) btnCopiarCVU.setAttribute('data-valor', datosBancarios.cvu || '');
    if (btnCopiarAlias) btnCopiarAlias.setAttribute('data-valor', datosBancarios.alias || '');
    
    // Mostrar modal
    const modal = document.getElementById('mercadoPagoModal');
    if (modal) {
        modal.style.display = 'flex';
        console.log('✓ Modal de Mercado Pago mostrado');
    } else {
        console.error('ERROR: Modal mercadoPagoModal no encontrado en el DOM');
        showAlert('Error: Modal de Mercado Pago no encontrado', 'error');
    }
}

/**
 * Cierra el modal de Mercado Pago
 */
function cerrarModalMercadoPago() {
    const modal = document.getElementById('mercadoPagoModal');
    if (modal) modal.style.display = 'none';
    // NO limpiar ventaPendienteMercadoPago aquí porque se usa después
}

/**
 * Confirma el pago y procesa la venta
 */
function confirmarPagoMercadoPago() {
    console.log('=== CONFIRMAR PAGO MERCADO PAGO ===');
    console.log('ventaPendienteMercadoPago:', ventaPendienteMercadoPago);
    
    if (!ventaPendienteMercadoPago) {
        console.error('ERROR: No hay venta pendiente');
        showAlert('No hay venta pendiente', 'error');
        return;
    }
    
    // Validar que tenga productos
    if (!ventaPendienteMercadoPago.productos || ventaPendienteMercadoPago.productos.length === 0) {
        console.error('ERROR: No hay productos en ventaPendienteMercadoPago');
        showAlert('Error: No hay productos en la venta', 'error');
        return;
    }
    
    // ⭐ IMPORTANTE: Guardar COPIA de los datos ANTES de hacer el fetch
    const datosVentaCopia = JSON.parse(JSON.stringify(ventaPendienteMercadoPago));
    console.log('Copia de datos guardada:', datosVentaCopia);
    
    // Deshabilitar botón
    const btnConfirmar = event.target;
    const btnTextoOriginal = btnConfirmar.innerHTML;
    btnConfirmar.disabled = true;
    btnConfirmar.textContent = 'Procesando...';
    
    console.log('Enviando venta al servidor...');
    
    // Enviar al servidor
    fetch(BASE_URL + '/ventas/procesarVenta', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(ventaPendienteMercadoPago)
    })
    .then(response => {
        console.log('Respuesta recibida:', response);
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Datos procesados:', data);
        
        if (data.success) {
            console.log('✓ Venta exitosa, cerrando modal y mostrando recibo');
            
            // Cerrar modal de Mercado Pago PRIMERO
            cerrarModalMercadoPago();
            
            // Limpiar carrito PRIMERO
            cart = [];
            updateCartDisplay();
            
            // Limpiar variable global
            ventaPendienteMercadoPago = null;
            
            // Esperar un momento antes de mostrar el recibo
            setTimeout(() => {
                // Preparar datos del recibo
                const datosRecibo = {
                    numero_venta: data.numero_venta || 'N/A',
                    empleado_nombre: empleadoNombre || data.empleado_nombre || 'Empleado'
                };
                
                console.log('⭐ USANDO COPIA GUARDADA ⭐');
                console.log('datosVentaCopia:', datosVentaCopia);
                console.log('Mostrando recibo con datos:', datosRecibo, datosVentaCopia);
                
                // ⭐ Usar la COPIA guardada, no la variable global que ya es null
                try {
                    if (!datosVentaCopia || !datosVentaCopia.productos) {
                        console.error('CRÍTICO: datosVentaCopia es null o no tiene productos!');
                        console.error('Este NO debería ser el caso. Revisar closure.');
                        throw new Error('datosVentaCopia es null');
                    }
                    showReceipt(datosRecibo, datosVentaCopia);
                    showAlert('¡Venta procesada exitosamente!', 'success');
                } catch (error) {
                    console.error('ERROR al mostrar recibo:', error);
                    console.error('Stack:', error.stack);
                    showAlert('Venta procesada pero hubo un error al mostrar el recibo', 'error');
                }
            }, 300);
            
        } else {
            console.error('ERROR del servidor:', data.error);
            showAlert('Error: ' + (data.error || 'Error desconocido'), 'error');
            btnConfirmar.disabled = false;
            btnConfirmar.innerHTML = btnTextoOriginal;
        }
    })
    .catch(error => {
        console.error('ERROR en fetch:', error);
        showAlert('Error de conexión al procesar la venta: ' + error.message, 'error');
        btnConfirmar.disabled = false;
        btnConfirmar.innerHTML = btnTextoOriginal;
    });
}

/**
 * Copia CVU o Alias al portapapeles
 */
function copiarDatoMP(tipo) {
    const btn = tipo === 'cvu' ? document.getElementById('btnCopiarCVU') : document.getElementById('btnCopiarAlias');
    const valor = btn.getAttribute('data-valor');
    
    if (!valor || valor === 'No configurado') {
        showAlert('No hay datos para copiar', 'error');
        return;
    }
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(valor).then(() => {
            showAlert('¡Copiado!', 'success');
        }).catch(err => {
            fallbackCopiarTexto(valor);
        });
    } else {
        fallbackCopiarTexto(valor);
    }
}

/**
 * Fallback para copiar texto
 */
function fallbackCopiarTexto(texto) {
    const textarea = document.createElement('textarea');
    textarea.value = texto;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
        showAlert('¡Copiado!', 'success');
    } catch (err) {
        showAlert('No se pudo copiar', 'error');
    }
    document.body.removeChild(textarea);
}

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
 * Procesa la venta o muestra modal de Mercado Pago
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
    
    // ⭐ SI ES MERCADO PAGO → Mostrar modal y esperar confirmación
    if (paymentMethod === 'MercadoPago') {
        const datosBancarios = window.DATOS_MP || {
            cvu: 'No configurado',
            alias: 'No configurado',
            titular: 'ALTO VOLTAJE S.R.L.',
            banco: 'Mercado Pago'
        };
        mostrarModalMercadoPago(saleData, datosBancarios);
        return; // No procesar todavía, esperar confirmación
    }
    
    // ⭐ OTROS MÉTODOS → Procesar inmediatamente
    const processBtn = document.getElementById('processBtn');
    processBtn.disabled = true;
    processBtn.textContent = 'Procesando...';
    
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
        processBtn.innerHTML = '<i class="fa fa-check-circle"></i> Procesar Venta';
        
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
        processBtn.innerHTML = '<i class="fa fa-check-circle"></i> Procesar Venta';
        showAlert('Error de conexión al procesar la venta', 'error');
    });
}

/**
 * Muestra modal con datos bancarios para transferencia o Mercado Pago
 */
function mostrarDatosTransferencia(datosBancarios, ventaData, saleData) {
    const metodo = saleData.metodo_pago || 'Transferencia';
    const esMercadoPago = metodo === 'MercadoPago';
    
    // Colores y título según método
    const colorHeader = esMercadoPago ? '#00a650' : '#007bff';
    const colorMonto = esMercadoPago ? '#009ee3' : '#28a745';
    const titulo = esMercadoPago ? '💰 Transferir por Mercado Pago' : '💰 Datos para Transferencia';
    const instruccion = esMercadoPago 
        ? 'El cliente debe abrir su app de Mercado Pago y transferir a:'
        : 'Transferí el monto a los siguientes datos:';
    
    const modal = document.getElementById('transferModal');
    if (!modal) {
        // Crear modal si no existe
        const modalHTML = `
            <div id="transferModal" class="modal" style="display: flex;">
                <div class="receipt" style="max-width: 500px;">
                    <div class="receipt-header" id="transferModalHeader">
                        <h2 style="color: white;" id="transferModalTitle"></h2>
                        <button class="close-btn" onclick="closeTransferModal()">✕</button>
                    </div>
                    <div style="padding: 20px;">
                        <p id="transferModalInstruccion" style="text-align: center; margin-bottom: 20px; font-size: 14px; color: #666;">
                        </p>
                        <div id="transferDataContent"></div>
                        <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 5px; border: 1px solid #ffc107;">
                            <p style="margin: 0; font-size: 13px; text-align: center;">
                                <strong>⚠️ Importante:</strong> Una vez recibido el pago, entregar el recibo al cliente
                            </p>
                        </div>
                    </div>
                    <div class="receipt-actions" style="border-top: 2px solid #eee; padding: 15px;">
                        <button class="btn btn-primary" onclick="verRecibo()" style="background: ${colorMonto};">
                            <i class="fa fa-receipt"></i> Ver Recibo
                        </button>
                        <button class="btn btn-secondary" onclick="closeTransferModal()">
                            <i class="fa fa-times"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }
    
    // Actualizar header según método
    document.getElementById('transferModalHeader').style.background = colorHeader;
    document.getElementById('transferModalTitle').textContent = titulo;
    document.getElementById('transferModalInstruccion').textContent = instruccion;
    
    // Llenar datos
    const content = document.getElementById('transferDataContent');
    content.innerHTML = `
        <div style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); padding: 25px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            
            ${esMercadoPago ? `
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="https://http2.mlstatic.com/storage/logos-api-admin/a5f047d0-9be0-11ec-aad4-c3381f368aaf-xl.svg" 
                         alt="Mercado Pago" style="height: 40px;">
                </div>
            ` : ''}
            
            <div style="margin-bottom: 20px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <label style="font-size: 12px; color: #666; display: block; margin-bottom: 8px; font-weight: 600;">
                    📋 CVU / CBU
                </label>
                <div style="font-size: 20px; font-weight: bold; font-family: 'Courier New', monospace; color: ${colorHeader}; letter-spacing: 1px;">
                    ${datosBancarios.cvu}
                </div>
                <button onclick="copiarTexto('${datosBancarios.cvu}')" 
                        style="margin-top: 8px; padding: 5px 10px; background: ${colorHeader}; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                    📋 Copiar CVU
                </button>
            </div>
            
            <div style="margin-bottom: 20px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <label style="font-size: 12px; color: #666; display: block; margin-bottom: 8px; font-weight: 600;">
                    🏷️ Alias
                </label>
                <div style="font-size: 22px; font-weight: bold; color: ${colorHeader};">
                    ${datosBancarios.alias}
                </div>
                <button onclick="copiarTexto('${datosBancarios.alias}')" 
                        style="margin-top: 8px; padding: 5px 10px; background: ${colorHeader}; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                    📋 Copiar Alias
                </button>
            </div>
            
            <div style="margin-bottom: 15px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <label style="font-size: 12px; color: #666; display: block; margin-bottom: 5px; font-weight: 600;">
                    👤 Titular
                </label>
                <div style="font-size: 16px; font-weight: 600; color: #333;">
                    ${datosBancarios.titular}
                </div>
            </div>
            
            <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, ${colorHeader} 0%, ${colorMonto} 100%); border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.15);">
                <label style="font-size: 14px; color: white; display: block; margin-bottom: 5px; font-weight: 600;">
                    💵 Monto a Transferir
                </label>
                <div style="font-size: 32px; font-weight: bold; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                    $${formatPrice(saleData.total)}
                </div>
            </div>
        </div>
    `;
    
    // Guardar datos para el recibo
    window.currentVentaData = ventaData;
    window.currentSaleData = saleData;
    
    document.getElementById('transferModal').style.display = 'flex';
}

/**
 * Función para copiar texto al portapapeles
 */
function copiarTexto(texto) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(texto).then(() => {
            showAlert('¡Copiado al portapapeles!', 'success');
        }).catch(err => {
            console.error('Error al copiar:', err);
            showAlert('No se pudo copiar', 'error');
        });
    } else {
        // Fallback para navegadores antiguos
        const textarea = document.createElement('textarea');
        textarea.value = texto;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            showAlert('¡Copiado al portapapeles!', 'success');
        } catch (err) {
            showAlert('No se pudo copiar', 'error');
        }
        document.body.removeChild(textarea);
    }
}

function closeTransferModal() {
    const modal = document.getElementById('transferModal');
    if (modal) modal.style.display = 'none';
}

function verRecibo() {
    closeTransferModal();
    if (window.currentVentaData && window.currentSaleData) {
        showReceipt(window.currentVentaData, window.currentSaleData);
    }
}

/**
 * Muestra el recibo de la venta
 */
/**
 * Muestra el modal del recibo con validaciones robustas
 */
function showReceipt(ventaData, saleData) {
    console.log('=== MOSTRAR RECIBO ===');
    console.log('ventaData recibido:', ventaData);
    console.log('saleData recibido:', saleData);
    
    // Validación 1: Verificar que los datos existan
    if (!ventaData) {
        console.error('ERROR CRÍTICO: ventaData es null o undefined');
        showAlert('Error al mostrar el recibo: datos de venta no disponibles', 'error');
        return;
    }
    
    if (!saleData) {
        console.error('ERROR CRÍTICO: saleData es null o undefined');
        showAlert('Error al mostrar el recibo: datos de venta no disponibles', 'error');
        return;
    }
    
    // Validación 2: Verificar que tenga productos
    if (!saleData.productos) {
        console.error('ERROR CRÍTICO: saleData.productos es null o undefined');
        console.error('saleData completo:', JSON.stringify(saleData));
        showAlert('Error al mostrar el recibo: no hay productos', 'error');
        return;
    }
    
    if (!Array.isArray(saleData.productos)) {
        console.error('ERROR CRÍTICO: saleData.productos no es un array');
        console.error('Tipo:', typeof saleData.productos);
        console.error('Valor:', saleData.productos);
        showAlert('Error al mostrar el recibo: formato de productos incorrecto', 'error');
        return;
    }
    
    if (saleData.productos.length === 0) {
        console.error('ERROR: saleData.productos está vacío');
        showAlert('Error al mostrar el recibo: no hay productos en la venta', 'error');
        return;
    }
    
    console.log('✓ Validaciones pasadas, mostrando recibo...');
    
    // Obtener modal
    const modal = document.getElementById('receiptModal');
    if (!modal) {
        console.error('ERROR: Modal de recibo no encontrado en el DOM');
        showAlert('Error: No se encontró el modal del recibo', 'error');
        return;
    }
    
    try {
        // Información básica
        const receiptNumber = document.getElementById('receiptNumber');
        const receiptDate = document.getElementById('receiptDate');
        const receiptEmployee = document.getElementById('receiptEmployee');
        
        if (receiptNumber) receiptNumber.textContent = ventaData.numero_venta || 'N/A';
        if (receiptDate) receiptDate.textContent = new Date().toLocaleString('es-AR');
        if (receiptEmployee) receiptEmployee.textContent = ventaData.empleado_nombre || empleadoNombre || 'Empleado';
        
        // Items
        let itemsHTML = '';
        saleData.productos.forEach((item, index) => {
            console.log(`Procesando producto ${index}:`, item);
            itemsHTML += `
                <div class="receipt-item">
                    <div>
                        <strong>${item.nombre || 'Producto'}</strong><br>
                        <small>${item.cantidad || 0} x $${formatPrice(item.precio || 0)}</small>
                    </div>
                    <span>$${formatPrice(item.subtotal || 0)}</span>
                </div>
            `;
        });
        
        const receiptItems = document.getElementById('receiptItems');
        if (receiptItems) {
            receiptItems.innerHTML = itemsHTML;
        } else {
            console.warn('ADVERTENCIA: Elemento receiptItems no encontrado');
        }
        
        // Totales
        const receiptSubtotal = document.getElementById('receiptSubtotal');
        const receiptIVA = document.getElementById('receiptIVA');
        const receiptTotal = document.getElementById('receiptTotal');
        const receiptPayment = document.getElementById('receiptPayment');
        
        if (receiptSubtotal) receiptSubtotal.textContent = formatPrice(saleData.subtotal || 0);
        if (receiptIVA) receiptIVA.textContent = formatPrice(saleData.iva || 0);
        if (receiptTotal) receiptTotal.textContent = formatPrice(saleData.total || 0);
        if (receiptPayment) {
            const metodoPago = (saleData.metodo_pago || 'N/A').replace('_', ' ').replace('MercadoPago', 'MERCADO PAGO');
            receiptPayment.textContent = metodoPago.toUpperCase();
        }
        
        // Datos del cliente (si es transferencia)
        const receiptClientData = document.getElementById('receiptClientData');
        if (receiptClientData) {
            if (saleData.metodo_pago === 'Transferencia' && saleData.datos_cliente) {
                receiptClientData.innerHTML = `
                    <div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                        <strong>Datos del Cliente:</strong><br>
                        <strong>Nombre:</strong> ${saleData.datos_cliente.nombre || 'N/A'}<br>
                        <strong>Alias:</strong> ${saleData.datos_cliente.alias || 'N/A'}
                        ${saleData.datos_cliente.cbu ? `<br><strong>CBU:</strong> ${saleData.datos_cliente.cbu}` : ''}
                    </div>
                `;
            } else {
                receiptClientData.innerHTML = '';
            }
        }
        
        // Mostrar modal
        console.log('✓ Recibo construido correctamente, mostrando modal');
        modal.style.display = 'flex';
        
    } catch (error) {
        console.error('ERROR INESPERADO al construir el recibo:', error);
        console.error('Stack:', error.stack);
        showAlert('Error inesperado al mostrar el recibo: ' + error.message, 'error');
    }
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
