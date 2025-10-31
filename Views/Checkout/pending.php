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
    color: #ff9500;
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

.pending-info {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
    font-size: 14px;
    color: #856404;
}

.loading-dots {
    display: inline-block;
}

.loading-dots:after {
    content: '';
    animation: dots 1.5s infinite;
}

@keyframes dots {
    0%, 20% { content: ''; }
    40% { content: '.'; }
    60% { content: '..'; }
    80%, 100% { content: '...'; }
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
            <i class="fa fa-clock-o"></i>
        </div>
        
        <h1 class="result-title">Pago en proceso<span class="loading-dots"></span></h1>
        
        <p class="result-message">
            Tu pago está siendo procesado. Te notificaremos por email cuando esté confirmado.
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
                <strong style="color: #ff9500;">Pendiente</strong>
            </div>
            
            <div class="payment-detail">
                <span>Fecha:</span>
                <strong><?= date('d/m/Y H:i') ?></strong>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="pending-info">
            <strong>⏳ ¿Qué sigue?</strong><br>
            • Tu pago está siendo verificado<br>
            • Recibirás un email con la confirmación<br>
            • El proceso puede tomar unos minutos<br>
            • No cierres esta ventana hasta recibir la confirmación
        </div>
        
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
        
        <div style="margin-top: 30px; font-size: 14px; color: #666;">
            <p>¿Tenés dudas? Contactanos:</p>
            <p>
                <i class="fa fa-whatsapp" style="color: #25d366;"></i>
                <strong>+54 3704-804704</strong>
            </p>
        </div>
    </div>
</div>

<script>
// Auto-refresh cada 30 segundos para verificar el estado del pago
let refreshCount = 0;
const maxRefresh = 10; // Máximo 5 minutos (10 * 30 segundos)

const autoRefresh = setInterval(() => {
    refreshCount++;
    
    if (refreshCount >= maxRefresh) {
        clearInterval(autoRefresh);
        console.log('Auto-refresh detenido después de 5 minutos');
        return;
    }
    
    // Aquí podrías hacer una consulta AJAX para verificar el estado del pago
    console.log(`Verificando estado del pago... (${refreshCount}/${maxRefresh})`);
    
    // Por ahora solo refrescamos la página
    // location.reload();
}, 30000);

// Limpiar carrito después de 5 minutos si sigue pendiente
setTimeout(() => {
    localStorage.removeItem('altoVoltajeCart');
    sessionStorage.removeItem('pendingOrder');
    
    if (window.carritoLateral) {
        window.carritoLateral.clear();
    }
}, 300000); // 5 minutos
</script>

<?php 
    footerTienda($data);
?>