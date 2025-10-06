<!-- Modal -->
<div class="modal fade" id="modalFormProveedor" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <div class="modal-header-content">
          <div class="modal-icon">
            <i class="fa-solid fa-truck"></i>
          </div>
          <div class="modal-title-section">
            <h5 class="modal-title" id="titleModal">Nuevo Proveedor</h5>
            <p class="modal-subtitle">Complete la información del proveedor</p>
          </div>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formProveedor" name="formProveedor">
          <input type="hidden" id="idProveedor" name="idProveedor" value="">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Nombre Proveedor <span class="required">*</span></label>
                <input class="form-control" id="txtNombre" name="txtNombre" type="text" required="">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">CUIT <span class="required">*</span></label>
                <input class="form-control" id="txtCUIT" name="txtCUIT" type="text" required="">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Teléfono <span class="required">*</span></label>
                <input class="form-control" id="txtTelefono" name="txtTelefono" type="text" required="">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Email <span class="required">*</span></label>
                <input class="form-control" id="txtEmail" name="txtEmail" type="email" required="">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label">Dirección <span class="required">*</span></label>
                <input class="form-control" id="txtDireccion" name="txtDireccion" type="text" required="">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Ciudad <span class="required">*</span></label>
                <input class="form-control" id="txtCiudad" name="txtCiudad" type="text" required="">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Provincia <span class="required">*</span></label>
                <input class="form-control" id="txtProvincia" name="txtProvincia" type="text" required="">
              </div>
            </div>
          </div>
          <div class="tile-footer">
            <button id="btnActionForm" class="btn btn-primary" type="submit">
              <i class="fa fa-fw fa-lg fa-check-circle"></i>
              <span id="btnText">Guardar</span>
            </button>&nbsp;&nbsp;&nbsp;
            <button class="btn btn-secondary" type="button" data-dismiss="modal">
              <i class="fa fa-fw fa-lg fa-times-circle"></i>Cancelar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>