<?php
    headerTienda($data);
?>

<!-- Estilos del checkout -->
<link rel="stylesheet" type="text/css" href="<?= media() ?>/css/checkout.css?v=1.0">

<div class="checkout-container">
    <div class="container">
        <!-- Breadcrumb -->
        <div class="checkout-breadcrumb">
            <div class="breadcrumb-step active" data-step="1">
                <span class="step-number">1</span>
                <span class="step-title">Carrito</span>
            </div>
            <div class="breadcrumb-step" data-step="2">
                <span class="step-number">2</span>
                <span class="step-title">Envío</span>
            </div>
            <div class="breadcrumb-step" data-step="3">
                <span class="step-number">3</span>
                <span class="step-title">Pago</span>
            </div>
            <div class="breadcrumb-step" data-step="4">
                <span class="step-number">4</span>
                <span class="step-title">Confirmación</span>
            </div>
        </div>

        <div class="checkout-content">
            <div class="checkout-main">
                <!-- PASO 1: Revisión del Carrito -->
                <div class="checkout-step" id="step-1">
                    <div class="step-header">
                        <h2>Revisá tu pedido</h2>
                        <p>Verificá los productos antes de continuar</p>
                    </div>
                    
                    <div class="cart-review" id="cartReviewItems">
                        <!-- Los productos se cargarán aquí dinámicamente -->
                    </div>
                    
                    <div class="step-actions">
                        <button type="button" class="btn-secondary" onclick="window.history.back()">
                            <i class="fa fa-arrow-left"></i> Seguir comprando
                        </button>
                        <button type="button" class="btn-primary" id="continueToShipping">
                            Continuar <i class="fa fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- PASO 2: Datos de Envío -->
                <div class="checkout-step" id="step-2" style="display: none;">
                    <div class="step-header">
                        <h2>¿Dónde lo entregamos?</h2>
                        <p>Completá tus datos para el envío</p>
                    </div>
                    
                    <form class="shipping-form" id="shippingForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nombre">Nombre *</label>
                                <input type="text" id="nombre" name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="apellido">Apellido *</label>
                                <input type="text" id="apellido" name="apellido" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono">Teléfono *</label>
                                <input type="tel" id="telefono" name="telefono" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="direccion">Dirección completa *</label>
                            <input type="text" id="direccion" name="direccion" placeholder="Calle, número, piso, depto" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="ciudad">Ciudad *</label>
                                <input type="text" id="ciudad" name="ciudad" required>
                            </div>
                            <div class="form-group">
                                <label for="provincia">Provincia *</label>
                                <select id="provincia" name="provincia" required>
                                    <option value="">Seleccionar provincia</option>
                                    <option value="Buenos Aires">Buenos Aires</option>
                                    <option value="Catamarca">Catamarca</option>
                                    <option value="Chaco">Chaco</option>
                                    <option value="Chubut">Chubut</option>
                                    <option value="Córdoba">Córdoba</option>
                                    <option value="Corrientes">Corrientes</option>
                                    <option value="Entre Ríos">Entre Ríos</option>
                                    <option value="Formosa">Formosa</option>
                                    <option value="Jujuy">Jujuy</option>
                                    <option value="La Pampa">La Pampa</option>
                                    <option value="La Rioja">La Rioja</option>
                                    <option value="Mendoza">Mendoza</option>
                                    <option value="Misiones">Misiones</option>
                                    <option value="Neuquén">Neuquén</option>
                                    <option value="Río Negro">Río Negro</option>
                                    <option value="Salta">Salta</option>
                                    <option value="San Juan">San Juan</option>
                                    <option value="San Luis">San Luis</option>
                                    <option value="Santa Cruz">Santa Cruz</option>
                                    <option value="Santa Fe">Santa Fe</option>
                                    <option value="Santiago del Estero">Santiago del Estero</option>
                                    <option value="Tierra del Fuego">Tierra del Fuego</option>
                                    <option value="Tucumán">Tucumán</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="codigo_postal">Código Postal</label>
                                <input type="text" id="codigo_postal" name="codigo_postal">
                            </div>
                        </div>
                        
                        <div class="shipping-options">
                            <h3>Opciones de envío</h3>
                            <div class="shipping-option selected" data-cost="0">
                                <input type="radio" name="shipping_type" value="standard" id="shipping_standard" checked>
                                <label for="shipping_standard">
                                    <div class="option-info">
                                        <strong>Envío a domicilio</strong>
                                        <span class="shipping-time">Llega en 3-5 días hábiles</span>
                                    </div>
                                    <div class="option-price" id="shippingCost">Gratis</div>
                                </label>
                            </div>
                        </div>
                    </form>
                    
                    <div class="step-actions">
                        <button type="button" class="btn-secondary" id="backToCart">
                            <i class="fa fa-arrow-left"></i> Volver al carrito
                        </button>
                        <button type="button" class="btn-primary" id="continueToPayment">
                            Continuar <i class="fa fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- PASO 3: Método de Pago -->
                <div class="checkout-step" id="step-3" style="display: none;">
                    <div class="step-header">
                        <h2>¿Cómo querés pagar?</h2>
                        <p>Elegí tu método de pago preferido</p>
                    </div>
                    
                    <div class="payment-methods">
                        <!-- Vexor/MercadoPago -->
                        <div class="payment-option" data-method="vexor">
                            <input type="radio" name="payment_method" id="payment_vexor" value="vexor" checked>
                            <label for="payment_vexor">
                                <div class="payment-header">
                                    <i class="fa fa-credit-card" style="color: #009ee3;"></i>
                                    <span>MercadoPago (Recomendado)</span>
                                    <span class="payment-badge">Seguro</span>
                                </div>
                            </label>
                            <div class="payment-details" style="display: block;">
                                <div class="vexor-info">
                                    <p><strong>Pagá de forma segura con MercadoPago:</strong></p>
                                    <ul class="payment-features">
                                        <li><i class="fa fa-check"></i> Tarjetas de crédito y débito</li>
                                        <li><i class="fa fa-check"></i> Transferencia desde tu banco</li>
                                        <li><i class="fa fa-check"></i> Dinero en cuenta de MercadoPago</li>
                                        <li><i class="fa fa-check"></i> Efectivo en PagoFácil y Rapipago</li>
                                        <li><i class="fa fa-check"></i> Cuotas sin interés disponibles</li>
                                    </ul>
                                    <div class="payment-security">
                                        <i class="fa fa-shield" style="color: #00a650;"></i>
                                        <span>Procesado de forma segura por Vexor & MercadoPago</span>
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        Al continuar serás redirigido a MercadoPago para completar el pago de forma segura
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tarjetas de Crédito/Débito -->
                        <div class="payment-option" data-method="card">
                            <input type="radio" name="payment_method" id="payment_card" value="card">
                            <label for="payment_card">
                                <div class="payment-header">
                                    <i class="fa fa-credit-card"></i>
                                    <span>Tarjeta de Crédito o Débito</span>
                                </div>
                            </label>
                            <div class="payment-details" style="display: none;">
                                <div class="form-group">
                                    <label for="card_number">Número de tarjeta</label>
                                    <input type="text" id="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="card_name">Nombre del titular</label>
                                        <input type="text" id="card_name" placeholder="Como aparece en la tarjeta">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="card_expiry">Vencimiento</label>
                                        <input type="text" id="card_expiry" placeholder="MM/AA" maxlength="5">
                                    </div>
                                    <div class="form-group">
                                        <label for="card_cvc">CVC</label>
                                        <input type="text" id="card_cvc" placeholder="123" maxlength="4">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transferencia Bancaria -->
                        <div class="payment-option" data-method="transfer">
                            <input type="radio" name="payment_method" id="payment_transfer" value="transfer">
                            <label for="payment_transfer">
                                <div class="payment-header">
                                    <i class="fa fa-university"></i>
                                    <span>Transferencia Bancaria</span>
                                </div>
                            </label>
                            <div class="payment-details" style="display: none;">
                                <div class="transfer-info">
                                    <p><strong>Datos para transferencia:</strong></p>
                                    <p>Banco: Banco Nación</p>
                                    <p>CBU: 0110599520000012345678</p>
                                    <p>Alias: ALTO.VOLTAJE.FSA</p>
                                    <p>Titular: Alto Voltaje</p>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        Enviá el comprobante por WhatsApp al +54 3704-804704
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Efectivo -->
                        <div class="payment-option" data-method="cash">
                            <input type="radio" name="payment_method" id="payment_cash" value="cash">
                            <label for="payment_cash">
                                <div class="payment-header">
                                    <i class="fa fa-money"></i>
                                    <span>Efectivo contra entrega</span>
                                </div>
                            </label>
                            <div class="payment-details" style="display: none;">
                                <div class="cash-info">
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-circle"></i>
                                        Disponible solo para envíos en Formosa Capital
                                    </div>
                                    <p>Pagás cuando recibís el producto</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="step-actions">
                        <button type="button" class="btn-secondary" id="backToShipping">
                            <i class="fa fa-arrow-left"></i> Volver
                        </button>
                        <button type="button" class="btn-primary" id="finalizePurchase">
                            Finalizar compra <i class="fa fa-check"></i>
                        </button>
                    </div>
                </div>

                <!-- PASO 4: Confirmación -->
                <div class="checkout-step" id="step-4" style="display: none;">
                    <div class="step-header">
                        <div class="success-icon">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <h2>¡Pedido confirmado!</h2>
                        <p>Tu pedido fue procesado correctamente</p>
                    </div>
                    
                    <div class="order-confirmation" id="orderConfirmation">
                        <!-- Se llenará dinámicamente -->
                    </div>
                    
                    <div class="step-actions">
                        <button type="button" class="btn-primary" onclick="window.location.href='<?= BASE_URL ?>'">
                            Volver al inicio
                        </button>
                        <button type="button" class="btn-secondary" onclick="window.location.href='<?= BASE_URL ?>/tienda'">
                            Seguir comprando
                        </button>
                    </div>
                </div>
            </div>

            <!-- Resumen lateral -->
            <div class="checkout-sidebar">
                <div class="order-summary">
                    <h3>Resumen del pedido</h3>
                    
                    <div class="summary-items" id="summaryItems">
                        <!-- Items se cargan dinámicamente -->
                    </div>
                    
                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>Productos (<span id="totalItems">0</span>)</span>
                            <span id="summarySubtotal">$0</span>
                        </div>
                        <div class="summary-row">
                            <span>Envío</span>
                            <span id="summaryShipping">Gratis</span>
                        </div>
                        <div class="summary-total">
                            <span>Total</span>
                            <span id="summaryTotal">$0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Pasar BASE_URL a JavaScript
    window.BASE_URL = '<?= BASE_URL ?>';
</script>
<script src="<?= media() ?>/js/checkout.js?v=1.0"></script>

<?php
    footerTienda($data);
?>