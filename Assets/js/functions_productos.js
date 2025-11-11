let tableProductos;

// Variables globales para manejar imágenes
var selectedImages = [];
var imageCounter = 0;

// Variable para evitar peticiones AJAX duplicadas
var isProcessingRequest = false;
var lastClickTime = 0;

// Inicialización directa
$(document).ready(function() {
    // Verificar que estamos en la página de productos
    if ($('#tableProductos').length === 0) {
        console.log('Not in products page, skipping initialization');
        return;
    }
    
    console.log('Products page detected, initializing...');
    console.log('Base URL:', base_url);
    
    // Inicializar DataTable directamente
    initBasicDataTable();
    
    // Inicializar funciones de productos
    initializeProductHandlers();
    
    // Cargar categorías para los modales
    loadProductCategories();
    
    // Agregar handler para limpiar cuando se cierra el modal
    $('#modalFormProductos').on('hidden.bs.modal', function() {
        console.log('🚪 Modal cerrado, limpiando datos...');
        clearImageGallery();
        $('#formProductos')[0].reset();
    });
    
    // Agregar handler para cuando se abre el modal (por si acaso)
    $('#modalFormProductos').on('show.bs.modal', function() {
        console.log('🚪 Modal abriéndose...');
        // Si no hay ID de producto, es un nuevo producto, limpiar todo
        if (!$('#idProducto').val()) {
            console.log('🚪 Modal para nuevo producto detectado, limpiando...');
            setTimeout(() => clearImageGallery(), 50);
        }
    });
});

function initProductsPage() {
    // Función mantenida para compatibilidad
    initBasicDataTable();
}

function initBasicDataTable() {
    console.log('Initializing DataTable...');
    console.log('Base URL:', typeof base_url !== 'undefined' ? base_url : 'NOT DEFINED');
    
    // Verificar que base_url esté definido
    if (typeof base_url === 'undefined') {
        console.error('base_url is not defined!');
        alert('Error: base_url no está definido. Recarga la página.');
        return;
    }
    
    // Destruir tabla existente si existe
    if ($.fn.DataTable.isDataTable('#tableProductos')) {
        $('#tableProductos').DataTable().destroy();
        $('#tableProductos').empty();
    }
    
    // Configuración simple de DataTable
    tableProductos = $('#tableProductos').DataTable({
        "processing": true,
        "serverSide": false,
        "language": {
            "processing": "Cargando...",
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "ajax": {
            "url": base_url + "/Productos/getProductos",
            "type": "GET",
            "dataSrc": function(json) {
                console.log('Response from server:', json);
                if (json && json.data) {
                    console.log('Products found:', json.data.length);
                    if (json.data.length > 0) {
                        console.log('First product:', json.data[0]);
                    }
                    return json.data;
                } else {
                    console.error('No data property in response:', json);
                    return [];
                }
            },
            "error": function(xhr, error, thrown) {
                console.error('AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error,
                    thrown: thrown
                });
            }
        },
        "columns": [
            {"data": "idProducto", "title": "ID"},
            {
                "data": "imagen", 
                "title": "Imagen",
                "orderable": false,
                "render": function(data, type, row) {
                    if (data && data !== '' && data !== null) {
                        // Si la imagen viene de BLOB (nueva implementación) o del sistema de archivos (legacy)
                        if (row.ruta === 'blob' || data.includes('/productos/obtenerImagen/')) {
                            // Nueva URL para imágenes BLOB
                            return '<img src="' + data + '" style="width: 50px; height: 50px; object-fit: cover;">';
                        } else {
                            // Mantener compatibilidad con imágenes del sistema de archivos legacy
                            return '<img src="' + base_url + '/Assets/images/uploads/' + data + '" style="width: 50px; height: 50px; object-fit: cover;">';
                        }
                    } else {
                        return '<span class="text-muted">Sin imagen</span>';
                    }
                }
            },
            {"data": "SKU", "title": "SKU"},
            {"data": "codigo_barras", "title": "Código Barras"},
            {"data": "Nombre_Producto", "title": "Nombre"},
            {"data": "Marca", "title": "Marca"},
            {
                "data": "precio_costo_formateado", 
                "title": "P. Costo"
            },
            {
                "data": "precio_formateado", 
                "title": "P. Venta"
            },
            {
                "data": "precio_oferta_formateado", 
                "title": "P. Oferta",
                "render": function(data, type, row) {
                    if (data && row.En_Oferta == 1) {
                        return '<span class="precio-oferta">' + data + '</span>';
                    }
                    return data || '<span class="text-muted">-</span>';
                }
            },
            {
                "data": "margen_porcentaje", 
                "title": "Margen %"
            },
            {"data": "Stock", "title": "Stock"},
            {
                "data": "En_Oferta",
                "title": "Oferta",
                "render": function(data, type, row) {
                    return (data == 1) ? '<span class="badge badge-warning">Sí</span>' : '<span class="badge badge-secondary">No</span>';
                }
            },
            {
                "data": "Destacado",
                "title": "Destacado",
                "render": function(data, type, row) {
                    return (data == 1) ? '<span class="badge badge-warning">Sí</span>' : '<span class="badge badge-secondary">No</span>';
                }
            },
            {
                "data": "Estado_Producto",
                "title": "Estado",
                "render": function(data, type, row) {
                    if (data == 'Activo') {
                        return '<span class="badge badge-success">Activo</span>';
                    } else if (data == 'Inactivo') {
                        return '<span class="badge badge-danger">Inactivo</span>';
                    } else if (data == 'Descontinuado') {
                        return '<span class="badge badge-warning">Descontinuado</span>';
                    } else {
                        // Fallback para cualquier estado no reconocido
                        return '<span class="badge badge-light">' + data + '</span>';
                    }
                }
            },
            {
                "data": "options",
                "title": "Acciones",
                "orderable": false,
                "width": "140px",
                "className": "text-center",
                "render": function(data, type, row) {
                    let id = row.idProducto || 0;
                    return '<div class="text-center">' +
                           '<button class="btn btn-sm btn-info" onclick="viewProduct(' + id + ')" title="Ver"><i class="fas fa-eye"></i></button> ' +
                           '<button class="btn btn-sm btn-primary" onclick="editProduct(' + id + ')" title="Editar"><i class="fas fa-edit"></i></button> ' +
                           '<button class="btn btn-sm btn-danger" onclick="deleteProduct(' + id + ')" title="Eliminar"><i class="fas fa-trash"></i></button>' +
                           '</div>';
                }
            }
        ],
        "pageLength": 10,
        "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
        "order": [[0, "desc"]],
        "responsive": false,
        "scrollX": true,
        "autoWidth": false,
        "columnDefs": [
            {
                "targets": [1, 14], // Imagen y Acciones no se pueden ordenar
                "orderable": false
            }
        ],
        "rowCallback": function(row, data) {
            // Agregar clase especial para productos destacados
            if (data.Destacado == 1) {
                $(row).addClass('producto-destacado');
            }
        }
    });
    
    console.log('DataTable initialized successfully');
}

