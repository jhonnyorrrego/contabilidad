<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

function guardar_ingreso_egreso(){
  global $conexion;
  $tipo = false;
  $tabla = 'ingreso_egreso';
  $retorno = array();
  
  $retorno["exito"] = 0;
  $retorno["mensaje"] = "Problemas al insertar";
  
  $empresa = "'" . @$_REQUEST["empresa"] . "'";
  $fecha = @$_REQUEST["fecha"];
  $grupo = "'" . @$_REQUEST["grupo"] . "'";
  $categoria = "'" . @$_REQUEST["categoria"] . "'";
  $concepto = "'" . @$_REQUEST["concepto"] . "'";
  $valor = "'" . str_replace('.', '', @$_REQUEST["valor"]) . "'";
  $tipo_pago = "'" . @$_REQUEST["tipo_pago"] . "'";
  $bolsillo = "0";
  
  $fechaHoy = date('Y-m-d H:i:s');
  $fk_idusu = @$_SESSION["idusu"];
  
  if(@$_REQUEST["grupo"] == 1){
    $tipo  = "'1'";//Ingreso
  } else if(@$_REQUEST["grupo"] == 2 || @$_REQUEST["grupo"] == 3){
    $tipo  = "'2'";//Egreso
  } else if(@$_REQUEST["grupo"] == 4){
    $tipo  = "'3'";//Traslado
    $categoria = "'-3'";
  } else if(@$_REQUEST["grupo"] == 5){
    $tipo  = "'4'";//Bolsillo
  } else if(@$_REQUEST["grupo"] == 6){
    $tipo  = "'5'";//Saldo inicial Bolsillo
  }
  
  if(@$_REQUEST["tipo_pago"] == 3 || @$_REQUEST["grupo"] == 6){//Si tipo de pago es bolsillo o si grupo es saldo inicial bolsillo, obtengo el bolsillo a seleccionar
    $bolsillo = "'" . @$_REQUEST["bolsillo"] . "'";
  }

  $consultaCierreMes = validarCierreMes($empresa,$fecha);  
  if($consultaCierreMes["exito"]){//Existe un cierre y no debe dejar registrar
    $retorno["exito"] = 0;
    $retorno["mensaje"] = 'Este mes ya se encuentra cerrado';
    echo(json_encode($retorno));
    die();
  }
  
  if(@$_REQUEST["categoria"] == -1){
    $campoCategoria = array('nombre', 'fk_idemp','fk_idgru', 'fecha_creacion', 'fk_idusu');
    $valoresCategoria = array();
    $valoresCategoria[] = "'" . @$_REQUEST["otra_categoria"] . "'";
    $valoresCategoria[] = $empresa;
    $valoresCategoria[] = $grupo;
    $valoresCategoria[] = "date_format('" . $fechaHoy . "', '%Y-%m-%d %H:%i:%s')";
    $valoresCategoria[] = $fk_idusu;
    
    $resultadoCategoria = $conexion -> insertar('categoria',$campoCategoria,$valoresCategoria);
    if($resultadoCategoria){
      $categoria = "'" . $resultadoCategoria . "'";
    } else {
      $categoria = "";
    }
  }
  
  $campos_insertar = array('fk_idemp', 'fecha', 'fk_idgru', 'fk_idcat', 'concepto', 'valor', 'tipo', 'tipo_pago', 'fecha_creacion', 'fk_idusu', 'fk_idbol');
  $valores_insertar = array();
  $valores_insertar[] = $empresa;
  $valores_insertar[] = "date_format('" . $fecha . "', '%Y-%m-%d')";
  $valores_insertar[] = $grupo;
  $valores_insertar[] = $categoria;
  $valores_insertar[] = $concepto;
  $valores_insertar[] = $valor;
  $valores_insertar[] = $tipo;
  $valores_insertar[] = $tipo_pago;
  $valores_insertar[] = "date_format('" . $fechaHoy . "', '%Y-%m-%d %H:%i:%s')";
  $valores_insertar[] = $fk_idusu;
  $valores_insertar[] = $bolsillo;
  
  $resultado = $conexion -> insertar($tabla,$campos_insertar,$valores_insertar);
  
  if($resultado){
    $retorno["exito"] = 1;
    $retorno["mensaje"] = "Datos insertados con exito";
  }
  
  echo(json_encode($retorno));
}

function validarCierreMes($empresa,$fecha){
  global $conexion;
  $retorno = array();
  $retorno["exito"] = 0;
  
  $datosFecha = explode("-",$fecha);
  
  $sqlConsultaCierre = "select * from cierre_mes a where a.fk_idemp=" . $empresa . " and a.ano=" . $datosFecha[0] . " and a.mes=" . intval($datosFecha[1]) . "";  
  $datosConsultaCierre = $conexion -> listar_datos($sqlConsultaCierre);
  
  if($datosConsultaCierre["cant_resultados"]){
    $retorno["exito"] = 1;
  }  
  return($retorno);
}

