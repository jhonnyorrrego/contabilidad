<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");

global $conexion, $raiz;
$raiz = $atras;

//Inclusiones de la libreria en especifico
include_once($atras."ventanas/categoria/librerias_reporte_categoria.php");

$inicio = @$_REQUEST["actual_row"];
$cantidad = @$_REQUEST["numfilas"];
$search = @$_REQUEST["buscar"];
$asc_desc = @$_REQUEST["order"];
$campo_ordenar = @$_REQUEST["sort"];

$order = "";
$where_contenedor = array();
$hoy = date('Y-m-d');

if(@$_REQUEST["fk_idemp_filtro"]){
  $where_contenedor[] = ' and a.fk_idemp in (' . implode(",",$_REQUEST["fk_idemp_filtro"]) . ')';
}
if(@$_REQUEST["nombre_filtro"]){
  $where_contenedor[] = " and a.nombre like '%" . $_REQUEST["nombre_filtro"] . "%'";
}
if(@$_REQUEST["grupo_filtro"]){
  $where_contenedor[] = ' and a.fk_idgru in (' . implode(",",@$_REQUEST["grupo_filtro"]) . ')';
}
if(@$_REQUEST["estado_filtro"]){
  $where_contenedor[] = ' and a.estado in (' . implode(",",@$_REQUEST["estado_filtro"]) . ')';
}

if($campo_ordenar){
  $order .= "order by " . $campo_ordenar . " " . $asc_desc;
} else {
  $order .= "order by idcat desc";
}

$sql = "select a.idcat,a.nombre,a.fk_idemp,d.nombre as grupo,a.estado from categoria a, grupo d where a.fk_idgru=d.idgru " . implode("",$where_contenedor) . " " . $order;
$datos = $conexion -> listar_datos($sql,$inicio,$cantidad);

$arreglo = array();

//Obteniendo el total de registros de la consulta
$sql_cantidad = "select count(*) as cantidad from categoria a, grupo d where a.fk_idgru=d.idgru " . implode("",$where_contenedor);
$datos_cantidad = $conexion -> listar_datos($sql_cantidad);
$arreglo["total"] = $datos_cantidad[0]["cantidad"];
//-----

//----------------

for($i=0;$i<$datos["cant_resultados"];$i++){
  $datos[$i]["nombre"] = $conexion -> mayuscula($datos[$i]["nombre"]);
  $datos[$i]["grupo"] = $conexion -> mayuscula($datos[$i]["grupo"]);
  $datos[$i]["acciones_categoria"]=(acciones_categoria($datos[$i]["idcat"]));
  $datos[$i]["empresa_vinculada"]= $conexion -> mayuscula(empresa_vinculada($datos[$i]["fk_idemp"]));
  $datos[$i]["estado_funcion"]=(estado_funcion($datos[$i]["estado"]));
}
//----------------

unset($datos["sql"]);
unset($datos["cant_resultados"]);

$arreglo["rows"] = $datos;

echo(json_encode($arreglo));
?>