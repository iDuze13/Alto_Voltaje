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
    color: #e60e2e;
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

.support-info {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
    font-size: 14px;
    color: #856404;
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
            <i class="fa fa-times-circle"></i>
        </div>
        
        <h1 class="result-title">Pago no realizado</h1>
        
        <p class="result-message">
            Tu pago no pudo ser procesado. No te preocupes, no se realizó ningún cargo a tu cuenta.
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
                <strong style="color: #e60e2e;">Fallido</strong>
            </div>
            
            <div class="payment-detail">
                <span>Fecha:</span>
                <strong><?= date('d/m/Y H:i') ?></strong>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="support-info">
            <strong>💡 ¿Qué puedes hacer?</strong><br>
            • Verificá los datos de tu tarjeta<br>
            • Asegurate de tener fondos suficientes<br>
            • Probá con otro método de pago<br>
            • Contactános si el problema persiste
        </div>
        
        <div class="btn-group">
            <a href="<?= base_url() ?>/checkout" class="btn btn-primary">
                <i class="fa fa-refresh"></i>
                Intentar nuevamente
            </a>
            <a href="<?= base_url() ?>/tienda" class="btn btn-secondary">
                <i class="fa fa-shopping-bag"></i>
                Seguir comprando
            </a>
        </div>
        
        <div style="margin-top: 30px; font-size: 14px; color: #666;">
            <p>¿Necesitás ayuda? Contactanos:</p>
            <p>
                <i class="fa fa-whatsapp" style="color: #25d366;"></i>
                <strong>+54 3704-804704</strong>
            </p>
        </div>
    </div>
</div>

<?php 
    footerTienda($data);
?>