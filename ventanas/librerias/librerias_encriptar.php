<?php
function metodo_encriptar($cadena){
	$nueva_cadena = md5(md5($cadena));
	//$nueva_cadena = $cadena;
	return($nueva_cadena);
}
?>