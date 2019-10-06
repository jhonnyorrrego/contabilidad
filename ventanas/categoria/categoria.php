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

$alto_tabla = "alto_documento-280";
if (@$_SESSION["dispositivo"] == 'phone') {
  $alto_tabla = "alto_documento+330";
}
?>
<?php echo(encabezado());?>
<?php echo(funciones_js_tema()); ?>
<style>
.error{
  color:red;
}
</style>
<script type='text/javascript'>
$().ready(function() {
  procesamiento_listar();
  
  // validar los campos del formato
  $('#categoria_add').validate();
  
  $("#guardar_categoria_formulario").click(function(){
    var formulario = $("#categoria_add");
    var resultado = formulario.valid();
    
    if(resultado){
      var data = $(formulario).serializeArray(); // convert form to array
      data.push({name: "ejecutar", value: 'guardar_categoria_formulario'});
      
      $.ajax({
        url : 'ejecutar_acciones.php',
        type : 'POST',
        dataType: 'json',
        data: $.param(data),
        success : function(resultado){
          if(resultado.exito){
            notificacion(resultado.mensaje,'success',5000);
            
            procesamiento_listar();
          }else{
            notificacion(resultado.mensaje,'warning',5000);
          }
        }
      });
    }
  });
});
</script>

<div class="row">
  <div class="col-md-12 grid-margin">
    <div class="d-flex justify-content-between flex-wrap">
      <div class="d-flex align-items-end flex-wrap">
        <div class="mr-md-3 mr-xl-5">
          <h2>Administración de categorias</h2>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="capa_categoria_add" class="row">
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Crear categoria</h4>
        <form class="" name="categoria_add" id="categoria_add">
          <div class="row">
              <div class="col-md-3 form-group">
                <label class="">Nombre*</label>
                <input type="text" id="nombre" name="nombre" class="form-control form-control-sm required">
              </div>
              
              <div class="col-md-3 form-group">
                  <label>Empresa vinculada</label>
                  <select class="form-control form-control-sm" id="fk_idemp" name="fk_idemp">
                    <option value="">Seleccione</option>
<?php
$sql = "select idemp,nombre from empresa order by nombre asc";
$empresas = $conexion -> listar_datos($sql);
for ($i=0; $i < $empresas["cant_resultados"]; $i++) { 
	echo("<option value='" . $empresas[$i]["idemp"] . "'>" . $empresas[$i]["nombre"] . "</option>");
}
?>
                  </select>
              </div>
              
              <div class="col-md-3 form-group">
                  <label class="">Estado*</label>
                  <select class="form-control form-control-sm required" id="estado" name="estado">
                    <option value="">Estado</option>
                    <option value="1" selected>Activo</option>
                    <option value="2">Inactivo</option>
                  </select>
              </div>
          </div>
          <button type="button" id="guardar_categoria_formulario" class="btn btn-primary mb-2">Guardar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="capa_categoria_edit" class="row" style="display:none">
</div>

<div class="row">
  <div class="col-md-12 stretch-card">
    <div class="card">
      <div class="card-body">
        <p class="card-title">Lista categorias</p>
        <div class="table-responsive">
          <table id="table" class="table" role="grid" style="width: 100%">
            <thead class="">
              <tr>
                <th data-field="nombre" data-sortable="true" data-visible="true">Nombre</th>
                <th data-field="empresa_vinculada" data-sortable="false" data-visible="true">Empresa vinculada</th>
                <th data-field="estado_funcion" data-sortable="false" data-visible="true">Estado</th>
                <th data-field="acciones_categoria" data-sortable="false" data-visible="true"></th>
              </tr>
            </thead>
          </table>
          <input type="hidden" id="cantidad_total">
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$body = $("body");

var cantidad_registros = 10;
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
    height: alto_tabla
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
    height: 3250
  });
  <?php } ?>
  
  procesamiento_listar();
});
function procesamiento_listar(){
  var alto_documento = $(document).height();

  var data = $('#form_table').serializeObject();
  
  $('#table').bootstrapTable('getOptions').sidePagination = 'client';
  $('#table').bootstrapTable('selectPage', 1);
  $('#table').bootstrapTable('getOptions').sidePagination = 'server';
  
  $('#table').bootstrapTable('refreshOptions', {
    url: 'obtener_categorias.php',
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
        
      var cantidad_total = $("#cantidad_total").val();
      if(cantidad_total){
        $.extend(data,{total:cantidad_total});
      }
      
      return data;
    },
        onLoadSuccess: function(data){
      $("#cantidad_total").val(data.total);
      var altoTabla = $("#table").height();

      <?php if(@$_SESSION["dispositivo"] == 'computer'){ ?>
      
      <?php } ?>
      <?php if(@$_SESSION["dispositivo"] == 'phone'){ ?>
      setTimeout(function(){
        $('#table').bootstrapTable('resetView' , {height: altoTabla+150} );
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
</script>
<?php echo(pie()); ?>
<?php include_once($atras . "ventanas/categoria/librerias_reporte_categoria_js.php"); ?>