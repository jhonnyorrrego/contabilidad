<?php
$atras="";
require_once 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion;

include_once ($atras . 'librerias.php');
echo(jquery_js());

$movil = $conexion -> detectar_movil(1);

if(@$_SESSION["idusu"]){
	if(@$_SESSION["tipo"] == 1){//Cliente
		$inicio = $atras . "ventanas/usuario/ver_usuario_consulta.php";
	} else if(@$_SESSION["tipo"] == 2){//Administrador
		$inicio = $atras . "ventanas/ingreso_egreso/area_ingreso_egreso.php";
	}
} else {
	if(@$_REQUEST["consulta"]){
		$inicio = $atras . "ventanas/ingreso/login_consulta.php";
	} else {
		$inicio = $atras . "ventanas/ingreso/login.php";
	}
}

?>
<script>
$(document).ready(function(){
  window.open("<?php echo($inicio); ?>", "_self");
});
</script>