<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

function guardar_perfil_formulario(){
  global $conexion, $atras;
  $hoy = date('Y-m-d H:i:s');
  $retorno = array();

  unset($_REQUEST["ejecutar"]);
  unset($_REQUEST["_sd_demo_page_promo"]);
  unset($_REQUEST["_sd_cs_visible"]);
  unset($_REQUEST["PHPSESSID"]);
  
  foreach($_REQUEST as $llave => $val){
      $campos[] = $llave;
      $valores[] = "'" . $val . "'";
  }
  $campos[] = "fecha_creacion";
  $campos[] = "fk_idusu";
  
  $valores[] = "date_format('" . $hoy . "', '%Y-%m-%d %H:%i:%s')";
  $valores[] = @$_SESSION["idusu"];
  
  $resultado = $conexion -> insertar('perfil',$campos,$valores);
  if($resultado){
    $retorno["mensaje"] = "perfil registrado!";
    $retorno["exito"] = 1;
    $retorno["idusu"] = $resultado;
  }else{
    $retorno["exito"] = 0;
    $retorno["mensaje"] = "Problemas en la inserci&oacute;n";
  }
  echo(json_encode($retorno));
}
function mostrar_actualizar_perfil_formulario(){
  global $conexion;
  $estadoActivo = '';
  $estadoInactivo = '';
  $retorno = array();
  $retorno["exito"] = 1;
  $idper = @$_REQUEST["idper"];
  
  $datosperfilSql = "select * from perfil where idper=" . $idper;
  $datosperfil = $conexion -> listar_datos($datosperfilSql);  
  
  if($datosperfil[0]["estado"] == 1){
    $estadoActivo = 'selected';
  } else if($datosperfil[0]["estado"] == 2){
    $estadoInactivo = 'selected';
  }
  
  $html = '';
  $html .= '
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Actualizar perfil</h4>
        <form class="" name="perfil_edit" id="perfil_edit">
          <div class="row">              
              <div class="col-md-3 form-group">
                <label class="">Etiqueta*</label>
                <input type="text" id="etiqueta_edit" name="etiqueta" class="form-control form-control-sm required" value="' . $datosperfil[0]["etiqueta"] . '">
              </div>

              <div class="col-md-3 form-group">
                  <label class="">Estado*</label>
                  <select class="form-control form-control-sm required" id="estado" name="estado">
                    <option value="">Estado</option>
                    <option value="1" ' . $estadoActivo . '>Activo</option>
                    <option value="2" ' . $estadoInactivo . '>Inactivo</option>
                  </select>
              </div>

          </div>
          <button type="button" id="actualizar_perfil_formulario" class="btn btn-primary mb-2">Actualizar</button>
          <button type="button" id="cancelar_actualizar_perfil_formulario" class="btn btn-primary mb-2">Cancelar</button>
          
          <input type="hidden" id="idper" name="idper" value="' . $idper . '">
        </form>
      </div>
    </div>
  </div>
  ';
  
  $retorno["html"] = $html;
  
  echo(json_encode($retorno));
}
function actualizar_perfil_formulario(){
  global $conexion, $atras;
  $idper = @$_REQUEST["idper"];
  $tabla = "perfil";

  $retorno = array();
  
  $valor_guardar = array();

  $campos_validos = array('etiqueta','estado');
  foreach ($campos_validos as $key => $value) {
    if(array_key_exists($value, $_REQUEST)){
      $valor_guardar[] = " " . $value . "='" . @$_REQUEST[$value] . "' ";
    }
  }

  $condicion_update = "idper=" . $idper;
  $conexion -> modificar($tabla,$valor_guardar,$condicion_update);

  $retorno["exito"] = 1;
  $retorno["mensaje"] = 'Modificacion realizada';

  echo json_encode($retorno);
}

if(@$_REQUEST["ejecutar"]){
  $_REQUEST["ejecutar"]();
}
?>