function mostrar_actualizar_ingreso_egreso_formulario(){
  global $conexion;
  $tipoPago1 = '';
  $tipoPago2 = '';
  $tipoPago3 = '';
  $tipoPago21 = '';
  $tipoPago22 = '';
  
  $retorno = array();
  $retorno["exito"] = 1;
  
  $iding = @$_REQUEST["iding"];
  $sql1 = "select * from ingreso_egreso where iding=" . $iding;
  $datos = $conexion -> listar_datos($sql1);
  $categorias = obtener_categorias($datos[0]["fk_idemp"],$datos[0]["fk_idgru"],$datos[0]["fk_idcat"],1);
  $bolsillos = obtener_bolsillos($datos[0]["fk_idemp"],$datos[0]["fk_idbol"],1);
  $grupos = $conexion -> obtener_grupos($datos[0]["fk_idgru"],'grupo_edit',1);
  
  if($datos[0]["tipo_pago"] == 1){
    $tipoPago1 = 'checked';
  }
  if($datos[0]["tipo_pago"] == 2){
    $tipoPago2 = 'checked';
  }
  if($datos[0]["tipo_pago"] == 3){
    $tipoPago3 = 'checked';
  }
  
  if($datos[0]["tipo_pago"] == 1){
    $tipoPago1 = 'checked';
  }
  if($datos[0]["tipo_pago"] == 2){
    $tipoPago2 = 'checked';
  }
  
  $html = '';
  $html .= '<div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Actualizar Ingreso</h4>
        <form class="" onsubmit="return false;" name="form_ingreso_egreso_edit" id="form_ingreso_egreso_edit">
          <div class="row">
            <div class="form-group col-md-1">
              <label>Fecha*</label>
              <input type="text" class="form-control form-control-sm " id="fecha_edit" readonly="" value="' . $datos[0]["fecha"] . '">
            </div>
            <div class="form-group col-md-2">
              <label>Grupo*</label>
                <div id="capa_edit_grupo">' . $grupos["opciones_adicionar"] . '
                </div>
            </div>
            
            <div id="capa_categoria_edit" class="form-group col-md-2">
              <label for="exampleFormControlSelect1">Categoría*</label>
              <select class="form-control form-control-sm " id="categoria_edit">
                ' . $categorias["opciones"] . '
              </select>
              <textarea class="form-control form-control-sm mt-2" style="display:none" placeholder="Otra categoria" id="otra_categoria_edit" rows="6"></textarea>
            </div>
            
            <div id="capa_concepto_edit" class="form-group col-md-2">
              <label>Concepto</label>
              <textarea style="height:100px" class="form-control form-control-sm" id="concepto_edit">' . $datos[0]["concepto"] . '</textarea>
            </div>
            <div class="form-group col-md-2">
              <label>Valor*</label>
              <input type="text" class="form-control form-control-sm " id="valor_edit" value="' . number_format($datos[0]["valor"],0,",",".") . '">
            </div>
            
            <div id="capa_tipo_pago_edit" class="form-group col-md-2">
              <label>Tipo de pago*</label>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="tipo_pago_edit" id="tipo_pago1" value="1" ' . $tipoPago1 . '>
                    Efectivo
                  <i class="input-helper"></i></label>
                </div>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="tipo_pago_edit" id="tipo_pago2" value="2" ' . $tipoPago2 . '>
                    Banco
                  <i class="input-helper"></i></label>
                </div>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="tipo_pago_edit" id="tipo_pago3" value="3" ' . $tipoPago3 . '>
                    Bolsillo
                  <i class="input-helper"></i></label>
                </div>
            </div>
            <div id="capa_traslado_a_edit" class="form-group col-md-2" style="display:none">
              <label>Traslado a*</label>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="tipo_pago2_edit" id="tipo_pago21" value="1" ' . $tipoPago1 . '>
                    Efectivo
                  <i class="input-helper"></i></label>
                </div>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="tipo_pago2_edit" id="tipo_pago22" value="2" ' . $tipoPago2 . '>
                    Banco
                  <i class="input-helper"></i></label>
                </div>
            </div>
            <div id="capa_bolsillo_edit" class="form-group col-md-1" style="display:none">
              <label for="exampleFormControlSelect1">Bolsillo*</label>
              <select class="form-control form-control-sm " id="bolsillo_edit">
                ' . $bolsillos["opciones"] . '
              </select>
            </div>
            
          </div>          
          <button type="button" id="actualizar_ingreso_egreso_formulario" class="btn btn-primary mb-2">Actualizar</button>
          <button type="button" id="cancelar_actualizar_ingreso_egreso_formulario" class="btn btn-primary mb-2">Cancelar</button>
          
          <input type="hidden" id="iding" name="iding" value="' . $iding . '">
          <input type="hidden" id="empresa_edit" name="empresa_edit" value="' . $datos[0]["fk_idemp"] . '">
        </form>
      </div>
    </div>
  </div>';
  
  $retorno["html"] = $html;
  
  echo(json_encode($retorno));
}
function actualizar_ingreso_egreso_formulario(){
  global $conexion, $atras;
  
  $iding = @$_REQUEST["iding"];
  $tabla = "ingreso_egreso";

  $retorno = array();
  
  $empresa = @$_REQUEST["empresa"];
  $fecha = @$_REQUEST["fecha"];
  $grupo = @$_REQUEST["grupo"];
  $categoria = @$_REQUEST["categoria"];
  $concepto = @$_REQUEST["concepto"];
  $valor = str_replace('.', '', @$_REQUEST["valor"]);
  $tipo_pago = @$_REQUEST["tipo_pago"];
  $bolsillo = 0;
  
  if(@$_REQUEST["grupo"] == 1){
    $tipo  = "1";//Ingreso
  } else if(@$_REQUEST["grupo"] == 2 || @$_REQUEST["grupo"] == 3){
    $tipo  = "2";//Egreso
  } else if(@$_REQUEST["grupo"] == 4){
    $tipo  = "3";//Traslado
  } else if(@$_REQUEST["grupo"] == 5){
    $tipo  = "4";//Bolsillo
  } else if(@$_REQUEST["grupo"] == 6){
    $tipo  = "5";//Saldo inicial Bolsillo
  }
  
  if(@$_REQUEST["tipo_pago"] == 3 || @$_REQUEST["grupo"] == 6){//Si tipo de pago es bolsillo o grupo es saldo inicial bolsillo, obtengo el bolsillo a seleccionar
    $bolsillo = @$_REQUEST["bolsillo"];
  }
  
  $consultaCierreMes = validarCierreMes($empresa,$fecha);
  if($consultaCierreMes["exito"]){//Existe un cierre y no debe dejar registrar
    $retorno["exito"] = 0;
    $retorno["mensaje"] = 'Este mes ya se encuentra cerrado';
    echo(json_encode($retorno));
    die();
  }
  
  $fk_idusu = @$_SESSION["idusu"];
  $fechaHoy = date('Y-m-d H:i:s');
  
  if(@$_REQUEST["categoria"] == -1){    
    $campoCategoria = array('nombre', 'fk_idemp', 'fecha_creacion', 'fk_idusu');
    $valoresCategoria = array();
    $valoresCategoria[] = "'" . @$_REQUEST["otra_categoria"] . "'";
    $valoresCategoria[] = @$_REQUEST["empresa"];
    $valoresCategoria[] = "date_format('" . $fechaHoy . "', '%Y-%m-%d %H:%i:%s')";
    $valoresCategoria[] = $fk_idusu;
    
    $resultadoCategoria = $conexion -> insertar('categoria',$campoCategoria,$valoresCategoria);
    if($resultadoCategoria){
      $categoria = $resultadoCategoria;
    } else {
      $categoria = "";
    }
  }

  $valoresModificar = array();
  $valoresModificar[] = "fk_idemp='" . $empresa . "'";
  $valoresModificar[] = "fecha=date_format('" . $fecha . "', '%Y-%m-%d')";
  $valoresModificar[] = "fk_idgru='" . $grupo . "'";
  $valoresModificar[] = "fk_idcat='" . $categoria . "'";
  $valoresModificar[] = "concepto='" . $concepto . "'";
  $valoresModificar[] = "valor='" . $valor . "'";
  $valoresModificar[] = "tipo='" . $tipo . "'";
  $valoresModificar[] = "tipo_pago='" . $tipo_pago . "'";
  $valoresModificar[] = "fk_idbol='" . $bolsillo . "'";

  $condicion_update = "iding=" . $iding;
  $conexion -> modificar($tabla,$valoresModificar,$condicion_update,$iding);

  $retorno["exito"] = 1;
  $retorno["mensaje"] = 'Modificacion realizada';

  echo json_encode($retorno);
}
function eliminar_ingreso_egreso(){
  global $conexion;
  $tabla = 'ingreso_egreso';
  $where = '';
  $retorno = array();
  $valor = array();
  
  $valor[] = 'estado=0';
  $where .= "iding=" . @$_REQUEST["iding"];
  
  $retorno["exito"] = 1;
  $retorno["mensaje"] = "Registro eliminado de manera correcta";
  
  $resultado = $conexion -> modificar($tabla, $valor, $where);
  
  echo(json_encode($retorno));
}
function obtener_listas(){
  global $conexion, $atras;
  $retorno = array();
  $retorno["exito"] = 1;
  
  $empresa = @$_REQUEST["empresa"];
  
  $opcionesBolsillo = obtener_bolsillos($empresa,'',1);
  
  $retorno["opciones_bolsillo"] = $opcionesBolsillo["opciones"];
  
  echo(json_encode($retorno));
}

