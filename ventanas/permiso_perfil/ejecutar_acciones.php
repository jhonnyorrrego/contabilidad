<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

function guardar_permiso_perfil_formulario(){
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
  
  $resultado = $conexion -> insertar('permiso_perfil',$campos,$valores);
  if($resultado){
    $retorno["mensaje"] = "Asignacion de permiso registrado!";
    $retorno["exito"] = 1;
    $retorno["idusu"] = $resultado;
  }else{
    $retorno["exito"] = 0;
    $retorno["mensaje"] = "Problemas en la inserci&oacute;n";
  }
  echo(json_encode($retorno));
}
function mostrar_actualizar_permiso_perfil_formulario(){
  global $conexion;
  $estadoActivo = '';
  $estadoInactivo = '';
  $opcionesPermiso = '';
  $adicionalPermiso = '';
  $opcionesPerfl = '';
  $adicionalPerfil = '';
  $retorno = array();
  $retorno["exito"] = 1;
  $idper = @$_REQUEST["idper"];
  
  $datospermiso_perfilSql = "select * from permiso_perfil where idper=" . $idper;
  $datospermiso_perfil = $conexion -> listar_datos($datospermiso_perfilSql);  
  
  if($datospermiso_perfil[0]["estado"] == 1){
    $estadoActivo = 'selected';
  } else if($datospermiso_perfil[0]["estado"] == 2){
    $estadoInactivo = 'selected';
  }
  
  $permisos = $conexion -> listar_datos('select idper,etiqueta from permiso order by etiqueta asc');
  for ($i=0; $i < $permisos["cant_resultados"]; $i++) {
    $adicionalPermiso = '';
    if($permisos[$i]["idper"] == $datospermiso_perfil[0]["fk_idperm"]){
      $adicionalPermiso = 'selected';
    }
    $opcionesPermiso .= "<option value='" . $permisos[$i]["idper"] . "' " . $adicionalPermiso . ">" . $permisos[$i]["etiqueta"] . "</option>";
  }
  
  $perfiles = $conexion -> listar_datos('select idper,etiqueta from perfil order by etiqueta asc');
  for ($i=0; $i < $perfiles["cant_resultados"]; $i++) {
    $adicionalPerfil = '';
    if($perfiles[$i]["idper"] == $datospermiso_perfil[0]["fk_idperf"]){
      $adicionalPerfil = 'selected';
    }
    $opcionesPerfl .= "<option value='" . $perfiles[$i]["idper"] . "' " . $adicionalPerfil . ">" . $perfiles[$i]["etiqueta"] . "</option>";
  }
  
  $html = '';
  $html .= '
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Actualizar asignación de permiso</h4>
        <form class="" name="permiso_perfil_edit" id="permiso_perfil_edit">
          <div class="row">
              
              <div class="col-md-3 form-group">
                  <label>Permiso</label>
                  <select class="form-control form-control-sm" id="fk_idperm_edit" name="fk_idperm">
                    <option value="">Seleccione</option>
                    ' . $opcionesPermiso . '
                  </select>
              </div>
              
              <div class="col-md-3 form-group">
                  <label>Perfil</label>
                  <select class="form-control form-control-sm" id="fk_idperf_edit" name="fk_idperf">
                    <option value="">Seleccione</option>
                    ' . $opcionesPerfl . '
                  </select>
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
          <button type="button" id="actualizar_permiso_perfil_formulario" class="btn btn-primary mb-2">Actualizar</button>
          <button type="button" id="cancelar_actualizar_permiso_perfil_formulario" class="btn btn-primary mb-2">Cancelar</button>
          
          <input type="hidden" id="idper" name="idper" value="' . $idper . '">
        </form>
      </div>
    </div>
  </div>
  ';
  
  $retorno["html"] = $html;
  
  echo(json_encode($retorno));
}
function actualizar_permiso_perfil_formulario(){
  global $conexion, $atras;
  $idper = @$_REQUEST["idper"];
  $tabla = "permiso_perfil";

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