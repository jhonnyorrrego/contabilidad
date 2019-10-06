<?php
$atras="../../";
require_once $atras . 'vendor/autoload.php';
include_once($atras."lib_gym.php");
global $conexion, $raiz;
$raiz = $atras;

$conexion -> validar_acceso_sesion();

include_once ($atras . 'librerias.php');
echo(tema_majestic_master());
echo(notificacion());
echo(jquery_validate());

echo(bootstrap_table());
echo(bootstrap_datepicker());
echo(date_format_jquery());

$alto_tabla = "alto_documento-280";
if (@$_SESSION["dispositivo"] == 'phone') {
  $alto_tabla = "alto_documento+330";
}

$fechai = date('Y-m-01');
$fechaf = $conexion -> sumar_fecha($fechai,1,'month','Y-m-d');
$fechaf = $conexion -> sumar_fecha($fechaf,-1,'day','Y-m-d');
?>
<?php echo(encabezado());?>
<?php echo(funciones_js_tema()); ?>
<style type="text/css">
    .campo_editar{
      cursor: pointer;
    }
    .error{
    color:red;
  }
    </style>
<script>
$(document).ready(function(){  
  procesar_capa_info_ingreso_egreso();
  
  $("#guardar_ingreso_egreso").click(function(){
    
    var x_empresa = $("#empresa").val();
    var x_fecha = $("#fecha").val();
    var x_grupo = $('input:radio[name=grupo]:checked').val();
    var x_categoria = $("#categoria").val();
    var x_concepto = $("#concepto").val();
    var x_valor = $("#valor").val();
    var x_tipo_pago = $('input:radio[name=tipo_pago]:checked').val();
    var x_bolsillo = $('#bolsillo').val();
    
    var x_otra_categoria = $("#otra_categoria").val();
    
    if(x_grupo == 4){//Si es grupo Traslado
      var x_tipo_pago = $('input:radio[name=tipo_pago2]:checked').val();//
      
      if(!x_empresa || !x_fecha || !x_grupo || (!x_valor || x_valor == 0)){//No se valida la categoria
        notificacion('Por favor llenar los campos obligatorios','warning',4000);
        return false;
      }
    } else if(x_tipo_pago == 3){//Si tipo de pago es bolsillo
      if(!x_empresa || !x_fecha || !x_grupo || !x_categoria || (!x_valor || x_valor == 0) || !x_bolsillo){
        notificacion('Por favor llenar los campos obligatorios','warning',4000);
        return false;
      }
    } else {
      if(!x_empresa || !x_fecha || !x_grupo || !x_categoria || (!x_valor || x_valor == 0)){
        notificacion('Por favor llenar los campos obligatorios','warning',4000);
        return false;
      }
      
      if(x_categoria == -1){
        var valorOtraCategoria = x_otra_categoria;
        if(!valorOtraCategoria){
          notificacion('Por favor llenar los campos obligatorios','warning',4000);
          return false;
        }
        
        var otraCategoria = confirm("Esta seguro de vincular esta nueva categoria?");
        if(!otraCategoria){
          return false;
        } else {
          $("#otra_categoria").hide();
          $("#otra_categoria").val("");
        }
      }
    }
    
    $.ajax({
      url: 'ejecutar_acciones.php',
      type: 'POST',
      dataType: 'json',
      async: false,
      data: {ejecutar: 'guardar_ingreso_egreso', empresa: x_empresa, fecha : x_fecha, grupo: x_grupo, categoria: x_categoria, concepto: x_concepto, valor: x_valor, tipo_pago: x_tipo_pago, otra_categoria: x_otra_categoria, bolsillo: x_bolsillo},
      success : function(respuesta){
        if(respuesta.exito){
          notificacion(respuesta.mensaje,'success',4000);
          
          procesar_capa_info_ingreso_egreso();
          procesamiento_listar();
          
          $("#concepto").val("");
          $("#valor").val("");
          if(x_categoria == -1){
            $("#empresa").trigger("change");
          }
        } else {
          notificacion(respuesta.mensaje,'warning',4000);
        }
      }
    });
    
  });
  
  $("#empresa").change(function(){
    var valor = $(this).val();
    
    if(valor){
      $.ajax({
        url: 'ejecutar_acciones.php',
        type: 'POST',
        dataType: 'json',
        async: false,
        data: {ejecutar: 'obtener_listas', empresa: valor},
        success : function(respuesta){
          if(respuesta.exito){
            //notificacion(respuesta.mensaje,'success',4000);
            $("#categoria,#categoria_edit,#categoria_filtro").html(respuesta.opciones_categoria);
            $("#bolsillo,#bolsillo_edit,#bolsillo_filtro").html(respuesta.opciones_bolsillo);
          } else {
            notificacion(respuesta.mensaje,'warning',4000);
            $("#categoria,#categoria_edit,#categoria_filtro").html("<option value=''>Seleccione</option><option value='-1'>Otro</option>");
            $("#bolsillo,#bolsillo_edit,#bolsillo_filtro").html("<option value=''>Seleccione</option>");
          }
          
          procesamiento_listar();
        }
      });
    } else {
      procesamiento_listar();
    }
    
    procesar_capa_info_ingreso_egreso();
  });
  
  $("#categoria").change(function(){
    var valor = $(this).val();
    if(valor == -1){
      $("#otra_categoria").show();
    }
  });
  
  $("input[name$='grupo']").click(function(){
    var x_valor = $(this).val();
    
    if(x_valor == 4){//Si grupo es traslado, solo necesitamos el campo valor y traslado a
      $("#capa_categoria").hide();
      $("#capa_concepto").hide();
      $("#capa_tipo_pago").hide();
      
      $("#capa_traslado_a").show();
    } else {
      $("#capa_traslado_a").hide();
      
      $("#capa_categoria").show();
      $("#capa_concepto").show();
      $("#capa_tipo_pago").show();
    }
  });
  
  $("input[name$='tipo_pago']").click(function(){
    var x_valor = $(this).val();
    
    if(x_valor == 3){//Si tipo de pago es bolsillo      
      $("#capa_bolsillo").show();
      $("#bolsillo").addClass("required");
      
    } else {
      $("#capa_bolsillo").hide();
      $("#bolsillo").removeClass("required");
    }
  });
  
  $(".descargar_reporte").click(function(){
    var x_empresa = $("#empresa").val();
    var x_fechaInicial = $("#fechai").val();
    var x_fechaFinal = $("#fechaf").val();
    
    if(!x_empresa){
      notificacion('Seleccione empresa','warning',4000);
      return false;
    }
    
    window.open("export_ingreso_egreso.php?empresa=" + x_empresa + "&fechai=" + x_fechaInicial + "&fechaf=" + x_fechaFinal, "_self");
  });
  
  $('input[name$="grupo_filtro[]"],#categoria_filtro,input[name$="tipo_pago_filtro[]"],#bolsillo_filtro').change(function(){
    procesamiento_listar();
  });
  $("#concepto_filtro,#valor_filtro").keyup(function(){
    setTimeout(function(){
      procesamiento_listar();
    }, 100);
  });
  
});