function obtener_listas_categorias(){
  global $conexion, $atras;
  $retorno = array();
  $retorno["exito"] = 1;
  
  $empresa = @$_REQUEST["empresa"];
  $grupo = @$_REQUEST["grupo"];
  
  if($empresa && $grupo){
    $opcionesCategorias = obtener_categorias($empresa, $grupo, '',1);
    $retorno["opciones_categoria"] = $opcionesCategorias["opciones"];
  } else {
    $retorno["exito"] = 0;
    $retorno["mensaje"] = '';
  }
  
  echo(json_encode($retorno));
}
function obtener_listas_categorias_filtro(){
  global $conexion, $atras;
  $retorno = array();
  $retorno["exito"] = 1;
  
  $empresa = @$_REQUEST["empresa"];
  $grupo = @$_REQUEST["grupo"];
  
  if($empresa && $grupo){
    $opcionesCategoriasFiltro = obtener_categorias_filtro($empresa, $grupo, '',1);
    $retorno["opciones_categoria"] = $opcionesCategoriasFiltro["opciones"];
  } else {
    $retorno["exito"] = 0;
    $retorno["mensaje"] = '';
  }
  
  echo(json_encode($retorno));
}
function obtener_categorias($empresa=false,$grupo=false,$seleccionado=false,$return = 0){
    global $conexion, $atras;
    $adicional = '';
        
    $retorno = array();
    $where = array();
    
    $retorno["exito"] = 1;
    
    if(@$empresa){
      $where[] = " AND a.fk_idemp=" . $empresa;
    }
    if(@$grupo){
      $where[] = " AND a.fk_idgru=" . $grupo;
    }
    
    $sql1 = "select a.idcat, a.nombre from categoria a where a.estado=1 " . implode("", $where) . "";
    $datos = $conexion -> listar_datos($sql1);
    
    if($datos["cant_resultados"]){
      $html = "";
      $html .= "<option value=''>Seleccione</option>";
      for ($i=0; $i < $datos["cant_resultados"]; $i++) {
        $adicional = '';
        if($seleccionado && $datos[$i]["idcat"] == $seleccionado){
          $adicional = 'selected';
        }
         
        $html .= "<option value='" . $datos[$i]["idcat"] . "' " . $adicional . ">" . $datos[$i]["nombre"] . "</option>";
      }
      $html .= "<option value='-1'>Otro</option>";
      $retorno["opciones"] = $html;
    } else {
      $retorno["exito"] = 0;
      $retorno["mensaje"] = "No existen categorías relacionadas a esta empresa";
      $retorno["opciones"] = "<option value=''>Seleccione</option><option value='-1'>Otro</option>";
    }
    
    if($return == 0){
      echo(json_encode($retorno));
    } else {
      return($retorno);
    }
}
function obtener_categorias_filtro($empresa=false,$grupo=false,$seleccionado=false,$return = 0){
    global $conexion, $atras;
    $adicional = '';
        
    $retorno = array();
    $where = array();
    
    $retorno["exito"] = 1;
    
    if(@$empresa){
      $where[] = " AND a.fk_idemp=" . $empresa;
    }
    if(@$grupo){
      $where[] = " AND a.fk_idgru in(" . $grupo . ")";
    }
    
    $sql1 = "select a.idcat, a.nombre from categoria a where a.estado=1 " . implode("", $where) . " order by a.nombre asc";
    $datos = $conexion -> listar_datos($sql1);
    
    if($datos["cant_resultados"]){
      $html = "";
      for ($i=0; $i < $datos["cant_resultados"]; $i++) {
        $html .= '<div class="form-check form-check-primary">
                    <label class="form-check-label">
                      <input type="checkbox" class="form-check-input" name="categoria_filtro[]" id="categoria_filtro' . $datos[$i]["idcat"] . '" value="' . $datos[$i]["idcat"] . '">
                      ' . $datos[$i]["nombre"] . '
                    <i class="input-helper"></i></label>
                  </div>';
      }
      $retorno["opciones"] = $html;
    } else {
      $retorno["exito"] = 0;
    }
    
    if($return == 0){
      echo(json_encode($retorno));
    } else {
      return($retorno);
    }
}
function obtener_bolsillos($empresa=false,$seleccionado=false,$return = 0){
    global $conexion, $atras;
    $adicional = '';
        
    $retorno = array();
    $retorno["exito"] = 1;
    
    if(@$_REQUEST["empresa"]){
      $empresa = @$_REQUEST["empresa"];
    }
    
    $sql1 = "select a.idbol, a.nombre from bolsillo a where a.fk_idemp=" . $empresa . " and a.estado=1";
    $datos = $conexion -> listar_datos($sql1);
    
    if($datos["cant_resultados"]){
      $html = "";
      $html .= "<option value=''>Seleccione</option>";
      for ($i=0; $i < $datos["cant_resultados"]; $i++) {
        $adicional = '';
        if($seleccionado && $datos[$i]["idbol"] == $seleccionado){
          $adicional = 'selected';
        }
         
        $html .= "<option value='" . $datos[$i]["idbol"] . "' " . $adicional . ">" . $datos[$i]["nombre"] . "</option>";
      }
      //$html .= "<option value='-1'>Otro</option>";
      $retorno["opciones"] = $html;
    } else {
      $retorno["exito"] = 0;
      $retorno["mensaje"] = "No existen bolsillos relacionadas a esta empresa";
      $retorno["opciones"] = "<option value=''>Seleccione</option>";
    }
    
    if($return == 0){
      echo(json_encode($retorno));
    } else {
      return($retorno);
    }
}

