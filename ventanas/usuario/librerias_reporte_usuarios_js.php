<?php
global $atras;
?>
<script>
$(document).ready(function(){
  var alto_documento=$(document).height();
  $("#pantallas_usuarios").height(alto_documento-150);
  
  $( window ).resize(function() {
	  var alto_documento=$(document).height();
	  $("#pantallas_usuarios").height(alto_documento-150);
  });
});
</script>

<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <iframe class="modal-content" id="pantallas_usuarios" name="pantallas_usuarios" border="0px" frameborder="0"></iframe>
  </div>
</div>
<script>  
$(document).on('click','.editar_usuario',function(){
	var idusuario = $(this).attr("idusuario");
	
	$.ajax({
    url: 'ejecutar_acciones.php',
    type: 'POST',
    dataType: 'json',
    async: false,
    data: {ejecutar: 'mostrar_actualizar_usuario_formulario', idusu: idusuario},
    success : function(respuesta){
      if(respuesta.exito){
        //notificacion(respuesta.mensaje,'success',4000);
        $("#capa_usuario_add").hide();
        $("#capa_usuario_edit").show();
        
        $("#capa_usuario_edit").html(respuesta.html);
      } else {
        notificacion(respuesta.mensaje,'warning',4000);
      }
    }
  });
  
  $(document).scrollTop( $('#capa_usuario_edit').offset().top -80 );
});
$(document).on('click','#actualizar_usuario_formulario',function(){
  var formulario = $("#usuario_edit");
  var resultado = formulario.valid();
  
  if(resultado){
    var data = $(formulario).serializeArray(); // convert form to array
    data.push({name: "ejecutar", value: 'actualizar_usuario_formulario'});
    
    $.ajax({
      url : 'ejecutar_acciones.php',
      type : 'POST',
      dataType: 'json',
      data: $.param(data),
      success : function(resultado){
        if(resultado.exito){
          notificacion(resultado.mensaje,'success',5000);
          
          procesamiento_listar();
          
          $("#cancelar_actualizar_usuario_formulario").click();
        }else{
          notificacion(resultado.mensaje,'warning',5000);
        }
      }
    });
  }
});

$(document).on('blur','#identificacion_edit',function(){
  var x_idusu = $("#idusu").val();
  var x_identificacion = $("#identificacion_edit").val();
  if(identificacion){
    $.ajax({
      url : 'ejecutar_acciones.php',
      type : 'POST',
      dataType: 'json',
      data: {ejecutar: 'validar_cedula', identificacion : x_identificacion, idusu : x_idusu},
      success : function(resultado){
        if(!resultado.exito){
          notificacion(resultado.mensaje,'warning',5000);
        }
      }
    });
  }
});
  
$(document).on('click','#cancelar_actualizar_usuario_formulario',function(){
  $("#capa_usuario_add").show();
  $("#capa_usuario_edit").hide();
});
</script>