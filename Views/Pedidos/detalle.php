<?php 
    headerAdmin($data);
    
    // Extraer datos
    $pedido = $data['pedido']['orden'];
    $cliente = $data['pedido']['cliente'];
    $detalle = $data['pedido']['detalle'];
    
    // Determinar color del badge según estado
    $badgeClass = 'badge-secondary';
    switch($pedido['status']) {
        case 'Procesando':
            $badgeClass = 'badge-warning';
            break;
        case 'Confirmado':
            $badgeClass = 'badge-info';
            break;
        case 'En preparación':
            $badgeClass = 'badge-primary';
            break;
        case 'Enviado':
        case 'Entregado':
            $badgeClass = 'badge-success';
            break;
        case 'Cancelado':
            $badgeClass = 'badge-danger';
            break;
        case 'Reembolsado':
            $badgeClass = 'badge-dark';
            break;
    }
    
    // Verificar si tiene permiso de actualización
    $permiso_actualizar = !empty($_SESSION['admin']) || (isset($_SESSION['permisos_modulos'][5]) && $_SESSION['permisos_modulos'][5]['u'] == 1);
?>
<style>
#divLoading {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}
#divLoading > div {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}
.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #009688;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
<div id="divLoading">
  <div>
    <div class="spinner"></div>
    <p style="margin-top: 15px; text-align: center; color: #333;">Cargando...</p>
  </div>
