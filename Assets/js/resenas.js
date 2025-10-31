/**
 * Gestor de Reseñas para productos
 */
class ResenasManager {
    constructor(productoId) {
        this.productoId = productoId;
        this.currentPage = 1;
        this.loading = false;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadResenas();
    }

    setupEventListeners() {
        // Botón para mostrar formulario de nueva reseña
        const btnNuevaResena = document.querySelector('.btn-nueva-resena');
        if (btnNuevaResena) {
            btnNuevaResena.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleFormulario();
            });
        }

        // Selector de calificación con estrellas
        this.setupCalificacionSelector();

        // Formulario de envío
        const formulario = document.getElementById('formulario-resena');
        if (formulario) {
            formulario.addEventListener('submit', (e) => {
                e.preventDefault();
                this.enviarResena();
            });
        }

        // Botón cancelar
        const btnCancelar = document.querySelector('.btn-cancelar');
        if (btnCancelar) {
            btnCancelar.addEventListener('click', () => {
                this.ocultarFormulario();
            });
        }

        // Botones de utilidad en reseñas existentes
        this.setupUtilidadBotones();
    }

    setupCalificacionSelector() {
        const selector = document.querySelector('.calificacion-selector');
        if (!selector) return;

        const estrellas = selector.querySelectorAll('.estrella');
        const inputCalificacion = document.getElementById('calificacion');

        estrellas.forEach((estrella, index) => {
            estrella.addEventListener('mouseenter', () => {
                this.highlightEstrellas(estrellas, index + 1);
            });

            estrella.addEventListener('mouseleave', () => {
                const calificacion = inputCalificacion.value || 0;
                this.highlightEstrellas(estrellas, calificacion);
            });

            estrella.addEventListener('click', () => {
                const calificacion = index + 1;
                inputCalificacion.value = calificacion;
                this.highlightEstrellas(estrellas, calificacion);
            });
        });
    }

    highlightEstrellas(estrellas, cantidad) {
        estrellas.forEach((estrella, index) => {
            if (index < cantidad) {
                estrella.classList.add('activa');
            } else {
                estrella.classList.remove('activa');
            }
        });
    }

    setupUtilidadBotones() {
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-util')) {
                e.preventDefault();
                const resenaId = e.target.dataset.resenaId;
                const tipo = e.target.dataset.tipo;
                this.marcarUtil(resenaId, tipo, e.target);
            }
        });
    }

    toggleFormulario() {
        const formulario = document.querySelector('.formulario-resena');
        if (!formulario) return;

        if (formulario.classList.contains('activo')) {
            this.ocultarFormulario();
        } else {
            this.mostrarFormulario();
        }
    }

    mostrarFormulario() {
        const formulario = document.querySelector('.formulario-resena');
        const btn = document.querySelector('.btn-nueva-resena');
        
        if (formulario && btn) {
            formulario.classList.add('activo');
            btn.textContent = 'Cancelar';
            formulario.scrollIntoView({ behavior: 'smooth' });
        }
    }

    ocultarFormulario() {
        const formulario = document.querySelector('.formulario-resena');
        const btn = document.querySelector('.btn-nueva-resena');
        
        if (formulario && btn) {
            formulario.classList.remove('activo');
            btn.textContent = 'Escribir reseña';
            this.resetFormulario();
        }
    }

    resetFormulario() {
        const form = document.getElementById('formulario-resena');
        if (form) {
            form.reset();
            document.getElementById('calificacion').value = '';
            const estrellas = document.querySelectorAll('.calificacion-selector .estrella');
            estrellas.forEach(e => e.classList.remove('activa'));
        }
    }

    async loadResenas() {
        if (this.loading) return;
        
        this.loading = true;
        this.showLoading();

        try {
            const response = await fetch(`${window.BASE_URL}/resenas/obtener/${this.productoId}?page=${this.currentPage}`);
            const data = await response.json();

            if (data.success) {
                this.renderEstadisticas(data.estadisticas);
                this.renderResenas(data.resenas);
            } else {
                this.showError('Error al cargar las reseñas');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showError('Error de conexión');
        } finally {
            this.loading = false;
            this.hideLoading();
        }
    }

    renderEstadisticas(stats) {
        if (!stats) return;

        const totalResenas = stats.total_resenas || 0;
        const promedio = parseFloat(stats.promedio_calificacion || 0);

        // Actualizar promedio
        const promedioNumero = document.querySelector('.promedio-numero');
        if (promedioNumero) {
            promedioNumero.textContent = totalResenas > 0 ? promedio.toFixed(1) : '0.0';
        }

        // Actualizar estrellas del promedio
        const promedioEstrellas = document.querySelector('.promedio-estrellas');
        if (promedioEstrellas) {
            promedioEstrellas.innerHTML = this.generateEstrellas(promedio);
        }

        // Actualizar total
        const totalElement = document.querySelector('.total-resenas');
        if (totalElement) {
            totalElement.textContent = `${totalResenas} reseña${totalResenas !== 1 ? 's' : ''}`;
        }

        // Actualizar barras de progreso
        this.updateBarrasProgreso(stats, totalResenas);
    }

    updateBarrasProgreso(stats, total) {
        for (let i = 1; i <= 5; i++) {
            const cantidad = parseInt(stats[`estrella_${i}`] || 0);
            const porcentaje = total > 0 ? (cantidad / total) * 100 : 0;

            const barra = document.querySelector(`.barra-calificacion[data-estrella="${i}"]`);
            if (barra) {
                const relleno = barra.querySelector('.barra-relleno');
                const cantidadElement = barra.querySelector('.barra-cantidad');
                
                if (relleno) relleno.style.width = `${porcentaje}%`;
                if (cantidadElement) cantidadElement.textContent = cantidad;
            }
        }
    }

    renderResenas(resenas) {
        const lista = document.querySelector('.resenas-lista');
        if (!lista) return;

        if (!resenas || resenas.length === 0) {
            lista.innerHTML = '<div class="no-resenas">No hay reseñas aún. ¡Sé el primero en escribir una!</div>';
            return;
        }

        const html = resenas.map(resena => this.generateResenaHTML(resena)).join('');
        
        if (this.currentPage === 1) {
            lista.innerHTML = html;
        } else {
            lista.insertAdjacentHTML('beforeend', html);
        }
    }

    generateResenaHTML(resena) {
        const fecha = new Date(resena.fecha_creacion).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        const estrellas = this.generateEstrellas(resena.calificacion);
        const verificado = resena.verificado ? '<span class="resena-verificada">Verificada</span>' : '';

        return `
            <div class="resena-item">
                <div class="resena-header">
                    <div class="resena-autor">
                        <div class="autor-nombre">${this.escapeHtml(resena.usuario_nombre)}</div>
                        <div class="resena-fecha">${fecha}</div>
                    </div>
                    <div class="resena-calificacion">
                        ${estrellas}
                        ${verificado}
                    </div>
                </div>
                
                <h4 class="resena-titulo">${this.escapeHtml(resena.titulo)}</h4>
                <p class="resena-comentario">${this.escapeHtml(resena.comentario)}</p>
                
                <div class="resena-acciones">
                    <button class="btn-util" data-resena-id="${resena.id}" data-tipo="positivo">
                        👍 Útil (${resena.util_positivo || 0})
                    </button>
                    <button class="btn-util" data-resena-id="${resena.id}" data-tipo="negativo">
                        👎 No útil (${resena.util_negativo || 0})
                    </button>
                </div>
            </div>
        `;
    }

    generateEstrellas(calificacion) {
        const estrellasCompletas = Math.floor(calificacion);
        const tieneMedia = calificacion % 1 >= 0.5;
        let html = '';

        for (let i = 1; i <= 5; i++) {
            if (i <= estrellasCompletas) {
                html += '<span class="estrella activa">★</span>';
            } else if (i === estrellasCompletas + 1 && tieneMedia) {
                html += '<span class="estrella activa">★</span>';
            } else {
                html += '<span class="estrella">★</span>';
            }
        }

        return `<div class="estrellas">${html}</div>`;
    }

    async enviarResena() {
        const form = document.getElementById('formulario-resena');
        const formData = new FormData(form);
        formData.append('producto_id', this.productoId);

        // Validación básica
        if (!this.validarFormulario(formData)) {
            return;
        }

        try {
            this.showEnviando();

            const response = await fetch(`${window.BASE_URL}/resenas/crear`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                this.ocultarFormulario();
                // Recargar reseñas para mostrar la nueva (si está aprobada)
                this.currentPage = 1;
                setTimeout(() => this.loadResenas(), 1000);
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            this.showError('Error al enviar la reseña');
        } finally {
            this.hideEnviando();
        }
    }

    validarFormulario(formData) {
        const nombre = formData.get('usuario_nombre');
        const email = formData.get('usuario_email');
        const calificacion = formData.get('calificacion');
        const titulo = formData.get('titulo');
        const comentario = formData.get('comentario');

        if (!nombre || !email || !calificacion || !titulo || !comentario) {
            this.showError('Todos los campos son obligatorios');
            return false;
        }

        if (calificacion < 1 || calificacion > 5) {
            this.showError('Selecciona una calificación de 1 a 5 estrellas');
            return false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            this.showError('Ingresa un email válido');
            return false;
        }

        return true;
    }

    async marcarUtil(resenaId, tipo, button) {
        if (button.classList.contains('activo')) return;

        try {
            const formData = new FormData();
            formData.append('resena_id', resenaId);
            formData.append('tipo', tipo);

            const response = await fetch(`${window.BASE_URL}/resenas/marcar_util`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                button.classList.add('activo');
                // Actualizar contador
                const match = button.textContent.match(/\((\d+)\)/);
                if (match) {
                    const newCount = parseInt(match[1]) + 1;
                    button.textContent = button.textContent.replace(/\(\d+\)/, `(${newCount})`);
                }
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Métodos de UI
    showLoading() {
        const lista = document.querySelector('.resenas-lista');
        if (lista) {
            lista.innerHTML = '<div class="loading">Cargando reseñas</div>';
        }
    }

    hideLoading() {
        // El loading se reemplaza con el contenido
    }

    showEnviando() {
        const btn = document.querySelector('.btn-enviar');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Enviando...';
        }
    }

    hideEnviando() {
        const btn = document.querySelector('.btn-enviar');
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'Enviar reseña';
        }
    }

    showSuccess(message) {
        this.showAlert(message, 'success');
    }

    showError(message) {
        this.showAlert(message, 'error');
    }

    showAlert(message, type) {
        // Remover alertas existentes
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());

        // Crear nueva alerta
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;

        // Insertar después del header de reseñas
        const header = document.querySelector('.resenas-header');
        if (header) {
            header.insertAdjacentElement('afterend', alert);
        }

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    const productoId = window.PRODUCTO_ID;
    if (productoId) {
        window.resenasManager = new ResenasManager(productoId);
    }
});