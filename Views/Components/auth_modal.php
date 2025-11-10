<!-- Modal de Autenticación para Favoritos -->
<div class="modal fade" id="authModal" tabindex="-1" role="dialog" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content auth-modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="authModalLabel">
                    <i class="fa fa-heart text-danger"></i>
                    Iniciar Sesión para Guardar Favoritos
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="auth-icon-container mb-3">
                    <i class="fa fa-lock fa-3x text-primary"></i>
                </div>
                <p class="mb-4">Para agregar productos a tu lista de favoritos necesitas tener una cuenta.</p>
                <div class="auth-options">
                    <a href="<?= BASE_URL ?>/auth/login" class="btn btn-primary btn-block btn-lg mb-2">
                        <i class="fa fa-sign-in"></i> Iniciar Sesión
                    </a>
                    <a href="<?= BASE_URL ?>/auth/register" class="btn btn-outline-primary btn-block btn-lg">
                        <i class="fa fa-user-plus"></i> Crear Cuenta Nueva
                    </a>
                </div>
                <div class="mt-3">
                    <small class="text-muted">¿Por qué crear una cuenta?</small>
                    <ul class="text-left mt-2" style="font-size: 0.9rem;">
                        <li>Guarda tus productos favoritos</li>
                        <li>Acceso rápido a tus compras</li>
                        <li>Ofertas exclusivas</li>
                        <li>Historial de pedidos</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
