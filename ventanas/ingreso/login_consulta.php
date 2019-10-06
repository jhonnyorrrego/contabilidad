<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

include_once ($atras . 'librerias.php');
echo(tema_dashboard_lite());
echo(notificacion());
echo(login_css());
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

		if(!x_identificacion){
			notificacion('Llene el campo','warning',4000);
			return false;
		}

		$.ajax({
			url: 'ejecutar_acciones.php',
			type: 'POST',
			dataType: 'json',
			data: {ejecutar: 'validar_ingreso_consulta', identificacion : x_identificacion},
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
	      $("#ingresar").click();
	    }
	});
});
		</script>
	</head>
	<body class="h-100" style="background: #d2d6de">

<div class="main-content-container container-fluid px-4 p-5">
	<div class="row justify-content-md-center mb-3">
		<img id="main-logo" class="d-inline-block align-top mr-1 img-fluid rounded" src="<?php echo($atras); ?>img/logo.png" alt="logo" style="" alt="GYM Admin">
	</div>
	<div class="row justify-content-md-center">
		<div class="col-lg-3 md-center">
			<div class="card card-small">
				<div class="card-header border-bottom">
					<h6 class="m-0"><b>Consultar datos personales</b></h6>
				</div>
				<div class="card-body">
					<form>
					  	<div class="input-group input-group-seamless mb-3">
							<input type="text" class="form-control" id="identificacion" name="identificacion" placeholder="Identificacion">
							<span class="input-group-append">
                          		<span class="input-group-text">
                            		<i class="fas fa-arrow-left"></i>
                          		</span>
                        	</span>
					  	</div>
					  
						<div class="form-check">
							<button type="button" id="ingresar" class="mb-2 btn btn-outline-success mr-2 float-right">Ingresar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>


	</body>
</html>