console.log("functions_categorias.js cargado");
var base_url = "http://localhost/AltoVoltaje";
var tableCategorias;

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable
    tableCategorias = $('#tableCategorias').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": base_url + "/categorias/getCategorias",
            "dataSrc": ""
        },
        "columns": [
            { "data": "idcategoria" },
            { "data": "nombre" },
            { "data": "descripcion" },
            { "data": "status" },
            { "data": "options" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    if(document.querySelector("#foto")){
	    let foto = document.querySelector("#foto");
	    foto.onchange = function(e) {
	        let uploadFoto = document.querySelector("#foto").value;
	        let fileimg = document.querySelector("#foto").files;
	        let nav = window.URL || window.webkitURL;
	        let contactAlert = document.querySelector('#form_alert');
	        if(uploadFoto !=''){
	            let type = fileimg[0].type;
	            let name = fileimg[0].name;
	            if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png'){
	                contactAlert.innerHTML = '<p class="errorArchivo">El archivo no es válido.</p>';
	                if(document.querySelector('#img')){
	                    document.querySelector('#img').remove();
	                }
	                document.querySelector('.delPhoto').classList.add("notBlock");
	                foto.value="";
	                return false;
	            }else{  
	                    contactAlert.innerHTML='';
	                    if(document.querySelector('#img')){
	                        document.querySelector('#img').remove();
	                    }
	                    document.querySelector('.delPhoto').classList.remove("notBlock");
	                    let objeto_url = nav.createObjectURL(this.files[0]);
	                    document.querySelector('.prevPhoto div').innerHTML = "<img id='img' src="+objeto_url+">";
	                }
	        }else{
	            alert("No selecciono foto");
	            if(document.querySelector('#img')){
	                document.querySelector('#img').remove();
	            }
	        }
	    }
	}

	if(document.querySelector(".delPhoto")){
	    let delPhoto = document.querySelector(".delPhoto");
	    delPhoto.onclick = function(e) {
            console.log("Botón eliminar foto clickeado");
	        removePhoto();
	    }
	}

    //Nueva Categoria
    let formCategoria = document.querySelector("#formCategoria");
    if(formCategoria) {
        formCategoria.onsubmit = function(e) {
        e.preventDefault();
        var intIdCategoria = document.querySelector('#idCategoria').value;
        let strNombre = document.querySelector('#txtNombre').value;
        let strDescripcion = document.querySelector('#txtDescripcion').value;
        let intStatus = document.querySelector('#listStatus').value;        
        if(strNombre == '' || strDescripcion == '' || intStatus == '')
        {
            swal("Atención", "Todos los campos son obligatorios." , "error");
            return false;
        }
        
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url+'/Categorias/setCategoria'; 
        let formData = new FormData(formCategoria);
        request.open("POST",ajaxUrl,true);
        request.send(formData);
        request.onreadystatechange = function(){
           if(request.readyState == 4 && request.status == 200){
                let objData = JSON.parse(request.responseText);
                if(objData.status)
                {
                    tableCategorias.ajax.reload();
                    $('#modalFormCategorias').modal("hide");
                    formCategoria.reset();
                    swal("Categoria", objData.msg ,"success");
                    removePhoto();
                }else{
                    swal("Error", objData.msg , "error");
                }              
            } 
            return false;
        }
        };
    }
});

function fntViewInfo(idcategoria){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Categorias/getCategoria/'+idcategoria;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status)
            {
                let estado = objData.data.status == 1 ? 
                '<span class="badge badge-success">Activo</span>' : 
                '<span class="badge badge-danger">Inactivo</span>';
                document.querySelector("#celId").innerHTML = objData.data.idcategoria;
                document.querySelector("#celNombre").innerHTML = objData.data.nombre;
                document.querySelector("#celDescripcion").innerHTML = objData.data.descripcion;
                document.querySelector("#celEstado").innerHTML = estado;
                document.querySelector("#imgCategoria").innerHTML = '<img src="'+objData.data.url_portada+'"></img>';
                $('#modalViewCategoria').modal('show');
            }else{
                swal("Error", objData.msg , "error");
            }
        }
    }
}