function initializeProductHandlers() {
    // Inicializar handlers de formulario e imágenes
    initProductForm();
}

function initProductForm() {
    // Handler para el formulario
    $(document).off('submit', '#formProductos').on('submit', '#formProductos', function(e) {
        e.preventDefault();
        saveProduct();
    });
    
    // Handler para botón agregar imagen
    $(document).off('click', '.btnAddImage').on('click', '.btnAddImage', function(e) {
        e.preventDefault();
        $('#fileInput').click();
    });
    
    // Handler para selección de archivos múltiples
    $(document).off('change', '#fileInput').on('change', '#fileInput', function(e) {
        const files = e.target.files;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Validar tipo de archivo
            if (!file.type.startsWith('image/')) {
                swal("Tipo no válido", `El archivo ${file.name} no es una imagen válida.`, "warning");
                continue;
            }
            
            // Validar tamaño (aumentado a 5MB para coincidir con el servidor)
            if (file.size > 5 * 1024 * 1024) { // 5MB
                swal("Archivo muy grande", `El archivo ${file.name} es muy grande. Máximo 5MB permitido.`, "warning");
                continue;
            }
            
            console.log('Adding image to gallery:', file.name, 'Size:', file.size, 'Type:', file.type);
            addImageToGallery(file);
        }
        // Limpiar input para permitir seleccionar los mismos archivos otra vez
        $(this).val('');
    });
}

