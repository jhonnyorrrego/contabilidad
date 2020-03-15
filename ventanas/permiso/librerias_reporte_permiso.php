<?php
function acciones_permiso($idper){
  $cadena = '';
  $cadena .= "<button class='btn btn-sm editar_permiso' idper='" . $idper . "' title='Editar permiso'><i class='mdi mdi-lead-pencil'></i></button>";
  
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