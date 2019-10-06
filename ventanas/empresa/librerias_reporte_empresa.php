<?php
function acciones_empresa($idemp){
  $cadena = '';
  $cadena .= "<button class='btn btn-sm editar_empresa' idemp='" . $idemp . "' title='Editar empresa'><i class='mdi mdi-lead-pencil'></i></button>";
  
  return($cadena);
}
function tipo_empresa_funcion($tipo){
  $cadena = '';
  if($tipo == 1){
    $cadena = 'Persona natural';
  } else if($tipo == 2){
    $cadena = 'Persona jurídica';
  }
  
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