$(document).on('keydown', function(event) {
   if (event.key == "Escape") {
      $(".valores").each(function(){
        $(this).removeClass("active");
      });
      
      $("#calculadora_total").html(0);
      $("#calculadora").hide(100);  
   }
});
</script>

<div id="calculadora" class="row" style="display:none">
  <div class=""></div>
  <div class="card fixed-bottom col-md-2 offset-md-10 grid-margin">
    <div class="d-flex flex-grow-1 align-items-center justify-content-center p-4 item">
      <i class="mdi mdi-currency-usd mr-3 icon-lg text-danger"></i>
      <div class="d-flex flex-column justify-content-around">
        <small class="mb-1 text-muted"></small>
        <h5 class="mr-2 mb-0" id="calculadora_total">0</h5>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12 grid-margin">
    <div class="d-flex justify-content-between flex-wrap">
      <div class="d-flex align-items-end flex-wrap">
        <div class="mr-md-3 mr-xl-5">
          <h2>Área de trabajo</h2>
        </div>
      </div>
      <div class="d-flex justify-content-between align-items-end flex-wrap">
        <button class="btn btn-primary mt-2 mt-xl-0 descargar_reporte">Descargar reporte</button>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form class="row form-group" name="filtro_ingreso_egreso" id="filtro_ingreso_egreso" onsubmit="return false;">
          <h4 class="card-title">Seleccione la empresa*</h4>
          <div class="form-group col-md-12">
            <label for="exampleFormControlSelect1"></label>
            <select class="form-control form-control-sm" id="empresa" name="empresa">
              <option value="">Seleccione</option>
  <?php
  $sql1 = "select idemp, nombre from empresa where estado=1 order by nombre asc";
  $datosEmpresa = $conexion -> listar_datos($sql1);
  for ($i=0; $i < $datosEmpresa["cant_resultados"]; $i++) { 
  	echo("<option value='" . $datosEmpresa[$i]["idemp"] . "'>" . $datosEmpresa[$i]["nombre"] . "</option>");
  }
  ?>
              <option value="-1">Todos</option>
            </select>
          </div>
          
          <div class="form-group col-md-6">
            <label>Fecha inicial</label>
            <!--input type="text" class="form-control form-control-lg" placeholder="Username" aria-label="Username"-->
            <input type="text" class="form-control form-control-sm " name="fechai" id="fechai" readonly="" value="<?php echo($fechai); ?>">
          </div>
          <div class="form-group col-md-6">
            <label>Fecha final</label>
            <!--input type="text" class="form-control form-control-lg" placeholder="Username" aria-label="Username"-->
            <input type="text" class="form-control form-control-sm " name="fechaf" id="fechaf" readonly="" value="<?php echo($fechaf); ?>">
          </div>
        
        </form>
      </div>
    </div>
  </div>
  
  
  <div class="col-md-8 grid-margin stretch-card">
    <div class="card">
      <div class="card-body dashboard-tabs p-0">
        
        <ul class="nav nav-tabs px-4" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="capa_datos-tab" data-toggle="tab" href="#capa_datos" role="tab" aria-controls="capa_datos" aria-selected="true">Datos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="capa_bolsillos-tab" data-toggle="tab" href="#capa_bolsillos" role="tab" aria-controls="capa_bolsillos" aria-selected="false">Bolsillo</a>
          </li>
        </ul>
        <div class="tab-content py-0 px-0" id="capa_info_ingreso_egreso">
        </div>
      </div>
    </div>
  </div>
  
