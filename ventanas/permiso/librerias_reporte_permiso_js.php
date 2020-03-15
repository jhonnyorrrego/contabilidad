<?php
global $atras;
?>
<script>  
$(document).on('click','.editar_permiso',function(){
  var x_idper = $(this).attr("idper");
  
  $.ajax({
    url: 'ejecutar_acciones.php',
    type: 'POST',
    dataType: 'json',
    async: false,
    data: {ejecutar: 'mostrar_actualizar_permiso_formulario', idper: x_idper},
    success : function(respuesta){
      if(respuesta.exito){
        //notificacion(respuesta.mensaje,'success',4000);
        $("#capa_permiso_add").hide();
        $("#capa_permiso_edit").show();
        
        $("#capa_permiso_edit").html(respuesta.html);
      } else {
        notificacion(respuesta.mensaje,'warning',4000);
      }
    }
  });
  
  $(document).scrollTop( $('#capa_permiso_edit').offset().top -80 );
});
$(document).on('click','#actualizar_permiso_formulario',function(){
  var formulario = $("#permiso_edit");
  var resultado = formulario.valid();
  
  if(resultado){
    var data = $(formulario).serializeArray(); // convert form to array
    data.push({name: "ejecutar", value: 'actualizar_permiso_formulario'});
    
    $.ajax({
      url : 'ejecutar_acciones.php',
      type : 'POST',
      dataType: 'json',
      data: $.param(data),
      success : function(resultado){
        if(resultado.exito){
          notificacion(resultado.mensaje,'success',5000);
          
          procesamiento_listar();
          
          $("#cancelar_actualizar_permiso_formulario").click();
        }else{
          notificacion(resultado.mensaje,'warning',5000);
        }
      }
    });
  }
});
  
$(document).on('click','#cancelar_actualizar_permiso_formulario',function(){
  $("#capa_permiso_add").show();
  $("#capa_permiso_edit").hide();
});
</script>