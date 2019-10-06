<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");

global $conexion, $raiz;
$raiz = $atras;

//Inclusiones de la libreria en especifico
include_once($atras."ventanas/bolsillo/librerias_reporte_bolsillo.php");

$inicio = @$_REQUEST["actual_row"];
$cantidad = @$_REQUEST["numfilas"];
$search = @$_REQUEST["buscar"];
$asc_desc = @$_REQUEST["order"];
$campo_ordenar = @$_REQUEST["sort"];

$order = "";
$where_contenedor = array();
$hoy = date('Y-m-d');

if($campo_ordenar){
  $order .= "order by " . $campo_ordenar . " " . $asc_desc;
}

$sql = "select idbol,nombre,fk_idemp,estado from bolsillo where 1=1 " . implode("",$where_contenedor) . " " . $order;
$datos = $conexion -> listar_datos($sql,$inicio,$cantidad);

$arreglo = array();

//Obteniendo el total de registros de la consulta
$sql_cantidad = "select count(*) as cantidad from bolsillo where 1=1 " . implode("",$where_contenedor);
$datos_cantidad = $conexion -> listar_datos($sql_cantidad);
$arreglo["total"] = $datos_cantidad[0]["cantidad"];
//-----

//----------------

for($i=0;$i<$datos["cant_resultados"];$i++){
  $datos[$i]["acciones_bolsillo"]=(acciones_bolsillo($datos[$i]["idbol"]));
  $datos[$i]["empresa_vinculada"]=(empresa_vinculada($datos[$i]["fk_idemp"]));
  $datos[$i]["estado_funcion"]=(estado_funcion($datos[$i]["estado"]));
}
//----------------

unset($datos["sql"]);
unset($datos["cant_resultados"]);

$arreglo["rows"] = $datos;

echo(json_encode($arreglo));
?>