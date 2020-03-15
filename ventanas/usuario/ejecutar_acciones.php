<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

function guardar_usuario_formulario(){
	global $conexion, $atras;
	include_once($atras . "ventanas/librerias/librerias_encriptar.php");

	$retorno = array();
	
	$existencia = consultar_existencia($_REQUEST["identificacion"],2);
	
	if($existencia){//Identificacion ya se encuentra registrada
		$retorno["mensaje"] = "Identificacion existente";
		$retorno["exito"] = 0;
		echo(json_encode($retorno));
		die();
	}
	unset($_REQUEST["ejecutar"]);
  unset($_REQUEST["_sd_demo_page_promo"]);
  unset($_REQUEST["_sd_cs_visible"]);
  unset($_REQUEST["PHPSESSID"]);
	
	foreach($_REQUEST as $llave => $val){
		if($llave == 'clave'){
			$campos[] = $llave;
			$valores[] = "'" . metodo_encriptar($val) . "'";
		} else {
			$campos[] = $llave;
			$valores[] = "'" . $val . "'";
		}
	}
	$campos[] = "fecha";
	$valores[] = "date_format('" . date('Y-m-d H:i:s') . "', '%Y-%m-%d %H:%i:%s')";
	
	$resultado = $conexion -> insertar('usuario',$campos,$valores);
	if($resultado){
		$retorno["mensaje"] = "Usuario registrado!";
		$retorno["exito"] = 1;
		$retorno["idusu"] = $resultado;
	}else{
		$retorno["exito"] = 0;
		$retorno["mensaje"] = "Problemas en la inserci&oacute;n";
	}
	echo(json_encode($retorno));
}
function mostrar_actualizar_usuario_formulario(){
  global $conexion;
  $retorno = array();
  $retorno["exito"] = 1;
  
  $idusu = @$_REQUEST["idusu"];
  $datosUsuario = $conexion -> obtener_datos_usuario($idusu);
  
  $tipoCliente = '';
  $tipoAdmin = '';
  
  $capaClave = 'display:none';
  
  $estadoActivo = '';
  $estadoInacctivo = '';
  if($datosUsuario[0]["estado"] == 1){
    $estadoActivo = 'selected';
  } else if($datosUsuario[0]["estado"] == 2){
    $estadoInacctivo = 'selected';
  }
  
  $perfiles = $conexion -> listar_datos('select idper,etiqueta from perfil order by etiqueta asc');
  for ($i=0; $i < $perfiles["cant_resultados"]; $i++) {
    $adicionalPerfil = '';
    if($perfiles[$i]["idper"] == $datosUsuario[0]["tipo"]){
      $adicionalPerfil = 'selected';
    }
    $opcionesPerfl .= "<option value='" . $perfiles[$i]["idper"] . "' " . $adicionalPerfil . ">" . $perfiles[$i]["etiqueta"] . "</option>";
  }
  
  $html = '';
  $html .= '
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Actualizar usuario</h4>
        <form class="" name="usuario_edit" id="usuario_edit">
          <div class="row">
              <div class="col-md-3 form-group">
                  <label>Tipo de usuario</label>
                  <select class="form-control form-control-sm" id="tipo_edit" name="tipo">
                    <option value="">Tipo de usuario</option>              
                    ' . $opcionesPerfl . '
                  </select>
              </div>

              <div class="col-md-3 form-group">
                <label class="">Identificaci&oacute;n*</label>
                <input type="number" id="identificacion_edit" name="identificacion" class="form-control form-control-sm required number" pattern="[0-9]*" value="' . $datosUsuario[0]["identificacion"] . '">
              </div>
              <div class="col-md-3 form-group">
                 <label class="">Clave</label>
                <input type="password" id="clave_edit" name="clave" class="form-control form-control-sm">
              </div>
              <div class="col-md-3 form-group">
                  <label class="">Repita su clave</label>
                  <input type="password" id="clave2_edit" class="form-control form-control-sm" equalTo="#clave_edit">
              </div>
              <div class="col-md-3 form-group">
                  <label class="">Nombres*</label>
                    <input type="text" id="nombre_edit" name="nombres" class="form-control form-control-sm required" value="' . $datosUsuario[0]["nombres"] . '">
              </div>
              <div class="col-md-3 form-group">
                  <label class="">Apellidos*</label>
                  <input type="text" id="apellido_edit" name="apellidos" class="form-control form-control-sm required" value="' . $datosUsuario[0]["apellidos"] . '">
              </div>
              <div class="col-md-3 form-group">
                  <label class="">Email</label>
                  <input type="text" id="email_edit" name="email" class="form-control form-control-sm email" value="' . $datosUsuario[0]["email"] . '">
              </div>
              <div class="col-md-3 form-group">
                  <label class="">Celular</label>
                  <input type="text" id="celular_edit" name="celular" class="form-control form-control-sm" value="' . $datosUsuario[0]["celular"] . '">
              </div>
              <div class="col-md-3 form-group">
                  <label class="">Estado*</label>
                  <select class="form-control form-control-sm required" id="estado_edit" name="estado">
                    <option value="">Estado</option>
                    <option value="1" ' . $estadoActivo . '>Activo</option>
                    <option value="2" ' . $estadoInacctivo . '>Inactivo</option>
                  </select>
              </div>
          </div>
          <button type="button" id="actualizar_usuario_formulario" class="btn btn-primary mb-2">Actualizar</button>
          <button type="button" id="cancelar_actualizar_usuario_formulario" class="btn btn-primary mb-2">Cancelar</button>
          
          <input type="hidden" id="idusu" name="idusu" value="' . $idusu . '">
        </form>
      </div>
    </div>
  </div>
  ';
  
  $retorno["html"] = $html;
  
  echo(json_encode($retorno));
}
function actualizar_usuario_formulario(){
	global $conexion, $atras;
  include_once($atras . "ventanas/librerias/librerias_encriptar.php");
  
	$idusu = @$_REQUEST["idusu"];
	$tabla = "usuario";

	$retorno = array();
  
  if(@$_REQUEST["idusu"]){
    $idusu = $_REQUEST["idusu"];
    $where = "idusu<>" . $idusu;
  }
  
  $existencia = consultar_existencia($_REQUEST["identificacion"],2,$where);
  
  if($existencia){//Identificacion ya se encuentra registrada
    $retorno["mensaje"] = "Identificacion existente";
    $retorno["exito"] = 0;
    echo(json_encode($retorno));
    die();
  }
  
	$valor_guardar = array();

	$campos_validos = array('tipo','identificacion','nombres','apellidos','email','celular','estado');
	foreach ($campos_validos as $key => $value) {
		if(array_key_exists($value, $_REQUEST)){
			$valor_guardar[] = " " . $value . "='" . @$_REQUEST[$value] . "' ";
		}
	}
	if(@$_REQUEST["clave"]){
		$valor_guardar[] = " clave='" . metodo_encriptar($_REQUEST["clave"]) . "' ";
	}

	$condicion_update = "idusu=" . $idusu;
	$conexion -> modificar($tabla,$valor_guardar,$condicion_update,$idusu);

	$retorno["exito"] = 1;
	$retorno["mensaje"] = 'Modificacion realizada';

	echo json_encode($retorno);
}
function validar_cedula(){
	global $conexion;
	$where = "";
	$identificacion = @$_REQUEST["identificacion"];
	if(@$_REQUEST["idusu"]){
		$idusu = $_REQUEST["idusu"];
		$where = "idusu<>" . $idusu;
	}
	$resultado = consultar_existencia($identificacion,1,$where);
	
	echo(json_encode($resultado));
}
function consultar_existencia($identificacion,$tipo_retorno=1,$where=false){
	global $conexion;
	$existe = False;
	
	$sql = "select identificacion from usuario where identificacion=" . $identificacion;
	if($where){
		$sql .= " and " . $where;
	}
	$datos = $conexion -> listar_datos($sql);
	if($datos["cant_resultados"]){
		$existe = True;
	}
	
	if($tipo_retorno == 1){
		$retorno = array();
		
		if($existe){
			$retorno["mensaje"] = "Identificacion existente";
			$retorno["exito"] = 0;
		}else{
			$retorno["exito"] = 1;
		}
		return($retorno);
	}else if($tipo_retorno == 2){
		return($existe);
	}
}

if(@$_REQUEST["ejecutar"]){
	$_REQUEST["ejecutar"]();
}
?>