</div>

<div id="capa_ingreso_egreso_add" class="row">
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Realizar Ingreso</h4>
        <form class="" onsubmit="return false;" name="form_ingreso_egreso" id="form_ingreso_egreso">
          <div class="row">
            <div class="form-group col-md-2">
              <label>Fecha*</label>
              <input type="text" class="form-control form-control-sm " id="fecha" readonly="" value="">
            </div>
            <div class="form-group col-md-2">
              <label>Grupo*</label>
                <div id="capa_grupo">
                  <div class="form-check form-check-primary">
                    <label class="form-check-label">
                      <input type="radio" class="form-check-input " name="grupo" id="grupo1" value="1" checked="">
                      Ingreso
                    <i class="input-helper"></i></label>
                  </div>
                  <div class="form-check form-check-primary">
                    <label class="form-check-label">
                      <input type="radio" class="form-check-input" name="grupo" id="grupo12" value="2">
                      Gastos operativos
                    <i class="input-helper"></i></label>
                  </div>
                  <div class="form-check form-check-primary">
                    <label class="form-check-label">
                      <input type="radio" class="form-check-input" name="grupo" id="grupo3" value="3">
                      Gastos externos
                    <i class="input-helper"></i></label>
                  </div>
                  <div class="form-check form-check-primary">
                    <label class="form-check-label">
                      <input type="radio" class="form-check-input" name="grupo" id="grupo4" value="4">
                      Traslado
                    <i class="input-helper"></i></label>
                  </div>
                  <div class="form-check form-check-primary">
                    <label class="form-check-label">
                      <input type="radio" class="form-check-input" name="grupo" id="grupo5" value="5">
                      Gastos bolsillo
                    <i class="input-helper"></i></label>
                  </div>
                </div>
            </div>
            
            <div id="capa_categoria" class="form-group col-md-1">
              <label for="exampleFormControlSelect1">Categoría*</label>
              <select class="form-control form-control-sm " id="categoria">
                <option value="">Seleccione</option>
              </select>
              <input type="text" class="form-control form-control-sm mt-2" style="display:none" placeholder="Otra categoria" id="otra_categoria">
            </div>
            
            <div id="capa_concepto" class="form-group col-md-2">
              <label>Concepto</label>
              <textarea style="height:100px" class="form-control form-control-sm" id="concepto"></textarea>
            </div>
            <div class="form-group col-md-2">
              <label>Valor*</label>
              <input type="text" class="form-control form-control-sm " id="valor">
            </div>
            
            <div id="capa_tipo_pago" class="form-group col-md-2">
              <label>Tipo de pago*</label>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="tipo_pago" id="tipo_pago1" value="1" checked="">
                    Efectivo
                  <i class="input-helper"></i></label>
                </div>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="tipo_pago" id="tipo_pago2" value="2">
                    Banco
                  <i class="input-helper"></i></label>
                </div>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="tipo_pago" id="tipo_pago3" value="3">
                    Bolsillo
                  <i class="input-helper"></i></label>
                </div>
            </div>
            <div id="capa_traslado_a" class="form-group col-md-2" style="display:none">
              <label>Traslado a*</label>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="tipo_pago2" id="tipo_pago21" value="1" checked="">
                    Efectivo
                  <i class="input-helper"></i></label>
                </div>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="tipo_pago2" id="tipo_pago22" value="2">
                    Banco
                  <i class="input-helper"></i></label>
                </div>
            </div>
            <div id="capa_bolsillo" class="form-group col-md-1" style="display:none">
              <label for="exampleFormControlSelect1">Bolsillo*</label>
              <select class="form-control form-control-sm" id="bolsillo">
                <option value="">Seleccione</option>
              </select>
            </div>
          </div>
          <button class="btn btn-primary mb-2" id="guardar_ingreso_egreso">Guardar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="capa_ingreso_egreso_edit" class="row" style="display:none">