function saveProduct() {
    // Evitar envíos duplicados
    if (isProcessingRequest) {
        console.log('⚠️ Guardado ya en proceso, ignorando...');
        return;
    }
    
    isProcessingRequest = true;
    
    // Crear FormData para manejar archivos
    let formData = new FormData();
    
    // Agregar campos del formulario
    formData.append('idProducto', $('#idProducto').val() || '');
    formData.append('txtNombre', $('#txtNombre').val() || '');
    formData.append('txtSKU', $('#txtSKU').val() || '');
    formData.append('txtCodigoBarras', $('#txtCodigoBarras').val() || '');
    formData.append('txtPrecioCosto', $('#txtPrecioCosto').val() || '');
    formData.append('txtPrecio', $('#txtPrecio').val() || '');
    formData.append('txtPrecioOferta', $('#txtPrecioOferta').val() || '');
    formData.append('txtMargenGanancia', $('#txtMargenGanancia').val() || '');
    formData.append('txtStock', $('#txtStock').val() || '');
    formData.append('txtDescripcion', $('#txtDescripcion').val() || '');
    formData.append('txtMarca', $('#txtMarca').val() || '');
    formData.append('listCategoriaPrincipal', $('#listCategoriaPrincipal').val() || '');
    formData.append('listCategoria', $('#listCategoria').val() || '');
    formData.append('listStatus', $('#listStatus').val() || '1');
    formData.append('chkEnOferta', $('#chkEnOferta').is(':checked') ? '1' : '0');
    formData.append('chkDestacado', $('#chkDestacado').is(':checked') ? '1' : '0');
    
    // Agregar imágenes de la galería
    console.log('Total images to send:', selectedImages.length);
    selectedImages.forEach((imgData, index) => {
        console.log(`Adding image ${index}:`, imgData.file.name, 'Size:', imgData.file.size);
        formData.append(`imagen_${index}`, imgData.file);
    });
    formData.append('totalImages', selectedImages.length);
    
    // Validación básica
    if (!$('#txtNombre').val() || !$('#txtSKU').val() || !$('#txtPrecioCosto').val() || !$('#txtPrecio').val() || !$('#txtStock').val()) {
        isProcessingRequest = false; // Liberar flag
        swal("Campos requeridos", "Todos los campos marcados con * son obligatorios", "warning");
        return;
    }
    
    // Validar que se haya seleccionado categoría y subcategoría
    if (!$('#listCategoriaPrincipal').val() || !$('#listCategoria').val()) {
        isProcessingRequest = false; // Liberar flag
        swal("Categorías requeridas", "Debe seleccionar una categoría y subcategoría", "warning");
        return;
    }
    
    // Enviar via AJAX
    $.ajax({
        url: base_url + '/Productos/setProducto',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $('#btnActionForm').prop('disabled', true);
            $('#btnText').text('Guardando...');
        },
        success: function(response) {
            $('#btnActionForm').prop('disabled', false);
            $('#btnText').text($('#idProducto').val() ? 'Actualizar' : 'Guardar');
            isProcessingRequest = false; // Liberar flag
            
            console.log('Respuesta del servidor:', response);
            
            try {
                // Si la respuesta es string, intentar parsear
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                if (response && response.status) {
                    $('#modalFormProductos').modal('hide');
                    $('#formProductos')[0].reset();
                    swal("¡Éxito!", "Producto guardado exitosamente", "success");
                    
                    if (tableProductos) {
                        tableProductos.ajax.reload();
                    }
                } else {
                    swal("Error", response.msg || "Error desconocido", "error");
                }
            } catch (e) {
                console.error('Error parseando respuesta:', e);
                console.error('Respuesta original:', response);
                swal("Error de servidor", "Respuesta inválida del servidor", "error");
            }
        },
        error: function(xhr, status, error) {
            $('#btnActionForm').prop('disabled', false);
            $('#btnText').text($('#idProducto').val() ? 'Actualizar' : 'Guardar');
            isProcessingRequest = false; // Liberar flag
            
            console.error('Error AJAX:', {xhr, status, error});
            console.error('Respuesta:', xhr.responseText);
            
            swal("Error de conexión", "No se pudo conectar con el servidor: " + error, "error");
        },
        complete: function() {
            // Asegurarse de que siempre se libere el flag
            isProcessingRequest = false;
        }
    });
}

