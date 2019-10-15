<?php
function obtener_tipo($tipo){
  $cadena = '';
  if($tipo == 1){
    $cadena = 'INGRESO';
  } else if($tipo == 2){
    $cadena = 'EGRESO';
  } else if($tipo == 3){
    $cadena = 'TRASLADO';
  } else if($tipo == 4){
    $cadena = 'BOLSILLO';
  } else if($tipo == 5){
    $cadena = 'SALDO INICIAL BOLSILLO';
  }
  
  return($cadena);
}
function obtener_tipo_pago($tipo_pago){
  $cadena = '';
  if($tipo_pago == 1){
    $cadena = 'EFECTIVO';
  } else if($tipo_pago == 2){
    $cadena = 'BANCO';
  }  else if($tipo_pago == 3){
    $cadena = 'BOLSILLO';
  }
  
  return($cadena);
}
function obtener_bolsillo($idbol){
  global $conexion;
  $cadena = '';
  
  $sql = "select nombre from bolsillo where idbol=" . $idbol;
  $datosBolsillo = $conexion -> listar_datos($sql);
  if($datosBolsillo["cant_resultados"]){
    $cadena = $conexion -> mayuscula($datosBolsillo[0]["nombre"]);
  }
  
  return($cadena);
}
function parsear_valor_ingreso_egreso($valor,$tipo,$categoria){
  $cadena = '';
  if(strpos(strtolower($categoria), 'saldo inicial') !== false){
    $cadena .= '<button type="button" class="btn btn-outline-primary" tipo="' . $tipo . '">' . number_format($valor,0,",",".") . "</button>";
  } else {
    $cadena .= '<button type="button" class="btn btn-outline-primary valores" tipo="' . $tipo . '">' . number_format($valor,0,",",".") . "</button>";
  }
  return($cadena);
}
function accion_ingreso_egreso($iding,$fecha,$empresa){
  global $conexion;
  $datosFecha = explode("-",$fecha);
  $consultaCierre = "select count(*) as cant from cierre_mes a where a.fk_idemp=" . $empresa . " and a.ano=" . $datosFecha[0] . " and a.mes=" . intval($datosFecha[1]) . " and a.estado=1";
  $datosConsultaCierre = $conexion -> listar_datos($consultaCierre);
  
  $cadena = '';
  
  if(!$datosConsultaCierre[0]["cant"]){
    $cadena .= "<button class='btn btn-sm editar_ingreso_egreso' idingreso_egreso='" . $iding . "' title='Editar registro'><i class='mdi mdi-lead-pencil'></i></button>";
    $cadena .= "<button class='btn btn-sm eliminar_ingreso_egreso' iding='" . $iding . "'><i class='mdi mdi-delete'></i></button>";
  }
  
  return($cadena);
}
?>