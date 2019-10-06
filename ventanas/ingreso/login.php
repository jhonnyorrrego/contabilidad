<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

include_once ($atras . 'librerias.php');
echo(tema_majestic_master());
echo(notificacion());

$usuario = '';
$clave = '';
$recordar = '';

if(@$_COOKIE["usuario_contabilidad"]){
  $usuario = $_COOKIE["usuario_contabilidad"];
  
  $recordar = 'checked';
}
if(@$_COOKIE["clave_contabilidad"]){
  $clave = $_COOKIE["clave_contabilidad"];
}
?>
<!doctype html>
<html class="no-js h-100" lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="A high-quality &amp; free Bootstrap admin dashboard template pack that comes with lots of templates and components.">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, maximum-scale=1">

		<script>
$(document).ready(function(){
	$("#ingresar").click(function(){
		var x_identificacion = $("#identificacion").val();
		var x_clave = $("#clave").val();
		var x_recordar = $("#recordar").is(':checked');

		if(!x_identificacion || !x_clave){
			notificacion('Llene los campos','warning',4000);
			return false;
		}

		$.ajax({
			url: 'ejecutar_acciones.php',
			type: 'POST',
			dataType: 'json',
			data: {ejecutar: 'validar_ingreso', identificacion : x_identificacion, clave : x_clave, recordar: x_recordar},
			success : function(html){
				if(html.exito){
					notificacion(html.mensaje,'success',1500);

					setTimeout(function(){window.parent.open("<?php echo($atras); ?>index.php", "_self");},1500); 
				} else {
					notificacion(html.mensaje,'warning',4000);
				}
			}
		});		
	});

	$(document).keypress(function(event) {
	    var keycode = (event.keyCode ? event.keyCode : event.which);
	    if(keycode == '13') {
	      //$("#ingresar").click();
	    }
	});
});
		</script>
	</head>
  <body>
	  <div class="container-scroller">
		<div class="container-fluid page-body-wrapper full-page-wrapper">
		  <div class="content-wrapper d-flex align-items-center auth px-0">
			<div class="row w-100 mx-0">
			  <div class="col-lg-4 mx-auto">
				<div class="auth-form-light text-left py-5 px-4 px-sm-5">
				  <div class="brand-logo">
					<!--img src="../../images/logo.svg" alt="logo"-->
					Contabilidad
				  </div>
				  <h4>¡Hola!</h4>
				  <h6 class="font-weight-light">Inicia sesión para continuar.</h6>
				  <form class="pt-3" onsubmit="return false">
					<div class="form-group">
					  <input type="text" class="form-control form-control-lg" id="identificacion" placeholder="Identificación" value="<?php echo($usuario); ?>">
					</div>
					<div class="form-group">
					  <input type="password" class="form-control form-control-lg" id="clave" placeholder="Clave" value="<?php echo($clave); ?>">
					</div>
					<div class="my-2 d-flex justify-content-between align-items-center">
            <div class="form-check">
              <label class="form-check-label text-muted">
                <input type="checkbox" class="form-check-input" name="recordar[]" id="recordar" value="1" <?php echo($recordar); ?> >
                Recordar datos?
              </label>
            </div>
          </div>
					<div class="mt-3">
					  <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" id="ingresar">INGRESAR</button>
					</div>
				  </form>
				</div>
			  </div>
			</div>
		  </div>
		  <!-- content-wrapper ends -->
		</div>
		<!-- page-body-wrapper ends -->
	  </div>
  </body>
</html>