function openModal() {
    console.log('🆕 Abriendo modal para nuevo producto...');
    
    // PASO 1: Limpiar galería de imágenes PRIMERO
    clearImageGallery();
    
    // PASO 2: Resetear formulario completamente
    $('#formProductos')[0].reset();
    
    // PASO 3: Limpiar campos específicos
    $('#idProducto').val('');
    $('#imagenesEliminadas').val('');
    $('#titleModal').text('Nuevo Producto');
    $('#btnText').text('Guardar');
    
    // PASO 4: Resetear selectores de categorías
    $('#listCategoriaPrincipal').val('');
    $('#listCategoria').html('<option value="">Seleccionar Subcategoría</option>').prop('disabled', true);
    
    // PASO 5: Cargar categorías principales
    loadMainCategories();
    
    // PASO 6: Limpiar campos de precios y checkboxes explícitamente
    $('#txtNombre').val('');
    $('#txtSKU').val('');
    $('#txtCodigoBarras').val('');
    $('#txtDescripcion').val('');
    $('#txtMarca').val('');
    $('#txtPrecioCosto').val('');
    $('#txtPrecio').val('');
    $('#txtPrecioOferta').val('');
    $('#txtMargenGanancia').val('');
    $('#txtStock').val('');
    $('#chkEnOferta').prop('checked', false);
    $('#chkDestacado').prop('checked', false);
    $('#grupoPrecioOferta').hide();
    
    // PASO 7: Resetear estado a Activo por defecto
    $('#listStatus').val('1');
    
    // PASO 8: Segunda limpieza de imágenes por seguridad
    setTimeout(() => {
        clearImageGallery();
        console.log('🆕 Segunda limpieza ejecutada');
    }, 100);
    
    // PASO 9: Limpiar cualquier preview de imagen que pueda quedar
    $('.prevPhoto').empty();
    $('.prevImage').empty();
    
    // PASO 10: Limpiar input de archivo
    $('#fileInput').val('');
    
    console.log('🆕 Modal limpiado y listo para nuevo producto');
    
    // PASO 11: Verificación final antes de mostrar modal
    setTimeout(() => {
        const remainingImages = $('#containerImages').children().length;
        if (remainingImages > 0) {
            console.warn('⚠️ Aún hay imágenes en el contenedor, forzando limpieza...', remainingImages);
            $('#containerImages').empty();
            $('.prevImage').remove();
            $('.prevPhoto').remove();
            $('[id^="existing-image-"]').remove();
        }
        console.log('🆕 Verificación final: contenedor limpio =', $('#containerImages').children().length === 0);
    }, 150);
    
    // PASO 12: Mostrar modal
    $('#modalFormProductos').modal('show');
}

function viewProduct(id) {
    $.ajax({
        url: base_url + '/Productos/getProducto/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.status && response.data) {
                let producto = response.data;
                let info = "ID: " + producto.idProducto + "\n";
                info += "Código: " + producto.SKU + "\n";
                info += "Nombre: " + producto.Nombre_Producto + "\n";
                info += "Precio: $" + parseFloat(producto.Precio_Venta || 0).toFixed(2) + "\n";
                info += "Stock: " + producto.Stock_Actual + "\n";
                info += "Estado: " + producto.Estado_Producto;
                
                swal("Información del Producto", info, "info");
            } else {
                swal("Error", "Error al cargar información del producto", "error");
            }
        },
        error: function() {
            swal("Error de conexión", "No se pudo conectar con el servidor", "error");
        }
    });
}

