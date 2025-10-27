var base_url = "http://localhost/AltoVoltaje";
let tableProductos;

// Variables globales para manejar imágenes
var selectedImages = [];
var imageCounter = 0;

// Inicialización solo para página de productos
$(document).ready(function() {
    // Verificar que estamos en la página de productos
    if ($('#tableProductos').length === 0) {
        console.log('Not in products page, skipping initialization');
        return;
    }
    
    console.log('Products page detected, initializing...');
    
    // Esperar a que todo esté cargado
    setTimeout(function() {
        initProductsPage();
    }, 2000);
});

function initProductsPage() {
    // Verificar dependencias
    if (!window.jQuery || !$.fn.DataTable) {
        console.error('Required libraries not loaded');
        return;
    }
    
    // Inicializar DataTable muy simple
    initBasicDataTable();
    
    // Inicializar handlers del formulario
    initProductForm();
    
    // Cargar categorías
    loadProductCategories();
}

function initBasicDataTable() {
    // Destruir tabla existente si existe
    if ($.fn.DataTable.isDataTable('#tableProductos')) {
        $('#tableProductos').DataTable().destroy();
    }
    
    $('#tableProductos').empty();
    
    console.log('Initializing DataTable...');
    
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
            {"data": "idProducto", "title": "ID", "width": "60px", "className": "text-center"},
            {
                "data": "imagen", 
                "title": "Imagen",
                "orderable": false,
                "width": "90px",
                "className": "text-center",
                "render": function(data, type, row) {
                    if (data && data !== '' && data !== null) {
                        return '<img src="' + base_url + '/Assets/images/uploads/' + data + '" alt="Producto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">';
                    } else {
                        return '<div style="width: 50px; height: 50px; background-color: #f8f9fa; border: 1px dashed #dee2e6; display: flex; align-items: center; justify-content: center; border-radius: 4px;"><i class="fas fa-image text-muted"></i></div>';
                    }
                }
            },
            {"data": "SKU", "title": "SKU", "width": "120px", "className": "text-center"},
            {
                "data": "codigo_barras", 
                "title": "Código Barras",
                "width": "140px",
                "className": "text-center",
                "render": function(data, type, row) {
                    return (data && data !== '' && data !== null) ? data : '<span class="text-muted">-</span>';
                }
            },
            {"data": "Nombre_Producto", "title": "Nombre", "width": "200px", "className": "text-left"},
            {
                "data": "Marca", 
                "title": "Marca", 
                "width": "120px", 
                "className": "text-center",
                "render": function(data, type, row) {
                    return (data && data !== '' && data !== null) ? data : '<span class="text-muted">-</span>';
                }
            },
            {
                "data": "Precio_Costo", 
                "title": "P. Costo",
                "width": "100px",
                "className": "text-right",
                "render": function(data, type, row) { 
                    return '$' + parseFloat(data || 0).toLocaleString('es-CO', {minimumFractionDigits: 2}); 
                }
            },
            {
                "data": "Precio_Venta", 
                "title": "P. Venta",
                "width": "100px",
                "className": "text-right",
                "render": function(data, type, row) { 
                    return '$' + parseFloat(data || 0).toLocaleString('es-CO', {minimumFractionDigits: 2}); 
                }
            },
            {
                "data": "Precio_Oferta", 
                "title": "P. Oferta",
                "width": "100px",
                "className": "text-right",
                "render": function(data, type, row) {
                    if (row.En_Oferta == 1 && data && parseFloat(data) > 0) {
                        return '<span class="text-warning font-weight-bold">$' + parseFloat(data).toLocaleString('es-CO', {minimumFractionDigits: 2}) + '</span>';
                    } else {
                        return '<span class="text-muted">-</span>';
                    }
                }
            },
            {
                "data": "Margen_Ganancia", 
                "title": "Margen %",
                "width": "90px",
                "className": "text-center",
                "render": function(data, type, row) { 
                    return parseFloat(data || 0).toFixed(1) + '%'; 
                }
            },
            {"data": "Stock_Actual", "title": "Stock", "width": "80px", "className": "text-center"},
            {
                "data": "En_Oferta",
                "title": "Oferta",
                "width": "80px",
                "className": "text-center",
                "render": function(data, type, row) {
                    return (data == 1 || data == '1') ? '<span class="badge badge-warning">Sí</span>' : '<span class="badge badge-light">No</span>';
                }
            },
            {
                "data": "Es_Destacado",
                "title": "Destacado",
                "width": "90px",
                "className": "text-center",
                "render": function(data, type, row) {
                    return (data == 1 || data == '1') ? '<span class="badge badge-primary">Sí</span>' : '<span class="badge badge-light">No</span>';
                }
            },
            {
                "data": "Estado_Producto",
                "title": "Estado",
                "width": "90px",
                "className": "text-center",
                "render": function(data, type, row) {
                    if (data == 'Activo' || data == 1) {
                        return '<span class="badge badge-success">Activo</span>';
                    } else if (data == 'Descontinuado' || data == 3) {
                        return '<span class="badge badge-warning">Descontinuado</span>';
                    } else {
                        return '<span class="badge badge-danger">Inactivo</span>';
                    }
                }
            },
            {
                "data": null,
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
        ]
    });
    
    console.log('DataTable initialized successfully');
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
        swal("Campos requeridos", "Todos los campos marcados con * son obligatorios", "warning");
        return;
    }
    
    // Validar que se haya seleccionado categoría y subcategoría
    if (!$('#listCategoriaPrincipal').val() || !$('#listCategoria').val()) {
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
            
            console.error('Error AJAX:', {xhr, status, error});
            console.error('Respuesta:', xhr.responseText);
            
            swal("Error de conexión", "No se pudo conectar con el servidor: " + error, "error");
        }
    });
}

