<?php require_once(__DIR__ . '/../../Helpers/Helpers.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['page_title'] ?></title>
    <link rel="icon" type="image/png" href="<?= media() ?>/images/altovoltaje_logo.png">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom Ventas CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>/Views/Ventas/ventas.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&display=swap" rel="stylesheet">
<<<<<<< Updated upstream
=======
    
    <!-- CSS de Ventas -->
    <link rel="stylesheet" href="<?= media() ?>/css/ventav2.css">
>>>>>>> Stashed changes
</head>
<body>
    <div class="container">
        <div class="main-panel">
            <div class="header">
                <a href="<?= base_url(); ?>/empleados/dashboard" class="back-btn">
                    <i class="fa fa-arrow-left"></i> Volver
                </a>
                <div class="logo">Alto Voltaje - Ventas</div>
                <div class="user-info">
                    <strong><?= htmlspecialchars($data['nombre_usuario'] ?? 'Usuario') ?></strong><br>
                    ID: <?= htmlspecialchars($data['id_usuario'] ?? 'N/A') ?>
                </div>
            </div>
            
            <div class="search-section">
                <div class="search-bar">
                    <input type="text" class="search-input" id="searchInput" 
                           placeholder="Buscar: nombre, código, SKU..." autofocus>
                    <button class="search-btn" onclick="filtrarProductos()">Buscar</button>
                    <button class="reload-btn" onclick="location.reload()">↻</button>
                </div>
            </div>
            
            <div class="products-section">
                <?php if (count($data['productos_activos'] ?? []) == 0): ?>
                    <div style="text-align: center; padding: 60px 20px;">
                        <div style="font-size: 48px; margin-bottom: 20px;">📦</div>
                        <h3>No hay productos activos</h3>
                    </div>
                <?php else: ?>
                    <table class="products-table" id="productosTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>SKU</th>
                                <th>Marca</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Código Barras</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['productos_activos'] as $p): ?>
                                <tr>
                                    <td><strong><?= $p['idProducto'] ?></strong></td>
                                    <td>
                                        <div class="prod-name" title="<?= htmlspecialchars($p['Nombre_Producto']) ?>">
                                            <?= htmlspecialchars($p['Nombre_Producto']) ?>
                                        </div>
                                    </td>
                                    <td><span class="prod-sku"><?= htmlspecialchars($p['SKU']) ?></span></td>
                                    <td><?= htmlspecialchars(substr($p['Marca'], 0, 12)) ?></td>
                                    <td class="prod-precio">$<?= number_format($p['Precio_Venta'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="prod-stock <?= $p['Stock_Actual'] < 10 ? 'stock-low' : ($p['Stock_Actual'] < 25 ? 'stock-medium' : 'stock-high') ?>">
                                            <?= $p['Stock_Actual'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($p['codigo_barras'])): ?>
                                            <div class="barcode-cell">*<?= $p['codigo_barras'] ?>*</div>
                                            <div style="font-size: 9px; text-align: center;"><?= $p['codigo_barras'] ?></div>
                                        <?php else: ?>
                                            <span style="color: #888; font-size: 10px;">Sin código</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="add-btn" 
                                                data-id="<?= $p['idProducto'] ?>"
                                                data-nombre="<?= htmlspecialchars($p['Nombre_Producto']) ?>"
                                                data-precio="<?= $p['Precio_Venta'] ?>"
                                                data-stock="<?= $p['Stock_Actual'] ?>"
                                                data-categoria="<?= $p['Nombre_Categoria'] ?? 'N/A' ?>"
                                                <?= $p['Stock_Actual'] <= 0 ? 'disabled' : '' ?>>
                                            <?= $p['Stock_Actual'] <= 0 ? 'Sin Stock' : '+ Agregar' ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <div class="cart-panel">
            <div class="cart-header">
                <h2>Carrito</h2>
                <button class="clear-cart-btn" onclick="clearCart()">Vaciar</button>
            </div>
            
            <div class="cart-items" id="cartItems">
                <div class="empty-cart">
                    <div>🛒</div>
                    <p>Carrito vacío</p>
                </div>
            </div>
            
            <div class="cart-summary" id="cartSummary" style="display: none;">
                <div class="summary-row"><span>Subtotal:</span><span>$<span id="subtotal">0</span></span></div>
                <div class="summary-row"><span>IVA (21%):</span><span>$<span id="iva">0</span></span></div>
                <div class="summary-row total"><span>TOTAL:</span><span>$<span id="total">0</span></span></div>
            </div>
            
            <div class="payment-section" id="paymentSection" style="display: none;">
                <h3>Método de Pago</h3>
                <div class="payment-methods">
                    <label class="payment-option">
<<<<<<< Updated upstream
                        <input type="radio" name="payment" value="Efectivo" checked> Efectivo
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment" value="Tarjeta_Debito"> Débito
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment" value="Tarjeta_Credito"> Crédito
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment" value="Transferencia"> Transfer
=======
                        <input type="radio" name="payment" value="Efectivo" checked onchange="cambiarBotonProcesar()">
                        💵 Efectivo
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment" value="Tarjeta_Debito" onchange="cambiarBotonProcesar()">
                        💳 Débito
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment" value="Tarjeta_Credito" onchange="cambiarBotonProcesar()">
                        💳 Crédito
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment" value="Transferencia" onchange="cambiarBotonProcesar()">
                        🏦 Transfer
>>>>>>> Stashed changes
                    </label>
                    <label class="payment-option" style="background: linear-gradient(135deg, #00a650 0%, #009ee3 100%); border: 2px solid #00a650;">
                        <input type="radio" name="payment" value="MercadoPago" onchange="cambiarBotonProcesar()">
                        <img src="https://http2.mlstatic.com/storage/logos-api-admin/a5f047d0-9be0-11ec-aad4-c3381f368aaf-m.svg" 
                             alt="Mercado Pago" style="height: 20px; vertical-align: middle; filter: brightness(0) invert(1);">
                        Mercado Pago
                    </label>
                </div>
                <button class="process-btn" id="processBtn" onclick="processSale()">Procesar Venta</button>
            </div>
        </div>
    </div>

<<<<<<< Updated upstream
    <!-- Modal de Recibo -->
=======
    <!-- MODAL DE MERCADO PAGO -->
    <div id="mercadoPagoModal" class="modal" style="display: none;">
        <div class="receipt" style="max-width: 500px; background: linear-gradient(180deg, #00a650 0%, #009ee3 100%);">
            <div class="receipt-header" style="background: rgba(0,0,0,0.2); border: none;">
                <div style="text-align: center;">
                    <img src="https://http2.mlstatic.com/storage/logos-api-admin/a5f047d0-9be0-11ec-aad4-c3381f368aaf-xl.svg" 
                         alt="Mercado Pago" style="height: 50px; filter: brightness(0) invert(1); margin-bottom: 10px;">
                    <h2 style="color: white; margin: 0;">Esperando Pago</h2>
                </div>
            </div>
            
            <div style="padding: 30px; background: white; border-radius: 0 0 12px 12px;">
                <p style="text-align: center; font-size: 16px; color: #666; margin-bottom: 25px;">
                    El cliente debe transferir a:
                </p>
                
                <!-- CVU -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #00a650;">
                    <label style="font-size: 12px; color: #666; font-weight: 600; display: block; margin-bottom: 8px;">
                        📋 CVU / CBU
                    </label>
                    <div id="mpModalCVU" style="font-size: 20px; font-weight: bold; font-family: 'Courier New', monospace; color: #00a650; letter-spacing: 1px; word-break: break-all; margin-bottom: 10px;">
                    </div>
                    <button type="button" id="btnCopiarCVU" onclick="copiarDatoMP('cvu')" 
                            style="width: 100%; padding: 10px; background: #00a650; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600;">
                        📋 Copiar CVU
                    </button>
                </div>
                
                <!-- ALIAS -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #009ee3;">
                    <label style="font-size: 12px; color: #666; font-weight: 600; display: block; margin-bottom: 8px;">
                        🏷️ Alias
                    </label>
                    <div id="mpModalAlias" style="font-size: 26px; font-weight: bold; color: #009ee3; margin-bottom: 10px;">
                    </div>
                    <button type="button" id="btnCopiarAlias" onclick="copiarDatoMP('alias')" 
                            style="width: 100%; padding: 10px; background: #009ee3; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600;">
                        📋 Copiar Alias
                    </button>
                </div>
                
                <!-- Monto -->
                <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, #00a650 0%, #009ee3 100%); border-radius: 10px; margin-bottom: 20px;">
                    <label style="font-size: 14px; color: white; display: block; margin-bottom: 5px; font-weight: 600;">
                        💵 Monto a Pagar
                    </label>
                    <div id="mpModalMonto" style="font-size: 36px; font-weight: bold; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                    </div>
                </div>
                
                <!-- Instrucciones -->
                <div style="background: #fff3cd; padding: 15px; border-radius: 8px; border: 1px solid #ffc107; margin-bottom: 20px;">
                    <p style="margin: 0; font-size: 13px; text-align: center; color: #856404;">
                        <strong>⏳ Espera a que el cliente transfiera</strong><br>
                        Verifica que llegó el pago antes de continuar
                    </p>
                </div>
                
                <!-- Botones -->
                <div style="display: flex; gap: 10px;">
                    <button onclick="cerrarModalMercadoPago()" 
                            style="flex: 1; padding: 15px; background: #6c757d; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600;">
                        ✕ Cancelar
                    </button>
                    <button onclick="confirmarPagoMercadoPago()" 
                            style="flex: 2; padding: 15px; background: linear-gradient(135deg, #00a650 0%, #009ee3 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; box-shadow: 0 4px 8px rgba(0,166,80,0.3);">
                        ✓ Confirmar Pago y Generar Recibo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE RECIBO -->
>>>>>>> Stashed changes
    <div id="receiptModal" class="modal">
        <div class="receipt">
            <div class="receipt-header">
                <h2>RECIBO OFICIAL</h2>
                <button class="close-btn" onclick="closeReceipt()">✕</button>
            </div>
            <div class="receipt-info">
                <p style="font-size: 18px; font-weight: bold; color: #F5A623; text-align: center;">ALTO VOLTAJE S.R.L.</p>
                <p style="text-align: center; font-size: 12px;">Formosa, Argentina</p>
            </div>
            <div style="background: #fff3cd; padding: 15px;">
                <p><strong>N° RECIBO:</strong> <span id="receiptNumber"></span></p>
                <p><strong>FECHA:</strong> <span id="receiptDate"></span></p>
            </div>
            <div style="padding: 15px;">
                <p><strong>EMPLEADO:</strong> <span id="receiptEmployee"></span></p>
            </div>
            <div class="receipt-items" id="receiptItems"></div>
            <div class="receipt-total">
                <div class="receipt-row"><span>Subtotal:</span><span>$<span id="receiptSubtotal">0</span></span></div>
                <div class="receipt-row"><span>IVA:</span><span>$<span id="receiptIVA">0</span></span></div>
                <div class="receipt-row total"><span>TOTAL:</span><span>$<span id="receiptTotal">0</span></span></div>
                <div class="receipt-row"><span>Pago:</span><span id="receiptPayment"></span></div>
            </div>
            <div id="receiptClientData"></div>
            <div class="receipt-actions">
                <button class="btn btn-primary" onclick="printReceipt()">Imprimir</button>
                <button class="btn btn-secondary" onclick="closeReceipt()">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
<<<<<<< Updated upstream
        // Variables globales
        let cart = [];
        const BASE_URL = '<?= base_url(); ?>';
        const empleadoNombre = '<?= $data['nombre_usuario'] ?>';
        const empleadoId = '<?= $data['id_usuario'] ?>';
        
        // Función para convertir número a letras
        function numeroALetras(numero) {
            // Implementación básica - se puede expandir
            return numero.toFixed(2) + ' PESOS';
        }
        
        // Función para agregar al carrito
        function addToCart(producto) {
            console.log('Agregando producto al carrito:', producto);
            
            if (!producto || !producto.idProducto) {
                alert('Error: Producto no válido');
                return;
            }

            const existingItem = cart.find(item => item.idProducto == producto.idProducto);
            
            if (existingItem) {
                if (existingItem.cantidad < producto.stock) {
                    existingItem.cantidad++;
                    existingItem.subtotal = existingItem.cantidad * existingItem.precio;
                    alert(`Cantidad actualizada: ${producto.nombre}`);
                } else {
                    alert('No hay suficiente stock disponible');
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
                alert(`Producto agregado: ${producto.nombre}`);
            }

            updateCartDisplay();
        }

        function removeFromCart(productId) {
            const item = cart.find(item => item.idProducto == productId);
            cart = cart.filter(item => item.idProducto != productId);
            if (item) {
                alert(`Producto removido: ${item.nombre}`);
            }
            updateCartDisplay();
        }

        function updateQuantity(productId, newQuantity) {
            const item = cart.find(item => item.idProducto == productId);
            if (item) {
                if (newQuantity <= 0) {
                    removeFromCart(productId);
                } else if (newQuantity <= item.stock) {
                    item.cantidad = newQuantity;
                    item.subtotal = item.cantidad * item.precio;
                    updateCartDisplay();
                } else {
                    alert('No hay suficiente stock disponible');
                }
            }
        }

        function updateCartDisplay() {
            const cartItems = document.getElementById('cartItems');
            const cartSummary = document.getElementById('cartSummary');
            const paymentSection = document.getElementById('paymentSection');
            
            if (cart.length === 0) {
                cartItems.innerHTML = `
                    <div class="empty-cart">
                        <div>🛒</div>
                        <p>Carrito vacío</p>
                    </div>
                `;
                cartSummary.style.display = 'none';
                paymentSection.style.display = 'none';
            } else {
                let itemsHtml = '';
                let subtotal = 0;
                
                cart.forEach(item => {
                    subtotal += item.subtotal;
                    itemsHtml += `
                        <div class="cart-item">
                            <div class="cart-item-header">
                                <div class="cart-item-name">${item.nombre}</div>
                                <button class="remove-btn" onclick="removeFromCart('${item.idProducto}')">×</button>
                            </div>
                            <div class="cart-item-controls">
                                <div class="quantity-controls">
                                    <button class="qty-btn" onclick="updateQuantity('${item.idProducto}', ${item.cantidad - 1})">-</button>
                                    <span class="quantity">${item.cantidad}</span>
                                    <button class="qty-btn" onclick="updateQuantity('${item.idProducto}', ${item.cantidad + 1})">+</button>
                                </div>
                                <div class="item-total">$${item.subtotal.toFixed(2)}</div>
                            </div>
                        </div>
                    `;
                });
                
                const iva = subtotal * 0.21;
                const total = subtotal + iva;
                
                cartItems.innerHTML = itemsHtml;
                document.getElementById('subtotal').textContent = subtotal.toFixed(2);
                document.getElementById('iva').textContent = iva.toFixed(2);
                document.getElementById('total').textContent = total.toFixed(2);
                
                cartSummary.style.display = 'block';
                paymentSection.style.display = 'block';
            }
        }

        function clearCart() {
            if (cart.length > 0) {
                if (confirm('¿Está seguro de que desea vaciar el carrito?')) {
                    cart = [];
                    updateCartDisplay();
                    alert('Carrito vaciado');
                }
            }
        }

        function processSale() {
            if (cart.length === 0) {
                alert('El carrito está vacío');
                return;
            }
            
            const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
            const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
            const iva = subtotal * 0.21;
            const total = subtotal + iva;
            
            const saleData = {
                productos: cart.map(item => ({
                    id: item.idProducto,
                    cantidad: item.cantidad,
                    precio: item.precio,
                    subtotal: item.subtotal
                })),
                subtotal: subtotal,
                iva: iva,
                total: total,
                metodo_pago: paymentMethod
            };
            
            // Procesar venta en el servidor
            fetch(BASE_URL + '/ventas/procesarVenta', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(saleData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showReceipt(data.venta_id, saleData);
                    cart = [];
                    updateCartDisplay();
                    alert('Venta procesada exitosamente. ID: ' + data.venta_id);
                } else {
                    alert('Error al procesar la venta: ' + (data.error || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al procesar la venta');
            });
        }

        function showReceipt(ventaId, saleData) {
            document.getElementById('receiptNumber').textContent = String(ventaId).padStart(6, '0');
            document.getElementById('receiptDate').textContent = new Date().toLocaleString('es-AR');
            document.getElementById('receiptEmployee').textContent = empleadoNombre;
            
            let itemsHtml = '';
            cart.forEach(item => {
                itemsHtml += `
                    <div class="receipt-row">
                        <span>${item.nombre} (x${item.cantidad})</span>
                        <span>$${item.subtotal.toFixed(2)}</span>
                    </div>
                `;
            });
            
            document.getElementById('receiptItems').innerHTML = itemsHtml;
            document.getElementById('receiptSubtotal').textContent = saleData.subtotal.toFixed(2);
            document.getElementById('receiptIVA').textContent = saleData.iva.toFixed(2);
            document.getElementById('receiptTotal').textContent = saleData.total.toFixed(2);
            document.getElementById('receiptPayment').textContent = saleData.metodo_pago;
            
            document.getElementById('receiptModal').style.display = 'block';
        }

        function closeReceipt() {
            document.getElementById('receiptModal').style.display = 'none';
        }

        function printReceipt() {
            window.print();
        }
        
        function filtrarProductos() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#productosTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }
        
        document.getElementById('searchInput').addEventListener('keyup', filtrarProductos);
        
        // Eventos para botones de agregar al carrito
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
=======
        // Variables globales definidas desde PHP
        const empleadoNombre = '<?= htmlspecialchars($data['nombre_usuario']) ?>';
        const BASE_URL = '<?= base_url() ?>'; // ⭐ URL base del proyecto
        
        // Datos de Mercado Pago (disponibles globalmente)
        window.DATOS_MP = {
            cvu: '<?= $data['datos_bancarios']['cvu'] ?? 'No configurado' ?>',
            alias: '<?= $data['datos_bancarios']['alias'] ?? 'No configurado' ?>',
            titular: '<?= $data['datos_bancarios']['titular'] ?? 'ALTO VOLTAJE S.R.L.' ?>',
            banco: '<?= $data['datos_bancarios']['banco'] ?? 'Mercado Pago' ?>'
        };
    </script>
    <script src="<?= media() ?>/js/functions_ventasV3.js"></script>
>>>>>>> Stashed changes
</body>
</html>
