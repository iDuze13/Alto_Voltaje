var base_url = "http://localhost/AltoVoltaje";
var tableSubcategorias;

function openModal() {
    // Limpiar campos del formulario
    document.querySelector('#idSubcategoria').value = "";
    
    // Cambiar estilos del modal para "nuevo"
    let modalHeader = document.querySelector('.modal-header');
    if(modalHeader){
        modalHeader.classList.remove("headerUpdate");
        modalHeader.classList.add("headerRegister");
    }
    
    let btnAction = document.querySelector('#btnActionForm');
    if(btnAction){
        btnAction.classList.remove("btn-info");
        btnAction.classList.add("btn-primary");
    }
    
    let btnText = document.querySelector('#btnText');
    if(btnText){
        btnText.innerHTML = "Guardar";
    }
    
    let titleModal = document.querySelector('#titleModal');
    if(titleModal){
        titleModal.innerHTML = "Nueva Subcategoría";
    }
    
    // Resetear formulario
    let form = document.querySelector("#formSubcategoria");
    if(form){
        form.reset();
    }
    
    // Refrescar selectpicker si existe (las categorías ya están cargadas)
    if(typeof $.fn.selectpicker !== 'undefined'){
        $('#listCategoria').selectpicker('refresh');
    }
    
    // Mostrar el modal
    $('#modalFormSubcategoria').modal('show');
}

function loadCategorias() {
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Subcategorias/getCategoriasSelect';
    request.open("GET", ajaxUrl, true);
    request.send();
    
    request.onreadystatechange = function() {
        if(request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            let select = document.querySelector('#listCategoria');
            if(select) {
                select.innerHTML = '<option value="">Seleccionar categoría</option>';
                objData.forEach(function(categoria) {
                    if(categoria.status == 1) {
                        select.innerHTML += '<option value="'+categoria.idcategoria+'">'+categoria.nombre+'</option>';
                    }
                });
                
                // Actualizar selectpicker si existe
                if(typeof $.fn.selectpicker !== 'undefined'){
                    $('#listCategoria').selectpicker('refresh');
                }
            }
        }
    }
}