function openModal() {
    $('#modalFormProductos').modal('show');
    $('#formProductos')[0].reset();
    $('#idProducto').val('');
    $('#imagenesEliminadas').val('');
    $('#titleModal').text('Nuevo Producto');
    $('#btnText').text('Guardar');
    
    // Resetear selectores de categorías
    $('#listCategoriaPrincipal').val('');
    $('#listCategoria').html('<option value="">Seleccionar Subcategoría</option>').prop('disabled', true);
    
    // Limpiar campos de precios y checkboxes
    $('#txtSKU').val('');
    $('#txtCodigoBarras').val('');
    $('#txtPrecioCosto').val('');
    $('#txtPrecio').val('');
    $('#txtPrecioOferta').val('');
    $('#txtMargenGanancia').val('');
    $('#chkEnOferta').prop('checked', false);
    $('#chkDestacado').prop('checked', false);
    $('#grupoPrecioOferta').hide();
    
    // Limpiar galería de imágenes
    clearImageGallery();
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
    $.ajax({
        url: base_url + '/Productos/getProducto/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.status && response.data) {
                let producto = response.data;
                
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
                $('#chkDestacado').prop('checked', producto.Es_Destacado == 1);
                
                // Mostrar/ocultar precio de oferta según checkbox
                if (producto.En_Oferta == 1) {
                    $('#grupoPrecioOferta').show();
                } else {
                    $('#grupoPrecioOferta').hide();
                }
                
                // Limpiar galería de imágenes
                clearImageGallery();
                
                // Mostrar imágenes existentes
                if (producto.imagen && producto.ruta) {
                    showExistingImage(producto.imagen, producto.ruta, producto.idProducto);
                }
                
                // Si hay más imágenes (en caso de que el producto tenga múltiples imágenes)
                if (producto.imagenes && producto.imagenes.length > 0) {
                    producto.imagenes.forEach(function(img) {
                        showExistingImage(img.nombre, img.ruta, producto.idProducto, img.id);
                    });
                }
                
                // Cargar categoría principal y subcategoría si existe
                if (producto.idCategoria) {
                    $('#listCategoriaPrincipal').val(producto.idCategoria);
                    // Cargar subcategorías de esta categoría
                    loadSubcategoriesByCategory(producto.idCategoria);
                    // Esperar un momento y luego seleccionar la subcategoría
                    setTimeout(function() {
                        $('#listCategoria').val(producto.idSubCategoria || '').prop('disabled', false);
                    }, 500);
                } else {
                    $('#listCategoriaPrincipal').val('');
                    $('#listCategoria').html('<option value="">Seleccionar Subcategoría</option>').prop('disabled', true);
                }
                
                $('#titleModal').text('Actualizar Producto');
                $('#btnText').text('Actualizar');
                $('#modalFormProductos').modal('show');
            } else {
                swal("Error", "Error al cargar información del producto", "error");
            }
        },
        error: function() {
            swal("Error de conexión", "No se pudo conectar con el servidor", "error");
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
    $('#containerImages').empty();
    selectedImages = [];
    imageCounter = 0;
}

function loadProductCategories() {
    // Cargar categorías principales
    if ($('#listCategoriaPrincipal').length > 0) {
        loadMainCategories();
    }
    
    // Setup del event listener para categorías en cascada
    setupCategoryChangeHandler();
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

// Función para limpiar la galería de imágenes
function clearImageGallery() {
    $('#containerImages').empty();
    totalImages = 0;
}

// Función para mostrar imagen existente en el modal de edición
function showExistingImage(imageName, imagePath, productId, imageId = null) {
    const imageUrl = base_url + '/Assets/images/uploads/' + imageName;
    const uniqueId = imageId || 'main_' + Date.now();
    
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

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, base_url:', base_url);
    console.log('Table element exists:', document.getElementById('tableProductos') !== null);
    
    // Verificar si jQuery y DataTables están disponibles
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded!');
        return;
    }
    
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('DataTables is not loaded!');
        return;
    }
    
    console.log('All dependencies loaded, initializing...');
    initProductos();
});