<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

include_once ($atras . 'librerias.php');

echo(bootstrap_css());
echo(jquery_js());
echo(bootstrap_js());
echo(notificacion());

$adicional = '';
if(@$_SESSION["tipo"] == 1){
	$adicional = '?consulta=1';
}

$conexion -> cerrar_sesion();
?>
<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
.cargando {
    position: fixed;
    z-index: 1000;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    background: rgba( 255, 255, 255, .8 ) url('<?php echo($atras); ?>img/ajax-loader.gif') 50% 50% no-repeat;
}
	</style>
</head>
<body>
	<div class="cargando"></div>
</body>
</html>
<script>
$(document).ready(function() {
	notificacion('Ha cerrado sesi&oacute;n de manera correcta','success',1400);
	setTimeout(function(){
		$(".cargando").hide();
		window.parent.open("<?php echo($atras); ?>index.php<?php echo($adicional); ?>", "_self");
	},1400); 
});
</script>