function fntEditInfo(element, idsubcategoria) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Subcategoría";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";
    
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Subcategorias/getSubcategoria/' + idsubcategoria;
    request.open("GET", ajaxUrl, true);
    request.send();
    
    request.onreadystatechange = function() {
        if(request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if(objData.status) {
                // Cargar categorías primero
                loadCategorias();
                
                // Llenar formulario después de un pequeño delay para que se carguen las categorías
                setTimeout(function() {
                    let idField = objData.data.IdSubCategoria || objData.data.idSubCategoria;
                    document.querySelector("#idSubcategoria").value = idField;
                    document.querySelector("#txtNombre").value = objData.data.Nombre_SubCategoria;
                    document.querySelector("#txtDescripcion").value = objData.data.Descripcion_SubCategoria;
                    
                    // Seleccionar la categoría y estado
                    document.querySelector("#listCategoria").value = objData.data.categoria_idcategoria;
                    if(objData.data.Estado_SubCategoria == 1) {
                        document.querySelector("#listStatus").value = 1;
                    } else {
                        document.querySelector("#listStatus").value = 2;
                    }
                    
                    // Actualizar selectpicker si existe
                    if(typeof $.fn.selectpicker !== 'undefined'){
                        $('#listCategoria').selectpicker('refresh');
                        $('#listStatus').selectpicker('refresh');
                    }
                    
                    $('#modalFormSubcategoria').modal('show');
                }, 200);
                
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    }
}

function fntViewInfo(idsubcategoria) {
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Subcategorias/getSubcategoria/' + idsubcategoria;
    request.open("GET", ajaxUrl, true);
    request.send();
    
    request.onreadystatechange = function () {
        if(request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if(objData.status) {
                document.querySelector("#celIdSubcategoria").innerHTML = objData.data.IdSubCategoria || objData.data.idSubCategoria;
                document.querySelector("#celNombre").innerHTML = objData.data.Nombre_SubCategoria;
                document.querySelector("#celDescripcion").innerHTML = objData.data.Descripcion_SubCategoria;
                document.querySelector("#celCategoria").innerHTML = objData.data.categoria_nombre || 'Sin categoría';
                
                // El estado puede ser 1, "ACTIVO" o "1"
                let estado = objData.data.Estado_SubCategoria;
                if(estado == 1 || estado == '1' || estado == 'ACTIVO') {
                    document.querySelector("#celStatus").innerHTML = '<span class="badge badge-success">Activo</span>';
                } else {
                    document.querySelector("#celStatus").innerHTML = '<span class="badge badge-danger">Inactivo</span>';
                }
                
                $('#modalViewSubcategoria').modal('show');
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    }
};

function fntDelInfo(idsubcategoria) {
    swal({
        title: "Eliminar Subcategoría",
        text: "¿Realmente quiere eliminar la subcategoría?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        if (isConfirm) {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Subcategorias/delSubcategoria';
            let strData = "idSubcategoria=" + idsubcategoria;
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            
            request.onreadystatechange = function () {
                if(request.readyState == 4 && request.status == 200) {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status) {
                        swal("Eliminar!", objData.msg, "success");
                        tableSubcategorias.ajax.reload();
                    } else {
                        // Si hay productos asociados, mostrar cuáles son
                        if(objData.msg.includes("productos asociados")) {
                            showProductosAsociados(idsubcategoria);
                        } else {
                            swal("Atención!", objData.msg, "error");
                        }
                    }
                }
            }
        }
    });
}

function showProductosAsociados(idsubcategoria) {
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Subcategorias/getProductosAsociados';
    let strData = "idSubcategoria=" + idsubcategoria;
    request.open("POST", ajaxUrl, true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(strData);
    
    request.onreadystatechange = function () {
        if(request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if(objData.status && objData.data.length > 0) {
                let productList = "<ul style='text-align: left; margin: 10px 0;'>";
                objData.data.forEach(function(producto) {
                    productList += "<li>" + producto.nombre + " - $" + producto.precio + "</li>";
                });
                productList += "</ul>";
                
                swal({
                    title: "No se puede eliminar",
                    text: "Esta subcategoría tiene " + objData.data.length + " producto(s) asociado(s):",
                    html: "Esta subcategoría tiene <b>" + objData.data.length + " producto(s)</b> asociado(s):" + productList + "<p>Debe eliminar o reasignar estos productos antes de eliminar la subcategoría.</p>",
                    type: "warning",
                    confirmButtonText: "Entendido"
                });
            } else {
                swal("Atención!", "No es posible eliminar una subcategoría con productos asociados.", "error");
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Cargar categorías al inicializar la página
    loadCategorias();
    
    // Inicializar DataTable
    tableSubcategorias = $('#tableSubcategorias').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": base_url + "/Subcategorias/getSubcategorias",
            "dataSrc": ""
        },
        "columns": [
            { "data": "idSubCategoria" },
            { "data": "Nombre_SubCategoria" },
            { "data": "Descripcion_SubCategoria" },
            { "data": "categoria_nombre_display" },
            { "data": "Estado_SubCategoria" },
            { "data": "options" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    //Nueva Subcategoria
    let formSubcategoria = document.querySelector("#formSubcategoria");
    if(formSubcategoria) {
        formSubcategoria.onsubmit = function(e) {
        e.preventDefault();
        var intIdSubcategoria = document.querySelector('#idSubcategoria').value;
        let strNombre = document.querySelector('#txtNombre').value;
        let strDescripcion = document.querySelector('#txtDescripcion').value;
        let intCategoria = document.querySelector('#listCategoria').value;
        let intStatus = document.querySelector('#listStatus').value;        
        if(strNombre == '' || strDescripcion == '' || intCategoria == '' || intStatus == '')
        {
            swal("Atención", "Todos los campos son obligatorios." , "error");
            return false;
        }
        
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url+'/Subcategorias/setSubcategoria'; 
        let formData = new FormData(formSubcategoria);
        request.open("POST",ajaxUrl,true);
        request.send(formData);
        request.onreadystatechange = function(){
           if(request.readyState == 4 && request.status == 200){
                let objData = JSON.parse(request.responseText);
                if(objData.status)
                {
                    tableSubcategorias.ajax.reload();
                    $('#modalFormSubcategoria').modal("hide");
                    formSubcategoria.reset();
                    swal("Subcategoría", objData.msg ,"success");
                }else{
                    swal("Error", objData.msg , "error");
                }              
            } 
            return false;
        }
        };
    }
});