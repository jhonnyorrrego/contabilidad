<?php
global $atras;
?>
<script>  
$(document).on('click','.editar_bolsillo',function(){
  var x_idbol = $(this).attr("idbol");
  
  $.ajax({
    url: 'ejecutar_acciones.php',
    type: 'POST',
    dataType: 'json',
    async: false,
    data: {ejecutar: 'mostrar_actualizar_bolsillo_formulario', idbol: x_idbol},
    success : function(respuesta){
      if(respuesta.exito){
        //notificacion(respuesta.mensaje,'success',4000);
        $("#capa_bolsillo_add").hide();
        $("#capa_bolsillo_edit").show();
        
        $("#capa_bolsillo_edit").html(respuesta.html);
      } else {
        notificacion(respuesta.mensaje,'warning',4000);
      }
    }
  });
});
$(document).on('click','#actualizar_bolsillo_formulario',function(){
  var formulario = $("#bolsillo_edit");
  var resultado = formulario.valid();
  
  if(resultado){
    var data = $(formulario).serializeArray(); // convert form to array
    data.push({name: "ejecutar", value: 'actualizar_bolsillo_formulario'});
    
    $.ajax({
      url : 'ejecutar_acciones.php',
      type : 'POST',
      dataType: 'json',
      data: $.param(data),
      success : function(resultado){
        if(resultado.exito){
          notificacion(resultado.mensaje,'success',5000);
          
          procesamiento_listar();
        }else{
          notificacion(resultado.mensaje,'warning',5000);
        }
      }
    });
  }
});
  
$(document).on('click','#cancelar_actualizar_bolsillo_formulario',function(){
  $("#capa_bolsillo_add").show();
  $("#capa_bolsillo_edit").hide();
});
</script>