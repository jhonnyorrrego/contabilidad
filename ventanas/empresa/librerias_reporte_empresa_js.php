<?php
global $atras;
?>
<script>  
$(document).on('click','.editar_empresa',function(){
  var x_idemp = $(this).attr("idemp");
  
  $.ajax({
    url: 'ejecutar_acciones.php',
    type: 'POST',
    dataType: 'json',
    async: false,
    data: {ejecutar: 'mostrar_actualizar_empresa_formulario', idemp: x_idemp},
    success : function(respuesta){
      if(respuesta.exito){
        //notificacion(respuesta.mensaje,'success',4000);
        $("#capa_empresa_add").hide();
        $("#capa_empresa_edit").show();
        
        $("#capa_empresa_edit").html(respuesta.html);
      } else {
        notificacion(respuesta.mensaje,'warning',4000);
      }
    }
  });
  
  $(document).scrollTop( $('#capa_empresa_edit').offset().top -80 );
});
$(document).on('click','#actualizar_empresa_formulario',function(){
  var formulario = $("#empresa_edit");
  var resultado = formulario.valid();
  
  if(resultado){
    var data = $(formulario).serializeArray(); // convert form to array
    data.push({name: "ejecutar", value: 'actualizar_empresa_formulario'});
    
    $.ajax({
      url : 'ejecutar_acciones.php',
      type : 'POST',
      dataType: 'json',
      data: $.param(data),
      success : function(resultado){
        if(resultado.exito){
          notificacion(resultado.mensaje,'success',5000);
          
          procesamiento_listar();
          
          $("#cancelar_actualizar_empresa_formulario").click();
        }else{
          notificacion(resultado.mensaje,'warning',5000);
        }
      }
    });
  }
});
  
$(document).on('click','#cancelar_actualizar_empresa_formulario',function(){
  $("#capa_empresa_add").show();
  $("#capa_empresa_edit").hide();
});
</script>