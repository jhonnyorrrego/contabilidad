<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

$conexion -> validar_acceso_sesion();

include_once ($atras . 'librerias.php');
include_once ($atras."ventanas/ingreso_egreso/librerias_reporte_ingreso_egreso.php");

$ingresoBanco = 0;
$ingresoEfectivo = 0;
$egresoBanco = 0;
$egresoEfectivo = 0;
$totalTraslado = 0;
$trasladoRestaEfectivo = 0;//Valor a quitar del saldo total efectivo
$trasladoRestaBanco = 0;//Valor a quitar del saldo total banco
$trasladoSumaBanco = 0;
$totalEfectivo = 0;
$totalBanco = 0;
$grupoOperativos = 0;//Saldo total grupos operativos
$grupoExternos = 0;//Saldo total grupos operativos

$empresa = $_REQUEST["empresa"];
$fechai = $_REQUEST["fechai"];
$fechaf = $_REQUEST["fechaf"];

$whereFiltro = array();

$order = "order by fecha asc";

if($empresa){
  $whereFiltro[] = " and a.fk_idemp=" . $empresa;
}
if($fechai){
  $whereFiltro[] = " and date_format(a.fecha,'%Y-%m-%d %H:%i:%s')>='" . $fechai . "'";
}
if($fechaf){
  $whereFiltro[] = " and date_format(a.fecha,'%Y-%m-%d %H:%i:%s')<='" . $fechaf . "'";
}

//$sql = "select * from ingreso_egreso a where estado=1 " . implode(" ", $whereFiltro) . " order by iding desc";

$sql = "select a.iding,b.nombre as empresa,a.fecha,a.grupo,c.nombre as categoria,a.concepto,a.valor,a.tipo,a.tipo_pago from ingreso_egreso a, empresa b, categoria c where a.estado=1 and a.fk_idemp=b.idemp and a.fk_idcat=c.idcat " . implode("",$whereFiltro) . " " . $order;
$datos = $conexion -> listar_datos($sql);

for ($i=0; $i < $datos["cant_resultados"]; $i++) {
  if(strtolower($datos[$i]["categoria"]) == 'saldo inicial'){
    continue;
  }
  
  if($datos[$i]["tipo"] == 1 && $datos[$i]["tipo_pago"] == 1){//Si tipo es Ingreso y tipo de pago es efectivo
    $ingresoEfectivo += $datos[$i]["valor"];
  } else if($datos[$i]["tipo"] == 1 && $datos[$i]["tipo_pago"] == 2){//Si tipo es Ingreso y tipo de pago es banco
    $ingresoBanco += $datos[$i]["valor"];
  } else if($datos[$i]["tipo"] == 2 && $datos[$i]["tipo_pago"] == 1){//Si tipo es Egreso y tipo de pago es efectivo
    $egresoEfectivo += $datos[$i]["valor"];
  } else if($datos[$i]["tipo"] == 2 && $datos[$i]["tipo_pago"] == 2){//Si tipo es Egreso y tipo de pago es banco
    $egresoBanco += $datos[$i]["valor"];
  } else if($datos[$i]["tipo"] == 3 && $datos[$i]["tipo_pago"] == 1){//Si tipo es traslado y tipo de pago es efectivo
    $trasladoRestaBanco += $datos[$i]["valor"];//Saldo a restar en banco si tipo de pago es efectivo total
    $trasladoSumaEfectivo += $datos[$i]["valor"];//Saldo a sumar al total de efectivo
    
  } else if($datos[$i]["tipo"] == 3 && $datos[$i]["tipo_pago"] == 2){//Si tipo es traslado y tipo de pago es Banco
    $trasladoRestaEfectivo += $datos[$i]["valor"];//Saldo a restar en efectivo si tipo de pago es banco
    $trasladoSumaBanco += $datos[$i]["valor"];//Saldo a sumar a banco
  }
  
  if($datos[$i]["grupo"] == 2){//Gastos operativos
    $grupoOperativos += $datos[$i]["valor"];
  }
  if($datos[$i]["grupo"] == 3){//Gastos externos
    $grupoExternos += $datos[$i]["valor"];
  }
}

$totalEfectivo = $ingresoEfectivo - $egresoEfectivo; //Total en efectivo
$totalBanco = $ingresoBanco - $egresoBanco; //Total en banco

if($trasladoSumaEfectivo > 0){
  $totalEfectivo += $trasladoSumaEfectivo;
}
if($trasladoSumaBanco > 0){
  $totalBanco += $trasladoSumaBanco;
}

if($trasladoRestaBanco > 0){
  $totalBanco -= $trasladoRestaBanco;
}
if($trasladoRestaEfectivo > 0){
  $totalEfectivo -= $trasladoRestaEfectivo;
}

$saldoTotal = ($ingresoEfectivo + $ingresoBanco) - ($egresoEfectivo + $egresoBanco);

$sqlEmpresa = "select * from empresa a where a.idemp=" . $empresa;
$datosEmpresa = $conexion -> listar_datos($sqlEmpresa);

header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=" . str_replace(" ","_",$datosEmpresa[0]["nombre"] . " " . $fechai . " " . $fechaf) . ".xls");  //File name extension was wrong
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
?>
<table>
  <tr>
    <td>Empresa:</td>
    <td><?php echo($datosEmpresa[0]["nombre"]); ?></td>
    <td></td>
  </tr>
  <tr>
    <td>Rango de fechas:</td>
    <td><?php echo($fechai); ?></td>
    <td><?php echo($fechaf); ?></td>
  </tr>
  <tr>
    <td>Total ingreso:</td>
    <td><?php echo(number_format(($ingresoEfectivo + $ingresoBanco),0,",",".")); ?></td>
    <td></td>
  </tr>
  <tr>
    <td>Total egreso:</td>
    <td><?php echo(number_format(($egresoEfectivo + $egresoBanco),0,",",".")); ?></td>
    <td></td>
  </tr>
  <tr>
    <td>Grupos</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td>Gastos operativos</td>
    <td><?php echo(number_format($grupoOperativos,0,",",".")); ?></td>
    <td></td>
  </tr>
  <tr>
    <td>Gastos externos</td>
    <td><?php echo(number_format($grupoExternos,0,",",".")); ?></td>
    <td></td>
  </tr>
  <tr>
    <td>Saldo</td>
    <td><?php echo(number_format($saldoTotal,0,",",".")); ?></td>
    <td></td>
  </tr>
  <tr>
    <td>Fecha</td>
    <td>Grupo</td>
    <td>Categor&iacute;a</td>
    <td>Concepto</td>
    <td>Valor</td>
    <td>Tipo</td>
    <td>Tipo de pago</td>
  </tr>
<?php
for($i=0;$i<$datos["cant_resultados"];$i++){
  echo('<tr>');
  echo('<td>' . $datos[$i]["fecha"] . '</td>');
  echo('<td>' . obtener_grupo($datos[$i]["grupo"]) . '</td>');
  echo('<td>' . $datos[$i]["categoria"] . '</td>');
  echo('<td>' . $datos[$i]["concepto"] . '</td>');
  echo('<td>' . parsear_valor_ingreso_egreso($datos[$i]["valor"]) . '</td>');
  echo('<td>' . obtener_tipo($datos[$i]["tipo"]) . '</td>');
  echo('<td>' . obtener_tipo_pago($datos[$i]["tipo_pago"]) . '</td>');
  echo('</tr>');
}
?>
</table>