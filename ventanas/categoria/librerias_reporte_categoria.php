<?php
function acciones_categoria($idcat){
  $cadena = '';
  $cadena .= "<button class='btn btn-sm editar_categoria' idcat='" . $idcat . "' title='Editar categoria'><i class='mdi mdi-lead-pencil'></i></button>";
  
  return($cadena);
}
function empresa_vinculada($fk_idemp){
  global $conexion;
  $cadena = '';
  
  $sql = "select nombre from empresa where idemp=" . $fk_idemp;
  $datoEmpresa = $conexion -> listar_datos($sql);
  
  $cadena = @$datoEmpresa[0]["nombre"];
  
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