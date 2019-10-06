<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

$conexion -> validar_acceso_consulta_sesion();

include_once ($atras . 'librerias.php');
echo(tema_dashboard_lite());
echo(notificacion());
echo(jquery_validate());

echo(bootstrap_datepicker());
echo(date_format_jquery());

$idusuario = @$_SESSION["idusu"];

$datos_usuario = $conexion -> obtener_datos_usuario($idusuario);

$defecto = $atras . "img/sin_foto.png";
$imagen_usuario = $conexion -> obtener_imagen_usuario($idusuario);
if(@$imagen_usuario && file_exists($atras . $imagen_usuario)){
	$defecto = $atras . $imagen_usuario;
}

$fechai = date('Y-m-d');
$fechaf = $conexion -> sumar_fecha($fechai,1,'month','Y-m-d');
?>
<?php echo(encabezado());?>
<?php echo(funciones_js_tema()); ?>

  	<style type="text/css">
  	.campo_editar{
  		cursor: pointer;
  	}
  	.error{
		color:red;
	}
  	</style>
	<script type='text/javascript'>
	$().ready(function() {
		listar_anexos();
		$('html, body').animate({scrollTop:0}, 'slow');

		$('#anexar').click(function(){
			$("#anexos").click();
		});

		$("#anexos").change(function(){
			var formData = new FormData(document.getElementById("anexos_usuario"));
			formData.append('idusu', '<?php echo($idusuario); ?>');
			formData.append('ejecutar', 'guardar_anexo');
			
			$.ajax({
				url : 'ejecutar_acciones.php',
				type : 'POST',
				dataType: 'json',
				async: false,
				cache: false,
				contentType: false,
				processData: false,
				data : formData,
				success : function(respuesta){
					if(respuesta.exito){
						notificacion(respuesta.mensaje,'success',4000);
						
						listar_anexos();
					} else {
						notificacion(respuesta.mensaje,'warning',4000);
					}
				}
			});
		});

		$(document).on('click' , '.enlace_anexo' , function() {
			var enlace = $(this).attr("enlace");

			window.open(enlace, '_blank');
		});

		$("#ver_graficos").click(function(){
			window.open('<?php echo($atras); ?>ventanas/graficos/generar_grafico.php?idusuario=<?php echo($idusuario); ?>','_self');
		});
	});

	function botones_usuario(){
		var x_idusu = '<?php echo($idusuario); ?>';
		$.ajax({
 			url: 'ejecutar_acciones.php',
 			type: 'POST',
			dataType: 'json',
			data: {ejecutar: 'botones_usuario', idusu : x_idusu},
			success : function(respuesta){
				if(respuesta.exito){
					$("#botones_usuario").html(respuesta.html);
				} else {
					$("#botones_usuario").html("");
				}
			}
 		});
	}

	function listar_anexos(){
		var x_idusu = '<?php echo($idusuario); ?>';
		$.ajax({
 			url: 'ejecutar_acciones.php',
 			type: 'POST',
			dataType: 'json',
			data: {ejecutar: 'listar_anexos', idusu : x_idusu},
			success : function(respuesta){
				if(respuesta.exito){
					$("#li_anexos").html(respuesta.lista_anexos);
				} else {
					$("#li_anexos").html("");
				}
			}
 		});
	}
	</script>

<div class="page-header row no-gutters py-4">
  <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
    <h3 class="page-title">Usuario</h3>
  </div>
</div>

<div class="row">
  <div id="capa_informacion_usuario" class="col-lg-4">
		<div class="card card-small mb-4">
			<div class="card-header border-bottom">
				<h6 class="m-0"><b>Estado de usuario</b></h6>
			</div>

			<div class="card-body p-0">
				<ul class="list-group list-group-flush">
                    <li class="list-group-item p-3 text-center">
						<form id="usuario_view_image" name="usuario_view_image" method="post" enctype="multipart/form-data">
							<img src="<?php echo($defecto); ?>" class="img-fluid rounded-circle" id="anexar_imagen" style="cursor:pointer;width:250px">
							<input type="file" name="imagen_usuario" id="imagen_usuario" style="display:none">
						</form>
					</li>
					<li class="list-group-item p-3" id="botones_usuario">
						<span class="d-flex mb-2">
                          <i class="fas fa-flag mr-1"></i>
                          <strong class="mr-1"> Estado:</strong>
                          <div id="info_estado">
							<?php
								echo($conexion -> obtener_texto_estado_usuario($idusuario));
							?>
							</div>
                        </span>
                        <span class="d-flex mb-2">
                        	<i class="far fa-calendar-alt mr-1"></i>
                          	<strong class="mr-1"> Mensualidad:</strong>
                          	<div id="info_mensualidad">
							<?php
								echo($conexion -> obtener_texto_mensualidad($idusuario));
							?>
							</div>
                        </span>
                        <span class="d-flex mb-2">
                        	<i class="fas fa-dollar-sign mr-1"></i>
                          	<strong class="mr-1"> Valor:</strong>
                          	<div id="info_valor">
							<?php
								echo($conexion -> obtener_texto_valor($idusuario));
							?>
							</div>
                        </span>
                        <span class="d-flex mb-2">
                        	<i class="far fa-clock mr-1"></i>
                        	<strong class="mr-1"> D&iacute;as faltantes:</strong>
                        	<div id="info_dias_faltantes">
                        		<?php
								echo($conexion -> obtener_dias_faltantes($idusuario));
								?>
                        	</div>
                        </span>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="card card-small mb-4">
			<div class="card-header border-bottom">
		    	<h6 class="m-0">Informaci&oacute;n de usuario</h6>
		    </div>
			<div class="card-body">
				<form id="usuario_view" name="usuario_view" method="post" enctype="multipart/form-data">
					<div class="form-row">
			            <div class="form-group col-md-12">
							<label class="">Identificaci&oacute;n*</label>
							<span class="form-control"><?php echo($datos_usuario[0]["identificacion"]); ?></span>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label class="">Nombres*</label>
							<span class="form-control"><?php echo($datos_usuario[0]["nombres"]); ?></span>
						</div>
						<div class="form-group col-md-6">
							<label class="">Apellidos*</label>
							<span class="form-control"><?php echo($datos_usuario[0]["apellidos"]); ?></span>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label class="">Email</label>
							<span class="form-control"><?php echo($datos_usuario[0]["email"]); ?></span>
						</div>
						<div class="form-group col-md-6">
							<label class="">Celular</label>
							<span class="form-control"><?php echo($datos_usuario[0]["celular"]); ?></span>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div id="capa_informacion_usuario" class="">
			<div class="card card-small mb-4">
				<div class="card-header border-bottom">
					<h6 class="m-0"><b>Anexos</b></h6>
				</div>
				<div id="li_anexos" class="card-body">

				</div>
			</div>
		</div>
	</div>
</div>

<?php echo(pie()); ?>