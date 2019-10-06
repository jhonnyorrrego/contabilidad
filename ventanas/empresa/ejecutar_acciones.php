<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

function guardar_empresa_formulario(){
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
  
  $resultado = $conexion -> insertar('empresa',$campos,$valores);
  if($resultado){
    $retorno["mensaje"] = "Empresa registrado!";
    $retorno["exito"] = 1;
    $retorno["idusu"] = $resultado;
  }else{
    $retorno["exito"] = 0;
    $retorno["mensaje"] = "Problemas en la inserci&oacute;n";
  }
  echo(json_encode($retorno));
}
function mostrar_actualizar_empresa_formulario(){
  global $conexion;
  $estadoActivo = '';
  $estadoInactivo = '';
  $tipoNatural = '';
  $tipoJuridico = '';
  $retorno = array();
  $retorno["exito"] = 1;
  $idemp = @$_REQUEST["idemp"];
  
  $datosEmpresaSql = "select * from empresa where idemp=" . $idemp;
  $datosempresa = $conexion -> listar_datos($datosEmpresaSql);
  
  $tipoNatural = '';
  $tipoJuridico = '';
  if($datosempresa[0]["tipo"] == 1){
    $tipoNatural = 'selected';
  } else if($datosempresa[0]["tipo"] == 2){
    $tipoJuridico = 'selected';
  }
  
  
  if($datosempresa[0]["estado"] == 1){
    $estadoActivo = 'selected';
  } else if($datosempresa[0]["estado"] == 2){
    $estadoInactivo = 'selected';
  }
  
  $html = '';
  $html .= '
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Actualizar empresa</h4>
        <form class="" name="empresa_edit" id="empresa_edit">
          <div class="row">
              <div class="col-md-3 form-group">
                <label class="">Nombre*</label>
                <input type="text" id="nombre_edit" name="nombre" class="form-control form-control-sm required" value="' . $datosempresa[0]["nombre"] . '">
              </div>
              <div class="col-md-3 form-group">
                <label class="">NIT</label>
                <input type="number" id="nit_edit" name="nit" class="form-control form-control-sm number" pattern="[0-9]*" value="' . $datosempresa[0]["nit"] . '">
              </div>
              
              <div class="col-md-3 form-group">
                  <label>Tipo contribuyente</label>
                  <select class="form-control form-control-sm" id="tipo_edit" name="tipo">
                    <option value="">Seleccione</option>
                    <option value="1" ' . $tipoNatural . '>Persona natural</option>
                    <option value="2" ' . $tipoJuridico . '>Persona jurídica</option>
                  </select>
              </div>
              
              <div class="col-md-3 form-group">
                  <label class="">Estado*</label>
                  <select class="form-control form-control-sm required" id="estado_edit" name="estado">
                    <option value="">Estado</option>
                    <option value="1" ' . $estadoActivo . '>Activo</option>
                    <option value="2" ' . $estadoInactivo . '>Inactivo</option>
                  </select>
              </div>

          </div>
          <button type="button" id="actualizar_empresa_formulario" class="btn btn-primary mb-2">Actualizar</button>
          <button type="button" id="cancelar_actualizar_empresa_formulario" class="btn btn-primary mb-2">Cancelar</button>
          
          <input type="hidden" id="idemp" name="idemp" value="' . $idemp . '">
        </form>
      </div>
    </div>
  </div>
  ';
  
  $retorno["html"] = $html;
  
  echo(json_encode($retorno));
}
function actualizar_empresa_formulario(){
  global $conexion, $atras;
  $idemp = @$_REQUEST["idemp"];
  $tabla = "empresa";

  $retorno = array();
  
  $valor_guardar = array();

  $campos_validos = array('nombre','nit','tipo','estado');
  foreach ($campos_validos as $key => $value) {
    if(array_key_exists($value, $_REQUEST)){
      $valor_guardar[] = " " . $value . "='" . @$_REQUEST[$value] . "' ";
    }
  }

  $condicion_update = "idemp=" . $idemp;
  $conexion -> modificar($tabla,$valor_guardar,$condicion_update,$idemp);

  $retorno["exito"] = 1;
  $retorno["mensaje"] = 'Modificacion realizada';

  echo json_encode($retorno);
}

if(@$_REQUEST["ejecutar"]){
  $_REQUEST["ejecutar"]();
}
?>