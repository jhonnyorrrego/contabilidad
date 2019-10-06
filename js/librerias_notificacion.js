function notificacion(texto='',tipo='success',tiempo=2500){
	/*new PNotify({
      text: texto,
      type: tipo,
      delay: tiempo
  	});*/

  	Command: toastr[tipo](texto);
  	
}