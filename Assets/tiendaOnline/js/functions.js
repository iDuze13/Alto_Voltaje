
if(document.querySelector("#frmSuscripcion")){
	let frmSuscripcion = document.querySelector("#frmSuscripcion");
	frmSuscripcion.addEventListener('submit',function(e) { 
		e.preventDefault();

		let nombre = document.querySelector("#nombreSuscripcion").value;
		let email = document.querySelector("#emailSuscripcion").value;

		if(nombre == ""){
			swal("", "El nombre es obligatorio" ,"error");
			return false;
		}

		if(!fntEmailValidate(email)){
			swal("", "El email no es válido." ,"error");
			return false;
		}	
		
		divLoading.style.display = "flex";
		let request = (window.XMLHttpRequest) ? 
                    new XMLHttpRequest() : 
                    new ActiveXObject('Microsoft.XMLHTTP');
		let ajaxUrl = base_url+'/Tienda/suscripcion';
		let formData = new FormData(frmSuscripcion);
	   	request.open("POST",ajaxUrl,true);
	    request.send(formData);
	    request.onreadystatechange = function(){
	    	if(request.readyState != 4) return;
	    	if(request.status == 200){
	    		let objData = JSON.parse(request.responseText);
	    		if(objData.status){
	    			swal("", objData.msg , "success");
                	document.querySelector("#frmSuscripcion").reset();
	    		}else{
	    			swal("", objData.msg , "error");
	    		}
	    	}
	    	divLoading.style.display = "none";
        	return false;
	    
		}

	},false);
}

if(document.querySelector("#frmContacto")){
	let frmContacto = document.querySelector("#frmContacto");
	frmContacto.addEventListener('submit',function(e) { 
		e.preventDefault();

		let nombre = document.querySelector("#nombreContacto").value;
		let email = document.querySelector("#emailContacto").value;
		let mensaje = document.querySelector("#mensaje").value;

		if(nombre == ""){
			swal("", "El nombre es obligatorio" ,"error");
			return false;
		}

		if(!fntEmailValidate(email)){
			swal("", "El email no es válido." ,"error");
			return false;
		}

		if(mensaje == ""){
			swal("", "Por favor escribe el mensaje." ,"error");
			return false;
		}	
		
		divLoading.style.display = "flex";
		let request = (window.XMLHttpRequest) ? 
                    new XMLHttpRequest() : 
                    new ActiveXObject('Microsoft.XMLHTTP');
		let ajaxUrl = base_url+'/Tienda/contacto';
		let formData = new FormData(frmContacto);
	   	request.open("POST",ajaxUrl,true);
	    request.send(formData);
	    request.onreadystatechange = function(){
	    	if(request.readyState != 4) return;
	    	if(request.status == 200){
	    		let objData = JSON.parse(request.responseText);
	    		if(objData.status){
	    			swal("", objData.msg , "success");
                	document.querySelector("#frmContacto").reset();
	    		}else{
	    			swal("", objData.msg , "error");
	    		}
	    	}
	    	divLoading.style.display = "none";
        	return false;
		}

	},false);
}