</div>

<div id="capa_ingreso_egreso_list" class="row">
  <div class="col-md-12 stretch-card">
    <div class="card">
      <div class="card-body">
        <p class="card-title">Datos ingresados</p>
        
        <p>
          <a data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
            + Filtros
          </a>
        </p>
        
        <div class="collapse" id="collapseExample">
          <form class="row" name="filtro_ingreso_egreso2" id="filtro_ingreso_egreso2" onsubmit="return false;">
            <div class="form-group col-md-2">
              <label>Grupo</label>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="checkbox" class="form-check-input " name="grupo_filtro[]" id="grupo1" value="1">
                    Ingreso
                  <i class="input-helper"></i></label>
                </div>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="grupo_filtro[]" id="grupo12" value="2">
                    Gastos operativos
                  <i class="input-helper"></i></label>
                </div>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="grupo_filtro[]" id="grupo3" value="3">
                    Gastos externos
                  <i class="input-helper"></i></label>
                </div>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="grupo_filtro[]" id="grupo4" value="4">
                    Traslado
                  <i class="input-helper"></i></label>
                </div>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="grupo_filtro[]" id="grupo5" value="5">
                    Gastos bolsillo
                  <i class="input-helper"></i></label>
                </div>
            </div>
            
            <div id="capa_categoria" class="form-group col-md-2">
              <label for="exampleFormControlSelect1">Categoría</label>
              <select class="form-control form-control-sm " name="categoria_filtro" id="categoria_filtro">
                <option value="">Seleccione</option>
              </select>
            </div>
            
            <div id="capa_concepto" class="form-group col-md-2">
              <label>Concepto</label>
              <textarea style="height:100px" class="form-control form-control-sm" name="concepto_filtro" id="concepto_filtro"></textarea>
            </div>
            <div class="form-group col-md-2">
              <label>Valor</label>
              <input type="text" class="form-control form-control-sm " name="valor_filtro" id="valor_filtro">
            </div>
            
            <div id="capa_tipo_pago" class="form-group col-md-2">
              <label>Tipo de pago</label>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="tipo_pago_filtro[]" id="tipo_pago1" value="1">
                    Efectivo
                  <i class="input-helper"></i></label>
                </div>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="tipo_pago_filtro[]" id="tipo_pago2" value="2">
                    Banco
                  <i class="input-helper"></i></label>
                </div>
                <div class="form-check form-check-primary">
                  <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="tipo_pago_filtro[]" id="tipo_pago3" value="3">
                    Bolsillo
                  <i class="input-helper"></i></label>
                </div>
            </div>
            <div class="form-group col-md-2">
              <label for="exampleFormControlSelect1">Bolsillo*</label>
              <select class="form-control form-control-sm " name="bolsillo_filtro" id="bolsillo_filtro">
                <option value="">Seleccione</option>
              </select>
            </div>
          </form>
        </div>
        <div class="table-responsive">
          <table id="table" class="table table-bordered table-responsive-md table-striped text-center" role="grid" style="">
            <thead class="">
              <tr>
                <th data-field="empresa" data-sortable="true" data-visible="true" data-align="center">Empresa</th>
                <th data-field="fecha" data-sortable="true" data-visible="true" data-align="center">Fecha</th>
                <th data-field="grupo" data-sortable="true" data-visible="true" data-align="center">Grupo</th>
                <th data-field="categoria" data-sortable="true" data-visible="true" data-align="center">Categoría</th>
                <th data-field="concepto" data-sortable="true" data-visible="true" data-align="center">Concepto</th>
                <th data-field="valor" data-sortable="true" data-visible="true" data-align="center">Valor</th>
                <th data-field="tipo" data-sortable="true" data-visible="true" data-align="center">Tipo</th>
                <th data-field="tipo_pago" data-sortable="true" data-visible="true" data-align="center">Tipo de pago</th>
                <th data-field="bolsillo" data-sortable="false" data-visible="true" data-align="center">Bolsillo</th>
                <th data-field="accion" data-sortable="false" data-visible="true" data-align="center">Accion</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th colspan="5"></th>
                <!--th></th>
                <th></th>
                <th></th>
                <th></th-->
                <th id="celda_total_valor"></th>
                <th colspan="4"></th>
                <!--th></th>
                <th></th>
                <th></th-->
              </tr>
            </tfoot>
          </table>
          <input type="hidden" id="cantidad_total">
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){  
  $('#fechai').datepicker({
    language : 'es',
    format: 'yyyy-mm-dd',
    autoclose: true,
    setEndDate: new Date('<?php echo($fechaf); ?>')
  }).on('changeDate', function(event){
    procesar_capa_info_ingreso_egreso();
    procesamiento_listar();
  });
  $('#fechaf').datepicker({
    language : 'es',
    format: 'yyyy-mm-dd',
    autoclose: true,
    setStartDate : new Date('<?php echo($fechai); ?>')
  }).on('changeDate', function(event){
    procesar_capa_info_ingreso_egreso();
    procesamiento_listar();
  });
  $('#fecha').datepicker({
    language : 'es',
    format: 'yyyy-mm-dd',
    autoclose: true
  });
});
$body = $("body");

