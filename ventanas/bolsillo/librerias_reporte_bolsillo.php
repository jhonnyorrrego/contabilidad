<?php
function acciones_bolsillo($idbol){
  $cadena = '';
  $cadena .= "<button class='btn btn-sm editar_bolsillo' idbol='" . $idbol . "' title='Editar bolsillo'><i class='mdi mdi-lead-pencil'></i></button>";
  
  return($cadena);
}
function empresa_vinculada($fk_idemp){
  global $conexion;
  $cadena = '';
  
  $sql = "select nombre from empresa where idemp=" . $fk_idemp;
  $datoEmpresa = $conexion -> listar_datos($sql);
  
  $cadena = $datoEmpresa[0]["nombre"];
  
  return($cadena);
}
function estado_funcion($estado){
  $cadena = "";
  if($estado == 1){
    $cadena = "<span class='badge badge-success'>Activo</span>";
  }else if($estado == 2){
    $cadena = "<span class='badge badge-danger'>Inactivo</span>";
  }
  return($cadena);
}
?>