function editProduct(id) {
    console.log('Editando producto ID:', id);
    
    // Prevenir clicks muy rápidos (debounce de 1 segundo)
    const now = Date.now();
    if (now - lastClickTime < 1000) {
        console.log('⚠️ Click muy rápido, ignorando...');
        return;
    }
    lastClickTime = now;
    
    // Evitar peticiones duplicadas
    if (isProcessingRequest) {
        console.log('⚠️ Petición ya en proceso, ignorando...');
        return;
    }
    
    isProcessingRequest = true;
    
    // Limpiar modal antes de cargar datos
    clearImageGallery();
    $('#titleModal').text('Editar Producto');
    $('#btnText').text('Actualizar');
    
    $.ajax({
        url: base_url + '/Productos/getProducto/' + id,
        type: 'GET',
        dataType: 'json',
        timeout: 10000, // 10 segundos de timeout
        success: function(response) {
            if (response && response.status && response.data) {
                let producto = response.data;
                
                // IMPORTANTE: Limpiar galería de imágenes primero antes de cargar las del producto
                clearImageGallery();
                
                $('#idProducto').val(producto.idProducto);
                $('#imagenesEliminadas').val('');
                $('#txtNombre').val(producto.Nombre_Producto);
                $('#txtSKU').val(producto.SKU);
                $('#txtCodigoBarras').val(producto.codigo_barras || '');
                $('#txtPrecioCosto').val(producto.Precio_Costo);
                $('#txtPrecio').val(producto.Precio_Venta);
                $('#txtPrecioOferta').val(producto.Precio_Oferta || '');
                $('#txtMargenGanancia').val(producto.Margen_Ganancia || '');
                $('#txtStock').val(producto.Stock_Actual);
                $('#txtDescripcion').val(producto.Descripcion_Producto || '');
                $('#txtMarca').val(producto.Marca || '');
                let estado = '2'; // Default Inactivo
                if (producto.Estado_Producto == 'Activo' || producto.Estado_Producto == 1) {
                    estado = '1';
                } else if (producto.Estado_Producto == 'Descontinuado' || producto.Estado_Producto == 3) {
                    estado = '3';
                }
                $('#listStatus').val(estado);
                
                // Configurar checkboxes
                $('#chkEnOferta').prop('checked', producto.En_Oferta == 1);
                $('#chkDestacado').prop('checked', (producto.Es_Destacado == 1) || (producto.Destacado == 1));
                
                // Mostrar/ocultar precio de oferta según checkbox
                if (producto.En_Oferta == 1) {
                    $('#grupoPrecioOferta').show();
                } else {
                    $('#grupoPrecioOferta').hide();
                }
                
                // Mostrar imágenes existentes DESPUÉS de limpiar
                if (producto.imagen_url) {
                    // Nueva estructura con imagen_url desde el controlador
                    showExistingImageFromUrl(producto.imagen_url, producto.idProducto);
                } else if (producto.imagen && producto.ruta) {
                    // Estructura legacy (por compatibilidad)
                    showExistingImage(producto.imagen, producto.ruta, producto.idProducto);
                }
                
                // Si hay más imágenes (en caso de que el producto tenga múltiples imágenes)
                if (producto.imagenes && producto.imagenes.length > 0) {
                    producto.imagenes.forEach(function(img) {
                        showExistingImage(img.nombre, img.ruta, producto.idProducto, img.id);
                    });
                }
                
                // Cargar categoría principal y subcategoría si existe
                console.log('📋 Cargando categorías para producto:', {
                    idCategoria: producto.idCategoria,
                    idSubCategoria: producto.idSubCategoria,
                    nombreCategoria: producto.Nombre_Categoria,
                    nombreSubcategoria: producto.Nombre_SubCategoria
                });
                
                // PASO 1: Cargar todas las categorías principales primero
                loadMainCategoriesForEdit(producto.idCategoria, producto.idSubCategoria);
                
                $('#titleModal').text('Actualizar Producto');
                $('#btnText').text('Actualizar');
                $('#modalFormProductos').modal('show');
                
                // Liberar flag de procesamiento
                isProcessingRequest = false;
            } else {
                isProcessingRequest = false;
                swal("Error", "Error al cargar información del producto", "error");
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            isProcessingRequest = false;
            console.error('Error en AJAX:', textStatus, errorThrown);
            
            if (textStatus === 'timeout') {
                swal("Error de tiempo", "La conexión tardó demasiado en responder", "error");
            } else if (textStatus === 'error') {
                swal("Error de conexión", "No se pudo conectar con el servidor. Verifique su conexión.", "error");
            } else {
                swal("Error", "Ocurrió un error inesperado: " + textStatus, "error");
            }
        },
        complete: function() {
            // Asegurarse de que siempre se libere el flag
            isProcessingRequest = false;
        }
    });
}

function deleteProduct(id) {
    swal({
        title: "¿Está seguro?",
        text: "Una vez eliminado, no podrá recuperar este producto!",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        if (isConfirm) {
            $.ajax({
                url: base_url + '/Productos/delProducto',
                type: 'POST',
                data: {idProducto: id},
                dataType: 'json',
                success: function(response) {
                    console.log('Respuesta del servidor:', response);
                    try {
                        // Si la respuesta es string, intentar parsear
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        
                        if (response && response.status) {
                            swal("¡Eliminado!", "Producto eliminado exitosamente", "success");
                            // Recargar la tabla después de un breve delay
                            setTimeout(function() {
                                if (tableProductos && $.fn.DataTable.isDataTable('#tableProductos')) {
                                    console.log('Recargando tabla después de eliminación...');
                                    tableProductos.ajax.reload(null, false); // false para mantener la paginación
                                } else {
                                    console.log('Recargando página completa...');
                                    window.location.reload();
                                }
                            }, 1000);
                        } else {
                            swal("Error", response.msg || "Error al eliminar", "error");
                        }
                    } catch (e) {
                        console.error('Error parseando respuesta:', e);
                        console.error('Respuesta original:', response);
                        swal("Error", "Respuesta inválida del servidor", "error");
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX en eliminación:', {xhr, status, error});
                    console.error('Respuesta:', xhr.responseText);
                    swal("Error de conexión", "No se pudo conectar con el servidor: " + error, "error");
                }
            });
        }
    });
}

// Variables ya declaradas globalmente al inicio del archivo

// Función para calcular el margen de ganancia automáticamente
function calcularMargen() {
    const precioCosto = parseFloat($('#txtPrecioCosto').val()) || 0;
    const precioVenta = parseFloat($('#txtPrecio').val()) || 0;
    
    if (precioCosto > 0 && precioVenta > 0) {
        const margen = ((precioVenta - precioCosto) / precioCosto) * 100;
        $('#txtMargenGanancia').val(margen.toFixed(2));
    } else {
        $('#txtMargenGanancia').val('');
    }
}

// Función para mostrar/ocultar el campo de precio de oferta
function togglePrecioOferta() {
    const checkbox = $('#chkEnOferta');
    const grupoPrecioOferta = $('#grupoPrecioOferta');
    
    if (checkbox.is(':checked')) {
        grupoPrecioOferta.slideDown();
    } else {
        grupoPrecioOferta.slideUp();
        $('#txtPrecioOferta').val('');
    }
}

// Variables globales para manejar imágenes

function addImageToGallery(file) {
    imageCounter++;
    const divId = 'img_' + imageCounter;
    
    // Crear el HTML para la imagen
    const imageHtml = `
        <div id="${divId}" class="containerImage">
            <div class="prevImage">
                <img id="preview_${imageCounter}" src="" alt="Preview" style="width: 150px; height: 150px; object-fit: cover;">
            </div>
            <input type="file" id="file_${imageCounter}" class="inputUploadfile" style="display: none;">
            <label for="file_${imageCounter}" class="btnUploadfile"><i class="fas fa-edit"></i></label>
            <button class="btnDeleteImage" type="button" onclick="removeImageFromGallery('${divId}')"><i class="fas fa-trash-alt"></i></button>
        </div>
    `;
    
    // Agregar al contenedor
    $('#containerImages').append(imageHtml);
    
    // Leer archivo y mostrar preview
    const reader = new FileReader();
    reader.onload = function(e) {
        $(`#preview_${imageCounter}`).attr('src', e.target.result);
    };
    reader.readAsDataURL(file);
    
    // Guardar referencia del archivo
    selectedImages.push({
        id: divId,
        file: file,
        counter: imageCounter
    });
}

function removeImageFromGallery(divId) {
    // Remover del DOM
    $(`#${divId}`).remove();
    
    // Remover del array
    selectedImages = selectedImages.filter(img => img.id !== divId);
}

function clearImageGallery() {
    console.log('🧹 Iniciando limpieza completa de galería de imágenes...');
    
    // Limpiar contenedor de imágenes completamente
    const containerImages = $('#containerImages');
    console.log('🧹 Elementos en containerImages antes de limpiar:', containerImages.children().length);
    containerImages.empty();
    
    // Verificar que se limpió correctamente
    console.log('🧹 Elementos en containerImages después de limpiar:', containerImages.children().length);
    
    // Limpiar cualquier preview de imagen que pueda existir
    $('.prevImage').empty();
    $('.prevPhoto').empty();
    $('[id^="existing-image-"]').remove(); // Remover específicamente elementos de imágenes existentes
    
    // Resetear variables globales
    selectedImages = [];
    imageCounter = 0;
    
    // Limpiar campo de imágenes eliminadas
    $('#imagenesEliminadas').val('');
    
    // Limpiar input de archivo
    $('#fileInput').val('');
    
    console.log('🧹 Galería limpiada completamente:', {
        selectedImages: selectedImages.length,
        imageCounter: imageCounter,
        containerEmpty: $('#containerImages').children().length === 0
    });
}

function loadProductCategories() {
    // Cargar categorías principales
    if ($('#listCategoriaPrincipal').length > 0) {
        loadMainCategories();
    }
    
    // Setup del event listener para categorías en cascada
    setupCategoryChangeHandler();
}

function loadMainCategoriesForEdit(selectedCategoriaId, selectedSubcategoriaId) {
    console.log('📋 Cargando categorías para edición con valores:', {selectedCategoriaId, selectedSubcategoriaId});
    
    $.ajax({
        url: base_url + '/Categorias/getCategoriasSimple',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('📋 Respuesta categorías:', response);
            let html = '<option value="">Seleccionar Categoría</option>';
            
            if (response && response.status && response.data && response.data.length > 0) {
                for (let i = 0; i < response.data.length; i++) {
                    let categoria = response.data[i];
                    let selected = (categoria.idCategoria == selectedCategoriaId) ? 'selected' : '';
                    html += '<option value="' + categoria.idCategoria + '" ' + selected + '>' + 
                           categoria.Nombre_Categoria + '</option>';
                }
            }
            
            $('#listCategoriaPrincipal').html(html);
            
            // PASO 2: Si hay categoría seleccionada, cargar subcategorías
            if (selectedCategoriaId) {
                console.log('📋 Cargando subcategorías para categoría:', selectedCategoriaId);
                loadSubcategoriesForEdit(selectedCategoriaId, selectedSubcategoriaId);
            } else {
                $('#listCategoria').html('<option value="">Seleccionar Subcategoría</option>').prop('disabled', true);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error cargando categorías:', error, xhr.responseText);
            $('#listCategoriaPrincipal').html('<option value="">Error cargando categorías</option>');
        }
    });
}

function loadSubcategoriesForEdit(categoriaId, selectedSubcategoriaId) {
    console.log('📋 Cargando subcategorías para categoría:', categoriaId, 'seleccionada:', selectedSubcategoriaId);
    
    $.ajax({
        url: base_url + '/Subcategorias/getSubcategoriasByCategoria/' + categoriaId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('📋 Respuesta subcategorías:', response);
            let html = '<option value="">Seleccionar Subcategoría</option>';
            
            if (response && response.data && response.data.length > 0) {
                for (let i = 0; i < response.data.length; i++) {
                    let subcategoria = response.data[i];
                    let selected = (subcategoria.idSubCategoria == selectedSubcategoriaId) ? 'selected' : '';
                    html += '<option value="' + subcategoria.idSubCategoria + '" ' + selected + '>' + 
                           subcategoria.Nombre_SubCategoria + '</option>';
                }
            } else {
                html = '<option value="">No hay subcategorías disponibles</option>';
            }
            
            $('#listCategoria').html(html).prop('disabled', false);
            console.log('📋 Subcategorías cargadas. Seleccionada:', selectedSubcategoriaId);
        },
        error: function() {
            console.error('❌ Error cargando subcategorías para categoría:', categoriaId);
            $('#listCategoria').html('<option value="">Error cargando subcategorías</option>');
        }
    });
}

function loadMainCategories() {
    console.log('Loading main categories...');
    $.ajax({
        url: base_url + '/Categorias/getCategoriasSimple',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Categories response:', response);
            let html = '<option value="">Seleccionar Categoría</option>';
            
            if (response && response.status && response.data && response.data.length > 0) {
                for (let i = 0; i < response.data.length; i++) {
                    let categoria = response.data[i];
                    html += '<option value="' + categoria.idCategoria + '">' + categoria.Nombre_Categoria + '</option>';
                }
            }
            
            $('#listCategoriaPrincipal').html(html);
            console.log('Categories loaded:', $('#listCategoriaPrincipal option').length - 1);
        },
        error: function(xhr, status, error) {
            console.error('Error loading categories:', error, xhr.responseText);
            $('#listCategoriaPrincipal').html('<option value="">Error cargando categorías</option>');
        }
    });
}

