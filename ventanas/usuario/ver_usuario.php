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
echo(jquery_validate());

echo(bootstrap_datepicker());
echo(date_format_jquery());

$idusuario = @$_REQUEST["idusuario"];

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
		$("#enlace_reporte_usuarios").addClass("active");
  		$("#navbarDropdown").click();

  		botones_usuario();
		listar_anexos();
		$('html, body').animate({scrollTop:0}, 'slow');
		$('#usuario_view').validate();
		$('#mensualidad').validate();
		$("#formulario_control_medida").validate();

		$('#anexar_imagen').click(function(){
			$("#imagen_usuario").click();
		});
		$('#anexar').click(function(){
			$("#anexos").click();
		});
		
		$("#imagen_usuario").change(function(){
			var formData = new FormData(document.getElementById("usuario_view_image"));
			formData.append('idusu', '<?php echo($idusuario); ?>');
			formData.append('ejecutar', 'guardar_imagen');
			
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
						$("#anexar_imagen").attr("src",respuesta.imagen);

						listar_anexos();
						actualizar_notificacion();

						<?php if($idusuario == @$_SESSION["idusu"]){ ?>
						actualizar_informacion_sesion();//Actualiza la informacion de sesion
						<?php } ?>
					} else {
						notificacion(respuesta.mensaje,'warning',4000);
					}
				}
			});
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

		$(document).on('click' , '.enlace_anexo_eliminar' , function() {
			var x_idane = $(this).attr("idane");
			if(!confirm("Esta seguro de eliminar este anexo?")){
				return false;
			}
			
			if(x_idane){
				$.ajax({
					url : 'ejecutar_acciones.php',
					type : 'POST',
					dataType: 'json',
					data: {ejecutar: 'eliminar_anexo', idane : x_idane},
					success : function(resultado){
						if(resultado.exito){
							notificacion(resultado.mensaje,'success',5000);

							listar_anexos();
						}
					}
				});
			}
		});

		$(document).on('click' , '#actualizar_usuario_formulario' , function() {
			var formulario = $("#usuario_view");
			var resultado = formulario.valid();

			if(resultado){
				var data = $(formulario).serializeArray(); // convert form to array
				data.push({name: "ejecutar", value: 'actualizar_usuario_formulario'});
				data.push({name: "idusu", value: '<?php echo($idusuario); ?>'});

				$.ajax({
					url: 'ejecutar_acciones.php',
					type: 'POST',
					dataType: 'json',
					async: false,
					data: $.param(data),
					success : function(respuesta){
						if(respuesta.exito){
							notificacion(respuesta.mensaje,'success',4000);
							$("#info_estado").html(respuesta.info_estado);

							<?php if($idusuario == @$_SESSION["idusu"]){ ?>
							actualizar_informacion_sesion();//Actualiza la informacion de sesion
							<?php } ?>
						} else {
							notificacion(respuesta.mensaje,'warning',4000);
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
					data: {ejecutar: 'validar_cedula', identificacion : x_identificacion, idusu : "<?php echo($idusuario); ?>"},
					success : function(resultado){
						if(!resultado.exito){
							notificacion(resultado.mensaje,'warning',5000);
						}
					}
				});
			}
		});

		$("#guardar_mensualidad_formulario").click(function(){
			var validar = $("#mensualidad").valid();
			if(!validar){
				return false;
			}

			var x_tipo = $('input:radio[name=tipo_pago]:checked').val();
			var x_fechai = $("#fechai").val();
			var x_fechaf = $("#fechaf").val();
			var x_cantidad_dias = $("#cantidad_dias").val();
			var valor_ =new String($("#valor").val());
            var x_valor = valor_.replace(/\./g,"");

            if(x_tipo == 1){
				if(x_fechai > x_fechaf){
					notificacion('La fecha inicial debe ser menor a la fecha final','warning',4000);
					return false;
				}
			} else if(x_tipo == 2){

			}

			$.ajax({
				url: 'ejecutar_acciones.php',
				type: 'POST',
				dataType: 'json',
				async: false,
				data: {ejecutar: 'agregar_mensualidad', tipo: x_tipo, fechai : x_fechai, fechaf: x_fechaf, cantidad_dias: x_cantidad_dias, valor: x_valor, id: '<?php echo($idusuario); ?>'},
				success : function(respuesta){
					if(respuesta.exito){
						notificacion(respuesta.mensaje,'success',4000);
						botones_usuario();
						actualizar_notificacion();

						$('html, body').animate({ scrollTop: $('#capa_informacion_usuario').offset().top -80 }, 'slow');
					} else {
						notificacion(respuesta.mensaje,'warning',4000);
					}
				}
			});
			
		});

		$("#registrar_medida").click(function(){
			var formulario = $("#formulario_control_medida");
			var validar = formulario.valid();
			if(!validar){
				return false;
			}

			if(!($("#valor_medida").val().length > 3)){
				notificacion('Ingrese los decimales al valor de medida','warning',4000);
				return false;
			}

			var fecha = $("#fecha").val();
			var fecha_array = fecha.split("-");
			var nueva_fecha = fecha_array[0] + "-" + fecha_array[1];
			$("#fecha_mensualidad").val(nueva_fecha);
			
			var data = $(formulario).serializeArray(); // convert form to array
			data.push({name: "ejecutar", value: 'registrar_medida'});
			data.push({name: "fk_idusu", value: '<?php echo($idusuario); ?>'});

			$.ajax({
				url: 'ejecutar_acciones.php',
				type: 'POST',
				dataType: 'json',
				async: false,
				data: $.param(data),
				success : function(respuesta){
					if(respuesta.exito){
						notificacion(respuesta.mensaje,'success',4000);
					} else {
						notificacion(respuesta.mensaje,'warning',4000);
					}
				}
			});
		});

		$("#ver_graficos").click(function(){
			window.open('<?php echo($atras); ?>ventanas/graficos/generar_grafico.php?idusuario=<?php echo($idusuario); ?>','_self');
		});

		$("#tipo").change(function(){
			var tipo = $(this).val();
			if(tipo == 2){//
				$("#clave").addClass("required");
				$("#clave").parent().parent().show(200);
				
				$("#clave2").addClass("required");
				$("#clave2").parent().parent().show(200);
			} else {
				$("#clave").removeClass("required");
				$("#clave").val("");
				$("#clave").parent().parent().hide(200);
				
				$("#clave2").removeClass("required");
				$("#clave2").val("");
				$("#clave2").parent().parent().hide(200);
				
			}
		});

		$("#tipo").trigger("change");

		$("#confirmar_ingreso").click(function(){
			if(!confirm('Está seguro de ingresar este usuario?')){
				return false;
			}

	 		var x_idusu = '<?php echo($idusuario); ?>';
	 		$.ajax({
	 			url: '<?php echo($atras); ?>ventanas/ingreso/ejecutar_acciones.php',
	 			type: 'POST',
				dataType: 'json',
				async: false,
				data: {ejecutar: 'confirmar_ingreso_usuario', idusu : x_idusu},
				success : function(respuesta){
					if(respuesta.exito){
						notificacion(respuesta.mensaje,'success',4000);

						botones_usuario();
					} else {
						notificacion(respuesta.mensaje,'warning',4000);
					}
				}
	 		});
	 	});

	 	$("#eliminar_mensualidad").click(function(){
	 		if(!confirm('Está seguro de eliminar la mensualidad?')){
				return false;
			}

			var x_idusu = '<?php echo($idusuario); ?>';
	 		$.ajax({
	 			url: 'ejecutar_acciones.php',
	 			type: 'POST',
				dataType: 'json',
				async: false,
				data: {ejecutar: 'eliminar_mensualidad', idusu : x_idusu},
				success : function(respuesta){
					if(respuesta.exito){
						notificacion(respuesta.mensaje,'success',4000);
						botones_usuario();
						actualizar_notificacion();
					} else {
						notificacion(respuesta.mensaje,'warning',4000);
					}
				}
	 		});
	 	});

	 	$(".tipo_pago").click(function(){
	 		var tipoPago = $(this).attr('valor');

	 		if(tipoPago == 1){
	 			$("#capa_mensualidad").show();
	 			$("#capa_cantidad_dias").hide();
	 		} else if(tipoPago == 2){
	 			$("#capa_cantidad_dias").show();
	 			$("#capa_mensualidad").hide();
	 		}
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

						<button type="button" class="mb-2 btn btn-sm btn-pill btn-outline-success mr-2" id="confirmar_ingreso">
                      		<i class="fas fa-check-circle mr-1"></i>Ingresar
                  		</button>
					</li>
					<li class="list-group-item p-3" id="botones_usuario">
					</li>
					<li class="list-group-item p-3 text-center">
						<button type="button" class="mb-2 btn btn-sm btn-pill btn-outline-danger mr-2" id="eliminar_mensualidad">
                      		<i class="fas fa-times-circle mr-1"></i>Eliminar mensualidad
                  		</button>
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
						<div class="form-group col-md-6">
			                <label>Tipo de usuario</label>
			                <select class="form-control" id="tipo" name="tipo">
			                	<option value="">Tipo de usuario</option>
								<option value="1" <?php if($datos_usuario[0]["tipo"] == 1)echo("selected"); ?>>Cliente</option>
								<option value="2" <?php if($datos_usuario[0]["tipo"] == 2)echo("selected"); ?>>Administrador</option>
			                </select>
			            </div>
			            <div class="form-group col-md-6">
							<label class="">Identificaci&oacute;n*</label>
							<input type="text" id="identificacion" name="identificacion" class="form-control required number" value="<?php echo($datos_usuario[0]["identificacion"]); ?>">
						</div>
					</div>
					<div class="form-row" style="display: none;">
						<div class="form-group col-md-6">
							<label class="">Nueva clave*</label>
							<input type="password" id="clave" name="clave" class="form-control" value="">
						</div>
						<div class="form-group col-md-6">
							<label class="">Repita su clave*</label>
							<input type="password" id="clave2" class="form-control" equalTo="#clave">
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label class="">Nombres*</label>
							<input type="text" id="nombre" name="nombres" class="form-control required" value="<?php echo($datos_usuario[0]["nombres"]); ?>">
						</div>
						<div class="form-group col-md-6">
							<label class="">Apellidos*</label>
							<input type="text" id="apellido" name="apellidos" class="form-control required" value="<?php echo($datos_usuario[0]["apellidos"]); ?>">
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label class="">Email</label>
							<input type="text" id="email" name="email" class="form-control email" value="<?php echo($datos_usuario[0]["email"]); ?>">
						</div>
						<div class="form-group col-md-6">
							<label class="">Celular</label>
							<input type="text" id="celular" name="celular" class="form-control" value="<?php echo($datos_usuario[0]["celular"]); ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="">Estado*</label>
						<select class="form-control custom-select required" id="estado" name="estado">
							<option value="">Estado</option>
							<option value="1" <?php if($datos_usuario[0]["estado"] == 1)echo("selected"); ?>>Activo</option>
							<option value="2" <?php if($datos_usuario[0]["estado"] == 2)echo("selected"); ?>>Inactivo</option>
						</select>
					</div>
					<button type="button" id="actualizar_usuario_formulario" class="btn btn-outline-success">Actualizar</button>
				</form>
			</div>
		</div>

		<div id="capa_informacion_usuario" class="">
			<div class="card card-small mb-4">
				<div class="card-header border-bottom">
					<h6 class="m-0"><b>Anexos</b></h6>
				</div>

				<div class="card-body border-bottom text-center">
					<form id="anexos_usuario" name="anexos_usuario" method="post" enctype="multipart/form-data">
							<button type="button" class="btn btn-outline-success" id="anexar">Anexar</button>
							<input type="file" name="anexos" id="anexos" style="display:none">
					</form>
				</div>

				<div id="li_anexos" class="card-body">

				</div>
			</div>
		</div>
	</div>
	
	<div class="col-lg-4">
		<div class="card card-small mb-4">
			<div class="card-header border-bottom">
				<h6 class="m-0"><b>Tipo pago</b></h6>
			</div>
			<div class="card-body">
				<form id="mensualidad" name="mensualidad" method="post" enctype="multipart/form-data">

					<div class="form-group">
			        	<label class="">Tipo pago</label>
			        	<br />
			        	<div class="btn-group btn-group-toggle" data-toggle="buttons">
                          	<label class="btn btn-white tipo_pago active" valor="1">
                            	<input type="radio" name="tipo_pago" id="tipo_pago1" value="1" autocomplete="off" class="" checked=""> Mensualidad 
                        	</label>
                          	<label class="btn btn-white tipo_pago" valor="2">
                            	<input type="radio" name="tipo_pago" id="tipo_pago2" value="2" autocomplete="off" class=""> Cantidad d&iacute;as 
                            </label>
                        </div>
				    </div>
					<div id="capa_mensualidad" class="row">
				        <div class="form-group col-6">
				        	<label class="">Fecha inicial</label>
				        	<div class="input-group" id="capa_fechai">
					    		<input type="text" class="form-control date" id="fechai" readonly="" value="<?php echo($fechai); ?>">
					    		<div class="input-group-append" id="ejecutar_fechai">
									<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
								</div>
					    	</div>
					    </div>

					    <div class="form-group col-6">
				        	<label class="">Fecha final</label>
				        	<div class="input-group" id="capa_fechaf">
					    		<input type="text" class="form-control date" id="fechaf" readonly="" value="<?php echo($fechaf); ?>">
					    		<div class="input-group-append" id="ejecutar_fechaf">
									<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
								</div>
					    	</div>
					    </div>
					</div>
					<div id="capa_cantidad_dias" style="display:none">
						<div class="form-group">
							<label class="">Cantidad d&iacute;as*</label>
							<input type="text" id="cantidad_dias" name="cantidad_dias" class="form-control required number" value="">
						</div>
					</div>
				    <div class="form-group">
						<label class="">Valor*</label>
						<input type="text" id="valor" name="valor" class="form-control required" value="">
					</div>

				    <button id="guardar_mensualidad_formulario" class="btn btn-outline-success" type="button">Registrar</button>
				</form>
				     
			    <script type="text/javascript">
			    	$(document).ready(function() {
			    		$("#valor").keyup(function(){
				            var valor=$(this).val().replace(/[^0-9]/g, '');
				            $(this).val(Moneda_r(valor));
				        });

			    		$("#valor_medida").keyup(function(){
				            var valor=$(this).val().replace(/[^0-9]/g, '');
				            $(this).val(Medida_r(valor));
				        });

				        $('#fechai').datepicker({
			            	language : 'es',
			           		format: 'yyyy-mm-dd',
			           		autoclose: true,
			           		setEndDate: new Date('<?php echo($fechaf); ?>')
						}).on('changeDate',function(event){
							var dia_mas = new Date(event.date);
							var fechaf = new Date(event.date);

							var nueva_fecha2 = dia_mas.setDate(dia_mas.getDate() + 1);
							var fecha_formateada2 = $.format.date(nueva_fecha2, "yyyy-MM-dd");
							$('#fechaf').datepicker('setStartDate', new Date(fecha_formateada2));

							var nueva_fecha = fechaf.setMonth(fechaf.getMonth() + 1);
							nueva_fecha = fechaf.setDate(fechaf.getDate());
							var fecha_formateada = $.format.date(nueva_fecha, "yyyy-MM-dd");
							$("#fechaf").val(fecha_formateada);
						});
						$('#fechaf').datepicker({
			            	language : 'es',
			           		format: 'yyyy-mm-dd',
			           		autoclose: true,
			           		setStartDate : new Date('<?php echo($fechai); ?>')
						}).on('changeDate', function(event){
							var endDate = new Date(event.date.valueOf());
							$('#fechai').datepicker('setEndDate', endDate);
						});

						$('#fecha').datepicker({
			            	language : 'es',
			           		format: 'yyyy-mm-dd',
			           		autoclose: true
						});

			            $("#ejecutar_fechai").click(function(){
			            	$("#fechai").datepicker('show');
			            });
			            $("#ejecutar_fechaf").click(function(){
			            	$("#fechaf").datepicker('show');
			            });
			            $("#ejecutar_fecha").click(function(){
			            	$("#fecha").datepicker('show');
			            });
			    	});

		            function Moneda_r(valor){
			            var num = valor.replace(/\./g,'');
			            if(!isNaN(num)){
			                 num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
			                num = num.split('').reverse().join('').replace(/^[\.]/,'');
			                return(num);
			            }
			        }

			        function Medida_r(valor){
			            var num = valor.replace(/\./g,'');
			            if(!isNaN(num)){
			                 num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{2})/g,'$1.');
			                num = num.split('').reverse().join('').replace(/^[\.]/,'');
			                return(num);
			            }
			        }
			    </script>
			</div>
		</div>

		<div class="card card-small mb-4">
			<div class="card-header border-bottom">
				<h6 class="m-0"><b>Control de medidas</b></h6>
			</div>

			<div class="card-body">
				<form id="formulario_control_medida" name="formulario_control_medida" method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="form-group col-6">
			                <label>Medida corporal*</label>
			                <select class="form-control required" id="medida_corporal" name="medida_corporal">
			                	<option value="">Medida corporal</option>
								<?php 
								$opciones_medir = $conexion -> obtener_opciones_campo('medir');
								$opciones_medir_elemento = array();
								
								for ($i=0; $i < $opciones_medir["cant_resultados"]; $i++) { 
									$opciones_medir_elemento[] = '<option value="' . $opciones_medir[$i]["id"] . '">' . $opciones_medir[$i]["nombre"] . '</option>';
								}
								echo(implode("",$opciones_medir_elemento));
								?>
			                </select>
			            </div>

			            <div class="form-group col-6">
				        	<label class="">Fecha</label>
				        	<div class="input-group" id="capa_fecha">
					    		<input type="text" class="form-control date" name="fecha" id="fecha" readonly="" value="<?php echo($fechai); ?>">
					    		<div class="input-group-append" id="ejecutar_fecha">
									<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
								</div>
					    	</div>
					    </div>

			            <div class="form-group col-12">
							<label class="">Medida*</label>
							<input type="text" id="valor_medida" name="valor_medida" class="form-control required" placeholder="01.55" value="" maxlength="5">
						</div>


						<input type="hidden" name="fecha_mensualidad" id="fecha_mensualidad" value="">

						<div class="form-group col-6 text-left">
							<button type="button" id="registrar_medida" class="btn btn-outline-success">Registrar</button>
						</div>
						<div class="form-group col-6 text-right">
							<button type="button" id="ver_graficos" class="btn btn-outline-success">Ver gr&aacute;ficos</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php echo(pie()); ?>