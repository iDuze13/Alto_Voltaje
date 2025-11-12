console.log("functions_proveedores.js cargado");
let tableProveedores;

document.addEventListener('DOMContentLoaded', function() {
    // Verificar que base_url esté definido (debería venir del footer)
    if (typeof base_url === 'undefined') {
        console.error('base_url no está definido en proveedores');
        return;
    }
    
    console.log('Initializing proveedores table...');
    console.log('Base URL:', base_url);
    console.log('SweetAlert available:', typeof swal !== 'undefined');
    console.log('DataTables available:', typeof $.fn.DataTable !== 'undefined');
    
    // Initialize DataTable
    tableProveedores = $('#tableProveedores').DataTable({
        "processing": true,
        "serverSide": false,
        "autoWidth": false,
        "language": {
            "decimal": "",
            "emptyTable": "No hay información disponible",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
            "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "ajax": {
            "url": base_url + "/Proveedores/getProveedores",
            "dataSrc": "data",
            "error": function(xhr, error, code) {
                console.error('DataTables AJAX error:', error, code);
                console.error('Response:', xhr.responseText);
            }
        },
        "columns": [
            {"data": "Nombre_Proveedor"},
            {"data": "CUIT_Proveedor"},
            {"data": "Telefono_Proveedor"},
            {"data": "Email_Proveedor"},
            {"data": "Direccion_Proveedor"},
            {"data": "Ciudad_Proveedor"},
            {"data": "Provincia_Proveedor"},
            { 
                "data": null,
                "render": function(data, type, row) {
                    return `
                        <div class="action-buttons">
                            <button class="btn-action btn-edit" onclick="btnEditProveedor(${row.id_Proveedor})" title="Editar Proveedor">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn-action btn-delete" onclick="btnDelProveedor(${row.id_Proveedor})" title="Eliminar Proveedor">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        "scrollX": false,
        "scrollY": false,
        "responsive": false,
        "destroy": true,
        "displayLength": 10,
        "order": [[0, "asc"]],
        "dom": 'rtip', // Remove default search and length controls
        "columnDefs": [
            { "width": "15%", "targets": 0 }, // Nombre
            { "width": "12%", "targets": 1 }, // CUIT
            { "width": "12%", "targets": 2 }, // Teléfono
            { "width": "18%", "targets": 3 }, // Email
            { "width": "18%", "targets": 4 }, // Dirección
            { "width": "10%", "targets": 5 }, // Ciudad
            { "width": "10%", "targets": 6 }, // Provincia
            { "width": "10%", "targets": 7, "className": "text-center" } // Acciones
        ]
    });

    // Load stats and province filter
    loadStats();
    loadProvinciaFilter();
    
    // Initialize form handler
    initializeFormHandler();
});

// Custom search function
function customSearch() {
    const searchValue = document.getElementById('customSearchInput').value;
    tableProveedores.search(searchValue).draw();
}

function loadStats() {
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Proveedores/getProveedores';
    request.open("GET", ajaxUrl, true);
    request.send();
    
    request.onreadystatechange = function() {
        if(request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if(objData.data && objData.data.length > 0) {
                updateStats(objData.data);
            }
        }
    }
}

function updateStats(data) {
    // Total proveedores
    if(document.getElementById('totalProveedores')) {
        document.getElementById('totalProveedores').textContent = data.length;
    }
    
    // Count unique cities
    let ciudades = [...new Set(data.map(item => item.Ciudad_Proveedor).filter(ciudad => ciudad && ciudad.trim() !== ''))];
    if(document.getElementById('totalCiudades')) {
        document.getElementById('totalCiudades').textContent = ciudades.length;
    }
    
    // Count unique provinces
    let provincias = [...new Set(data.map(item => item.Provincia_Proveedor).filter(provincia => provincia && provincia.trim() !== ''))];
    if(document.getElementById('totalProvincias')) {
        document.getElementById('totalProvincias').textContent = provincias.length;
    }
    
    // Count emails
    let emails = data.filter(item => item.Email_Proveedor && item.Email_Proveedor.trim() !== '').length;
    if(document.getElementById('totalEmails')) {
        document.getElementById('totalEmails').textContent = emails;
    }
}

function loadProvinciaFilter() {
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Proveedores/getProveedores';
    request.open("GET", ajaxUrl, true);
    request.send();
    
    request.onreadystatechange = function() {
        if(request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            let selectProvincia = document.getElementById('filterProvincia');
            
            if(selectProvincia && objData.data) {
                // Clear existing options (except the first one)
                selectProvincia.innerHTML = '<option value="">Todas las provincias</option>';
                
                // Get unique provinces
                let provincias = [...new Set(objData.data.map(item => item.Provincia_Proveedor).filter(provincia => provincia && provincia.trim() !== ''))];
                provincias.sort();
                
                // Add options
                provincias.forEach(provincia => {
                    let option = document.createElement('option');
                    option.value = provincia;
                    option.textContent = provincia;
                    selectProvincia.appendChild(option);
                });
            }
        }
    }
}

function filterByProvincia() {
    let provincia = document.getElementById('filterProvincia').value;
    tableProveedores.column(6).search(provincia).draw();
}

function openModal() {
    document.querySelector('#formProveedor').reset();
    document.querySelector('#idProveedor').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('.modal-title').innerHTML = "Nuevo Proveedor";
    document.querySelector('#titleModal').innerHTML = "Nuevo Proveedor";
    $('#modalFormProveedor').modal('show');
}

function btnEditProveedor(idproveedor) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Proveedor";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";
    
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Proveedores/getProveedor/' + idproveedor;
    request.open("GET", ajaxUrl, true);
    request.send();
    
    request.onreadystatechange = function() {
        if(request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if(objData.status) {
                document.querySelector("#idProveedor").value = objData.data.id_Proveedor;
                document.querySelector("#txtNombre").value = objData.data.Nombre_Proveedor;
                document.querySelector("#txtCUIT").value = objData.data.CUIT_Proveedor;
                document.querySelector("#txtTelefono").value = objData.data.Telefono_Proveedor;
                document.querySelector("#txtEmail").value = objData.data.Email_Proveedor;
                document.querySelector("#txtDireccion").value = objData.data.Direccion_Proveedor;
                document.querySelector("#txtCiudad").value = objData.data.Ciudad_Proveedor;
                document.querySelector("#txtProvincia").value = objData.data.Provincia_Proveedor;
                
                $('#modalFormProveedor').modal('show');
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    }
}

function btnDelProveedor(idproveedor) {
    // Validate ID
    if(!idproveedor || idproveedor <= 0) {
        swal("Error", "ID de proveedor inválido", "error");
        return;
    }
    
    swal({
        title: "Eliminar Proveedor",
        text: "¿Realmente quiere eliminar el Proveedor?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        if (isConfirm) {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Proveedores/delProveedor';
            let strData = "idProveedor=" + encodeURIComponent(idproveedor);
            
            console.log('Eliminando proveedor ID:', idproveedor);
            console.log('URL:', ajaxUrl);
            console.log('Data:', strData);
            
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            
            request.onreadystatechange = function() {
                if(request.readyState == 4) {
                    if(request.status == 200) {
                        console.log('Response:', request.responseText);
                        try {
                            let objData = JSON.parse(request.responseText);
                            if(objData.status) {
                                swal("¡Eliminado!", objData.msg, "success");
                                if(typeof tableProveedores !== 'undefined') {
                                    tableProveedores.ajax.reload(function() {
                                        loadStats();
                                        loadProvinciaFilter();
                                    });
                                }
                            } else {
                                swal("Error", objData.msg, "error");
                            }
                        } catch(e) {
                            console.error('Error parsing JSON:', e);
                            swal("Error", "Error en la respuesta del servidor", "error");
                        }
                    } else {
                        console.error('HTTP Error:', request.status);
                        swal("Error", "Error de conexión: " + request.status, "error");
                    }
                }
            }
        }
    });
}

function initializeFormHandler() {
    // Wait for modal to be available
    const checkForm = setInterval(function() {
        const formProveedor = document.querySelector("#formProveedor");
        if(formProveedor) {
            clearInterval(checkForm);
            
            formProveedor.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submitted!');
                
                let intIdProveedor = document.querySelector('#idProveedor').value;
                let strNombre = document.querySelector('#txtNombre').value;
                let strCUIT = document.querySelector('#txtCUIT').value;
                let strTelefono = document.querySelector('#txtTelefono').value;
                let strEmail = document.querySelector('#txtEmail').value;
                let strDireccion = document.querySelector('#txtDireccion').value;
                let strCiudad = document.querySelector('#txtCiudad').value;
                let strProvincia = document.querySelector('#txtProvincia').value;
                
                if(strNombre == '' || strCUIT == '' || strTelefono == '' || strEmail == '' || strDireccion == '' || strCiudad == '' || strProvincia == '') {
                    swal("Atención", "Todos los campos son obligatorios.", "error");
                    return false;
                }
                
                // Email validation
                let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if(!emailRegex.test(strEmail)) {
                    swal("Atención", "Por favor ingrese un email válido.", "error");
                    return false;
                }
                
                // CUIT validation
                let cuitRegex = /^\d{2}-\d{8}-\d{1}$/;
                if(!cuitRegex.test(strCUIT)) {
                    swal("Atención", "El formato del CUIT debe ser: XX-XXXXXXXX-X", "error");
                    return false;
                }
                
                let request = new XMLHttpRequest();
                let ajaxUrl = base_url + '/Proveedores/setProveedor';
                let formData = new FormData(formProveedor);
                
                console.log('Sending to:', ajaxUrl);
                
                request.open("POST", ajaxUrl, true);
                request.send(formData);
                
                request.onreadystatechange = function() {
                    if(request.readyState == 4 && request.status == 200) {
                        console.log('Response:', request.responseText);
                        try {
                            let objData = JSON.parse(request.responseText);
                            if(objData.status) {
                                $('#modalFormProveedor').modal("hide");
                                formProveedor.reset();
                                swal("Proveedores", objData.msg, "success");
                                if(typeof tableProveedores !== 'undefined') {
                                    tableProveedores.ajax.reload(function() {
                                        loadStats();
                                        loadProvinciaFilter();
                                    });
                                }
                            } else {
                                swal("Error", objData.msg, "error");
                            }
                        } catch(e) {
                            console.error('Error parsing JSON:', e);
                            swal("Error", "Error en la respuesta del servidor", "error");
                        }
                    } else if(request.readyState == 4) {
                        console.error('HTTP Error:', request.status);
                        swal("Error", "Error de conexión: " + request.status, "error");
                    }
                }
            });
        }
    }, 100);
}

function exportProveedores() {
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Proveedores/getProveedores';
    request.open("GET", ajaxUrl, true);
    request.send();
    
    request.onreadystatechange = function() {
        if(request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if(objData.data && objData.data.length > 0) {
                // Convert to CSV
                let csv = 'Nombre,CUIT,Telefono,Email,Direccion,Ciudad,Provincia\n';
                objData.data.forEach(function(row) {
                    csv += `"${row.Nombre_Proveedor}","${row.CUIT_Proveedor}","${row.Telefono_Proveedor}","${row.Email_Proveedor}","${row.Direccion_Proveedor}","${row.Ciudad_Proveedor}","${row.Provincia_Proveedor}"\n`;
                });
                
                // Download CSV file
                let blob = new Blob([csv], { type: 'text/csv' });
                let url = window.URL.createObjectURL(blob);
                let a = document.createElement('a');
                a.href = url;
                a.download = 'proveedores.csv';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
                
                swal("Export Complete", "Proveedores data has been exported successfully.", "success");
            } else {
                swal("Error", "No hay datos para exportar.", "error");
            }
        } else if(request.readyState == 4) {
            swal("Error", "Error al obtener los datos para exportar.", "error");
        }
    }
}