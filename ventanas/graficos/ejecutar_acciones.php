<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

function obtener_unidad_medida(){
	global $conexion, $atras;
	$retorno = array();
	$retorno["exito"] = 1;

	$datos_filtro = $_REQUEST;

	$datos = $conexion -> obtener_filtro_medida_grafico($datos_filtro);
	unset($datos["datos_medida_corporal"]["cant_resultados"]);
	unset($datos["datos_medida_corporal"]["sql"]);

	$retorno["control_medida"] = $datos["datos_medida_corporal"];
	$retorno["dias_asistidos"] = $datos["datos_dias_asistidos"];

	echo(json_encode($retorno));
}
function obtener_json_datos(){
	global $conexion, $atras;
	$campos = array();
	$valores = array();
	$nuevoArreglo = array();

	$datos_filtro = $_REQUEST;
	
	$data = $conexion -> procesar_filtro_medida_grafico($datos_filtro);

	if($data["cant_resultados"]){
		for ($i=0; $i < $data["cant_resultados"]; $i++) { 
			$fechaArray = explode("-" , $data[$i]["fecha"]);
			$nuevoArreglo[$i]["etiquetas"] = $fechaArray[2] . " " . substr($conexion -> mes($fechaArray[1]),0,3) . " " . substr($fechaArray[0],-2);
			$nuevoArreglo[$i]["valores"] = $data[$i]["valor_medida"];
		}
	}

	echo(json_encode($nuevoArreglo));
}

if(@$_REQUEST["ejecutar"]){
	$_REQUEST["ejecutar"]();
}
?>