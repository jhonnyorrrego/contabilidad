<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

$conexion -> validar_acceso_sesion();

include_once ($atras . 'librerias.php');
echo(tema_dashboard_lite());
echo(notificacion());

echo(bootstrap_table());

$alto_tabla = "alto_documento-350";
if (@$_SESSION["dispositivo"] == 'phone') {
	$alto_tabla = "alto_documento+330";
}
?>
<?php echo(encabezado());?>
<?php echo(funciones_js_tema()); ?>

<style>
.table {
  background-color: white;
}
table,th,td{
	font-size:10pt;
}
</style>
<script>
$(document).ready(function(){
  $("#enlace_reporte_usuarios").addClass("active");
  $("#navbarDropdown").click();

  <?php if(@$_REQUEST["vencen_hoy"]){ ?>
  $("input[name$='vencen_hoy']").trigger('click');
  <?php } ?>
});
</script>

<div class="page-header row no-gutters py-4">
  <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
    <h3 class="page-title">Reporte de usuarios</h3>
  </div>
</div>

<?php if(@$_SESSION["dispositivo"] == 'computer'){ ?>

<style>
#table td{
	font-size : 9pt;
}
</style>

<div class="row">
	<div class="col-lg-12 cargando">
		<div class="card card-small mb-4">
			<div class="card-header border-bottom">
				<h6 class="m-0"><b>Usuarios registrados</b></h6>
			</div>
			<div class="card-body">
				<form class="" id="form_table" name="form_table">

					<div class="row mb-2">
						<div class="col-md-2">
							<input class="form-control buscar" name="buscar" id="buscar" type="text" placeholder="Buscar">
						</div>
						<!--div class="col-md-2">
							<label class="checkbox-inline">
								<input type="checkbox" class="filtro_check" name="vencidos" value="1"> Vencidos
							</label>
						</div>
						<div class="col-md-2">
							<label class="checkbox-inline">
								<input type="checkbox" class="filtro_check" name="por_vencerse" value="1"> Por vencerse
							</label>
						</div-->
						<fieldset class="col-md-2">
					    	<div class="custom-control custom-checkbox mb-1 text-left">
					        	<input type="checkbox" class="custom-control-input filtro_check" name="vencidos" value="1" id="formsCheckboxChecked">
					          	<label class="custom-control-label" for="formsCheckboxChecked">Vencidos</label>
					        </div>
					        <div class="custom-control custom-checkbox mb-1 text-left">
					        	<input type="checkbox" class="custom-control-input filtro_check" name="por_vencerse" value="1" id="formsCheckboxDefault">
					          	<label class="custom-control-label" for="formsCheckboxDefault">Por vencerse</label>
					        </div>
					    </fieldset>
					    <fieldset class="col-md-2">
					    	<div class="custom-control custom-checkbox mb-1 text-left">
					        	<input type="checkbox" class="custom-control-input filtro_check" name="vencen_hoy" value="1" id="formsCheckbox">
					          	<label class="custom-control-label" for="formsCheckbox">Vencen hoy</label>
					        </div>
					    </fieldset>
					</div>

					<div class="card-body p-0 pb-3 text-center">
						<table id="table" class="table mb-0">
							<thead class="bg-light">
								<tr>
									<th data-field="mostrar_foto_usuario" data-sortable="false" data-visible="true"></th>
									<th data-field="acciones_usuario" data-sortable="false" data-visible="true">Acciones</th>
									<th data-field="identificacion" data-sortable="true" data-visible="true">Identificacion</th>
									<th data-field="nombres" data-sortable="true" data-visible="true">Nombres</th>
									<th data-field="apellidos" data-sortable="true" data-visible="true">Apellidos</th>
									<th data-field="email" data-sortable="true" data-visible="true">Email</th>
									<th data-field="celular" data-sortable="true" data-visible="true">Celular</th>
									<th data-field="tipo_usuario_funcion" data-sortable="false" data-visible="false">Tipo de usuario</th>
									<th data-field="estado_funcion" data-sortable="false" data-visible="true">Estado</th>
									<th data-field="tipo_pago" data-sortable="false" data-visible="true">Tipo pago</th>
									<th data-field="x_fechai" data-sortable="true" data-visible="true">Fecha inicial</th>
									<th data-field="x_fechaf" data-sortable="true" data-visible="true">Fecha final</th>
									<th data-field="cantidad_dias" data-sortable="true" data-visible="true">Cantidad de d&iacute;as</th>
									<th data-field="dias_faltantes" data-sortable="false" data-visible="true">Dias faltantes</th>
									<th data-field="ultimo_acceso" data-sortable="false" data-visible="true">Ultimo acceso</th>
								</tr>
							</thead>
						</table>
						<input type="hidden" id="cantidad_total">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php } else if(@$_SESSION["dispositivo"] == 'phone'){ ?>
<div class="row">
	
		
<form class="text-center" id="form_table" name="form_table">

	<div class="row mb-2">
		<div class="col-md-2 text-left mb-2">
			<input class="form-control buscar" name="buscar" id="buscar" type="text" placeholder="Buscar">
		</div>
		<div class="col-md-4 text-left mb-2">
			<label class="checkbox-inline">
				<input type="checkbox" class="filtro_check" name="vencidos" value="1"> Vencidos
			</label>
		</div>
		<div class="col-md-2 text-left mb-2">
			<label class="checkbox-inline">
				<input type="checkbox" class="filtro_check" name="por_vencerse" value="1"> Por vencerse
			</label>
		</div>
		<div class="col-md-2 text-left mb-2">
			<label class="checkbox-inline">
				<input type="checkbox" class="filtro_check" name="vencen_hoy" value="1"> Vencen hoy
			</label>
		</div>
	</div>

	<table id="table" class="table" style="width: 100%">
		<thead class="">
			<tr>
				<th data-field="mostrar_foto_usuario" data-sortable="false" data-visible="true"></th>
				<th data-field="acciones_usuario" data-sortable="false" data-visible="true"></th>
				<th data-field="identificacion" data-sortable="true" data-visible="true">Identificacion</th>
				<th data-field="nombres" data-sortable="true" data-visible="true">Nombres</th>
				<th data-field="apellidos" data-sortable="true" data-visible="true">Apellidos</th>
				<th data-field="email" data-sortable="true" data-visible="true">Email</th>
				<th data-field="celular" data-sortable="true" data-visible="true">Celular</th>
				<th data-field="tipo_usuario_funcion" data-sortable="false" data-visible="true">Tipo de usuario</th>
				<th data-field="estado_funcion" data-sortable="false" data-visible="true">Estado</th>
				<th data-field="tipo_pago" data-sortable="false" data-visible="true">Tipo pago</th>
				<th data-field="x_fechai" data-sortable="false" data-visible="true">Fecha inicial</th>
				<th data-field="x_fechaf" data-sortable="false" data-visible="true">Fecha final</th>
				<th data-field="cantidad_dias" data-sortable="true" data-visible="true">Cantidad de d&iacute;as</th>
				<th data-field="dias_faltantes" data-sortable="false" data-visible="true">Dias faltantes</th>
			</tr>
		</thead>
	</table>
	<input type="hidden" id="cantidad_total">
</form>
			
		
	
</div>
<?php } ?>
<?php echo(pie()); ?>

