let tablePedidos;
let rowTable;
let divLoading = document.querySelector("#divLoading");

tablePedidos = $('#tablePedidos').dataTable( {
    "aProcessing":true,
    "aServerSide":false,
    "language": {
        "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    },
    "ajax":{
        "url": base_url+"/Pedidos/getPedidos",
        "dataSrc":""
    },
    "columns":[
        {"data":"idpedido"},
        {"data":"transaccion"},
        {"data":"fecha"},
        {"data":"monto"},
        {"data":"tipopago"},
        {"data":"status"},
        {"data":"options"}
    ],
    "columnDefs": [
                    { 'className': "textcenter", "targets": [ 3 ] },
                    { 'className': "textright", "targets": [ 4 ] },
                    { 'className': "textcenter", "targets": [ 5 ] }
                  ],       
    'dom': 'lBfrtip',
    'buttons': [
        {
            "extend": "copyHtml5",
            "text": "<i class='far fa-copy'></i> Copiar",
            "titleAttr":"Copiar",
            "className": "btn btn-secondary",
            "exportOptions": { 
                "columns": [ 0, 1, 2, 3, 4, 5] 
            }
        },{
            "extend": "excelHtml5",
            "text": "<i class='fas fa-file-excel'></i> Excel",
            "titleAttr":"Esportar a Excel",
            "className": "btn btn-success",
            "exportOptions": { 
                "columns": [ 0, 1, 2, 3, 4, 5] 
            }
        },{
            "extend": "pdfHtml5",
            "text": "<i class='fas fa-file-pdf'></i> PDF",
            "titleAttr":"Esportar a PDF",
            "className": "btn btn-danger",
            "exportOptions": { 
                "columns": [ 0, 1, 2, 3, 4, 5] 
            }
        },{
            "extend": "csvHtml5",
            "text": "<i class='fas fa-file-csv'></i> CSV",
            "titleAttr":"Esportar a CSV",
            "className": "btn btn-info",
            "exportOptions": { 
                "columns": [ 0, 1, 2, 3, 4, 5] 
            }
        }
    ],
    "resonsieve":"true",
    "bDestroy": true,
    "iDisplayLength": 10,
    "order":[[0,"desc"]]  
});

function fntTransaccion(idtransaccion){
    let request = (window.XMLHttpRequest) ? 
                    new XMLHttpRequest() : 
                    new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Pedidos/getTransaccion/'+idtransaccion;
    divLoading.style.display = "flex";
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status){   
                document.querySelector("#divModal").innerHTML = objData.html;
                $('#modalTransaccion').modal('show');
            }else{
                swal("Error", objData.msg , "error");
            }
            divLoading.style.display = "none";
            return false;
        }
    }
}

function fntViewPedido(idpedido){
    // Abrir vista completa de detalle en nueva página
    window.location.href = base_url + '/Pedidos/ver/' + idpedido;
}

function fntSolicitarReembolso(idtransaccion){
    swal({
        title: "Solicitar Reembolso",
        text: "¿Está seguro de solicitar el reembolso de esta transacción? Esta acción puede tardar varios días en procesarse.",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, solicitar reembolso",
        cancelButtonText: "No, cancelar",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) { 
        if(isConfirm){ 
            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? 
                    new XMLHttpRequest() : 
                    new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Pedidos/setReembolso';
            let formData = new FormData();
            formData.append('idtransaccion', idtransaccion);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState != 4) return;
                if(request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    divLoading.style.display = "none";
                    if(objData.status){  
                        swal("Reembolso Solicitado", objData.msg, "success");
                        $('#modalTransaccion').modal('hide');
                        // Recargar tabla de pedidos
                        if(typeof tablePedidos !== 'undefined'){
                            tablePedidos.api().ajax.reload();
                        }
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                    return false;
                }
            }
        }
    });
}

function fntReembolsar(){
    let idtransaccion = document.querySelector("#idtransaccion").value;
    let observacion = document.querySelector("#txtObservacion").value;
    if(idtransaccion == '' || observacion == ''){
        swal("", "Complete los datos para continuar." , "error");
        return false;
    }

    swal({
        title: "Hacer Reembolso",
        text: "¿Realmente quiere realizar el reembolso?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: true,
        closeOnCancel: true
    }, function(isConfirm) { 

        if(isConfirm){ 
            $('#modalReembolso').modal('hide');
            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? 
                    new XMLHttpRequest() : 
                    new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Pedidos/setReembolso';
            let formData = new FormData();
            formData.append('idtransaccion',idtransaccion);
            formData.append('observacion',observacion);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState != 4) return;
                if(request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){  
                        window.location.reload();
                    }else{
                        swal("Error", objData.msg , "error");
                    }
                    divLoading.style.display = "none";
                    return false;
                }
            }
        }

    });
}

function fntEditPedido(idpedido){
    let request = (window.XMLHttpRequest) ? 
                    new XMLHttpRequest() : 
                    new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Pedidos/getPedido/'+idpedido;
    divLoading.style.display = "flex";
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status)
            {
                document.querySelector("#divModal").innerHTML = objData.html;
                $('#modalFormPedido').modal('show');
                $('select').selectpicker();
                fntUpdateInfo();
            }else{
                swal("Error", objData.msg , "error");
            }
            divLoading.style.display = "none";
            return false;

        }
    }
}

function fntUpdateInfo(){
    let formUpdatePedido = document.querySelector("#formUpdatePedido");
    formUpdatePedido.onsubmit = function(e) {
        e.preventDefault();
        
        let estado = document.querySelector("#listEstado").value;
        if(estado == ""){
            swal("", "Debe seleccionar un estado." , "error");
            return false;
        }

        let request = (window.XMLHttpRequest) ? 
                    new XMLHttpRequest() : 
                    new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url+'/Pedidos/setPedido';
        divLoading.style.display = "flex";
        let formData = new FormData(formUpdatePedido);
        request.open("POST",ajaxUrl,true);
        request.send(formData);
        request.onreadystatechange = function(){
            if(request.readyState != 4) return;
            if(request.status == 200){
                let objData = JSON.parse(request.responseText);
                if(objData.status){
                     swal("Actualizado", objData.msg ,"success");
                     $('#modalFormPedido').modal('hide');
                     // Recargar la tabla completa para reflejar cambios
                     tablePedidos.api().ajax.reload();
                }else{
                    swal("Error", objData.msg , "error");
                } 

                divLoading.style.display = "none";
                return false;
            }
        }

    }
}