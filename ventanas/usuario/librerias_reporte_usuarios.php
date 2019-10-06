<?php
function tipo_usuario_funcion($tipo){
	$cadena = "";
	if($tipo == 1){
		$cadena = "Cliente";
	}else if($tipo == 2){
		$cadena = "Administrador";
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
function acciones_usuario($idusu){
	global $conexion, $raiz;
	$cadena = "";
	$cadena .= "<button class='btn btn-sm editar_usuario' idusuario='" . $idusu . "' title='Editar usuario'><i class='mdi mdi-lead-pencil'></i></button>";
	
	return($cadena);
}
?>