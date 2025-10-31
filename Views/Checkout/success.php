<?php 
    headerTienda($data);
?>

<style>
.payment-result {
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
}

.result-card {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    text-align: center;
    max-width: 500px;
    width: 100%;
}

.result-icon {
    font-size: 64px;
    margin-bottom: 20px;
    color: #00a650;
}

.result-title {
    font-size: 28px;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
}

.result-message {
    font-size: 16px;
    color: #666;
    margin-bottom: 30px;
    line-height: 1.5;
}

.payment-details {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    text-align: left;
}

.payment-detail {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 14px;
}

.payment-detail:last-child {
    margin-bottom: 0;
    font-weight: 600;
    font-size: 16px;
    padding-top: 10px;
    border-top: 1px solid #dee2e6;
}

.payment-detail strong {
    color: #333;
}

.btn-group {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #3483fa;
    color: white;
}

.btn-primary:hover {
    background: #2968c8;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    color: white;
}

@media (max-width: 768px) {
    .result-card {
        padding: 30px 20px;
    }
    
    .btn-group {
        flex-direction: column;
    }
}
</style>

<div class="payment-result">
    <div class="result-card">
        <div class="result-icon">
            <i class="fa fa-check-circle"></i>
        </div>
        
        <h1 class="result-title">¡Pago Exitoso!</h1>
        
        <p class="result-message">
            Tu pago ha sido procesado correctamente. Te hemos enviado un email con los detalles de tu compra.
        </p>
        
        <?php if (isset($data['payment_info'])): ?>
        <div class="payment-details">
            <?php if (!empty($data['payment_info']['order_number'])): ?>
            <div class="payment-detail">
                <span>Número de pedido:</span>
                <strong><?= $data['payment_info']['order_number'] ?></strong>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($data['payment_info']['payment_id'])): ?>
            <div class="payment-detail">
                <span>ID de pago:</span>
                <strong><?= $data['payment_info']['payment_id'] ?></strong>
            </div>
            <?php endif; ?>
            
            <div class="payment-detail">
                <span>Estado:</span>
                <strong style="color: #00a650;">Pagado</strong>
            </div>
            
            <div class="payment-detail">
                <span>Fecha:</span>
                <strong><?= date('d/m/Y H:i') ?></strong>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="btn-group">
            <a href="<?= base_url() ?>/tienda" class="btn btn-primary">
                <i class="fa fa-shopping-bag"></i>
                Seguir comprando
            </a>
            <a href="<?= base_url() ?>" class="btn btn-secondary">
                <i class="fa fa-home"></i>
                Volver al inicio
            </a>
        </div>
    </div>
</div>

<script>
// Limpiar carrito al confirmar pago exitoso
localStorage.removeItem('altoVoltajeCart');
sessionStorage.removeItem('pendingOrder');

// Actualizar contador del carrito
if (window.carritoLateral) {
    window.carritoLateral.clear();
}
</script>

<?php 
    footerTienda($data);
?>