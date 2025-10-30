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
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&display=swap" rel="stylesheet">
    
    <!-- CSS de Ventas -->
    <link rel="stylesheet" href="<?= media() ?>/css/ventas.css">
</head>
<body>
    <div class="container">
        <!-- PANEL PRINCIPAL -->
        <div class="main-panel">
            <!-- HEADER -->
            <div class="header">
                <a href="<?= base_url(); ?>/empleados/dashboard" class="back-btn">
                    <i class="fa fa-arrow-left"></i> Volver
                </a>
                <div class="logo">🔌 Alto Voltaje - Ventas</div>
                <div class="user-info">
                    <strong><?= htmlspecialchars($data['nombre_usuario'] ?? 'Usuario') ?></strong><br>
                    <small>ID: <?= htmlspecialchars($data['id_usuario'] ?? 'N/A') ?></small>
                </div>
            </div>
            
            <!-- BÚSQUEDA -->
            <div class="search-section">
                <div class="search-bar">
                    <input type="text" 
                           class="search-input" 
                           id="searchInput" 
                           placeholder="🔍 Buscar por nombre, código, SKU o escanear código de barras..." 
                           autofocus>
                    <button class="search-btn" onclick="filtrarProductos()">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                    <button class="reload-btn" onclick="location.reload()" title="Recargar">
                        <i class="fa fa-sync"></i>
                    </button>
                </div>
            </div>
            
            <!-- TABLA DE PRODUCTOS -->
            <div class="products-section">
                <?php if (empty($data['productos_activos'])): ?>
                    <div style="text-align: center; padding: 60px 20px;">
                        <div style="font-size: 48px; margin-bottom: 20px;">📦</div>
                        <h3 style="color: #888;">No hay productos activos</h3>
                        <p style="color: #666;">Agrega productos desde el panel de administración</p>
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
                                    <td>
                                        <span class="prod-sku"><?= htmlspecialchars($p['SKU']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars(substr($p['Marca'], 0, 15)) ?></td>
                                    <td class="prod-precio">
                                        $<?= number_format($p['Precio_Venta'], 2, ',', '.') ?>
                                    </td>
                                    <td>
                                        <span class="prod-stock <?= $p['Stock_Actual'] < 10 ? 'stock-low' : ($p['Stock_Actual'] < 25 ? 'stock-medium' : 'stock-high') ?>">
                                            <?= $p['Stock_Actual'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($p['codigo_barras'])): ?>
                                            <div class="barcode-cell">*<?= $p['codigo_barras'] ?>*</div>
                                            <div style="font-size: 9px; text-align: center; color: #666;">
                                                <?= $p['codigo_barras'] ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #888; font-size: 11px;">Sin código</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="add-btn" 
                                                data-id="<?= $p['idProducto'] ?>"
                                                data-nombre="<?= htmlspecialchars($p['Nombre_Producto']) ?>"
                                                data-precio="<?= $p['Precio_Venta'] ?>"
                                                data-stock="<?= $p['Stock_Actual'] ?>"
                                                data-categoria="<?= htmlspecialchars($p['Nombre_Categoria'] ?? 'N/A') ?>"
                                                <?= $p['Stock_Actual'] <= 0 ? 'disabled' : '' ?>>
                                            <?= $p['Stock_Actual'] <= 0 ? '❌ Sin Stock' : '✅ Agregar' ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- PANEL DEL CARRITO -->
        <div class="cart-panel">
            <div class="cart-header">
                <h2>🛒 Carrito</h2>
                <button class="clear-cart-btn" onclick="clearCart()">
                    <i class="fa fa-trash"></i> Vaciar
                </button>
            </div>
            
            <div class="cart-items" id="cartItems">
                <div class="empty-cart">
                    <div>🛒</div>
                    <p>Carrito vacío</p>
                    <small>Agrega productos para comenzar</small>
                </div>
            </div>
            
            <div class="cart-summary" id="cartSummary" style="display: none;">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$<span id="subtotal">0.00</span></span>
                </div>
                <div class="summary-row">
                    <span>IVA (21%):</span>
                    <span>$<span id="iva">0.00</span></span>
                </div>
                <div class="summary-row total">
                    <span>TOTAL:</span>
                    <span>$<span id="total">0.00</span></span>
                </div>
            </div>
            
            <div class="payment-section" id="paymentSection" style="display: none;">
                <h3>💳 Método de Pago</h3>
                <div class="payment-methods">
                    <label class="payment-option">
                        <input type="radio" name="payment" value="Efectivo" checked>
                        💵 Efectivo
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment" value="Tarjeta_Debito">
                        💳 Débito
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment" value="Tarjeta_Credito">
                        💳 Crédito
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment" value="Transferencia">
                        🏦 Transfer
                    </label>
                </div>
                
                <!-- Campos adicionales para transferencia -->
                <div id="transferData" style="display: none; margin-top: 15px; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                    <h4 style="margin-top: 0; color: #ffc107; font-size: 14px;">Datos del Cliente</h4>
                    <input type="text" id="clienteNombre" placeholder="Nombre completo" 
                           style="width: 100%; padding: 8px; margin-bottom: 8px; border-radius: 5px; border: 1px solid #444; background: rgba(255,255,255,0.1); color: white;">
                    <input type="text" id="clienteAlias" placeholder="Alias/CVU" 
                           style="width: 100%; padding: 8px; margin-bottom: 8px; border-radius: 5px; border: 1px solid #444; background: rgba(255,255,255,0.1); color: white;">
                    <input type="text" id="clienteCBU" placeholder="CBU (opcional)" 
                           style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #444; background: rgba(255,255,255,0.1); color: white;">
                </div>
                
                <button class="process-btn" id="processBtn" onclick="processSale()">
                    <i class="fa fa-check-circle"></i> Procesar Venta
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL DE RECIBO -->
    <div id="receiptModal" class="modal">
        <div class="receipt">
            <div class="receipt-header">
                <h2>📄 RECIBO OFICIAL</h2>
                <button class="close-btn" onclick="closeReceipt()">✕</button>
            </div>
            
            <div class="receipt-info">
                <p style="font-size: 18px; font-weight: bold; color: #F5A623; text-align: center; margin: 5px 0;">
                    ⚡ ALTO VOLTAJE S.R.L.
                </p>
                <p style="text-align: center; font-size: 12px; color: #666; margin: 3px 0;">
                    Formosa, Argentina
                </p>
            </div>
            
            <div style="background: #fff3cd; padding: 15px; border-bottom: 2px solid #ffc107;">
                <p style="margin: 3px 0;"><strong>N° RECIBO:</strong> <span id="receiptNumber"></span></p>
                <p style="margin: 3px 0;"><strong>FECHA:</strong> <span id="receiptDate"></span></p>
            </div>
            
            <div style="padding: 15px; border-bottom: 1px solid #eee;">
                <p style="margin: 3px 0;"><strong>EMPLEADO:</strong> <span id="receiptEmployee"></span></p>
            </div>
            
            <div class="receipt-items" id="receiptItems">
                <!-- Los items se cargan dinámicamente -->
            </div>
            
            <div class="receipt-total">
                <div class="receipt-row">
                    <span>Subtotal:</span>
                    <span>$<span id="receiptSubtotal">0.00</span></span>
                </div>
                <div class="receipt-row">
                    <span>IVA (21%):</span>
                    <span>$<span id="receiptIVA">0.00</span></span>
                </div>
                <div class="receipt-row total">
                    <span>TOTAL:</span>
                    <span>$<span id="receiptTotal">0.00</span></span>
                </div>
            </div>
            
            <div style="padding: 15px; background: #f8f9fa;">
                <p style="margin: 5px 0;">
                    <strong>MÉTODO DE PAGO:</strong> 
                    <span id="receiptPayment" style="color: #28a745; font-weight: bold;"></span>
                </p>
                <div id="receiptClientData"></div>
            </div>
            
            <div class="receipt-actions">
                <button class="btn btn-primary" onclick="printReceipt()">
                    <i class="fa fa-print"></i> Imprimir
                </button>
                <button class="btn btn-secondary" onclick="closeReceipt()">
                    <i class="fa fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Variables globales definidas desde PHP
        const empleadoNombre = '<?= htmlspecialchars($data['nombre_usuario']) ?>';
        const BASE_URL = '<?= base_url() ?>'; // ⭐ URL base del proyecto
    </script>
    <script src="<?= media() ?>/js/functions_ventas.js"></script>
</body>
</html>