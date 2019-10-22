<?php
global $atras;
?>
<script>
$(document).ready(function(){
  
});

$(document).on('click','.editar_ingreso_egreso',function(){
  var idingreso = $(this).attr("idingreso_egreso");
  
  $.ajax({
    url: 'ejecutar_acciones.php',
    type: 'POST',
    dataType: 'json',
    async: false,
    data: {ejecutar: 'mostrar_actualizar_ingreso_egreso_formulario', iding: idingreso},
    success : function(respuesta){
      if(respuesta.exito){
        //notificacion(respuesta.mensaje,'success',4000);
        $("#capa_ingreso_egreso_add").hide();
        $("#capa_ingreso_egreso_edit").show();
        
        $("#capa_ingreso_egreso_edit").html(respuesta.html);
        
        $('#fecha_edit').datepicker({
          language : 'es',
          format: 'yyyy-mm-dd',
          autoclose: true
        });
        
        procesar_grupo_edit();
        procesar_bolsillo_edit();
      } else {
        notificacion(respuesta.mensaje,'warning',4000);
      }
    }
  });
  
  $(document).scrollTop( $('#capa_ingreso_egreso_edit').offset().top -80 );
});

$(document).on('click', "#actualizar_ingreso_egreso_formulario", function(){
  var x_iding = $("#iding").val();
  var x_empresa = $("#empresa_edit").val();
  var x_fecha = $("#fecha_edit").val();
  var x_grupo = $('input:radio[name=grupo_edit]:checked').val();
  var x_categoria = $("#categoria_edit").val();
  var x_concepto = $("#concepto_edit").val();
  var x_valor = $("#valor_edit").val();
  var x_tipo_pago = $('input:radio[name=tipo_pago_edit]:checked').val();
  var x_bolsillo = $('#bolsillo_edit').val();
  
  var x_otra_categoria = $("#otra_categoria_edit").val();
  
  if(x_grupo == 4){//Si es grupo Traslado
    var x_tipo_pago = $('input:radio[name=tipo_pago2_edit]:checked').val();//
    
    if(!x_empresa || !x_fecha || !x_grupo || (!x_valor || x_valor == 0)){//No se valida la categoria
      notificacion('Por favor llenar los campos obligatorios','warning',4000);
      return false;
    }
  }  else if(x_tipo_pago == 3){//Si tipo de pago es bolsillo
      if(!x_empresa || !x_fecha || !x_grupo || !x_categoria || (!x_valor || x_valor == 0) || !x_bolsillo){
        notificacion('Por favor llenar los campos obligatorios','warning',4000);
        return false;
      }
  } else if(x_grupo == 6){//Si grupo es saldo inicial bolsillo
      if(!x_empresa || !x_fecha || !x_grupo || (!x_valor || x_valor == 0) || !x_bolsillo){
        notificacion('Por favor llenar los campos obligatorios','warning',4000);
        return false;
      }
  } else {
    if(!x_empresa || !x_fecha || !x_grupo || !x_categoria || (!x_valor || x_valor == 0)){
      notificacion('Por favor llenar los campos obligatorios','warning',4000);
      return false;
    }
    
    if(x_categoria == -1){
      var valorOtraCategoria = x_otra_categoria;
      if(!valorOtraCategoria){
        notificacion('Por favor llenar los campos obligatorios','warning',4000);
        return false;
      }
      
      var otraCategoria = confirm("Esta seguro de vincular esta nueva categoria?");
      if(!otraCategoria){
        return false;
      }
    }
  }
  
  $.ajax({
    url: 'ejecutar_acciones.php',
    type: 'POST',
    dataType: 'json',
    async: false,
    data: {ejecutar: 'actualizar_ingreso_egreso_formulario', empresa: x_empresa, fecha : x_fecha, grupo: x_grupo, categoria: x_categoria, concepto: x_concepto, valor: x_valor, tipo_pago: x_tipo_pago, otra_categoria: x_otra_categoria, bolsillo: x_bolsillo, iding: x_iding},
    success : function(respuesta){
      if(respuesta.exito){
        notificacion(respuesta.mensaje,'success',4000);
        
        procesar_capa_info_ingreso_egreso();
        procesamiento_listar();
        
        if(x_categoria == -1){
          $("#empresa").trigger("change");
        }
        
        $("#otra_categoria_edit").hide();
        $("#otra_categoria_edit").val("");
        
        $("#cancelar_actualizar_ingreso_egreso_formulario").click();
      } else {
        notificacion(respuesta.mensaje,'warning',4000);
      }
    }
  });
  
});

