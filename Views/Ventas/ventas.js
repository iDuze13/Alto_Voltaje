// Variables globales
let cart = [];

// ConfiguraciÃ³n de la API
const API_URL = 'ventas_api.php';

// Funciones de utilidad
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

function formatPrice(price) {
    return parseFloat(price).toLocaleString('es-AR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// FunciÃ³n para convertir nÃºmero a letras
function numeroALetras(numero) {
    const unidades = ['', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
    const decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
    const especiales = ['', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
    const centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];
    
    function convertirGrupo(num) {
        if (num == 0) return 'CERO';
        if (num == 100) return 'CIEN';
        
        let texto = '';
        const c = Math.floor(num / 100);
        if (c > 0) {
            texto += centenas[c] + ' ';
            num %= 100;
        }
        
        if (num >= 11 && num <= 19) {
            return texto.trim() + (texto ? ' ' : '') + especiales[num - 10];
        }
        
        const d = Math.floor(num / 10);
        if (d > 0) {
            if (d == 2 && num % 10 > 0) {
                texto += 'VEINTI';
            } else {
                texto += decenas[d];
                if (num % 10 > 0 && d > 2) texto += ' Y ';
            }
            num %= 10;
        }
        
        if (num > 0) texto += unidades[num];
        
        return texto.trim();
    }
    
    const partes = numero.toFixed(2).split('.');
    let entero = parseInt(partes[0]);
    const decimales = partes[1];
    
    let resultado = '';
    
    if (entero >= 1000000) {
        const millones = Math.floor(entero / 1000000);
        resultado += (millones == 1 ? 'UN MILLON ' : convertirGrupo(millones) + ' MILLONES ');
        entero %= 1000000;
    }
    
    if (entero >= 1000) {
        const miles = Math.floor(entero / 1000);
        resultado += (miles == 1 ? 'MIL ' : convertirGrupo(miles) + ' MIL ');
        entero %= 1000;
    }
    
    if (entero > 0) {
        resultado += convertirGrupo(entero);
    }
    
    if (!resultado.trim()) resultado = 'CERO';
    
    return resultado.trim() + ' CON ' + decimales + '/100 PESOS';
}

// FunciÃ³n para agregar al carrito - RECIBE OBJETO COMPLETO DEL PRODUCTO
function addToCart(producto) {
    console.log('Agregando producto al carrito:', producto);
    
    if (!producto || !producto.idProducto) {
        showAlert('Error: Producto no valido', 'error');
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
            rubro: producto.rubro || 'N/A',
            subrubro: producto.subrubro || 'N/A'
        });
        showAlert(`Producto agregado: ${producto.nombre}`, 'success');
    }

    updateCartDisplay();
}

function removeFromCart(productId) {
    const item = cart.find(item => item.idProducto == productId);
    cart = cart.filter(item => item.idProducto != productId);
    if (item) {
        showAlert(`Producto removido: ${item.nombre}`, 'info');
    }
    updateCartDisplay();
}

function updateQuantity(productId, change) {
    const item = cart.find(item => item.idProducto == productId);
    if (!item) return;

    const newQuantity = item.cantidad + change;
    
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

function updateCartDisplay() {
    const cartItemsContainer = document.getElementById('cartItems');
    const cartSummary = document.getElementById('cartSummary');
    const paymentSection = document.getElementById('paymentSection');

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = `
            <div class="empty-cart">
                <div style="font-size: 48px; margin-bottom: 15px;">X›’</div>
                <p>El carrito esta vacio­o</p>
                <small>Agrega productos para comenzar la venta</small>
            </div>
        `;
        cartSummary.style.display = 'none';
        paymentSection.style.display = 'none';
        return;
    }

    cartItemsContainer.innerHTML = cart.map(item => `
        <div class="cart-item" style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; margin-bottom: 10px;">
            <div class="cart-item-header" style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                <div class="cart-item-name" style="font-weight: 600; color: #F5A623;">${item.nombre}</div>
                <button class="remove-btn" onclick="removeFromCart(${item.idProducto})" style="background: #dc3545; color: white; border: none; border-radius: 4px; padding: 4px 8px; cursor: pointer;">X</button>
            </div>
            <div style="font-size: 11px; color: #888; margin-bottom: 8px;">
                ${item.rubro} > ${item.subrubro} | Stock: ${item.stock}
            </div>
            <div class="cart-item-controls" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="quantity-controls" style="display: flex; gap: 10px; align-items: center;">
                    <button class="qty-btn" onclick="updateQuantity(${item.idProducto}, -1)" style="background: #6c757d; color: white; border: none; padding: 5px 12px; border-radius: 4px; cursor: pointer; font-weight: bold;">-</button>
                    <span class="quantity" style="font-weight: 600; min-width: 30px; text-align: center;">${item.cantidad}</span>
                    <button class="qty-btn" onclick="updateQuantity(${item.idProducto}, 1)" style="background: #28a745; color: white; border: none; padding: 5px 12px; border-radius: 4px; cursor: pointer; font-weight: bold;">+</button>
                </div>
                <div class="item-total" style="font-weight: 600; color: #51cf66;">$${formatPrice(item.subtotal)}</div>
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

    document.getElementById('subtotal').textContent = formatPrice(subtotal);
    document.getElementById('iva').textContent = formatPrice(iva);
    document.getElementById('total').textContent = formatPrice(total);

    cartSummary.style.display = 'block';
    paymentSection.style.display = 'block';
}

function clearCart() {
    if (cart.length === 0) {
        showAlert('El carrito ya esta vacio', 'info');
        return;
    }
    
    if (confirm('Estas seguro de vaciar el carrito?')) {
        cart = [];
        updateCartDisplay();
        showAlert('Carrito vaciado', 'info');
    }
}

// Funciones de venta
function processSale() {
    if (cart.length === 0) {
        showAlert('El carrito esta vacio', 'error');
        return;
    }

    const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
    const processBtn = document.getElementById('processBtn');
    
    // Validar datos para transferencia
    if (paymentMethod === 'Transferencia') {
        const clienteNombre = document.getElementById('clienteNombre').value.trim();
        const clienteAlias = document.getElementById('clienteAlias').value.trim();
        
        if (!clienteNombre || !clienteAlias) {
            showAlert('Complete los datos del cliente para transferencia', 'error');
            return;
        }
    }
    
    const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
    const total = subtotal * 1.21;
    
    if (!confirm(`Â¿Procesar venta por ${paymentMethod.toUpperCase()}?\n\nTotal: $${formatPrice(total)}`)) {
        return;
    }

    // Deshabilitar botÃ³n y mostrar loading
    processBtn.textContent = 'Procesando...';
    processBtn.disabled = true;
    
    // Preparar datos del cliente para transferencia
    const datosCliente = paymentMethod === 'Transferencia' ? {
        nombre: document.getElementById('clienteNombre').value.trim(),
        alias: document.getElementById('clienteAlias').value.trim(),
        cbu: document.getElementById('clienteCBU').value.trim()
    } : {};

    // Preparar productos para la venta
    const productosVenta = cart.map(item => ({
        idProducto: item.idProducto,
        cantidad: item.cantidad
    }));

    fetch(API_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'procesar_venta',
            productos: productosVenta,
            metodo_pago: paymentMethod,
            datos_cliente: datosCliente
        })
    })
    .then(response => response.json())
    .then(data => {
        processBtn.textContent = 'Procesar Venta';
        processBtn.disabled = false;
        
        if (data.success) {
            showAlert('¡Venta procesada correctamente!', 'success');
            
            // Mostrar recibo con opciÃ³n de ver/descargar PDF
            showReceipt(data);
            
            // Limpiar carrito
            cart = [];
            updateCartDisplay();
            
            // Limpiar datos de transferencia
            if (paymentMethod === 'Transferencia') {
                document.getElementById('clienteNombre').value = '';
                document.getElementById('clienteAlias').value = '';
                document.getElementById('clienteCBU').value = '';
            }
            
        } else {
            showAlert(data.error || 'Error al procesar la venta', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error de conexion al procesar la venta', 'error');
        processBtn.textContent = 'Procesar Venta';
        processBtn.disabled = false;
    });
}

// Funciones del recibo
function showReceipt(saleData) {
    const modal = document.getElementById('receiptModal');
    const receiptItems = document.getElementById('receiptItems');
    const receiptClientData = document.getElementById('receiptClientData');
    
    // Fecha y hora actual
    const now = new Date();
    document.getElementById('receiptDate').textContent = now.toLocaleString('es-AR');
    document.getElementById('receiptEmployee').textContent = `Empleado: ${window.empleadoNombre} (ID: ${window.empleadoId})`;
    document.getElementById('receiptNumber').textContent = `La Venta: ${saleData.numero_venta}`;
    
    // Items del recibo
    receiptItems.innerHTML = saleData.productos.map(item => `
        <div class="receipt-item" style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee;">
            <div>
                <strong>${item.producto.Nombre_Producto}</strong><br>
                <small>${item.cantidad} x ${formatPrice(item.precio_unitario)}</small>
            </div>
            <span style="font-weight: 600;">${formatPrice(item.subtotal)}</span>
        </div>
    `).join('');
    
    // Totales
    document.getElementById('receiptSubtotal').textContent = formatPrice(saleData.subtotal);
    document.getElementById('receiptIVA').textContent = formatPrice(saleData.iva);
    document.getElementById('receiptTotal').textContent = formatPrice(saleData.total);
    document.getElementById('receiptPayment').textContent = saleData.metodo_pago.toUpperCase();
    
    // Datos del cliente para transferencia
    if (saleData.metodo_pago === 'Transferencia' && saleData.datos_cliente) {
        receiptClientData.innerHTML = `
            <div style="font-size: 12px; margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                <strong>Datos del Cliente:</strong><br>
                <strong>Nombre:</strong> ${saleData.datos_cliente.nombre}<br>
                <strong>Alias:</strong> ${saleData.datos_cliente.alias}
                ${saleData.datos_cliente.cbu ? `<br><strong>CBU:</strong> ${saleData.datos_cliente.cbu}` : ''}
            </div>
        `;
    } else {
        receiptClientData.innerHTML = '';
    }
    
    // Agregar botÃ³n de descarga PDF si existe
    const receiptActions = document.querySelector('.receipt-actions');
    if (saleData.pdf && saleData.pdf.success) {
        // Verificar si ya existe el botÃ³n de PDF
        let pdfBtn = document.getElementById('pdfDownloadBtn');
        if (!pdfBtn) {
            pdfBtn = document.createElement('button');
            pdfBtn.id = 'pdfDownloadBtn';
            pdfBtn.className = 'btn btn-success';
            pdfBtn.style.background = '#28a745';
            receiptActions.insertBefore(pdfBtn, receiptActions.firstChild);
        }
        
        pdfBtn.innerHTML = 'X“„ Ver/Descargar PDF';
        pdfBtn.onclick = function() {
            window.open(saleData.pdf.url_descarga, '_blank');
        };
        
        showAlert('PDF generado: ' + saleData.pdf.nombre_archivo, 'success');
    }
    
    modal.style.display = 'flex';
}

function closeReceipt() {
    document.getElementById('receiptModal').style.display = 'none';
}

function printReceipt() {
    window.print();
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar/ocultar datos de transferencia
    document.addEventListener('change', function(e) {
        if (e.target.name === 'payment') {
            const transferData = document.getElementById('transferData');
            if (e.target.value === 'Transferencia') {
                transferData.style.display = 'block';
            } else {
                transferData.style.display = 'none';
            }
        }
    });

    // Cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeReceipt();
        }
    });
    
    console.log('Sistema de ventas cargado correctamente');
});
//.....................................

// Sistema de escaneo automÃ¡tico de cÃ³digo de barras
let codigoBuffer = '';
let ultimaTecla = Date.now();
let timerEscaneo = null;
let contadorTeclas = 0;

document.getElementById('searchInput').addEventListener('input', function(e) {
    const ahora = Date.now();
    const intervalo = ahora - ultimaTecla;
    
    // Si el intervalo es muy corto (< 30ms), es un escÃ¡ner
    if (intervalo < 30) {
        contadorTeclas++;
    } else {
        // Escritura manual detectada, reiniciar
        contadorTeclas = 0;
        codigoBuffer = '';
    }
    
    ultimaTecla = ahora;
    codigoBuffer = this.value;
    
    // Limpiar timer anterior
    if (timerEscaneo) {
        clearTimeout(timerEscaneo);
    }
    
    // Si detectamos escritura rÃ¡pida (mÃ¡s de 5 caracteres en menos de 200ms)
    if (contadorTeclas >= 5) {
        // Esperar 100ms despuÃ©s de la Ãºltima tecla para procesar
        timerEscaneo = setTimeout(() => {
            if (codigoBuffer.length >= 8) { // CÃ³digos de barras tÃ­picamente tienen 8+ dÃ­gitos
                procesarCodigoEscaneado(codigoBuffer);
                this.value = '';
                codigoBuffer = '';
                contadorTeclas = 0;
            }
        }, 100);
    }
});

// Mantener funcionalidad de Enter para bÃºsqueda manual
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const termino = this.value.trim();
        
        // Si es un cÃ³digo corto, buscar manualmente
        if (termino.length > 0 && contadorTeclas < 5) {
            filtrarProductos();
        }
    }
});

function procesarCodigoEscaneado(codigo) {
    console.log('Codigo escaneado detectado:', codigo);
    
    // Buscar en la tabla actual
    const rows = document.querySelectorAll('#productosTable tbody tr');
    let encontrado = false;
    
    rows.forEach(row => {
        const codigoBarrasCell = row.cells[6]; // Columna de cÃ³digo de barras
        if (codigoBarrasCell) {
            const textoBarras = codigoBarrasCell.textContent.replace(/\s/g, '');
            const codigoLimpio = codigo.replace(/\s/g, '');
            
            if (textoBarras.includes(codigoLimpio)) {
                encontrado = true;
                
                // Simular clic en el botÃ³n de agregar
                const btnAgregar = row.querySelector('.add-btn');
                if (btnAgregar && !btnAgregar.disabled) {
                    btnAgregar.click();
                    
                    // Efecto visual
                    row.style.backgroundColor = 'rgba(81, 207, 102, 0.3)';
                    setTimeout(() => {
                        row.style.backgroundColor = '';
                    }, 500);
                    
                    const nombreProducto = row.cells[1].querySelector('.prod-name').textContent.trim();
                    showAlert(`Escaneado: ${nombreProducto}`, 'success');
                } else {
                    showAlert('Producto sin stock disponible', 'error');
                }
            }
        }
    });
    
    if (!encontrado) {
        showAlert('Codigo no encontrado: ' + codigo, 'error');
        // Buscar en la API como respaldo
        buscarEnAPIYAgregar(codigo);
    }
}

function buscarEnAPIYAgregar(codigo) {
    fetch(API_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'buscar_productos',
            termino: codigo
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.productos && data.productos.length > 0) {
            const producto = data.productos[0];
            
            if (producto.Stock_Actual > 0) {
                addToCart({
                    idProducto: producto.idProducto,
                    nombre: producto.Nombre_Producto,
                    precio: producto.Precio_Venta,
                    stock: producto.Stock_Actual,
                    rubro: producto.Nombre_Rubro || 'N/A',
                    subrubro: producto.Nombre_SubRubro || 'N/A'
                });
                showAlert(`Producto agregado: ${producto.Nombre_Producto}`, 'success');
            } else {
                showAlert('Producto sin stock', 'error');
            }
        } else {
            showAlert('Codigo no encontrado en la base de datos', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error al buscar producto', 'error');
    });
}