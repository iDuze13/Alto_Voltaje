<?php
require_once(__DIR__ . '/../../Helpers/Helpers.php');
require_once(__DIR__ . '/../Template/headerEmpleado.php');
$producto = $data['producto'] ?? null;
$errores = $data['errores'] ?? [];
$isEdit = !empty($producto);
?>

<div class="container" style="max-width:900px;margin:20px auto;background:#1a1a1a;border-radius:15px;box-shadow:0 10px 30px rgba(245,166,35,.2);padding:30px;border:2px solid rgba(245,166,35,.3);position:relative;">
  <h2 style="text-align:center;color:#F5A623;margin-bottom:20px;">
    <?= $isEdit ? 'Editar Producto' : 'Crear Nuevo Producto' ?>
  </h2>

  <?php if (!empty($errores)): ?>
    <div class="alert" style="padding:15px 20px;margin-bottom:20px;border-radius:10px;border-left:5px solid #dc3545;background:rgba(220,53,69,.1);color:#ff6b6b;">
      <strong>Por favor corrige los siguientes errores:</strong>
      <ul>
        <?php foreach ($errores as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>/productos/guardar" method="post">
    <input type="hidden" name="idProducto" value="<?= htmlspecialchars($producto['idProducto'] ?? '') ?>" />

    <div class="form-group" style="margin-bottom:15px;">
      <label for="nombre" style="display:block;color:#F5A623;font-weight:700;">Nombre del Producto *</label>
      <input type="text" id="nombre" name="Nombre_Producto" required maxlength="100"
             value="<?= htmlspecialchars($producto['Nombre_Producto'] ?? '') ?>"
             style="width:100%;padding:12px;border:2px solid rgba(245,166,35,.3);border-radius:8px;background:#2c2c2c;color:#fff;">
    </div>

    <div class="form-group" style="margin-bottom:15px;">
      <label for="descripcion" style="display:block;color:#F5A623;font-weight:700;">Descripción</label>
      <textarea id="descripcion" name="Descripcion_Producto" maxlength="450" style="width:100%;padding:12px;border:2px solid rgba(245,166,35,.3);border-radius:8px;background:#2c2c2c;color:#fff;min-height:100px;"><?= htmlspecialchars($producto['Descripcion_Producto'] ?? '') ?></textarea>
    </div>

    <div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
      <div class="form-group">
        <label for="sku" style="display:block;color:#F5A623;font-weight:700;">SKU *</label>
        <input type="text" id="sku" name="SKU" required maxlength="50"
               value="<?= htmlspecialchars($producto['SKU'] ?? '') ?>"
               style="width:100%;padding:12px;border:2px solid rgba(245,166,35,.3);border-radius:8px;background:#2c2c2c;color:#fff;">
      </div>
      <div class="form-group">
        <label for="marca" style="display:block;color:#F5A623;font-weight:700;">Marca *</label>
        <input type="text" id="marca" name="Marca" required maxlength="45"
               value="<?= htmlspecialchars($producto['Marca'] ?? '') ?>"
               style="width:100%;padding:12px;border:2px solid rgba(245,166,35,.3);border-radius:8px;background:#2c2c2c;color:#fff;">
      </div>
    </div>

    <div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
      <div class="form-group">
        <label for="precio_costo" style="display:block;color:#F5A623;font-weight:700;">Precio Costo *</label>
        <input type="number" id="precio_costo" name="Precio_Costo" step="0.01" min="0" max="99999999.99" required
               value="<?= htmlspecialchars($producto['Precio_Costo'] ?? '') ?>"
               style="width:100%;padding:12px;border:2px solid rgba(245,166,35,.3);border-radius:8px;background:#2c2c2c;color:#fff;">
      </div>
      <div class="form-group">
        <label for="precio_venta" style="display:block;color:#F5A623;font-weight:700;">Precio Venta *</label>
        <input type="number" id="precio_venta" name="Precio_Venta" step="0.01" min="0" max="99999999.99" required
               value="<?= htmlspecialchars($producto['Precio_Venta'] ?? '') ?>"
               style="width:100%;padding:12px;border:2px solid rgba(245,166,35,.3);border-radius:8px;background:#2c2c2c;color:#fff;">
      </div>
    </div>

    <div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
      <div class="form-group">
        <label for="precio_oferta" style="display:block;color:#F5A623;font-weight:700;">Precio Oferta</label>
        <input type="number" id="precio_oferta" name="Precio_Oferta" step="0.01" min="0" max="99999999.99"
               value="<?= htmlspecialchars($producto['Precio_Oferta'] ?? '') ?>"
               style="width:100%;padding:12px;border:2px solid rgba(245,166,35,.3);border-radius:8px;background:#2c2c2c;color:#fff;">
      </div>
      <div class="form-group">
        <label for="margen" style="display:block;color:#F5A623;font-weight:700;">Margen Ganancia (%) *</label>
        <input type="number" id="margen" name="Margen_Ganancia" step="0.01" min="0" max="999.99" required
               value="<?= htmlspecialchars($producto['Margen_Ganancia'] ?? '') ?>"
               style="width:100%;padding:12px;border:2px solid rgba(245,166,35,.3);border-radius:8px;background:#2c2c2c;color:#fff;">
      </div>
    </div>

    <div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
      <div class="form-group">
        <label for="stock" style="display:block;color:#F5A623;font-weight:700;">Stock Actual *</label>
        <input type="number" id="stock" name="Stock_Actual" min="0" step="1" required
               value="<?= htmlspecialchars($producto['Stock_Actual'] ?? '') ?>"
               style="width:100%;padding:12px;border:2px solid rgba(245,166,35,.3);border-radius:8px;background:#2c2c2c;color:#fff;">
      </div>
      <div class="form-group">
        <label for="estado" style="display:block;color:#F5A623;font-weight:700;">Estado *</label>
        <select id="estado" name="Estado_Producto" required style="width:100%;padding:12px;border:2px solid rgba(245,166,35,.3);border-radius:8px;background:#2c2c2c;color:#fff;">
          <?php $estados = ['Activo','Inactivo','Descontinuado']; $sel = $producto['Estado_Producto'] ?? 'Activo'; foreach($estados as $estado): ?>
            <option value="<?= $estado ?>" <?= $sel===$estado?'selected':'' ?>><?= $estado ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-group" style="margin-top:10px;display:flex;gap:12px;align-items:center;">
      <label style="color:#fff;display:flex;align-items:center;gap:8px;">
        <input type="checkbox" id="oferta" name="En_Oferta" value="1" <?= !empty($producto['En_Oferta'])?'checked':'' ?>> Producto en Oferta
      </label>
      <label style="color:#fff;display:flex;align-items:center;gap:8px;">
        <input type="checkbox" id="destacado" name="Es_Destacado" value="1" <?= !empty($producto['Es_Destacado'])?'checked':'' ?>> Producto Destacado
      </label>
    </div>

    <div class="form-actions" style="text-align:center;margin-top:20px;">
      <button type="submit" class="btn btn-primary" style="padding:12px 24px;border-radius:8px;background:linear-gradient(135deg,#F5A623 0%,#E09500 100%);color:#1a1a1a;font-weight:700;border:none;">
        <?= $isEdit ? 'Actualizar Producto' : 'Crear Producto' ?>
      </button>
      <a href="<?= BASE_URL ?>/productos/listar" class="btn btn-secondary" style="padding:12px 24px;border-radius:8px;background:#6c757d;color:#fff;text-decoration:none;">Cancelar</a>
    </div>
  </form>
</div>

<script>
const costo = document.getElementById('precio_costo');
const venta = document.getElementById('precio_venta');
const margen = document.getElementById('margen');
function calc(){
  const c = parseFloat(costo.value)||0; const v = parseFloat(venta.value)||0;
  if(c>0 && v>0){ margen.value = (((v-c)/c)*100).toFixed(2); }
}
costo.addEventListener('input', calc); venta.addEventListener('input', calc);
document.querySelector('form').addEventListener('submit', function(e){
  const c = parseFloat(costo.value)||0; const v = parseFloat(venta.value)||0;
  if(v <= c){ e.preventDefault(); alert('El precio de venta debe ser mayor al precio de costo.'); }
});
</script>

<?php require_once(__DIR__ . '/../Template/footerEmpleado.php'); ?>
