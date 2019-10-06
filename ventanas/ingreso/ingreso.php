<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

include_once ($atras . 'librerias.php');
echo(tema_dashboard_lite());
echo(notificacion());
echo(jquery_validate());

$defecto = $atras . "img/sin_foto.png";
?>
<?php echo(encabezado());?>
<script type="text/javascript">
 $(document).ready(function() {
 	total_ingresados();

 	$("#buscar_usuario").click(function(){
 		var x_identificacion = $("#identificacion").val();
 		if(x_identificacion){
 			$.ajax({
				url: 'ejecutar_acciones.php',
				type: 'POST',
				dataType: 'json',
				async: false,
				data: {ejecutar: 'buscar_ingreso_usuario', identificacion : x_identificacion},
				success : function(respuesta){
					if(respuesta.exito){
						notificacion(respuesta.mensaje,'success',4000);
						$("#informacion_usuario").html(respuesta.html);
						$("#informacion_usuario_adicional").html(respuesta.adicional);
						$("#imagen_usuario").attr("src",respuesta.imagen);

						$("#info_adicional").html(respuesta.info_adicional);

						$("#capa_informacion_usuario").show(200);

						$('html, body').animate({ scrollTop: $('#capa_informacion_usuario').offset().top -80 }, 'slow');
					} else {
						notificacion(respuesta.mensaje,'warning',4000);
						$("#capa_informacion_usuario").hide();
					}
				}
			});
 		} else {
 			notificacion('Ingrese un n&uacute;mero v&aacute;lido','warning',4000);
 			$("#capa_informacion_usuario").hide();
 		}
 	});

 	$("#confirmar_ingreso").click(function(){
 		var x_idusu = $("#valor_idusu").val();
 		$.ajax({
 			url: 'ejecutar_acciones.php',
 			type: 'POST',
			dataType: 'json',
			async: false,
			data: {ejecutar: 'confirmar_ingreso_usuario', idusu : x_idusu},
			success : function(respuesta){
				if(respuesta.exito){
					notificacion(respuesta.mensaje,'success',4000);
					total_ingresados();//Actualizar contador de ingresados
				} else {
					notificacion(respuesta.mensaje,'warning',4000);
				}
			}
 		});
 	});

 	$(document).keypress(function(event) {
	    var keycode = (event.keyCode ? event.keyCode : event.which);
	    if(keycode == '13') {
	      $("#buscar_usuario").click();
	    }
	});
 });

 function total_ingresados(){
 	$.ajax({
 		url: 'ejecutar_acciones.php',
 		type: 'POST',
 		dataType: 'json',
 		data: {ejecutar: 'total_ingresados'},
 		success : function(respuesta){
 			if(respuesta.exito){
 				$("#total_ingresados").html(respuesta.total_ingresados);
 			}
 		}
 	})
 }
</script>

<div class="page-header row no-gutters py-4">
  <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
    <h3 class="page-title">Ingreso</h3>
  </div>
</div>

<div class="row">
	<div class="col-lg-3 col-md-6 col-sm-6 mb-4">
	    <div class="stats-small stats-small--1 card card-small mb-4">
	      	<div class="card-body p-0 d-flex">
	        	<div class="d-flex flex-column m-auto">
	          		<div class="stats-small__data text-center">
	            		<span class="stats-small__label text-uppercase">Ingresados hoy</span>
	            		<h6 class="stats-small__value count my-3" id="total_ingresados"></h6>
	          		</div>
	        	</div>
	        <canvas height="120" class="blog-overview-stats-small-2"></canvas>
	      </div>
	    </div>

	    <div class="card card-small mb-4">
			<div class="card-header border-bottom">
				<h6 class="m-0"><b>Registro de Ingreso</b></h6>
			</div>
			<div class="card-body">
				<form class="">
					<div class="form-group">
						<label class="">Identificaci&oacute;n</label>
						<input type="number" id="identificacion" class="form-control" placeholder="Identificaci&oacute;n" pattern="[0-9]*">
					</div>
					
					<div class="form-group">
						<label class="">Huella dactilar</label>
						<input type="text" id="huella" class="form-control" placeholder="Huella Dactilar">
					</div>
					<!-- Sign up button -->
					<button id="buscar_usuario" class="btn btn-outline-success" type="button">Buscar usuario</button>
				</form>
			</div>
		</div>
	</div>

	<div id="capa_informacion_usuario" class="col-lg-4" style="display: none;">
		<div class="card card-small">
			<div class="card-header border-bottom">
				<h6 class="m-0"><b>Informaci&oacute;n de usuario</b></h6>
			</div>
			<div class="card-body p-0">
				<ul class="list-group list-group-flush">
					<li class="list-group-item p-3 text-center">
						<img src="<?php echo($defecto); ?>" class="img-fluid rounded-circle" id="imagen_usuario" style="width:250px">
						<div id="informacion_usuario">
                    	</div>
                    	<button type="button" class="mb-2 btn btn-sm btn-pill btn-outline-success mr-2" id="confirmar_ingreso">
                      		<i class="fas fa-check-circle mr-1"></i>Confirmar
                  		</button>
					</li>
                    <li class="list-group-item p-3" id="info_adicional">

                    </li>
				</ul>
			</div>
		</div>
	</div>
</div>

<?php echo(pie()); ?>