function obtener_gastos_ingresos_egresos($retornoFinal = false){
  global $conexion, $atras;
  $ingresoBanco = 0;
  $ingresoEfectivo = 0;
  $egresoBanco = 0;
  $egresoEfectivo = 0;
  $totalTraslado = 0;
  $trasladoRestaEfectivo = 0;//Valor a quitar del saldo total efectivo
  $trasladoRestaBanco = 0;//Valor a quitar del saldo total banco
  $trasladoSumaBanco = 0;
  $trasladoSumaEfectivo = 0;
  $totalEfectivo = 0;
  $totalBanco = 0;
  $saldoInicialEfectivo = 0;//Suma de los valores en efectivo cuando categoria es saldo inicial
  $saldoInicialBanco = 0;//Suma de los valores en banco cuando categoria es saldo inicial
  $totalBolsillo1 = 0;//Total del bolsillo a sumar y a restar del saldo total
  $totalBolsillo2 = 0;//Total del bolsillo a restar
  $totalBolsillo3 = 0;//Saldo inicial del bolsillo
  $bolsillos = array();
  
  $capa1Show = '';
  $capa2Show = '';
  
  $order = 'order by a.fecha desc';
  
  $empresa = @$_REQUEST["empresa"];
  $fechai = @$_REQUEST["fechai"];
  $fechaf = @$_REQUEST["fechaf"];
  $capaActiva = @$_REQUEST["capa_activa"];
  
  $retorno = array();
  $retorno["exito"] = 1;
  
  $whereFiltro = array();
  if($empresa){
    if($empresa == -1){
      $whereFiltro[] = " ";
    } else {
      $whereFiltro[] = " and a.fk_idemp=" . $empresa;
    }
  } else {
    $whereFiltro[] = " and a.fk_idemp=0";
  }
  if($fechai){
    $whereFiltro[] = " and date_format(a.fecha,'%Y-%m-%d')>='" . $fechai . "'";
  }
  if($fechaf){
    $whereFiltro[] = " and date_format(a.fecha,'%Y-%m-%d')<='" . $fechaf . "'";
  }
  
  if($capaActiva == 'capa_datos-tab'){
    $capa1Show = 'show active';
  } else if($capaActiva == 'capa_bolsillos-tab'){
    $capa2Show = 'show active';
  }
  
  $sql = "select a.iding,b.nombre as empresa,a.fecha,d.idgru as grupo,c.nombre as categoria,a.concepto,a.valor,a.tipo,a.tipo_pago,a.fk_idbol from ingreso_egreso a, empresa b, categoria c, grupo d where a.estado=1 and a.fk_idemp=b.idemp and a.fk_idcat=c.idcat and a.fk_idgru=d.idgru " . implode("",$whereFiltro) . " " . $order;
  $datos = $conexion -> listar_datos($sql);
  
  for ($i=0; $i < $datos["cant_resultados"]; $i++) {
    if(strtolower($datos[$i]["categoria"]) == 'saldo inicial'){
      if($datos[$i]["tipo_pago"] == 1){//Efectivo
        $saldoInicialEfectivo += $datos[$i]["valor"];
      }
      if($datos[$i]["tipo_pago"] == 2){//Banco
        $saldoInicialBanco += $datos[$i]["valor"];
      }
      
      continue;
    }
    
    if($datos[$i]["tipo"] == 1 && $datos[$i]["tipo_pago"] == 1){//Si tipo es Ingreso y tipo de pago es efectivo
      $ingresoEfectivo += $datos[$i]["valor"];
    } else if($datos[$i]["tipo"] == 1 && $datos[$i]["tipo_pago"] == 2){//Si tipo es Ingreso y tipo de pago es banco
      $ingresoBanco += $datos[$i]["valor"];
    } else if($datos[$i]["tipo"] == 2 && $datos[$i]["tipo_pago"] == 1){//Si tipo es Egreso y tipo de pago es efectivo
      $egresoEfectivo += $datos[$i]["valor"];
    } else if($datos[$i]["tipo"] == 2 && $datos[$i]["tipo_pago"] == 2){//Si tipo es Egreso y tipo de pago es banco
      $egresoBanco += $datos[$i]["valor"];
    } else if($datos[$i]["tipo"] == 3 && $datos[$i]["tipo_pago"] == 1){//Si tipo es traslado y tipo de pago es efectivo
      $trasladoRestaBanco += $datos[$i]["valor"];//Saldo a restar en banco si tipo de pago es efectivo total
      $trasladoSumaEfectivo += $datos[$i]["valor"];//Saldo a sumar al total de efectivo
      
    } else if($datos[$i]["tipo"] == 3 && $datos[$i]["tipo_pago"] == 2){//Si tipo es traslado y tipo de pago es Banco
      $trasladoRestaEfectivo += $datos[$i]["valor"];//Saldo a restar en efectivo si tipo de pago es banco
      $trasladoSumaBanco += $datos[$i]["valor"];//Saldo a sumar a banco
    } else if($datos[$i]["tipo_pago"] == 3 && $datos[$i]["grupo"] == 2){//Si tipo de pago es bolsillo y grupo es gastos operativos, sumo al bolsillo seleccionado y resto al saldo total
      $bolsillos[$datos[$i]["fk_idbol"]] += $datos[$i]["valor"];
      
      $totalBolsillo1 += $datos[$i]["valor"];
    } else if($datos[$i]["tipo_pago"] == 3 && $datos[$i]["grupo"] == 5){//Si tipo de pago es bolsillo y grupo gastos bolsillo, se resta del bolsillo
      $bolsillos[$datos[$i]["fk_idbol"]] -= $datos[$i]["valor"];
      
      $totalBolsillo2 += $datos[$i]["valor"];
    } else if($datos[$i]["grupo"] == 6){//Si tipo es bolsillo y grupo es saldo inicial bolsillo, sumo al bolsillo seleccionado
      $bolsillos[$datos[$i]["fk_idbol"]] += $datos[$i]["valor"];
      
      $totalBolsillo3 += $datos[$i]["valor"];
    }
  }

  $totalEfectivo = $ingresoEfectivo - $egresoEfectivo; //Total en efectivo
  $totalBanco = $ingresoBanco - $egresoBanco; //Total en banco
  
  if($trasladoSumaEfectivo > 0){
    $totalEfectivo += $trasladoSumaEfectivo;
  }
  if($trasladoSumaBanco > 0){
    $totalBanco += $trasladoSumaBanco;
  }
  
  if($trasladoRestaBanco > 0){
    $totalBanco -= $trasladoRestaBanco;
  }
  if($trasladoRestaEfectivo > 0){
    $totalEfectivo -= $trasladoRestaEfectivo;
  }
  
  $totalEfectivo += $saldoInicialEfectivo - $totalBolsillo1;
  
  if($saldoInicialBanco > 0){
    $totalBanco += $saldoInicialBanco;
  }
  
  $totalBolsillos = ($totalBolsillo1 - $totalBolsillo2) + $totalBolsillo3;
  
  $saldoTotal = ((($ingresoEfectivo + $ingresoBanco) - ($egresoEfectivo + $egresoBanco)) + $saldoInicialEfectivo + $saldoInicialBanco) - $totalBolsillo1;
  
  $retorno["saldo_total"] = $saldoTotal;
  $retorno["total_efectivo"] = $totalEfectivo;
  $retorno["total_banco"] = $totalBanco;
  $retorno["bolsillos"] = $bolsillos;
  
  $html = '';
  $html .= '
          <div class="tab-pane fade ' . $capa1Show . '" id="capa_datos" role="tabpanel" aria-labelledby="capa_datos-tab">
            <div class="row">
              <div class="col-md-6 border-md-right border-md-bottom">
                  <div class="d-flex flex-grow-1 align-items-center justify-content-center p-3 item">
                    <i class="mdi mdi-currency-usd mr-3 icon-lg text-danger"></i>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Saldo total efectivo</small>
                      <h5 class="mr-2 mb-0">$' . number_format($totalEfectivo,0,",",".") . '</h5>
                    </div>
                  </div>
              </div>
              <div class="col-md-6 border-md-bottom">
                <div class="d-flex flex-grow-1 align-items-center justify-content-center p-3 item">
                  <i class="mdi mdi-currency-usd mr-3 icon-lg text-danger"></i>
                  <div class="d-flex flex-column justify-content-around">
                    <small class="mb-1 text-muted">Saldo total banco</small>
                    <h5 class="mr-2 mb-0">$' . number_format($totalBanco,0,",",".") . '</h5>
                  </div>
                </div>
              </div>
              <div class="col-md-12 border-md-bottom">
                <div class="d-flex flex-grow-1 align-items-center justify-content-center p-3 item">
                  <i class="mdi mdi-currency-usd mr-3 icon-lg text-danger"></i>
                  <div class="d-flex flex-column justify-content-around">
                    <small class="mb-1 text-muted">Saldo total</small>
                    <h5 class="mr-2 mb-0">$' . number_format($saldoTotal,0,",",".") . '</h5>
                  </div>
                </div>
              </div>
              <div class="col-md-6 border-md-right">
                  <div class="d-flex flex-grow-1 align-items-center justify-content-center p-3 item">
                    <i class="mdi mdi-currency-usd mr-3 icon-lg text-danger"></i>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Ingresos</small>
                      <h5 class="mr-2 mb-0">$' . number_format(($ingresoEfectivo + $ingresoBanco),0,",",".") . '</h5>
                    </div>
                  </div>
              </div>
              <div class="col-md-6 d-flex flex-wrap justify-content-xl-between">
                <div class="d-flex flex-grow-1 align-items-center justify-content-center p-3 item">
                  <i class="mdi mdi-currency-usd mr-3 icon-lg text-danger"></i>
                  <div class="d-flex flex-column justify-content-around">
                    <small class="mb-1 text-muted">Egresos</small>
                    <h5 class="mr-2 mb-0">$' . number_format(($egresoEfectivo + $egresoBanco),0,",",".") . '</h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade ' . $capa2Show . '" id="capa_bolsillos" role="tabpanel" aria-labelledby="capa_bolsillos-tab">
            <div class="row">
              <div class="d-flex flex-wrap justify-content-xl-between col-md-12 border-md-bottom">
                  <div class="d-flex flex-grow-1 align-items-center justify-content-center p-3 item">
                    <i class="mdi mdi-currency-usd mr-3 icon-lg text-danger"></i>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Saldo total bolsillos</small>
                      <h5 class="mr-2 mb-0">$' . number_format($totalBolsillos,0,",",".") . '</h5>
                    </div>
                  </div>
              </div>';
  
  foreach($bolsillos as $indice => $valor){
    $sqlBolsillo = "select idbol, nombre from bolsillo where idbol=" . $indice;
    $datosBolsillo = $conexion -> listar_datos($sqlBolsillo);
    $html .= '<div class="d-flex flex-wrap justify-content-xl-between col-md-3">
                  <div class="d-flex flex-grow-1 align-items-center justify-content-center p-3 item">
                    <i class="mdi mdi-currency-usd mr-3 icon-lg text-danger"></i>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">' . $datosBolsillo[0]["nombre"] . '</small>
                      <h5 class="mr-2 mb-0">$' . number_format($valor,0,",",".") . '</h5>
                    </div>
                  </div>
              </div>';
  }
              
            $html .= '</div>
          </div>';
              
  $retorno["html"] = $html;
  if($datos["cant_resultados"]){
    $retorno["fecha"] = $datos[0]["fecha"];
  } else {
    $retorno["fecha"] = date('Y-m-d');
  }
  
  if(!$retornoFinal){
    echo(json_encode($retorno));
  } else {
    return($retorno);
  }
}
function cierre_mes_guardar(){
  global $conexion;
  $retorno = array();
  $retorno["exito"] = 1;
  
  $idemp = @$_REQUEST["empresa"];
  $ano = @$_REQUEST["ano"];
  $mes = @$_REQUEST["mes"];
  $fechai = @$_REQUEST["fechai"];
  $fechaf = @$_REQUEST["fechaf"];
  
  $fechaHoy = date('Y-m-d H:i:s');
  $fk_idusu = @$_SESSION["idusu"];
  
  $sqlConsultaCierre = "select * from cierre_mes a where a.fk_idemp=" . $idemp . " and a.ano=" . $ano . " and a.mes=" . $mes . "";
  $datosConsultaCierre = $conexion -> listar_datos($sqlConsultaCierre);
  if($datosConsultaCierre["cant_resultados"]){
    $retorno["exito"] = 0;
    $retorno["mensaje"] = "Ya se ha registrado este cierre";
    echo(json_encode($retorno));
    die();
  }
  
  
  $saldos = obtener_gastos_ingresos_egresos(true);
  
  $sqlGrupoIngreso = "select * from grupo where lower(nombre) like 'ingreso'";
  $datosGrupoIngreso = $conexion -> listar_datos($sqlGrupoIngreso);
  if(!$datosGrupoIngreso["cant_resultados"]){
    $retorno["exito"] = 0;
    $retorno["mensaje"] = "El grupo con nombre ingreso no existe";
    echo(json_encode($retorno));
    die();
  }
  
  $sqlCategoriaInicial = "select * from categoria a where a.fk_idemp=" . $idemp . " and a.fk_idgru=" . $datosGrupoIngreso[0]["idgru"] . " and lower(a.nombre) like 'saldo inicial'";
  $datosCategoriaInicial = $conexion -> listar_datos($sqlCategoriaInicial);
  if(!$datosCategoriaInicial["cant_resultados"]){
    $retorno["exito"] = 0;
    $retorno["mensaje"] = "La categoría con nombre saldo inicial no existe para esta empresa y grupo";
    echo(json_encode($retorno));
    die();
  }
  
  if($saldos["total_efectivo"]){    
    $nuevaFecha = $conexion -> sumar_fecha($fechai,1,'month','Y-m-d');
    
    $valores = array();
    $valores["fk_idemp"] = $idemp;
    $valores["fecha"] = $nuevaFecha;
    $valores["grupo"] = $datosGrupoIngreso[0]["idgru"];
    $valores["categoria"] = $datosCategoriaInicial[0]["idcat"];
    $valores["concepto"] = 'Saldo mes anterior';
    $valores["valor"] = $saldos["total_efectivo"];
    $valores["tipo"] = 1;
    $valores["tipo_pago"] = 1;//Efectivo
    $valores["fk_idbol"] = 0;
    
    $resultadoEfectivo = insertar_ingreso_egreso_auto($valores);
    if(!$resultadoEfectivo["exito"]){
      $retorno["exito"] = 0;
      $retorno["mensaje"] = "Problemas al insertar el total de efectivo saldo inicial";
      echo(json_encode($retorno));
      die();
    }
  }
  if($saldos["total_banco"]){
    $nuevaFecha = $conexion -> sumar_fecha($fechai,1,'month','Y-m-d');
    
    $valores = array();
    $valores["fk_idemp"] = $idemp;
    $valores["fecha"] = $nuevaFecha;
    $valores["grupo"] = $datosGrupoIngreso[0]["idgru"];
    $valores["categoria"] = $datosCategoriaInicial[0]["idcat"];
    $valores["concepto"] = 'Saldo mes anterior';
    $valores["valor"] = $saldos["total_banco"];
    $valores["tipo"] = 1;
    $valores["tipo_pago"] = 2;//Banco
    $valores["fk_idbol"] = 0;
    
    $resultadoBanco = insertar_ingreso_egreso_auto($valores);
    if(!$resultadoBanco["exito"]){
      $retorno["exito"] = 0;
      $retorno["mensaje"] = "Problemas al insertar el total de banco saldo inicial";
      echo(json_encode($retorno));
      die();
    }
  }
  
  //------------------------------Bolsillos
  $sqlGrupoSaldoInicialBolsillo = "select * from grupo where lower(nombre) like 'saldo inicial bolsillo'";
  $datosGrupoSaldoInicialBolsillo = $conexion -> listar_datos($sqlGrupoSaldoInicialBolsillo);
  if(!$datosGrupoSaldoInicialBolsillo["cant_resultados"]){
    $retorno["exito"] = 0;
    $retorno["mensaje"] = "El grupo con nombre saldo inicial bolsillo no existe";
    echo(json_encode($retorno));
    die();
  }
  
  $sqlCategoriaBolsilloInicial = "select * from categoria a where a.fk_idemp=" . $idemp . " and a.fk_idgru=" . $datosGrupoSaldoInicialBolsillo[0]["idgru"] . " and lower(a.nombre) like 'saldo inicial%'";
  $datosCategoriaBolsilloInicial = $conexion -> listar_datos($sqlCategoriaBolsilloInicial);
  if(!$datosCategoriaBolsilloInicial["cant_resultados"]){
    $retorno["exito"] = 0;
    $retorno["mensaje"] = "La categoría con nombre saldo inicial no existe para esta empresa y grupo " . $datosGrupoSaldoInicialBolsillo[0]["nombre"];
    echo(json_encode($retorno));
    die();
  }
  
  if($saldos["bolsillos"]){
    $valorBolsillos = 0;
    foreach ($saldos["bolsillos"] as $idbol => $valor) {
      $valores = array();
      $valores["fk_idemp"] = $idemp;
      $valores["fecha"] = $nuevaFecha;
      $valores["grupo"] = $datosGrupoSaldoInicialBolsillo[0]["idgru"];
      $valores["categoria"] = $datosCategoriaBolsilloInicial[0]["idcat"];
      $valores["concepto"] = 'Saldo mes anterior';
      $valores["valor"] = $valor;
      $valores["tipo"] = 5;
      $valores["tipo_pago"] = 1;//Efectivo
      $valores["fk_idbol"] = $idbol;
      
      $resultadoBolsillo = insertar_ingreso_egreso_auto($valores);
      if(!$resultadoBanco["exito"]){
        $retorno["exito"] = 0;
        $retorno["mensaje"] = "Problemas al insertar el total de algun bolsillo";
        echo(json_encode($retorno));
        die();
      }
      
      $valorBolsillos += $valor;
    }
  }
  
  $campos = array('fk_idemp','ano','mes','fecha_cierre','fk_idusu','valor_saldo','valor_bolsillo');
  $valores = array();
  $valores[] = $idemp;
  $valores[] = $ano;
  $valores[] = $mes;
  $valores[] = "date_format('" . $fechaHoy . "', '%Y-%m-%d %H:%i:%s')";
  $valores[] = $fk_idusu;
  $valores[] = "'" . ($saldos["total_efectivo"] + $saldos["total_banco"]) . "'";
  $valores[] = "'" . ($valorBolsillos) . "'";
  
  $resultado = $conexion -> insertar('cierre_mes',$campos,$valores);
  if($resultado){
    $infoCierre = info_cierre_mes(true);
    
    $retorno["idcie"] = $resultado;
    $retorno["html"] = $infoCierre["html"];
  } else {
    $retorno["exito"] = 0;
    $retorno["mensaje"] = "Error al insertar";
  }
  
  echo(json_encode($retorno));
}
function insertar_ingreso_egreso_auto($valores){
  global $conexion;
  $retorno = array();
  $retorno["exito"] = 1;
  
  $fechaHoy = date('Y-m-d H:i:s');
  $fk_idusu = @$_SESSION["idusu"];
  
  $campos_insertar = array('fk_idemp', 'fecha', 'fk_idgru', 'fk_idcat', 'concepto', 'valor', 'tipo', 'tipo_pago', 'fecha_creacion', 'fk_idusu', 'fk_idbol');
  $valores_insertar = array();
  $valores_insertar[] = $valores["fk_idemp"];
  $valores_insertar[] = "date_format('" . $valores["fecha"] . "', '%Y-%m-%d')";
  $valores_insertar[] = $valores["grupo"];
  $valores_insertar[] = $valores["categoria"];
  $valores_insertar[] = "'" . $valores["concepto"] . "'";
  $valores_insertar[] = "'" . $valores["valor"] . "'";
  $valores_insertar[] = $valores["tipo"];
  $valores_insertar[] = $valores["tipo_pago"];
  $valores_insertar[] = "date_format('" . $fechaHoy . "', '%Y-%m-%d %H:%i:%s')";
  $valores_insertar[] = $fk_idusu;
  $valores_insertar[] = $valores["fk_idbol"];
  
  $resultado = $conexion -> insertar('ingreso_egreso',$campos_insertar,$valores_insertar);
  if($resultado){
    $retorno["iding"] = $resultado;
  } else {
    $retorno["exito"] = 0;
    $retorno["mensaje"] = "Error al insertar";
  }
  
  return($retorno);
}
function info_cierre_mes($retornoFinal = false){
  global $conexion;
  $retorno = array();
  $retorno["exito"] = 1;
  
  $idemp = @$_REQUEST["empresa"];
  $ano = @$_REQUEST["ano"];
  $mes = @$_REQUEST["mes"];
  $fechai = @$_REQUEST["fechai"];
  $fechaf = @$_REQUEST["fechaf"];
  
  $sqlConsultaCierre = "select * from cierre_mes a where a.fk_idemp=" . $idemp . " and a.ano=" . $ano . " and a.mes=" . $mes . "";
  $datosConsultaCierre = $conexion -> listar_datos($sqlConsultaCierre);
  
  if($datosConsultaCierre["cant_resultados"]){
    $retorno["html"] = '<button class="btn btn-success btn-icon-text"><i class="mdi mdi-file-check btn-icon-append"></i> Mes cerrado</button>';
  } else {
    $retorno["html"] = '<button class="btn btn-danger btn-icon-text cierre_mes"><i class="mdi mdi-file-check btn-icon-append"></i> Cerrar mes</button>';
  }
  
  if(!$retornoFinal){
    echo(json_encode($retorno));
  } else {
    return($retorno);
  }
}
if(@$_REQUEST["ejecutar"]){
  $_REQUEST["ejecutar"]();
}
?>