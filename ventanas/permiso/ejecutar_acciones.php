<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

function guardar_permiso_formulario(){
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
  
  $resultado = $conexion -> insertar('permiso',$campos,$valores);
  if($resultado){
    $retorno["mensaje"] = "permiso registrado!";
    $retorno["exito"] = 1;
    $retorno["idusu"] = $resultado;
  }else{
    $retorno["exito"] = 0;
    $retorno["mensaje"] = "Problemas en la inserci&oacute;n";
  }
  echo(json_encode($retorno));
}
function mostrar_actualizar_permiso_formulario(){
  global $conexion;
  $estadoActivo = '';
  $estadoInactivo = '';
  $retorno = array();
  $retorno["exito"] = 1;
  $idper = @$_REQUEST["idper"];
  
  $datospermisoSql = "select * from permiso where idper=" . $idper;
  $datospermiso = $conexion -> listar_datos($datospermisoSql);  
  
  if($datospermiso[0]["estado"] == 1){
    $estadoActivo = 'selected';
  } else if($datospermiso[0]["estado"] == 2){
    $estadoInactivo = 'selected';
  }
  
  $html = '';
  $html .= '
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Actualizar permiso</h4>
        <form class="" name="permiso_edit" id="permiso_edit">
          <div class="row">
              <div class="col-md-3 form-group">
                <label class="">Nombre*</label>
                <input type="text" id="nombre_edit" name="nombre" class="form-control form-control-sm required" value="' . $datospermiso[0]["nombre"] . '">
              </div>
              
              <div class="col-md-3 form-group">
                <label class="">Etiqueta*</label>
                <input type="text" id="etiqueta_edit" name="etiqueta" class="form-control form-control-sm required" value="' . $datospermiso[0]["etiqueta"] . '">
              </div>
              
              <div class="col-md-3 form-group">
                <label class="">Observaciones</label>
                <textarea style="height:100px" id="observaciones_edit" name="observaciones" class="form-control form-control-sm">' . $datospermiso[0]["observaciones"] . '</textarea>
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
          <button type="button" id="actualizar_permiso_formulario" class="btn btn-primary mb-2">Actualizar</button>
          <button type="button" id="cancelar_actualizar_permiso_formulario" class="btn btn-primary mb-2">Cancelar</button>
          
          <input type="hidden" id="idper" name="idper" value="' . $idper . '">
        </form>
      </div>
    </div>
  </div>
  ';
  
  $retorno["html"] = $html;
  
  echo(json_encode($retorno));
}
function actualizar_permiso_formulario(){
  global $conexion, $atras;
  $idper = @$_REQUEST["idper"];
  $tabla = "permiso";

  $retorno = array();
  
  $valor_guardar = array();

  $campos_validos = array('nombre','etiqueta','observaciones','fk_idemp','estado');
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