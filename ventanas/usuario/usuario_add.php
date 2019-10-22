<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

$conexion -> validar_acceso_sesion();

include_once ($atras . 'librerias.php');
echo(tema_majestic_master());
echo(notificacion());
echo(jquery_validate());

echo(bootstrap_table());

$alto_tabla = "alto_documento-280";
if (@$_SESSION["dispositivo"] == 'phone') {
  $alto_tabla = "alto_documento+330";
}
?>
<?php echo(encabezado());?>
<?php echo(funciones_js_tema()); ?>
<style>
.error{
	color:red;
}
</style>
<script>
$(document).ready(function(){
  $("#enlace_usuario_add").addClass("active");
  $("#navbarDropdown").click();
});
</script>
<script type='text/javascript'>
$().ready(function() {
  procesamiento_listar();
  
	// validar los campos del formato
	$('#usuario_add').validate();
	
	$("#guardar_usuario_formulario").click(function(){
		var formulario = $("#usuario_add");
		var resultado = formulario.valid();
		
		if(resultado){
			var data = $(formulario).serializeArray(); // convert form to array
			data.push({name: "ejecutar", value: 'guardar_usuario_formulario'});
      
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
	
	$("#identificacion").blur(function(){
		var x_identificacion = $("#identificacion").val();
		if(identificacion){
			$.ajax({
				url : 'ejecutar_acciones.php',
				type : 'POST',
				dataType: 'json',
				data: {ejecutar: 'validar_cedula', identificacion : x_identificacion},
				success : function(resultado){
					if(!resultado.exito){
						//$("#identificacion").focus();
						//$("#identificacion").select();
						notificacion(resultado.mensaje,'warning',5000);
					}
				}
			});
		}
	});
	
	$(document).on('change','input[name$="tipo_filtro[]"],input[name$="estado_filtro[]"]',function(){
    procesamiento_listar();
  });
  $("#identificacion_filtro,#nombres_filtro,#apellido_filtro,#email_filtro,#celular_filtro").keyup(function(){
    setTimeout(function(){
      procesamiento_listar();
    }, 100);
  });
});
</script>

<div class="row">
  <div class="col-md-12 grid-margin">
    <div class="d-flex justify-content-between flex-wrap">
      <div class="d-flex align-items-end flex-wrap">
        <div class="mr-md-3 mr-xl-5">
          <h2>Administración de usuarios</h2>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="capa_usuario_add" class="row">
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Crear usuario</h4>
        <form class="" name="usuario_add" id="usuario_add">
          <div class="row">
	            <div class="col-md-3 form-group">
	                <label>Tipo de usuario</label>
	                <select class="form-control form-control-sm" id="tipo" name="tipo">
	                	<option value="">Tipo de usuario</option>
        						<option value="1" selected>General</option>
        						<option value="2">Administrador</option>
	                </select>
	            </div>

	            <div class="col-md-3 form-group">
      					<label class="">Identificaci&oacute;n*</label>
      					<input type="number" id="identificacion" name="identificacion" class="form-control form-control-sm required number" pattern="[0-9]*">
        			</div>
        			<div class="col-md-3 form-group">
        			   <label class="">Clave*</label>
        				<input type="password" id="clave" name="clave" class="form-control form-control-sm required">
        			</div>
        			<div class="col-md-3 form-group">
        					<label class="">Repita su clave*</label>
        					<input type="password" id="clave2" class="form-control form-control-sm required" equalTo="#clave">
        			</div>
        			<div class="col-md-3 form-group">
        					<label class="">Nombres*</label>
        						<input type="text" id="nombre" name="nombres" class="form-control form-control-sm required">
        			</div>
        			<div class="col-md-3 form-group">
        					<label class="">Apellidos*</label>
        					<input type="text" id="apellido" name="apellidos" class="form-control form-control-sm required">
        			</div>
        			<div class="col-md-3 form-group">
        					<label class="">Email</label>
        					<input type="text" id="email" name="email" class="form-control form-control-sm email">
        			</div>
        			<div class="col-md-3 form-group">
        					<label class="">Celular</label>
        					<input type="text" id="celular" name="celular" class="form-control form-control-sm">
        			</div>
        			<div class="col-md-3 form-group">
        					<label class="">Estado*</label>
        					<select class="form-control form-control-sm required" id="estado" name="estado">
        						<option value="">Estado</option>
        						<option value="1" selected>Activo</option>
        						<option value="2">Inactivo</option>
        					</select>
        			</div>
          </div>
          <button type="button" id="guardar_usuario_formulario" class="btn btn-primary mb-2">Guardar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="capa_usuario_edit" class="row" style="display:none">
</div>

<div class="row">
  <div class="col-md-12 stretch-card">
    <div class="card">
      <div class="card-body">
        <p class="card-title">Lista usuarios</p>
        
        <a data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample" class="btn btn-mini">
          <i class="mdi mdi-filter"></i> Filtros
        </a>
        
        <button class="btn btn-mini limpiar_filtro">
            <i class="mdi mdi-filter-outline "></i> Limpiar filtros
        </button>
        
        <div class="collapse" id="collapseExample">
          <form class="row" name="filtro_usuario" id="filtro_usuario" onsubmit="return false;">              
              <div class="col-md-3 form-group">
                  <label class="">Tipo de usuario</label>                    
                  <div class="form-check form-check-primary">
                    <label class="form-check-label">
                      <input type="checkbox" class="form-check-input tipo_filtro" name="tipo_filtro[]" id="tipo_filtro1" value="1" texto="Cliente">
                      Cliente
                    <i class="input-helper"></i></label>
                  </div>
                  <div class="form-check form-check-primary">
                    <label class="form-check-label">
                      <input type="checkbox" class="form-check-input tipo_filtro" name="tipo_filtro[]" id="tipo_filtro2" value="2" texto="Administrador">
                      Administrador
                    <i class="input-helper"></i></label>
                  </div>
              </div>
              
              <div class="col-md-3 form-group">
                <label class="">Identificaci&oacute;n</label>
                <input type="text" id="identificacion_filtro" name="identificacion_filtro" class="form-control form-control-sm number">
              </div>

              <div class="col-md-3 form-group">
                  <label class="">Nombres</label>
                    <input type="text" id="nombres_filtro" name="nombres_filtro" class="form-control form-control-sm">
              </div>
              <div class="col-md-3 form-group">
                  <label class="">Apellidos</label>
                  <input type="text" id="apellido_filtro" name="apellido_filtro" class="form-control form-control-sm">
              </div>
              <div class="col-md-3 form-group">
                  <label class="">Email</label>
                  <input type="text" id="email_filtro" name="email_filtro" class="form-control form-control-sm email">
              </div>
              <div class="col-md-3 form-group">
                  <label class="">Celular</label>
                  <input type="text" id="celular_filtro" name="celular_filtro" class="form-control form-control-sm">
              </div>
              
              <div class="col-md-3 form-group">
                  <label class="">Estado</label>                    
                  <div class="form-check form-check-primary">
                    <label class="form-check-label">
                      <input type="checkbox" class="form-check-input estado_filtro" name="estado_filtro[]" id="estado_filtro1" value="1" texto="Activo">
                      Activo
                    <i class="input-helper"></i></label>
                  </div>
                  <div class="form-check form-check-primary">
                    <label class="form-check-label">
                      <input type="checkbox" class="form-check-input estado_filtro" name="estado_filtro[]" id="estado_filtro2" value="2" texto="Inactivo">
                      Inactivo
                    <i class="input-helper"></i></label>
                  </div>
              </div>
            
          </form>
        </div>
        
        <div class="table-responsive">
          <table id="table" class="table table-bordered table-responsive-md table-striped text-center" role="grid" style="width: 100%">
            <thead class="">
              <tr>
                <th data-field="identificacion" data-sortable="true" data-visible="true">Identificacion</th>
                <th data-field="nombres" data-sortable="true" data-visible="true">Nombres</th>
                <th data-field="apellidos" data-sortable="true" data-visible="true">Apellidos</th>
                <th data-field="email" data-sortable="true" data-visible="true">Email</th>
                <th data-field="celular" data-sortable="true" data-visible="true">Celular</th>
                <th data-field="tipo_usuario_funcion" data-sortable="false" data-visible="true">Tipo de usuario</th>
                <th data-field="estado_funcion" data-sortable="false" data-visible="true">Estado</th>
                <th data-field="acciones_usuario" data-sortable="false" data-visible="true"></th>
              </tr>
            </thead>
          </table>
          <input type="hidden" id="cantidad_total">
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$body = $("body");

var cantidad_registros = 1000;
$(document).ready(function(){//Se inicializa la tabla con estilos, el alto del documento y se ejecuta la accion para listar datos sobre la tabla
  $(".limpiar_filtro").click(function(){    
    $("#filtro_usuario").find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
    
    jQuery('#filtro_usuario').each(function(){
      this.reset();
    });
    
    procesamiento_listar();
  });
  
  var alto_documento = $(document).height();
  var alto_tabla = <?php echo($alto_tabla); ?>;

  <?php if($_SESSION["dispositivo"] == 'computer'){ ?>
  $('#table').bootstrapTable({
    method: 'get',
    cache: false,
    pagination: true,
    onlyInfoPagination: false,
    showColumns: false,
    showRefresh: false,
    minimumCountColumns: 2,
    clickToSelect: false,
    sidePagination: 'server',
    pageSize: cantidad_registros,
    search: false,
    cardView:false,
    pageList:'All',
    paginationVAlign: 'bottom',
    responsive: true,
    height: alto_tabla
  });
  <?php } else if(@$_SESSION["dispositivo"] == 'phone'){ ?>
  $('#table').bootstrapTable({
    method: 'get',
    cache: false,
    pagination: true,
    onlyInfoPagination: false,
    showColumns: false,
    showRefresh: false,
    minimumCountColumns: 2,
    clickToSelect: false,
    sidePagination: 'server',
    pageSize: cantidad_registros,
    search: false,
    searchAlign: 'left',
    cardView:true,
    pageList:'All',
    paginationVAlign: 'both',
    paginationHAlign: 'left',
    height: 3250
  });
  <?php } ?>
  
  procesamiento_listar();
});
function procesamiento_listar(){
  var alto_documento = $(document).height();

  var data = $('#filtro_usuario').serializeObject();
  
  $('#table').bootstrapTable('refreshOptions', {
    url: 'obtener_usuarios.php',
    queryParams: function (params) {
      
      var q = {
        "rows": cantidad_registros,
        "numfilas":cantidad_registros,
        "actual_row": params.offset,
        "pagina":(params.offset/cantidad_registros)+1,
        //"search": params.search,
        "sort": params.sort,
        "order": params.order
      };
      $.extend( data, q);
        
      var cantidad_total = $("#cantidad_total").val();
      if(cantidad_total){
        $.extend(data,{total:cantidad_total});
      }
      
      return data;
    },
        onLoadSuccess: function(data){
      $("#cantidad_total").val(data.total);
      var altoTabla = $("#table").height();

      <?php if(@$_SESSION["dispositivo"] == 'computer'){ ?>
      setTimeout(function(){
        $('#table').bootstrapTable('resetView' , {height: altoTabla+100} );
        //$(document).scrollTop( $('#capa_ingreso_egreso_list').offset().top -80 );        
      }, 500);
      
      <?php } ?>
      <?php if(@$_SESSION["dispositivo"] == 'phone'){ ?>
      setTimeout(function(){
        $('#table').bootstrapTable('resetView' , {height: altoTabla+150} );
        //$(document).scrollTop( $('#capa_ingreso_egreso_list').offset().top -80 );
      }, 500);
      <?php } ?>
    }
  });
}

function jsonConcat(o1, o2) {
   for (var key in o2) {
    o1[key] = o2[key];
   }
   return o1;
  }
$.fn.serializeObject = function(){
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

$(".redimensionar").click(function(){
   setTimeout(function(){ $('#table').bootstrapTable('resetWidth'); }, 500);
});
</script>
<?php echo(pie()); ?>
<?php include_once($atras . "ventanas/usuario/librerias_reporte_usuarios_js.php"); ?>