<!-- Sección de Reseñas -->
<div class="resenas-section" id="resenas-section">
    <div class="resenas-header">
        <h3 class="resenas-title">Reseñas y valoraciones</h3>
        <button class="btn-nueva-resena">Escribir reseña</button>
    </div>

    <!-- Estadísticas de reseñas -->
    <div class="resenas-estadisticas">
        <div class="estadisticas-promedio">
            <div class="promedio-numero">0.0</div>
            <div class="promedio-estrellas">
                <div class="estrellas estrellas-grandes">
                    <span class="estrella">★</span>
                    <span class="estrella">★</span>
                    <span class="estrella">★</span>
                    <span class="estrella">★</span>
                    <span class="estrella">★</span>
                </div>
            </div>
            <div class="total-resenas">0 reseñas</div>
        </div>

        <div class="estadisticas-barras">
            <div class="barra-calificacion" data-estrella="5">
                <div class="barra-numero">5</div>
                <div class="estrellas estrellas-pequenas">
                    <span class="estrella activa">★</span>
                </div>
                <div class="barra-progreso">
                    <div class="barra-relleno" style="width: 0%"></div>
                </div>
                <div class="barra-cantidad">0</div>
            </div>
            
            <div class="barra-calificacion" data-estrella="4">
                <div class="barra-numero">4</div>
                <div class="estrellas estrellas-pequenas">
                    <span class="estrella activa">★</span>
                </div>
                <div class="barra-progreso">
                    <div class="barra-relleno" style="width: 0%"></div>
                </div>
                <div class="barra-cantidad">0</div>
            </div>
            
            <div class="barra-calificacion" data-estrella="3">
                <div class="barra-numero">3</div>
                <div class="estrellas estrellas-pequenas">
                    <span class="estrella activa">★</span>
                </div>
                <div class="barra-progreso">
                    <div class="barra-relleno" style="width: 0%"></div>
                </div>
                <div class="barra-cantidad">0</div>
            </div>
            
            <div class="barra-calificacion" data-estrella="2">
                <div class="barra-numero">2</div>
                <div class="estrellas estrellas-pequenas">
                    <span class="estrella activa">★</span>
                </div>
                <div class="barra-progreso">
                    <div class="barra-relleno" style="width: 0%"></div>
                </div>
                <div class="barra-cantidad">0</div>
            </div>
            
            <div class="barra-calificacion" data-estrella="1">
                <div class="barra-numero">1</div>
                <div class="estrellas estrellas-pequenas">
                    <span class="estrella activa">★</span>
                </div>
                <div class="barra-progreso">
                    <div class="barra-relleno" style="width: 0%"></div>
                </div>
                <div class="barra-cantidad">0</div>
            </div>
        </div>
    </div>

    <!-- Formulario para nueva reseña -->
    <div class="formulario-resena">
        <h4>Escribir una reseña</h4>
        <form id="formulario-resena">
            <div class="form-group">
                <label class="form-label" for="usuario_nombre">Nombre *</label>
                <input type="text" id="usuario_nombre" name="usuario_nombre" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="usuario_email">Email *</label>
                <input type="email" id="usuario_email" name="usuario_email" class="form-input" required>
                <small style="color: #666; font-size: 12px;">No se mostrará públicamente</small>
            </div>

            <div class="form-group">
                <label class="form-label">Calificación *</label>
                <div class="calificacion-selector">
                    <span class="estrella" data-value="1">★</span>
                    <span class="estrella" data-value="2">★</span>
                    <span class="estrella" data-value="3">★</span>
                    <span class="estrella" data-value="4">★</span>
                    <span class="estrella" data-value="5">★</span>
                </div>
                <input type="hidden" id="calificacion" name="calificacion" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="titulo">Título de la reseña *</label>
                <input type="text" id="titulo" name="titulo" class="form-input" placeholder="Resumen de tu experiencia" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="comentario">Tu reseña *</label>
                <textarea id="comentario" name="comentario" class="form-textarea" placeholder="Cuéntanos sobre tu experiencia con este producto..." required></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancelar">Cancelar</button>
                <button type="submit" class="btn-enviar">Enviar reseña</button>
            </div>
        </form>
    </div>

    <!-- Lista de reseñas -->
    <div class="resenas-lista">
        <!-- Las reseñas se cargarán dinámicamente aquí -->
    </div>
</div>

<script>
    // Pasar el ID del producto al JavaScript
    window.PRODUCTO_ID = <?= $data['producto']['idproducto'] ?? 0 ?>;
</script>