</div>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1>
                <i class="fas fa-shopping-cart"></i> Pedido #<?= $pedido['idpedido'] ?>
                <span class="badge <?= $badgeClass ?> ml-2"><?= $pedido['status'] ?></span>
            </h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/pedidos">Pedidos</a></li>
            <li class="breadcrumb-item active">Detalle</li>
        </ul>
    </div>

    <div class="row">
        <!-- Información del Cliente -->
        <div class="col-md-6">
            <div class="tile">
                <h3 class="tile-title">
                    <i class="fas fa-user"></i> Información del Cliente
                </h3>
                <div class="tile-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="150">Nombre:</th>
                                <td><?= $cliente['nombres'] ?? '' ?> <?= $cliente['apellidos'] ?? '' ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?= $cliente['email_user'] ?? 'N/A' ?></td>
                            </tr>
                            <tr>
                                <th>Teléfono:</th>
                                <td><?= $cliente['telefono'] ?? 'N/A' ?></td>
                            </tr>
                            <?php if(!empty($cliente['nit'])): ?>
                            <tr>
                                <th>NIT:</th>
                                <td><?= $cliente['nit'] ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if(!empty($cliente['nombrefiscal'])): ?>
                            <tr>
                                <th>Nombre Fiscal:</th>
                                <td><?= $cliente['nombrefiscal'] ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Información del Pedido -->
        <div class="col-md-6">
            <div class="tile">
                <h3 class="tile-title">
                    <i class="fas fa-file-invoice"></i> Información del Pedido
                </h3>
                <div class="tile-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="150">ID Pedido:</th>
                                <td><strong>#<?= $pedido['idpedido'] ?></strong></td>
                            </tr>
                            <tr>
                                <th>Fecha:</th>
                                <td><?= $pedido['fecha'] ?></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    <?php if($permiso_actualizar): ?>
                                        <form id="formActualizarEstado" class="form-inline">
                                            <input type="hidden" name="idpedido" value="<?= $pedido['idpedido'] ?>">
                                            <select class="form-control form-control-sm mr-2" id="selectEstado" name="estado">
                                                <option value="Procesando" <?= $pedido['status'] == 'Procesando' ? 'selected' : '' ?>>Procesando</option>
                                                <option value="Confirmado" <?= $pedido['status'] == 'Confirmado' ? 'selected' : '' ?>>Confirmado</option>
                                                <option value="En preparación" <?= $pedido['status'] == 'En preparación' ? 'selected' : '' ?>>En preparación</option>
                                                <option value="Enviado" <?= $pedido['status'] == 'Enviado' ? 'selected' : '' ?>>Enviado</option>
                                                <option value="Entregado" <?= $pedido['status'] == 'Entregado' ? 'selected' : '' ?>>Entregado</option>
                                                <option value="Cancelado" <?= $pedido['status'] == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-save"></i> Actualizar
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge <?= $badgeClass ?>"><?= $pedido['status'] ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Tipo de Pago:</th>
                                <td><?= $pedido['tipopago'] ?></td>
                            </tr>
                            <?php if(!empty($pedido['referenciacobro'])): ?>
                            <tr>
                                <th>Referencia:</th>
                                <td><?= $pedido['referenciacobro'] ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if(!empty($pedido['idtransaccionmp'])): ?>
                            <tr>
                                <th>ID Transacción:</th>
                                <td><?= $pedido['idtransaccionmp'] ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Dirección de Envío -->
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">
                    <i class="fas fa-map-marker-alt"></i> Dirección de Envío
                </h3>
                <div class="tile-body">
                    <p class="mb-0"><?= $pedido['direccion_envio'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalle de Productos -->
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">
                    <i class="fas fa-box"></i> Productos del Pedido
                </h3>
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center" width="100">Cantidad</th>
                                    <th class="text-right" width="150">Precio Unitario</th>
                                    <th class="text-right" width="150">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $subtotal = 0;
                                foreach($detalle as $item): 
                                    $subtotal += $item['subtotal'];
                                ?>
                                <tr>
                                    <td><?= $item['producto'] ?></td>
                                    <td class="text-center"><?= $item['cantidad'] ?></td>
                                    <td class="text-right">$<?= number_format($item['precio_unitario'], 2) ?></td>
                                    <td class="text-right">$<?= number_format($item['subtotal'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                    <td class="text-right"><strong>$<?= number_format($subtotal, 2) ?></strong></td>
                                </tr>
                                <?php if($pedido['costo_envio'] > 0): ?>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Costo de Envío:</strong></td>
                                    <td class="text-right"><strong>$<?= number_format($pedido['costo_envio'], 2) ?></strong></td>
                                </tr>
                                <?php endif; ?>
                                <tr class="table-active">
                                    <td colspan="3" class="text-right"><strong>TOTAL:</strong></td>
                                    <td class="text-right"><strong style="font-size: 1.2em;">$<?= number_format($pedido['monto'], 2) ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="tile-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <a href="<?= base_url(); ?>/pedidos" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver a Pedidos
                            </a>
                            <?php if($permiso_actualizar): ?>
                            <button type="button" class="btn btn-info" onclick="window.print();">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
const divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function() {
    <?php if($permiso_actualizar): ?>
    // Manejar actualización de estado
    const formActualizar = document.getElementById('formActualizarEstado');
    if(formActualizar) {
        formActualizar.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const estado = formData.get('estado');
            
            if(confirm('¿Está seguro de actualizar el estado del pedido a "' + estado + '"?')) {
                divLoading.style.display = "flex";
                
                fetch('<?= base_url(); ?>/Pedidos/setPedido', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    divLoading.style.display = "none";
                    if(data.status) {
                        swal("Actualizado", data.msg, "success").then(() => {
                            location.reload();
                        });
                    } else {
                        swal("Error", data.msg, "error");
                    }
                })
                .catch(error => {
                    divLoading.style.display = "none";
                    swal("Error", "Ocurrió un error al procesar la solicitud", "error");
                    console.error('Error:', error);
                });
            }
        });
    }
    <?php endif; ?>
});
</script>

<style>
@media print {
    .app-sidebar,
    .app-title,
    .tile-footer,
    #formActualizarEstado button,
    .breadcrumb {
        display: none !important;
    }
    
    .app-content {
        margin-left: 0 !important;
        padding: 20px !important;
    }
    
    .tile {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>

<?php footerAdmin($data); ?>
