<!-- Modal -->
<div class="modal fade" id="modalFormProducto" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <div class="modal-header-content">
          <div class="modal-icon">
            <i class="fa-solid fa-box"></i>
          </div>
          <div class="modal-title-section">
            <h5 class="modal-title" id="titleModal">New Product</h5>
            <p class="modal-subtitle">Complete the product information</p>
          </div>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formProducto" name="formProducto" enctype="multipart/form-data">
          <input type="hidden" id="idProducto" name="idProducto" value="">
          <div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <label class="control-label">Product Name <span class="required">*</span></label>
                <input class="form-control" id="txtNombre" name="txtNombre" type="text" required="">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">SKU <span class="required">*</span></label>
                <input class="form-control" id="txtSKU" name="txtSKU" type="text" required="">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label">Description</label>
                <textarea class="form-control" id="txtDescripcion" name="txtDescripcion" rows="3"></textarea>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Price <span class="required">*</span></label>
                <input class="form-control" id="txtPrecio" name="txtPrecio" type="number" step="0.01" required="">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Stock <span class="required">*</span></label>
                <input class="form-control" id="txtStock" name="txtStock" type="number" required="">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Status</label>
                <select class="form-control" id="listStatus" name="listStatus">
                  <option value="Activo">Active</option>
                  <option value="Inactivo">Inactive</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Category</label>
                <select class="form-control" id="listCategoria" name="listCategoria">
                  <option value="">Select Category</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Brand</label>
                <input class="form-control" id="txtMarca" name="txtMarca" type="text">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label">Product Image</label>
                <input class="form-control" id="txtImagen" name="txtImagen" type="file" accept="image/*">
                <small class="form-text text-muted">Upload product image (JPG, PNG, GIF)</small>
              </div>
            </div>
          </div>
          <div class="tile-footer">
            <button id="btnActionForm" class="btn btn-primary" type="submit">
              <i class="fa fa-fw fa-lg fa-check-circle"></i>
              <span id="btnText">Save</span>
            </button>&nbsp;&nbsp;&nbsp;
            <button class="btn btn-secondary" type="button" data-dismiss="modal">
              <i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>