$(document).on('click','.eliminar_ingreso_egreso',function(){
  var elementoIding = $(this);
  var x_iding = elementoIding.attr("iding");
  
  if(!confirm("Esta seguro de eliminar este registro?")){
    return false;
  }
  
  $.ajax({
    url: 'ejecutar_acciones.php',
    type: 'POST',
    dataType: 'json',
    async: false,
    data: {ejecutar: 'eliminar_ingreso_egreso', iding: x_iding},
    success : function(respuesta){
      if(respuesta.exito){
        notificacion(respuesta.mensaje,'success',4000);
        //elementoIding.parent().parent().hide();
        procesamiento_listar();
        procesar_capa_info_ingreso_egreso();
      } else {
        notificacion(respuesta.mensaje,'warning',4000);
      }
    }
  });
});

$(document).on('change', '#categoria_edit', function(){
    var valor = $(this).val();
    if(valor == -1){
      $("#otra_categoria_edit").show();
      $("#otra_categoria_edit").focus();
    } else {
      $("#otra_categoria_edit").hide();
    }
  });
  
$(document).on('click', "input[name$='grupo_edit']", function(){
  procesar_grupo_edit();
  procesar_categorias_edit();
});

function procesar_grupo_edit(){
  var x_valor = $('input:radio[name=grupo_edit]:checked').val();
  
  if(x_valor == 4){//Si grupo es traslado, solo necesitamos el campo valor y traslado a
    $("#capa_categoria_edit").hide();
    $("#capa_concepto_edit").hide();
    $("#capa_tipo_pago_edit").hide();
    $("#capa_bolsillo_edit").hide();
    
    $("#capa_traslado_a_edit").show();
  } else if(x_valor == 6){//Saldo inicial bolsillo
      //$("#capa_adicionar_categoria").hide();
      $("#capa_concepto_edit").hide();
      $("#capa_tipo_pago_edit").hide();
      
      $("#capa_bolsillo_edit").show();
  } else {
    $("#capa_traslado_a_edit").hide();
    $("#capa_bolsillo_edit").hide();
    
    $("#capa_categoria_edit").show();
    $("#capa_concepto_edit").show();
    $("#capa_tipo_pago_edit").show();
  }
}

function procesar_categorias_edit(){
  var x_empresa = $("#empresa").val();
  var x_grupo = $('input:radio[name=grupo_edit]:checked').val();
  
  if(x_empresa && x_grupo){
    $.ajax({
      url: 'ejecutar_acciones.php',
      type: 'POST',
      dataType: 'json',
      async: false,
      data: {empresa: x_empresa, grupo: x_grupo, ejecutar: 'obtener_listas_categorias'},
      success : function(respuesta){
        if(respuesta.exito){
          $("#categoria_edit").html(respuesta.opciones_categoria);
        } else {
          $("#categoria_edit").html("<option value=''>Seleccione</option><option value='-1'>Otro</option>");
        }
      }
    });
  }
}

$(document).on('click', "input[name$='tipo_pago_edit']", function(){
  procesar_bolsillo_edit();
});

function procesar_bolsillo_edit(){
  var x_valor = $('input:radio[name=tipo_pago_edit]:checked').val();
    
  if(x_valor == 3){//Si tipo de pago es bolsillo      
    $("#capa_bolsillo_edit").show();
    $("#bolsillo_edit").addClass("required");
    
  } else {
    $("#capa_bolsillo_edit").hide();
    $("#bolsillo_edit").removeClass("required");
  }
}

$(document).on('keyup', "#valor_edit", function(){
    var valor=$(this).val().replace(/[^0-9]/g, '');
    $(this).val(Moneda_r(valor));
});
$(document).on('blur', "#valor_edit", function(){
    var valor=$(this).val().replace(/[^0-9]/g, '');
    $(this).val(Moneda_r(valor));
});

$(document).on('click','#cancelar_actualizar_ingreso_egreso_formulario',function(){
  $("#capa_ingreso_egreso_add").show();
  $("#capa_ingreso_egreso_edit").hide();
});

$(document).on('click', '.valores', function(){  
  var x_ingreso = 0;
  var x_egreso = 0;
  
  if($(this).hasClass('active')){
    $(this).removeClass('active');
  } else {
    $(this).addClass('active');
  }
  
  $(".valores").each(function(indice){
    var elemento = $(this);
    var x_valor = parseInt(elemento.html().replace(/[^0-9]/g, ''));
    
    if(elemento.hasClass('active')){
      if(elemento.attr("tipo") == 1){
        x_ingreso += x_valor;
      } else if(elemento.attr("tipo") == 2){
        x_egreso += x_valor;
      }
    }
  });
  
  var x_nuevoValor = x_ingreso - x_egreso;  
  var x_valorParseado = Moneda_r(x_nuevoValor.toString());
  
  $("#calculadora_total").html(x_valorParseado);
  
  if(x_ingreso != 0 || x_egreso != 0){
    $("#calculadora").show(100);
  } else {
    $("#calculadora").hide(100);
  }
});
</script>