<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

function guardar_categoria_formulario(){
  global $conexion, $atras;
  $hoy = date('Y-m-d H:i:s');
  $fk_idusu = $fk_idusu = @$_SESSION["idusu"];
  $retorno = array();
  
  $nombre = @$_REQUEST["nombre"];
  $grupo = @$_REQUEST["grupo"];
  $empresa = @$_REQUEST["fk_idemp"];
  $estado = @$_REQUEST["estado"];
  
  $campos = array('nombre', 'fk_idemp','fk_idgru', 'fecha_creacion', 'fk_idusu');
  $valores = array();
  $valores[] = "'" . $nombre . "'";
  $valores[] = $empresa;
  $valores[] = $grupo;
  $valores[] = "date_format('" . $hoy . "', '%Y-%m-%d %H:%i:%s')";
  $valores[] = $fk_idusu;
  
  $resultado = $conexion -> insertar('categoria',$campos,$valores);
  if($resultado){
    $retorno["mensaje"] = "Categoria registrado!";
    $retorno["exito"] = 1;
    $retorno["idusu"] = $resultado;
  }else{
    $retorno["exito"] = 0;
    $retorno["mensaje"] = "Problemas en la inserci&oacute;n";
  }
  echo(json_encode($retorno));
}
function mostrar_actualizar_categoria_formulario(){
  global $conexion;
  $estadoActivo = '';
  $estadoInactivo = '';
  $retorno = array();
  $opcionesEmpresa = '';
  $adicionalEmpresa = '';
  $retorno["exito"] = 1;
  $idcat = @$_REQUEST["idcat"];
  
  $datoscategoriaSql = "select * from categoria where idcat=" . $idcat;
  $datoscategoria = $conexion -> listar_datos($datoscategoriaSql);
  
  $grupos = $conexion -> obtener_grupos($datoscategoria[0]["fk_idgru"],'grupo_edit',1);
  
  if($datoscategoria[0]["estado"] == 1){
    $estadoActivo = 'selected';
  } else if($datoscategoria[0]["estado"] == 2){
    $estadoInactivo = 'selected';
  }
  
  $empresas = $conexion -> listar_datos('select idemp,nombre from empresa order by nombre asc');
  for ($i=0; $i < $empresas["cant_resultados"]; $i++) {
    $adicionalEmpresa = '';
    if($empresas[$i]["idemp"] == $datoscategoria[0]["fk_idemp"]){
      $adicionalEmpresa = 'selected';
    }
    $opcionesEmpresa .= "<option value='" . $empresas[$i]["idemp"] . "' " . $adicionalEmpresa . ">" . $empresas[$i]["nombre"] . "</option>";
  }
  
  $html = '';
  $html .= '
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Actualizar categoria</h4>
        <form class="" name="categoria_edit" id="categoria_edit">
          <div class="row">
              <div class="col-md-3 form-group">
                <label class="">Nombre*</label>
                <input type="text" id="nombre_edit" name="nombre" class="form-control form-control-sm required" value="' . $datoscategoria[0]["nombre"] . '">
              </div>
              
              <div class="col-md-3 form-group">
                  <label>Empresa vinculada</label>
                  <select class="form-control form-control-sm" id="fk_idemp_edit" name="fk_idemp">
                    <option value="">Seleccione</option>
                    ' . $opcionesEmpresa . '
                  </select>
              </div>
              
              <div class="form-group col-md-2">
                <label>Grupo*</label>
                  <div id="capa_edit_grupo">' . $grupos["opciones_adicionar"] . '
                  </div>
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
          <button type="button" id="actualizar_categoria_formulario" class="btn btn-primary mb-2">Actualizar</button>
          <button type="button" id="cancelar_actualizar_categoria_formulario" class="btn btn-primary mb-2">Cancelar</button>
          
          <input type="hidden" id="idcat" name="idcat" value="' . $idcat . '">
        </form>
      </div>
    </div>
  </div>
  ';
  
  $retorno["html"] = $html;
  
  echo(json_encode($retorno));
}
function actualizar_categoria_formulario(){
  global $conexion, $atras;
  $idcat = @$_REQUEST["idcat"];
  $tabla = "categoria";

  $retorno = array();
  
  $valoresModificar = array();  
  $valoresModificar[] = "nombre='" . @$_REQUEST["nombre"] . "'";
  $valoresModificar[] = "fk_idemp='" . @$_REQUEST["fk_idemp"] . "'";
  $valoresModificar[] = "fk_idgru='" . @$_REQUEST["grupo_edit"] . "'";
  $valoresModificar[] = "estado='" . @$_REQUEST["estado"] . "'";

  $condicion_update = "idcat=" . $idcat;
  $conexion -> modificar($tabla,$valoresModificar,$condicion_update);

  $retorno["exito"] = 1;
  $retorno["mensaje"] = 'Modificacion realizada';

  echo json_encode($retorno);
}

if(@$_REQUEST["ejecutar"]){
  $_REQUEST["ejecutar"]();
}
?>