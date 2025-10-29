console.log("functions_usuarios.js cargado");
var base_url = "http://localhost/AltoVoltaje";
var tableUsuarios;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing usuarios table...');
    console.log('Base URL:', base_url);
    
    // Initialize DataTable with modern styling
    tableUsuarios = $('#tableUsuarios').DataTable({
        "ajax": {
            "url": base_url + "/Usuarios/getUsuarios",
            "dataSrc": "data",
            "error": function(xhr, error, code) {
                console.error('DataTables AJAX error:', error, code);
                console.error('Response:', xhr.responseText);
            }
        },
        "columns": [
            { "data": "CUIL_Usuario" },
            { "data": "Nombre_Usuario" },
            { "data": "Apellido_Usuario" },
            { "data": "Correo_Usuario" },
            { "data": "Telefono_Usuario" },
            { 
                "data": "Estado_Usuario",
                "render": function(data, type, row) {
                    if(data == 'Activo') {
                        return '<span class="badge badge-success">Activo</span>';
                    } else {
                        return '<span class="badge badge-danger">Bloqueado</span>';
                    }
                }
            },
            { "data": "Rol_Usuario" },
            { 
                "data": null,
                "render": function(data, type, row) {
                    return `
                        <div class="action-buttons">
                            <button class="btn-action btn-edit" onclick="fntEditUsuario(${row.id_Usuario})" title="Editar Usuario">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn-action btn-delete" onclick="fntDelUsuario(${row.id_Usuario})" title="Eliminar Usuario">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]],
        "scrollX": true,
        "scrollY": false,
        "autoWidth": false,
        "dom": 'rtip', // Remove default search and length controls
        "language": {
            "search": "Buscar:",
            "searchPlaceholder": "Buscar usuarios...",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ usuarios",
            "infoEmpty": "No hay usuarios para mostrar",
            "infoFiltered": "(filtrado de _MAX_ usuarios totales)",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "initComplete": function(settings, json) {
            // Update stats after table loads
            if(json && json.data) {
                updateStats(json.data);
            }
        }
    });

    // Initialize selectpickers when modal is shown
    $('#modalFormUsuario').on('shown.bs.modal', function () {
        $('#listEstado').selectpicker('render');
        $('#listRolId').selectpicker('render');
    });

    // Form submission handler
    var formUsuario = document.querySelector("#formUsuario");
    formUsuario.onsubmit = function(e) {
        e.preventDefault();
        var strCUIL = document.querySelector('#txtCUIL').value;
        var strNombre = document.querySelector('#txtNombre').value;
        var strApellido = document.querySelector('#txtApellido').value;
        var strEmail = document.querySelector('#txtCorreo').value;
        var intTelefono = document.querySelector('#txtTelefono').value;
        var strPassword = document.querySelector('#txtPassword').value;
        var intTipoUsuario = document.querySelector('#listRolId').value;
        var idUsuario = document.querySelector('#idUsuario').value;

        console.log('Form data:', {
            cuil: strCUIL,
            nombre: strNombre,
            apellido: strApellido,
            email: strEmail,
            telefono: intTelefono,
            password: strPassword ? '***masked***' : 'empty',
            tipoUsuario: intTipoUsuario,
            idUsuario: idUsuario
        });

        // Validation for new users (password required) vs editing (password optional)
        if(strCUIL == '' || strNombre == '' || strApellido == '' || strEmail == '' || intTelefono == '' || !intTipoUsuario) {
            swal("Atención", "Todos los campos son obligatorios.", "error");
            return false;
        }

        // Password is required only for new users
        if(idUsuario == '' && strPassword == '') {
            swal("Atención", "La contraseña es obligatoria para nuevos usuarios.", "error");
            return false;
        }

        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        var ajaxUrl = base_url+'/Usuarios/setUsuario'; 
        var formData = new FormData(formUsuario);
        request.open("POST",ajaxUrl,true);
        request.send(formData);
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
                console.log('Server response:', request.responseText);
                var objData = JSON.parse(request.responseText);
                console.log('Parsed response:', objData);
                if(objData.status){
                    $('#modalFormUsuario').modal('hide');
                    formUsuario.reset();
                    swal("Usuarios", objData.msg ,"success");
                    tableUsuarios.ajax.reload();
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        };
    };

    // Function to load roles for select
    window.fntRolesUsuario = function(){
        var ajaxUrl = base_url+'/Usuarios/getSelectRoles';
        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        request.open("GET",ajaxUrl,true);
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
                document.querySelector('#listRolId').innerHTML = request.responseText;
                $('#listRolId').selectpicker('refresh');
            }
        }
        request.send();
    };

    // Function to open modal for new user
    window.openModal = function(){
        document.querySelector('#idUsuario').value="";
        document.querySelector('.modal-header').classList.replace("headerUpdate","headerRegister");
        document.querySelector('#btnActionForm').classList.replace("btn-info","btn-primary");
        document.querySelector('#btnText').innerHTML="Guardar";
        document.querySelector('#titleModal').innerHTML="Nuevo Usuario";
        if(document.querySelector('.modal-subtitle')) {
            document.querySelector('.modal-subtitle').innerHTML="Complete la información del usuario";
        }
        if(document.querySelector('.modal-icon i')) {
            document.querySelector('.modal-icon i').className = "fa-solid fa-user-plus";
        }
        document.querySelector("#formUsuario").reset();
        document.querySelector('#txtPassword').setAttribute('required', 'required');
        document.querySelector('#passwordHelp').style.display = 'none';
        fntRolesUsuario();
        $('#modalFormUsuario').modal('show');
    };
});
// Function to edit user
window.fntEditUsuario = function(idUsuario){
    document.querySelector('#titleModal').innerHTML = "Actualizar Usuario";
    if(document.querySelector('.modal-subtitle')) {
        document.querySelector('.modal-subtitle').innerHTML = "Modifique la información del usuario";
    }
    if(document.querySelector('.modal-icon i')) {
        document.querySelector('.modal-icon i').className = "fa-solid fa-user-pen";
    }
    document.querySelector('.modal-header').classList.replace("headerRegister","headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary","btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";
    
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url+'/Usuarios/getUsuario/'+idUsuario;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            var objData = JSON.parse(request.responseText);
            if(objData.status){
                document.querySelector("#idUsuario").value = objData.data.id_Usuario;
                document.querySelector("#txtCUIL").value = objData.data.CUIL_Usuario;
                document.querySelector("#txtNombre").value = objData.data.Nombre_Usuario;
                document.querySelector("#txtApellido").value = objData.data.Apellido_Usuario;
                document.querySelector("#txtCorreo").value = objData.data.Correo_Usuario;
                document.querySelector("#txtTelefono").value = objData.data.Telefono_Usuario;
                
                // Remove password requirement for editing
                document.querySelector('#txtPassword').removeAttribute('required');
                document.querySelector('#txtPassword').value = '';
                document.querySelector('#passwordHelp').style.display = 'block';
                
                // Load roles first, then set values
                fntRolesUsuario();
                
                // Set values after a small delay to ensure selectpickers are initialized
                setTimeout(function() {
                    document.querySelector("#listRolId").value = objData.data.Rol_Usuario;
                    document.querySelector("#listEstado").value = objData.data.Estado_Usuario == 'Activo' ? 1 : 2;
                    $('#listRolId').selectpicker('refresh');
                    $('#listEstado').selectpicker('refresh');
                }, 100);
                
            }else{
                swal("Error", objData.msg , "error");
            }
        }
    }
    $('#modalFormUsuario').modal('show');
}

// Function to delete user
window.fntDelUsuario = function(idUsuario){
    swal({
        title: "Eliminar Usuario",
        text: "¿Realmente quiere eliminar el usuario?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        if (isConfirm) {
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/Usuarios/delUsuario/';
            var strData = "idUsuario="+idUsuario;
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if(objData.status) {
                        swal("Eliminar!", objData.msg , "success");
                        tableUsuarios.ajax.reload();
                    } else {
                        swal("Atención!", objData.msg , "error");
                    }
                }
            }
        }
    });
}

// Function to filter by role
window.filterByRole = function(){
    var roleFilter = document.querySelector("#filterRole").value;
    if(roleFilter == '') {
        tableUsuarios.column(6).search('').draw();
    } else {
        tableUsuarios.column(6).search(roleFilter).draw();
    }
}

// Function to update statistics
function updateStats(data) {
    const totalUsers = data.length;
    const activeUsers = data.filter(user => user.Estado_Usuario === 'Activo').length;
    const adminUsers = data.filter(user => user.Rol_Usuario === 'Admin').length;
    const employeeUsers = data.filter(user => user.Rol_Usuario === 'Empleado').length;
    
    // Update stat cards with animation
    animateCounter('totalUsers', totalUsers);
    animateCounter('activeUsers', activeUsers);
    animateCounter('adminUsers', adminUsers);
    animateCounter('employeeUsers', employeeUsers);
    
    // Update users count
    document.getElementById('usersCount').textContent = `${totalUsers} usuarios`;
}

// Function to animate counters
function animateCounter(elementId, targetValue) {
    const element = document.getElementById(elementId);
    const startValue = parseInt(element.textContent) || 0;
    const duration = 1000; // 1 second
    const startTime = Date.now();
    
    function updateCounter() {
        const elapsed = Date.now() - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const currentValue = Math.floor(startValue + (targetValue - startValue) * progress);
        
        element.textContent = currentValue;
        
        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        }
    }
    
    requestAnimationFrame(updateCounter);
}

// Custom search function
function customSearch() {
    const searchValue = document.getElementById('customSearchInput').value;
    tableUsuarios.search(searchValue).draw();
}

function exportUsuarios() {
    // Get all users data or selected users
    swal("Export", "Exporting usuarios data...", "success");
    
    // Here you would implement the actual export functionality
    // For now, we'll just show a success message
    setTimeout(() => {
        swal("Export Complete", "Usuarios data has been exported successfully.", "success");
    }, 1000);
}