<script>
$body = $("body");

var cantidad_registros = 10;
$(document).ready(function(){//Se inicializa la tabla con estilos, el alto del documento y se ejecuta la accion para listar datos sobre la tabla
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
$(document).ready(function(){
	$("#form_table").submit(function(){
		return false;
	});
});

$(document).on({
    //ajaxStart: function() { $body.addClass("loading");},
    //ajaxStop: function() { $body.removeClass("loading");}
});

function procesamiento_listar(){
	var alto_documento = $(document).height();

	var data = $('#form_table').serializeObject();
	
	$('#table').bootstrapTable('getOptions').sidePagination = 'client';
	$('#table').bootstrapTable('selectPage', 1);
	$('#table').bootstrapTable('getOptions').sidePagination = 'server';
	
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
			
			<?php } ?>
			<?php if(@$_SESSION["dispositivo"] == 'phone'){ ?>
			setTimeout(function(){
				$('#table').bootstrapTable('resetView' , {height: altoTabla+150} );
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

$(document).ready(function(){
	$(".buscar").keyup(function () {
		setTimeout(function(){
			procesamiento_listar();
		}, 100);
	});
	$(".filtro_check").click(function(){
		procesamiento_listar();
	});
});
</script>
<?php
include_once($atras . "ventanas/usuario/librerias_reporte_usuarios_js.php");
?>