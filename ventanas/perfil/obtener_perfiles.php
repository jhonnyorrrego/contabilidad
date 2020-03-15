<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");

global $conexion, $raiz;
$raiz = $atras;

//Inclusiones de la libreria en especifico
include_once($atras."ventanas/perfil/librerias_reporte_perfil.php");

$inicio = @$_REQUEST["actual_row"];
$cantidad = @$_REQUEST["numfilas"];
$search = @$_REQUEST["buscar"];
$asc_desc = @$_REQUEST["order"];
$campo_ordenar = @$_REQUEST["sort"];

$order = "";
$where_contenedor = array();
$hoy = date('Y-m-d');

if(@$_REQUEST["etiqueta_filtro"]){
  $where_contenedor[] = " and a.etiqueta like '%" . $_REQUEST["etiqueta_filtro"] . "%'";
}
if(@$_REQUEST["estado_filtro"]){
  $where_contenedor[] = ' and a.estado in (' . implode(",",@$_REQUEST["estado_filtro"]) . ')';
}

if($campo_ordenar){
  $order .= "order by " . $campo_ordenar . " " . $asc_desc;
}

$sql = "select idper,etiqueta,estado from perfil a where 1=1 " . implode("",$where_contenedor) . " " . $order;
$datos = $conexion -> listar_datos($sql,$inicio,$cantidad);

$arreglo = array();

//Obteniendo el total de registros de la consulta
$sql_cantidad = "select count(*) as cantidad from perfil a where 1=1 " . implode("",$where_contenedor);
$datos_cantidad = $conexion -> listar_datos($sql_cantidad);
$arreglo["total"] = $datos_cantidad[0]["cantidad"];
//-----

//----------------

for($i=0;$i<$datos["cant_resultados"];$i++){
  $datos[$i]["acciones_perfil"]=(acciones_perfil($datos[$i]["idper"]));
  $datos[$i]["estado_funcion"]=(estado_funcion($datos[$i]["estado"]));
}
//----------------

unset($datos["sql"]);
unset($datos["cant_resultados"]);

$arreglo["rows"] = $datos;

echo(json_encode($arreglo));
?>