var cantidad_registros = 60;
$(document).ready(function(){//Se inicializa la tabla con estilos, el alto del documento y se ejecuta la accion para listar datos sobre la tabla
  var alto_documento = $(document).height();
  var alto_tabla = <?php echo($alto_tabla); ?>;

  <?php if($_SESSION["dispositivo"] == 'computer'){ ?>
  $('#table').bootstrapTable({
    method: 'get',
    cache: false,
    pagination: true,
    onlyInfoPagination: false,
    showColumns: false,
    showRefresh: false,
    minimumCountColumns: 2,
    clickToSelect: false,
    sidePagination: 'server',
    pageSize: cantidad_registros,
    search: false,
    cardView:false,
    pageList:'All',
    paginationVAlign: 'bottom',
    responsive: true,
    height: 200
  });
  <?php } else if(@$_SESSION["dispositivo"] == 'phone'){ ?>
  $('#table').bootstrapTable({
    method: 'get',
    cache: false,
    pagination: true,
    onlyInfoPagination: false,
    showColumns: false,
    showRefresh: false,
    minimumCountColumns: 2,
    clickToSelect: false,
    sidePagination: 'server',
    pageSize: cantidad_registros,
    search: false,
    searchAlign: 'left',
    cardView:true,
    pageList:'All',
    paginationVAlign: 'both',
    paginationHAlign: 'left',
    height: 200
  });
  <?php } ?>
  
  procesamiento_listar();
});
function procesamiento_listar(){
  
  var alto_documento = $(document).height();

  var data = $('#filtro_ingreso_egreso').serializeObject();
  var data2 = $('#filtro_ingreso_egreso2').serializeObject();
  
  $('#table').bootstrapTable('refreshOptions', {
    url: 'obtener_ingreso_egreso.php',
    queryParams: function (params) {
      
      var q = {
        "rows": cantidad_registros,
        "numfilas":cantidad_registros,
        "actual_row": params.offset,
        "pagina":(params.offset/cantidad_registros)+1,
        //"search": params.search,
        "sort": params.sort,
        "order": params.order
      };
      $.extend( data, q);
      $.extend( data, data2);
        
      var cantidad_total = $("#cantidad_total").val();
      if(cantidad_total){
        $.extend(data,{total:cantidad_total});
      }
      
      return data;
    },
        onLoadSuccess: function(data){
      var altoTabla = $("#table").height();
      var totalValores = 0;
      
      $("#cantidad_total").val(data.total);
      
      $("#calculadora").hide();
      $("#calculadora_total").html('0');
      
      $(".valores").each(function(){
        totalValores += parseInt($(this).html().replace(/[^0-9]/g, ''));
      });
      
      $("#celda_total_valor").html(Moneda_r(totalValores.toString()));

      <?php if(@$_SESSION["dispositivo"] == 'computer'){ ?>
      setTimeout(function(){
        $('#table').bootstrapTable('resetView' , {height: altoTabla+100} );
        //$(document).scrollTop( $('#capa_ingreso_egreso_list').offset().top -80 );        
      }, 500);
      
      <?php } ?>
      <?php if(@$_SESSION["dispositivo"] == 'phone'){ ?>
      setTimeout(function(){
        $('#table').bootstrapTable('resetView' , {height: altoTabla+150} );
        //$(document).scrollTop( $('#capa_ingreso_egreso_list').offset().top -80 );
      }, 500);
      <?php } ?>
    },
    onLoadError: function(data){
      var altoTabla = $("#table").height();
      
      $("#calculadora").hide();
      $("#calculadora_total").html('0');

      <?php if(@$_SESSION["dispositivo"] == 'computer'){ ?>
      setTimeout(function(){
        $('#table').bootstrapTable('resetView' , {height: altoTabla+100} );
        //$(document).scrollTop( $('#capa_ingreso_egreso_list').offset().top -80 );
      }, 500);
      <?php } ?>
      <?php if(@$_SESSION["dispositivo"] == 'phone'){ ?>
      setTimeout(function(){
        $('#table').bootstrapTable('resetView' , {height: altoTabla+150} );
        //$(document).scrollTop( $('#capa_ingreso_egreso_list').offset().top -80 );
      }, 500);
      <?php } ?>
    }
  });
}

