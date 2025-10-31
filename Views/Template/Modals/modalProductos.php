<!-- Modal -->
<div class="modal fade" id="modalFormProductos" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" >
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal">Nueva Producto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form id="formProductos" name="formProductos" class="form-horizontal" enctype="multipart/form-data">
              <input type="hidden" id="idProducto" name="idProducto" value="">
              <input type="hidden" id="imagenesEliminadas" name="imagenesEliminadas" value="">
              <p class="text-primary">Los campos con asterisco (<span class="required">*</span>) son obligatorios.</p>
              <div class="row">
                <!-- Columna Izquierda: Información Básica -->
                <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Nombre Producto <span class="required">*</span></label>
                      <input class="form-control" id="txtNombre" name="txtNombre" type="text" required="">
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="control-label">SKU del Producto <span class="required">*</span></label>
                            <input class="form-control" id="txtSKU" name="txtSKU" type="text" placeholder="Ej: PROD-001" required="">
                            <small class="form-text text-muted">Código interno único del producto</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">Código de Barras</label>
                            <input class="form-control" id="txtCodigoBarras" name="txtCodigoBarras" type="text" placeholder="Ej: 1234567890123">
                            <small class="form-text text-muted">Código de barras EAN-13 o UPC</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Descripción Producto</label>
                      <textarea class="form-control" id="txtDescripcion" name="txtDescripcion" rows="4"></textarea>
                    </div>
                    
                    <!-- Sección de Precios -->
                    <div class="form-group">
                        <label class="control-label">Información de Precios</label>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">Precio Costo <span class="required">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input class="form-control" id="txtPrecioCosto" name="txtPrecioCosto" type="number" step="0.01" min="0" required="" onchange="calcularMargen()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Precio Venta <span class="required">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input class="form-control" id="txtPrecio" name="txtPrecio" type="number" step="0.01" min="0" required="" onchange="calcularMargen()">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label class="control-label">Margen Ganancia (%)</label>
                                <div class="input-group">
                                    <input class="form-control" id="txtMargenGanancia" name="txtMargenGanancia" type="number" step="0.01" min="0" max="100" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Stock Inicial <span class="required">*</span></label>
                                <input class="form-control" id="txtStock" name="txtStock" type="number" min="0" required="">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campo de Precio de Oferta (inicialmente oculto) -->
                    <div class="form-group" id="grupoPrecioOferta" style="display: none;">
                        <label class="control-label">Precio de Oferta</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input class="form-control" id="txtPrecioOferta" name="txtPrecioOferta" type="number" step="0.01" min="0">
                        </div>
                        <small class="form-text text-muted">Precio especial cuando el producto esté en oferta</small>
                    </div>
                </div>
                
                <!-- Columna Derecha: Categorización y Opciones -->
                <div class="col-md-6">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="listCategoriaPrincipal">Categoría Principal <span class="required">*</span></label>
                            <select class="form-control" id="listCategoriaPrincipal" name="listCategoriaPrincipal" required="">
                                <option value="">Seleccionar Categoría</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="listCategoria">Subcategoría <span class="required">*</span></label>
                            <select class="form-control" id="listCategoria" name="listCategoria" required="" disabled>
                                <option value="">Seleccionar Subcategoría</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="listStatus">Estado <span class="required">*</span></label>
                            <select class="form-control selectpicker" id="listStatus" name="listStatus" required="">
                              <option value="1">Activo</option>
                              <option value="2">Inactivo</option>
                              <option value="3">Descontinuado</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">Marca</label>
                            <input class="form-control" id="txtMarca" name="txtMarca" type="text" placeholder="Marca del producto">
                        </div>
                    </div>
                    
                    <!-- Opciones especiales del producto -->
                    <div class="form-group">
                        <label class="control-label">Opciones Especiales</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="chkEnOferta" name="chkEnOferta" onchange="togglePrecioOferta()">
                                    <label class="form-check-label" for="chkEnOferta">
                                        <i class="fas fa-tag text-warning"></i> Producto en Oferta
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="chkDestacado" name="chkDestacado">
                                    <label class="form-check-label" for="chkDestacado">
                                        <i class="fas fa-star text-primary"></i> Producto Destacado
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sección de Imágenes -->
                    <div class="form-group">
                        <label class="control-label">Galería de Imágenes</label>
                        <div id="containerGallery" class="border rounded p-3 bg-light">
                            <div class="text-center">
                                <span class="text-muted">Agregar fotos del producto (440 x 545)</span><br>
                                <button class="btnAddImage btn btn-info btn-sm mt-2" type="button">
                                    <i class="fas fa-plus"></i> Agregar Imagen
                                </button>
                            </div>
                            <!-- Input hidden para manejar archivos -->
                            <input type="file" id="fileInput" name="imagen[]" multiple accept="image/*" style="display: none;">
                            
                            <!-- Contenedor para las imágenes -->
                            <div id="containerImages" class="mt-3">
                                <!-- Las imágenes se agregarán aquí dinámicamente -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones de Acción -->
                    <div class="row mt-4">
                       <div class="form-group col-md-6">
                           <button id="btnActionForm" class="btn btn-primary btn-lg btn-block" type="submit">
                               <i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Guardar</span>
                           </button>
                       </div> 
                       <div class="form-group col-md-6">
                           <button class="btn btn-danger btn-lg btn-block" type="button" data-dismiss="modal">
                               <i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar
                           </button>
                       </div> 
                    </div>
                </div>
              </div>
            </form>
      </div>
    </div>
  </div>
</div>

<style>
/* Estilos para la galería de imágenes */
.containerImage {
    position: relative;
    display: inline-block;
    margin: 5px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    background: #f8f9fa;
}
.prevImage {
    position: relative;
}
.prevImage img {
    display: block;
    border-radius: 4px;
}
.btnUploadfile, .btnDeleteImage {
    position: absolute;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
}
.btnUploadfile {
    bottom: 5px;
    left: 5px;
    background: #007bff;
    color: white;
}
.btnDeleteImage {
    top: 5px;
    right: 5px;
    background: #dc3545;
    color: white;
}
.btnUploadfile:hover {
    background: #0056b3;
    transform: scale(1.1);
}
.btnDeleteImage:hover {
    background: #c82333;
    transform: scale(1.1);
}

/* Estilos para mejorar el espaciado del formulario */
.form-group {
    margin-bottom: 1.5rem;
}
.form-group label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}
.form-check-label {
    font-weight: normal !important;
    cursor: pointer;
}
.form-check-input {
    margin-top: 0.25rem;
}
#containerGallery {
    min-height: 120px;
}
#containerImages {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
}
</style>

<!-- Modal -->
<div class="modal fade" id="modalViewProducto" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" >
    <div class="modal-content">
      <div class="modal-header header-primary">
        <h5 class="modal-title" id="titleModal">Datos del Producto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tbody>
            <tr>
              <td>Codigo:</td>
              <td id="celCodigo"></td>
            </tr>
            <tr>
              <td>Nombres:</td>
              <td id="celNombre"></td>
            </tr>
            <tr>
              <td>Precio:</td>
              <td id="celPrecio"></td>
            </tr>
            <tr>
              <td>Stock:</td>
              <td id="celStock"></td>
            </tr>
            <tr>
              <td>Categoría:</td>
              <td id="celCategoria"></td>
            </tr>
            <tr>
              <td>Estado:</td>
              <td id="celStatus"></td>
            </tr>
            <tr>
              <td>Descripción:</td>
              <td id="celDescripcion"></td>
            </tr>
            <tr>
              <td>Fotos de referencia:</td>
              <td id="celFotos">
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
