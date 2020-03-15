<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
include_once($atras."ventanas/librerias/librerias_encriptar.php");

global $conexion, $raiz;
$raiz = $atras;

function validar_ingreso(){
	global $conexion;
	$retorno = array();

	$identificacion = @$_REQUEST["identificacion"];
	$clave = @$_REQUEST["clave"];
  $recordar = @$_REQUEST["recordar"];

	//consulta existencia del usuario
	$sql = "select * from usuario a where a.estado=1 and a.identificacion='" . $identificacion . "'";
	$datos_usuario = $conexion -> listar_datos($sql);
	if($datos_usuario["cant_resultados"]){
		$clave_db = $datos_usuario[0]["clave"];
		if(metodo_encriptar($clave) == $clave_db){
			$retorno["exito"] = 1;
			$retorno["mensaje"] = 'Bienvenido';

			$conexion -> iniciar_variables_sesiones($datos_usuario);
      
      if($recordar == 'true'){
        mt_srand(time());
        $rand = mt_rand(1000000,9999999);
        
        setcookie("usuario_contabilidad", $identificacion, time()+(60*60*24*365));
        setcookie("clave_contabilidad", $clave, time()+(60*60*24*365));
      } else {
        setcookie("usuario_contabilidad", '', time()-100);
        setcookie("clave_contabilidad", '', time()-100);
        unset($_COOKIE['usuario_contabilidad']);
        unset($_COOKIE['clave_contabilidad']);
      }

			echo(json_encode($retorno));
		} else {
			$retorno["exito"] = 0;
			$retorno["mensaje"] = 'Clave erronea';

			echo(json_encode($retorno));
		}
	} else {
		$retorno["exito"] = 0;
		$retorno["mensaje"] = 'Usuario no encontrado';

		echo(json_encode($retorno));
	}
}
function validar_ingreso_consulta(){
	global $conexion;
	$retorno = array();

	$identificacion = @$_REQUEST["identificacion"];

	//consulta existencia del usuario
	$sql = "select * from usuario a where a.estado=1 and a.identificacion='" . $identificacion . "' and a.tipo=1";
	$datos_usuario = $conexion -> listar_datos($sql);
	if($datos_usuario["cant_resultados"]){
		$retorno["exito"] = 1;
		$retorno["mensaje"] = 'Bienvenido';

		$conexion -> iniciar_variables_sesiones($datos_usuario);

		echo(json_encode($retorno));
	} else {
		$retorno["exito"] = 0;
		$retorno["mensaje"] = 'Usuario no encontrado';

		echo(json_encode($retorno));
	}
}
function buscar_ingreso_usuario(){
	global $conexion, $atras;
	$retorno = array();
	$identificacion = @$_REQUEST["identificacion"];
	if($identificacion){
		$idusu = $conexion -> obtener_idusu_usuario($identificacion);
		if($idusu){
			$imagen = $conexion -> obtener_imagen_usuario($idusu);
			$datos = $conexion -> obtener_datos_usuario($idusu);

			$retorno["exito"] = 1;
			$retorno["mensaje"] = "Usuario encontrado!";
			$html = "";
			$adicional = "";
			$html2 = "";
			
			$html .= '<h4 class="mb-0"><a href="' . $atras . 'ventanas/usuario/ver_usuario.php?idusuario=' . $idusu . '">' . $datos[0]["nombres"] . " " . $datos[0]["apellidos"] . '</a></h4>';
			$html .= '<span class="text-muted d-block mb-2">' . $identificacion . '</span>';
			$html .= '<input type="hidden" id="valor_idusu" value="' . $idusu . '">';

			$retorno["html"] = $html;
			$retorno["adicional"] = $adicional;
			if(file_exists($atras . $imagen)){
				$retorno["imagen"] = $atras . $imagen;
			} else {
				$retorno["imagen"] = $atras . "img/sin_foto.png";
			}

			$html2 .= '<span class="d-flex mb-2"><i class="far fa-calendar-alt mr-1"></i><strong class="mr-1"> Mensualidad:</strong>';
            $html2 .= '<div id="info_mensualidad">' . $conexion -> obtener_texto_mensualidad($idusu) . '</div></span>';
            $html2 .= '<span class="d-flex mb-2"><i class="far fa-clock mr-1"></i><strong class="mr-1"> D&iacute;as faltantes:</strong>';
            $html2 .= '<div id="info_dias_faltantes">' . $conexion -> obtener_dias_faltantes($idusu) . '</div></span>';

			$retorno["info_adicional"] = $html2;
		} else {
			$retorno["exito"] = 0;
			$retorno["mensaje"] = "No se encontr&oacute; ningun usuario";
		}
	}

	echo(json_encode($retorno));
}
function confirmar_ingreso_usuario(){
	global $conexion;
	$idusu = @$_REQUEST["idusu"];
	$retorno = array();

	$resultado = $conexion -> ingreso_usuario($idusu);
	if($resultado["exito"]){
		$retorno["exito"] = 1;
		$retorno["mensaje"] = "Usuario ingresado";
	} else {
		if($resultado["mensaje"] == 'insertado'){
			$retorno["exito"] = 0;
			$retorno["mensaje"] = "Usuario ya ingresado en el dia de hoy";
		} else if($resultado["mensaje"] == 'inactivo'){
			$retorno["exito"] = 0;
			$retorno["mensaje"] = "Usuario se encuentra inactivo";
		} else if($resultado["mensaje"] == 'fuera_rango'){
			$retorno["exito"] = 0;
			$retorno["mensaje"] = "Usuario se encuentra fuera del rango de la mensualidad";
		} else if($resultado["mensaje"] == 'dias_agotados'){
			$retorno["exito"] = 0;
			$retorno["mensaje"] = "No tiene mas d&iacute;as habilitados";
		}
	}

	echo(json_encode($retorno));
}
function total_ingresados(){
	global $conexion;
	$retorno = array();

	$fecha = date('Y-m-d');
	$total_ingresados = $conexion -> total_ingresados($fecha);

	$retorno["exito"] = 1;
	$retorno["total_ingresados"] = $total_ingresados;

	echo(json_encode($retorno));
}

if(@$_REQUEST["ejecutar"]){
	$_REQUEST["ejecutar"]();
}
?>