function jsonConcat(o1, o2) {
   for (var key in o2) {
    o1[key] = o2[key];
   }
   return o1;
  }
$.fn.serializeObject = function(){
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};
/*
 * procesar_capa_info_ingreso_egreso
 * Encargada de mostrar los valores totales en la parte superior derecha
 */
function procesar_capa_info_ingreso_egreso(){
  var x_empresa = $("#empresa").val();
  var x_fechaInicial = $("#fechai").val();
  var x_fechaFinal = $("#fechaf").val();
  var x_capaActiva = '';
  
  $(".nav-link").each(function(indice){
    if($(this).attr("aria-selected") == 'true'){
      x_capaActiva = $(this).attr("id");
    }
  });
  
  $.ajax({
      url: 'ejecutar_acciones.php',
      type: 'POST',
      dataType: 'json',
      async: false,
      data: {ejecutar: 'obtener_gastos_ingresos_egresos', empresa: x_empresa, fechai: x_fechaInicial, fechaf: x_fechaFinal, capa_activa: x_capaActiva},
      success : function(respuesta){
        if(respuesta.exito){
          $("#capa_info_ingreso_egreso").html(respuesta.html);
          $("#fecha").val(respuesta.fecha);
        } else {
          notificacion(respuesta.mensaje,'warning',4000);
        }
      }
    });
}
function Moneda_r(valor){
    var num = valor.replace(/\./g,'');
    if(!isNaN(num)){
         num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
        num = num.split('').reverse().join('').replace(/^[\.]/,'');
        return(num);
    }
}

function Medida_r(valor){
    var num = valor.replace(/\./g,'');
    if(!isNaN(num)){
         num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{2})/g,'$1.');
        num = num.split('').reverse().join('').replace(/^[\.]/,'');
        return(num);
    }
}

$(document).ready(function(){
  $("#valor").keyup(function(){
      var valor=$(this).val().replace(/[^0-9]/g, '');
      $(this).val(Moneda_r(valor));
  });
  $("#valor").blur(function(){
      var valor=$(this).val().replace(/[^0-9]/g, '');
      $(this).val(Moneda_r(valor));
  });
  
  $("#valor_filtro").keyup(function(){
      var valor=$(this).val().replace(/[^0-9]/g, '');
      $(this).val(Moneda_r(valor));
  });
  $("#valor_filtro").blur(function(){
      var valor=$(this).val().replace(/[^0-9]/g, '');
      $(this).val(Moneda_r(valor));
  });
  
  $(".redimensionar").click(function(){
     setTimeout(function(){ $('#table').bootstrapTable('resetWidth'); }, 500);
  });
});
</script>
<?php echo(pie()); ?>
<?php include_once($atras . "ventanas/ingreso_egreso/librerias_js_reporte_ingreso_egreso.php"); ?>