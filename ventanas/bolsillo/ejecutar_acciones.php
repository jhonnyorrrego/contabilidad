<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

function guardar_bolsillo_formulario(){
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
  
  $resultado = $conexion -> insertar('bolsillo',$campos,$valores);
  if($resultado){
    $retorno["mensaje"] = "Bolsillo registrado!";
    $retorno["exito"] = 1;
    $retorno["idusu"] = $resultado;
  }else{
    $retorno["exito"] = 0;
    $retorno["mensaje"] = "Problemas en la inserci&oacute;n";
  }
  echo(json_encode($retorno));
}
function mostrar_actualizar_bolsillo_formulario(){
  global $conexion;
  $estadoActivo = '';
  $estadoInactivo = '';
  $retorno = array();
  $opcionesEmpresa = '';
  $adicionalEmpresa = '';
  $retorno["exito"] = 1;
  $idbol = @$_REQUEST["idbol"];
  
  $datosbolsilloSql = "select * from bolsillo where idbol=" . $idbol;
  $datosbolsillo = $conexion -> listar_datos($datosbolsilloSql);  
  
  if($datosbolsillo[0]["estado"] == 1){
    $estadoActivo = 'selected';
  } else if($datosbolsillo[0]["estado"] == 2){
    $estadoInactivo = 'selected';
  }
  
  $empresas = $conexion -> listar_datos('select idemp,nombre from empresa order by nombre asc');
  for ($i=0; $i < $empresas["cant_resultados"]; $i++) {
    $adicionalEmpresa = '';
    if($empresas[$i]["idemp"] == $datosbolsillo[0]["fk_idemp"]){
      $adicionalEmpresa = 'selected';
    }
    $opcionesEmpresa .= "<option value='" . $empresas[$i]["idemp"] . "' " . $adicionalEmpresa . ">" . $empresas[$i]["nombre"] . "</option>";
  }
  
  $html = '';
  $html .= '
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Actualizar Bolsillo</h4>
        <form class="" name="bolsillo_edit" id="bolsillo_edit">
          <div class="row">
              <div class="col-md-3 form-group">
                <label class="">Nombre*</label>
                <input type="text" id="nombre_edit" name="nombre" class="form-control form-control-sm required" value="' . $datosbolsillo[0]["nombre"] . '">
              </div>
              
              <div class="col-md-3 form-group">
                  <label>Empresa vinculada</label>
                  <select class="form-control form-control-sm" id="fk_idemp_edit" name="fk_idemp">
                    <option value="">Seleccione</option>
                    ' . $opcionesEmpresa . '
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
          <button type="button" id="actualizar_bolsillo_formulario" class="btn btn-primary mb-2">Actualizar</button>
          <button type="button" id="cancelar_actualizar_bolsillo_formulario" class="btn btn-primary mb-2">Cancelar</button>
          
          <input type="hidden" id="idbol" name="idbol" value="' . $idbol . '">
        </form>
      </div>
    </div>
  </div>
  ';
  
  $retorno["html"] = $html;
  
  echo(json_encode($retorno));
}
function actualizar_bolsillo_formulario(){
  global $conexion, $atras;
  $idbol = @$_REQUEST["idbol"];
  $tabla = "bolsillo";

  $retorno = array();
  
  $valor_guardar = array();

  $campos_validos = array('nombre','fk_idemp','estado');
  foreach ($campos_validos as $key => $value) {
    if(array_key_exists($value, $_REQUEST)){
      $valor_guardar[] = " " . $value . "='" . @$_REQUEST[$value] . "' ";
    }
  }

  $condicion_update = "idbol=" . $idbol;
  $conexion -> modificar($tabla,$valor_guardar,$condicion_update);

  $retorno["exito"] = 1;
  $retorno["mensaje"] = 'Modificacion realizada';

  echo json_encode($retorno);
}

if(@$_REQUEST["ejecutar"]){
  $_REQUEST["ejecutar"]();
}
?>