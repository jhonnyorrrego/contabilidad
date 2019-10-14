<?php
function encabezado(){
	global $atras;
	?>
<!doctype html>
<html class="no-js h-100" lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, maximum-scale=1">
  </head>
  <body>
    <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="navbar-brand-wrapper d-flex justify-content-center redimensionar">
        <div class="navbar-brand-inner-wrapper d-flex justify-content-between align-items-center w-100">  
          <a class="navbar-brand brand-logo" href="index.html"><img src="<?php echo($atras); ?>img/logo.svg" alt="logo"/></a>
          <a class="navbar-brand brand-logo-mini" href="index.html"><img src="<?php echo($atras); ?>img/logo-mini.svg" alt="logo"/></a>
          <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-sort-variant"></span>
          </button>
        </div>  
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown" aria-expanded="true">
              <!--img src="images/faces/face5.jpg" alt="profile"/-->
              <span class="nav-profile-name"><?php echo(@$_SESSION["nombres"] . " " .@$_SESSION["apellidos"]); ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item">
                <i class="mdi mdi-settings text-primary"></i>
                Configuración
              </a>
              <a class="dropdown-item" href="<?php echo($atras); ?>ventanas/ingreso/salir.php">
                <i class="mdi mdi-logout text-primary"></i>
                Salir
              </a>
            </div>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="mdi mdi-menu"></span>
        </button>
      </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo($atras); ?>ventanas/ingreso_egreso/area_ingreso_egreso.php">
              <i class="mdi mdi-home menu-icon"></i>
              <span class="menu-title">Área de trabajo</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
              <i class="mdi mdi-settings menu-icon"></i>
              <span class="menu-title">Administración</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="auth">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="<?php echo($atras); ?>ventanas/usuario/usuario_add.php"> Usuarios </a></li>
                <li class="nav-item"> <a class="nav-link" href="<?php echo($atras); ?>ventanas/empresa/empresa.php"> Empresas </a></li>
                <li class="nav-item"> <a class="nav-link" href="<?php echo($atras); ?>ventanas/categoria/categoria.php"> Categorias </a></li>
                <li class="nav-item"> <a class="nav-link" href="<?php echo($atras); ?>ventanas/bolsillo/bolsillo.php"> Bolsillos </a></li>
              </ul>
            </div>
          </li>
        </ul>
      </nav>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
	<?php
}
function pie(){
	global $atras;
	?>
		  </div>
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  </body>
</html>
	<?php
}
function funciones_js_tema(){
  global $atras;
  ?>
<script>
$(document).ready(function(){
});
function actualizar_informacion_sesion(){
    $.ajax({
        url : '<?php echo($atras); ?>ventanas/usuario/ejecutar_acciones.php',
        type : 'POST',
        dataType: 'json',
        data: {ejecutar: 'obtener_informacion_sesion'},
        success : function(resultado){
            if(resultado.exito){
                $(".datos_sesion").html(resultado.datos_sesion);
            }
        }
    });
}
function actualizar_notificacion(){
    $.ajax({
        url : '<?php echo($atras); ?>ventanas/usuario/ejecutar_acciones.php',
        type : 'POST',
        dataType: 'json',
        data: {ejecutar: 'obtener_notificaciones_usuario'},
        success : function(resultado){
            if(resultado.exito){
                $(".notificaciones").html(resultado.html_notificaciones);
            }
        }
    });
}
function str_pad(str, pad_length, pad_string, pad_type){
  var len = pad_length - str.length;
  if(len < 0) return str;
  var pad = new Array(len + 1).join(pad_string);
  if(pad_type == "STR_PAD_LEFT") return pad + str;
  return str + pad;
}
</script>
  <?php
}
?>