function setupCategoryChangeHandler() {
    $('#listCategoriaPrincipal').off('change').on('change', function() {
        let categoriaId = $(this).val();
        
        if (categoriaId) {
            loadSubcategoriesByCategory(categoriaId);
            $('#listCategoria').prop('disabled', false);
        } else {
            $('#listCategoria').html('<option value="">Seleccionar Subcategoría</option>').prop('disabled', true);
        }
    });
}

function loadSubcategoriesByCategory(categoriaId) {
    $.ajax({
        url: base_url + '/Subcategorias/getSubcategoriasByCategoria/' + categoriaId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            let html = '<option value="">Seleccionar Subcategoría</option>';
            
            if (response && response.data && response.data.length > 0) {
                for (let i = 0; i < response.data.length; i++) {
                    html += '<option value="' + response.data[i].idSubCategoria + '">' + 
                           response.data[i].Nombre_SubCategoria + '</option>';
                }
            } else {
                html = '<option value="">No hay subcategorías disponibles</option>';
            }
            
            $('#listCategoria').html(html);
        },
        error: function() {
            $('#listCategoria').html('<option value="">Error cargando subcategorías</option>');
        }
    });
}

// Función para mostrar imagen existente en el modal de edición
function showExistingImageFromUrl(imageUrl, productId) {
    console.log('📷 Mostrando imagen desde URL:', {imageUrl, productId});
    
    const uniqueId = 'main_' + Date.now();
    
    // Verificar que el contenedor esté limpio antes de agregar
    console.log('📷 Estado del contenedor antes de agregar imagen:', $('#containerImages').children().length, 'elementos');
    
    const imageHtml = `
        <div class="col-md-4 mb-3" id="existing-image-${uniqueId}">
            <div class="prevImage">
                <img src="${imageUrl}" alt="Imagen del producto" style="width: 100%; height: 150px; object-fit: cover;">
                <button type="button" class="btnDeleteImage" onclick="removeExistingImage('${uniqueId}', 'blob', ${productId})">
                    <i class="fas fa-times"></i>
                </button>
                <div class="mt-2 text-center">
                    <small class="text-muted">Imagen actual</small>
                </div>
            </div>
        </div>
    `;
    
    $('#containerImages').append(imageHtml);
    console.log('📷 Imagen agregada al contenedor. Total elementos:', $('#containerImages').children().length);
}

