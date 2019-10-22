<?php
global $atras;
?>
<script>  
$(document).on('click','.editar_categoria',function(){
  var x_idcat = $(this).attr("idcat");
  
  $.ajax({
    url: 'ejecutar_acciones.php',
    type: 'POST',
    dataType: 'json',
    async: false,
    data: {ejecutar: 'mostrar_actualizar_categoria_formulario', idcat: x_idcat},
    success : function(respuesta){
      if(respuesta.exito){
        //notificacion(respuesta.mensaje,'success',4000);
        $("#capa_categoria_add").hide();
        $("#capa_categoria_edit").show();
        
        $("#capa_categoria_edit").html(respuesta.html);
      } else {
        notificacion(respuesta.mensaje,'warning',4000);
      }
    }
  });
  
  $(document).scrollTop( $('#capa_categoria_edit').offset().top -80 );
});
$(document).on('click','#actualizar_categoria_formulario',function(){
  var formulario = $("#categoria_edit");
  var resultado = formulario.valid();
  
  if(resultado){
    var data = $(formulario).serializeArray(); // convert form to array
    data.push({name: "ejecutar", value: 'actualizar_categoria_formulario'});
    
    $.ajax({
      url : 'ejecutar_acciones.php',
      type : 'POST',
      dataType: 'json',
      data: $.param(data),
      success : function(resultado){
        if(resultado.exito){
          notificacion(resultado.mensaje,'success',5000);
          
          procesamiento_listar();
          
          $("#cancelar_actualizar_categoria_formulario").click();
        }else{
          notificacion(resultado.mensaje,'warning',5000);
        }
      }
    });
  }
});
  
$(document).on('click','#cancelar_actualizar_categoria_formulario',function(){
  $("#capa_categoria_add").show();
  $("#capa_categoria_edit").hide();
});
</script>