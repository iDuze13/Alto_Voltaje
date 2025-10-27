<!-- Modal -->
<div class="modal fade" id="modalFormUsuario" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <div class="modal-header-content">
          <div class="modal-icon">
            <i class="fa-solid fa-user-plus"></i>
          </div>
          <div class="modal-title-info">
            <h5 class="modal-title" id="titleModal">Nuevo Usuario</h5>
            <p class="modal-subtitle">Complete la información del usuario</p>
          </div>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
           <form id="formUsuario" name="formUsuario" class="form-horizontal">
                <input type="hidden" id="idUsuario" name="idUsuario" value="">
                <p class="text-primary">Todo los campos son obligatorios.</p>

                <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="txtCUIL">CUIL</label>
                      <input type="text" class="form-control" id="txtCUIL" name="txtCUIL" required="">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="txtNombre">Nombre</label>
                      <input type="text" class="form-control" id="txtNombre" name="txtNombre" required="">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="txtApellido">Apellido</label>
                      <input type="text" class="form-control" id="txtApellido" name="txtApellido" required="">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="txtTelefono">Teléfono</label>
                      <input type="text" class="form-control" id="txtTelefono" name="txtTelefono" required="">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="txtCorreo">Correo</label>
                      <input type="text" class="form-control" id="txtCorreo" name="txtCorreo" required="">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="listRolId">Tipo Usuario</label>
                      <select type="text" class="form-control selectpicker" data-live-search="true" id="listRolId" name="listRolId" required>
                      </select>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="listEstado">Estado</label>
                      <select class="form-control selectpicker" id="listEstado" name="listEstado" required>
                        <option value="1">Activo</option>
                        <option value="2">Inactivo</option>
                      </select>
                    </div>
                </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="txtPassword">Contraseña</label>
                  <input type="password" class="form-control" id="txtPassword" name="txtPassword" required="">
                  <small class="form-text text-muted" id="passwordHelp" style="display: none;">
                    Deje vacío para mantener la contraseña actual
                  </small>
                </div>
              </div>

        <div class="modal-footer-modern">
          <button class="btn-modern btn-secondary" type="button" data-dismiss="modal">
            <i class="fa-solid fa-times"></i>
            <span>Cancelar</span>
          </button>
          <button id="btnActionForm" class="btn-modern btn-primary" type="submit">
            <i class="fa-solid fa-check"></i>
            <span id="btnText">Guardar</span>
          </button>
        </div>
            </form>
      </div>
    </div>
  </div>
</div>