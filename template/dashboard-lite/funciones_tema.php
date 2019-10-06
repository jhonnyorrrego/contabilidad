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
  <body class="h-100">
    <div class="container-fluid">
      <div class="row">
        <!-- Main Sidebar -->
        <aside class="main-sidebar col-12 col-md-3 col-lg-2 px-0">
          <div class="main-navbar">
            <nav class="navbar align-items-stretch navbar-light bg-white flex-md-nowrap border-bottom p-0">
              <a class="navbar-brand w-100 mr-0" href="#" style="line-height: 25px;">
                <div class="d-table m-auto">
                  <img id="main-logo" class="d-inline-block align-top mr-1 img-fluid rounded" src="<?php echo($atras); ?>img/logo.png" alt="logo" style="width:150px;height:35px" alt="GYM Admin">
                  <span class="d-none d-md-inline ml-1"></span>
                </div>
              </a>
              <a class="toggle-sidebar d-sm-inline d-md-none d-lg-none">
                <i class="fas fa-arrow-left"></i>
              </a>
            </nav>
          </div>
          <?php if(@$_SESSION["tipo"] == 2){//Administrador ?>
          <div class="nav-wrapper">
            <ul class="nav flex-column">
              <!--li class="nav-item">
                <a id="enlace_ingreso" class="nav-link" href="<?php echo($atras); ?>ventanas/ingreso/ingreso.php">
                  <i class="fas fa-home"></i>
                  <span>Ingreso</span>
                </a>
              </li-->
              <li class="nav-item">

                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fas fa-users"></i>
                  <span>Usuarios</span>
                </a>
                <div class="dropdown-menu keep-open" aria-labelledby="navbarDropdown">
                  <a id="enlace_reporte_usuarios" class="dropdown-item enlaces" href="<?php echo($atras); ?>ventanas/usuario/reporte_usuarios.php">
                    <i class="fas fa-list-ul"></i> Lista de usuarios
                   </a>
                  <a id="enlace_usuario_add" class="dropdown-item enlaces" href="<?php echo($atras); ?>ventanas/usuario/usuario_add.php" id="usuario_nuevo">
                    <i class="fas fa-user-plus"></i> Usuario nuevo
                  </a>
                  
                   <!--div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Something else here</a-->
                </div>

              </li>
              <li class="nav-item">

                <a class="nav-link enlaces" href="<?php echo($atras); ?>ventanas/gestion/gestion.php" id="enlace_gestion">
                  <i class="fas fa-signal"></i>
                  <span>Gesti&oacute;n</span>
                </a>
                <!--div class="dropdown-menu keep-open" aria-labelledby="navbarDropdown2">
                  <a id="enlace_gestion" class="dropdown-item enlaces" href="<?php echo($atras); ?>ventanas/gestion/gestion.php">
                    <i class="far fa-money-bill-alt"></i> Ingresos
                  </a>
                </div-->

              </li>
              <!--li class="nav-item">
                <a class="nav-link " href="<?php echo($atras); ?>ventanas/ingreso/salir.php">
                  <i class="fas fa-power-off"></i>
                  <span>Cerrar sesi&oacute;n</span>
                </a>
              </li-->
            </ul>
          </div>
          <?php } ?>
        </aside>
        <!-- End Main Sidebar -->
        <main class="main-content col-lg-10 col-md-9 col-sm-12 p-0 offset-lg-2 offset-md-3">
          <div class="main-navbar sticky-top bg-white">
            <!-- Main Navbar -->
            <nav class="navbar align-items-stretch navbar-light flex-md-nowrap p-0">
              <form action="#" class="main-navbar__search w-100 d-none d-md-flex d-lg-flex">
                <div class="input-group input-group-seamless ml-3">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      
                    </div>
                  </div>
                </div>
              </form>
              <?php if(@$_SESSION["tipo"] == 1){//Cliente ?>
              <ul class="navbar-nav border-left flex-row ">
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle text-nowrap px-3 datos_sesion" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <img class="user-avatar rounded-circle mr-2" src="<?php echo($atras . @$_SESSION["img"]); ?>" alt="User Avatar">
                    <span class="d-none d-md-inline-block"><?php echo(@$_SESSION["nombres"] . " " .@$_SESSION["apellidos"]); ?></span>
                  </a>
                  <div class="dropdown-menu dropdown-menu-small">
                    <a class="dropdown-item" href="<?php echo($atras); ?>ventanas/usuario/ver_usuario_consulta.php?idusuario=<?php echo(@$_SESSION["idusu"]); ?>">
                      <i class="fas fa-user"></i> Perfil
                    </a>
                    <a class="dropdown-item" href="<?php echo($atras); ?>ventanas/graficos/generar_grafico_consulta.php?idusuario=<?php echo(@$_SESSION["idusu"]); ?>">
                      <i class="fas fa-chart-bar"></i> Estad&iacute;sticas
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="<?php echo($atras); ?>ventanas/ingreso/salir.php">
                      <i class="fas fa-power-off text-danger"></i> Salir </a>
                  </div>
                </li>
              </ul>
              <?php } ?>
              <?php if(@$_SESSION["tipo"] == 2){//Administrador ?>
              <ul class="navbar-nav border-left flex-row ">
                <li class="nav-item border-right dropdown notifications notificaciones" title="vencen hoy">
                  
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle text-nowrap px-3 datos_sesion" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <img class="user-avatar rounded-circle mr-2" src="<?php echo($atras . @$_SESSION["img"]); ?>" alt="User Avatar">
                    <span class="d-none d-md-inline-block"><?php echo(@$_SESSION["nombres"] . " " .@$_SESSION["apellidos"]); ?></span>
                  </a>
                  <div class="dropdown-menu dropdown-menu-small">
                    <a class="dropdown-item" href="<?php echo($atras); ?>ventanas/usuario/ver_usuario.php?idusuario=<?php echo(@$_SESSION["idusu"]); ?>">
                      <i class="fas fa-user"></i> Perfil
                    </a>
                    <a class="dropdown-item" href="<?php echo($atras); ?>ventanas/graficos/generar_grafico.php?idusuario=<?php echo(@$_SESSION["idusu"]); ?>">
                      <i class="fas fa-chart-bar"></i> Estad&iacute;sticas
                    </a>
                    <!--a class="dropdown-item" href="components-blog-posts.html">
                      <i class="fas fa-list-ul"></i> Blog Posts</a>
                    <a class="dropdown-item" href="add-new-post.html">
                      <i class="fas fa-plus-square"></i> Add New Post</a-->
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="<?php echo($atras); ?>ventanas/ingreso/salir.php">
                      <i class="fas fa-power-off text-danger"></i> Salir </a>
                  </div>
                </li>
              </ul>
              <?php } ?>
              <?php if(@$_SESSION["tipo"] == 2){//Administrador ?>
              <nav class="nav">
                <a href="#" class="nav-link nav-link-icon toggle-sidebar d-md-inline d-lg-none text-center border-left" data-toggle="collapse" data-target=".header-navbar" aria-expanded="false" aria-controls="header-navbar">
                  <i class="fas fa-align-justify"></i>
                </a>
              </nav>
            </nav>
            <?php } ?>
          </div>

          <div class="main-content-container container-fluid px-4">
	<?php
}
function pie(){
	global $atras;
	?>
		  </div>
		</main>
      </div>
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
  <?php if(@$_SESSION["tipo"] == 2){//Administrador ?>
  actualizar_notificacion();
  <?php } ?>

  $(document).on('click','.ver_usuario_notificacion',function(){
    var idusuario = $(this).attr("idusuario");
    var src = "<?php echo($atras); ?>ventanas/usuario/ver_usuario.php?idusuario=" + idusuario;
    window.open(src,"_self");
  });

  $('.dropdown-toggle').on('click', function (e) {
      $(this).next().toggle();
  });
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
</script>
  <?php
}
?>