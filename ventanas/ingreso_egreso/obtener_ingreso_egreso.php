<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");

global $conexion, $raiz;
$raiz = $atras;

//Inclusiones de la libreria en especifico
include_once($atras."ventanas/ingreso_egreso/librerias_reporte_ingreso_egreso.php");

$inicio = @$_REQUEST["actual_row"];
$cantidad = @$_REQUEST["numfilas"];
$search = @$_REQUEST["buscar"];
$asc_desc = @$_REQUEST["order"];
$campo_ordenar = @$_REQUEST["sort"];

$order = "";
$where_contenedor = array();
$hoy = date('Y-m-d');

if(@$_REQUEST["empresa"]){
  if($_REQUEST["empresa"] == -1){
    $where_contenedor[] = " ";
  } else {
    $where_contenedor[] = ' and a.fk_idemp=' . $_REQUEST["empresa"];
  }
} else {
  $where_contenedor[] = ' and a.fk_idemp=';
}
if(@$_REQUEST["fechai"]){
  $where_contenedor[] = " and date_format(a.fecha,'%Y-%m-%d')>='" . $_REQUEST["fechai"] . "'";
}
if(@$_REQUEST["fechaf"]){
  $where_contenedor[] = " and date_format(a.fecha,'%Y-%m-%d')<='" . $_REQUEST["fechaf"] . "'";
}
if(@$_REQUEST["grupo_filtro"]){
  $where_contenedor[] = ' and a.fk_idgru in (' . implode(",",@$_REQUEST["grupo_filtro"]) . ')';
}
if(@$_REQUEST["categoria_filtro"]){
  $where_contenedor[] = ' and a.fk_idcat in (' . implode(",",@$_REQUEST["categoria_filtro"]) . ')';
}
if(@$_REQUEST["bolsillo_filtro"]){
  $where_contenedor[] = ' and a.fk_idbol in (' . $_REQUEST["bolsillo_filtro"] . ')';
}
if(@$_REQUEST["concepto_filtro"]){
  $where_contenedor[] = " and a.concepto like '%" . $_REQUEST["concepto_filtro"] . "%'";
}
if(@$_REQUEST["valor_filtro"]){
  $where_contenedor[] = " and a.valor like '%" . str_replace('.', '', $_REQUEST["valor_filtro"]) . "%'";
}
if(@$_REQUEST["tipo_pago_filtro"]){
  $where_contenedor[] = ' and a.tipo_pago in (' . implode(",",@$_REQUEST["tipo_pago_filtro"]) . ')';
}

if($campo_ordenar){
  $order .= "order by " . $campo_ordenar . " " . $asc_desc;
} else {
  $order .= "order by fecha asc";
}

$sql = "select a.iding,b.nombre as empresa,date_format(a.fecha,'%Y-%m-%d') as fecha,c.nombre as categoria,d.nombre as grupo,a.concepto,a.valor,a.tipo,a.tipo_pago,a.fk_idbol from ingreso_egreso a, empresa b, categoria c, grupo d where a.estado=1 and a.fk_idemp=b.idemp and a.fk_idcat=c.idcat and a.fk_idgru=d.idgru " . implode("",$where_contenedor) . " " . $order;
$datos = $conexion -> listar_datos($sql,$inicio,$cantidad);

$arreglo = array();

//Obteniendo el total de registros de la consulta
$sql_cantidad = "select count(*) as cantidad from ingreso_egreso a, empresa b, categoria c, grupo d where a.estado=1 and a.fk_idemp=b.idemp and a.fk_idcat=c.idcat and a.fk_idgru=d.idgru " . implode("",$where_contenedor);
$datos_cantidad = $conexion -> listar_datos($sql_cantidad);
$arreglo["sql"] = $sql_cantidad;
$arreglo["total"] = @$datos_cantidad[0]["cantidad"];
//-----

//----------------

for($i=0;$i<$datos["cant_resultados"];$i++){
  $datos[$i]["categoria"] = $conexion -> mayuscula($datos[$i]["categoria"]);
  $datos[$i]["grupo"]=($conexion -> mayuscula($datos[$i]["grupo"]));
  $datos[$i]["valor"]=(parsear_valor_ingreso_egreso($datos[$i]["valor"],$datos[$i]["tipo"],$datos[$i]["categoria"]));
  $datos[$i]["tipo"]=(obtener_tipo($datos[$i]["tipo"]));
  $datos[$i]["tipo_pago"]=(obtener_tipo_pago($datos[$i]["tipo_pago"]));
  $datos[$i]["bolsillo"]=(obtener_bolsillo($datos[$i]["fk_idbol"]));
  $datos[$i]["accion"]=(accion_ingreso_egreso($datos[$i]["iding"])); 
}
//----------------

unset($datos["sql"]);
unset($datos["cant_resultados"]);

$arreglo["rows"] = $datos;

echo(json_encode($arreglo));
?>