function fntEditInfo(element,idcategoria){
    rowTable = element.parentNode.parentNode.parentNode;
    document.querySelector('#titleModal').innerHTML ="Actualizar Categoría";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML ="Actualizar";
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Categorias/getCategoria/'+idcategoria;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status)
            {
                document.querySelector("#idCategoria").value = objData.data.idcategoria;
                document.querySelector("#txtNombre").value = objData.data.nombre;
                document.querySelector("#txtDescripcion").value = objData.data.descripcion;
                document.querySelector('#foto_actual').value = objData.data.portada;
                document.querySelector("#foto_remove").value= 0;

                if(objData.data.status == 1){
                    document.querySelector("#listStatus").value = 1;
                }else{
                    document.querySelector("#listStatus").value = 2;
                }
                // Actualizar selectpicker si existe
                if(typeof $.fn.selectpicker !== 'undefined'){
                    $('#listStatus').selectpicker('refresh');
                }

                if(document.querySelector('#img')){
                    document.querySelector('#img').src = objData.data.url_portada;
                }else{
                    document.querySelector('.prevPhoto div').innerHTML = "<img id='img' src="+objData.data.url_portada+">";
                }

                if(objData.data.portada == 'portada_categoria.png'){
                    document.querySelector('.delPhoto').classList.add("notBlock");
                }else{
                    document.querySelector('.delPhoto').classList.remove("notBlock");
                }

                $('#modalFormCategorias').modal('show');

            }else{
                swal("Error", objData.msg , "error");
            }
        }
    }
}

function fntDelInfo(idcategoria){
    swal({
        title: "Eliminar Categoría",
        text: "¿Realmente quiere eliminar al categoría?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        
        if (isConfirm) 
        {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Categorias/delCategoria';
            let strData = "idCategoria="+idcategoria;
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        swal("Eliminar!", objData.msg , "success");
                        tableCategorias.ajax.reload();
                    }else{
                        swal("Atención!", objData.msg , "error");
                    }
                }
            }
        }

    });

}

function removePhoto(){
    console.log("Ejecutando removePhoto()");
    // Limpiar el input de archivo
    let fotoInput = document.querySelector('#foto');
    if(fotoInput){
        fotoInput.value = "";
    }
    
    // Ocultar el botón de eliminar
    let delPhoto = document.querySelector('.delPhoto');
    if(delPhoto){
        delPhoto.classList.add("notBlock");
    }
    
    // Remover la imagen preview
    let img = document.querySelector('#img');
    if(img){
        img.remove();
    }
    
    // Marcar para eliminación si existe el campo
    let fotoRemove = document.querySelector('#foto_remove');
    if(fotoRemove){
        fotoRemove.value = "1";
    }
    
    // Limpiar el contenedor de la imagen
    let prevPhotoDiv = document.querySelector('.prevPhoto div');
    if(prevPhotoDiv){
        prevPhotoDiv.innerHTML = '';
    }
}

function openModal()
{
    // Limpiar campos del formulario
    document.querySelector('#idCategoria').value = "";
    
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
        titleModal.innerHTML = "Nueva Categoria";
    }
    
    // Resetear formulario
    let form = document.querySelector("#formCategoria");
    if(form){
        form.reset();
    }
    
    // Resetear estado de la foto
    let fotoRemove = document.querySelector('#foto_remove');
    if(fotoRemove){
        fotoRemove.value = "0";
    }
    
    let delPhoto = document.querySelector('.delPhoto');
    if(delPhoto){
        delPhoto.classList.add("notBlock");
    }
    
    let prevPhotoDiv = document.querySelector('.prevPhoto div');
    if(prevPhotoDiv){
        prevPhotoDiv.innerHTML = '';
    }
    
    // Mostrar el modal
    $('#modalFormCategorias').modal('show');
}