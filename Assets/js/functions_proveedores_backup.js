var base_url = "http://localhost/AltoVoltaje";
let tableProveedores;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    tableProveedores = $('#tableProveedores').DataTable({
        "aProcessing": true,
        "aServerSide": false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": base_url + "/Proveedores/getProveedores",
            "dataSrc": "data"
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
        "scrollX": true,
        "scrollY": false,
        "responsive": false,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "asc"]],
        "dom": 'rtip' // Remove default search and length controls
    });

    // Load stats and province filter
    loadStats();
    loadProvinciaFilter();
    
    // Initialize form handler
    initializeForm();
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
            if(objData.length > 0) {
                updateStats(objData);
            }
        }
    }
}

function updateStats(data) {
    // Total proveedores
    document.getElementById('totalProveedores').textContent = data.length;
    
    // Count unique cities
    let ciudades = [...new Set(data.map(item => item.Ciudad_Proveedor).filter(ciudad => ciudad && ciudad.trim() !== ''))];
    document.getElementById('totalCiudades').textContent = ciudades.length;
    
    // Count unique provinces
    let provincias = [...new Set(data.map(item => item.Provincia_Proveedor).filter(provincia => provincia && provincia.trim() !== ''))];
    document.getElementById('totalProvincias').textContent = provincias.length;
    
    // Count emails
    let emails = data.filter(item => item.Email_Proveedor && item.Email_Proveedor.trim() !== '').length;
    document.getElementById('totalEmails').textContent = emails;
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
            
            // Clear existing options (except the first one)
            selectProvincia.innerHTML = '<option value="">Todas las provincias</option>';
            
            // Get unique provinces
            let provincias = [...new Set(objData.map(item => item.Provincia_Proveedor).filter(provincia => provincia && provincia.trim() !== ''))];
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
            let strData = "idProveedor=" + idproveedor;
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            
            request.onreadystatechange = function() {
                if(request.readyState == 4 && request.status == 200) {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status) {
                        swal("Eliminar!", objData.msg, "success");
                        tableProveedores.ajax.reload(function() {
                            loadStats();
                            loadProvinciaFilter();
                        });
                    } else {
                        swal("Atención!", objData.msg, "error");
                    }
                }
            }
        }
    });
}

function initializeForm() {
    const formProveedor = document.querySelector("#formProveedor");
    if(formProveedor) {
        formProveedor.onsubmit = function(e) {
            e.preventDefault();
            
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
            
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Proveedores/setProveedor';
            let formData = new FormData(formProveedor);
            request.open("POST", ajaxUrl, true);
            request.send(formData);
            
            request.onreadystatechange = function() {
                if(request.readyState == 4 && request.status == 200) {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status) {
                        $('#modalFormProveedor').modal("hide");
                        formProveedor.reset();
                        swal("Proveedores", objData.msg, "success");
                        tableProveedores.ajax.reload(function() {
                            loadStats();
                            loadProvinciaFilter();
                        });
                    } else {
                        swal("Error", objData.msg, "error");
                    }
                }
            }
        }
    }
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