function showExistingImage(imageName, imagePath, productId, imageId = null) {
    console.log('📷 Mostrando imagen existente:', {imageName, imagePath, productId, imageId});
    
    let imageUrl;
    // Determinar la URL de la imagen según el tipo
    if (imagePath === 'blob' || imageName.includes('/productos/obtenerImagen/')) {
        // Nueva implementación con BLOB
        imageUrl = base_url + '/productos/obtenerImagen/' + productId;
    } else {
        // Sistema legacy con archivos
        imageUrl = base_url + '/Assets/images/uploads/' + imageName;
    }
    
    const uniqueId = imageId || 'main_' + Date.now();
    
    // Verificar que el contenedor esté limpio antes de agregar
    console.log('📷 Estado del contenedor antes de agregar imagen:', $('#containerImages').children().length, 'elementos');
    
    const imageHtml = `
        <div class="col-md-4 mb-3" id="existing-image-${uniqueId}">
            <div class="prevImage">
                <img src="${imageUrl}" alt="Imagen del producto" style="width: 100%; height: 150px; object-fit: cover;">
                <button type="button" class="btnDeleteImage" onclick="removeExistingImage('${uniqueId}', '${imageName}', ${productId})">
                    <i class="fas fa-times"></i>
                </button>
                <div class="mt-2 text-center">
                    <small class="text-muted">Imagen actual</small>
                </div>
            </div>
        </div>
    `;
    
    $('#containerImages').append(imageHtml);
    console.log('📷 Imagen agregada. Estado del contenedor:', $('#containerImages').children().length, 'elementos');
}

// Función para eliminar imagen existente
function removeExistingImage(imageId, imageName, productId) {
    swal({
        title: "¿Eliminar imagen?",
        text: "Esta acción no se puede deshacer",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false
    }, function() {
        // Llamar al servidor para eliminar la imagen
        $.ajax({
            url: base_url + '/Productos/eliminarImagen',
            type: 'POST',
            dataType: 'json',
            data: {
                idProducto: productId,
                imagen: imageName
            },
            success: function(response) {
                if (response.status) {
                    $('#existing-image-' + imageId).fadeOut(300, function() {
                        $(this).remove();
                    });
                    // Marcar que se eliminó una imagen
                    $('#imagenesEliminadas').val('true');
                    swal("Eliminada", "La imagen ha sido eliminada correctamente", "success");
                } else {
                    swal("Error", response.msg || "No se pudo eliminar la imagen", "error");
                }
            },
            error: function() {
                swal("Error", "Error de conexión al servidor", "error");
            }
        });
    });
}

// Funciones de compatibilidad para nombres antiguos
function fntViewInfo(id) { viewProduct(id); }
function fntEditInfo(id) { editProduct(id); }
function fntDelInfo(id) { deleteProduct(id); }