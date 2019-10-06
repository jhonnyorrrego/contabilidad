<?php
function estilos_generales(){
	global $raiz; 
	$texto='<link rel="stylesheet" type="text/css" href="' . $raiz . 'css/gym.css">';
	$texto.='<script src="' . $raiz . 'js/gym.js"></script>';
	return($texto);
}
function bootstrap_css(){
	global $raiz; 
	$texto='<link rel="stylesheet" type="text/css" href="' . $raiz . 'vendor/twbs/bootstrap/dist/css/bootstrap.css">';
	return($texto);
}
function bootstrap_js(){
	global $raiz; 
	$texto='<script src="' . $raiz . 'vendor/twbs/bootstrap/dist/js/bootstrap.js"></script>';
	return($texto);
}
function jquery_js(){
	global $raiz; 
	$texto='<script src="' . $raiz . 'vendor/components/jquery/jquery.js"></script>';
	return($texto);
}
function notificacion(){
	global $raiz, $conexion; 
	//$texto='<script src="' . $raiz . 'js/jquery.growl.js"></script>';
	//$texto.='<link rel="stylesheet" type="text/css" href="' . $raiz . 'css/jquery.growl.css">';

	//$texto='<script src="' . $raiz . 'js/pnotify.custom.js"></script>';
	//$texto.='<link rel="stylesheet" type="text/css" href="' . $raiz . 'css/pnotify.custom.css">';

	$texto = '<script src="' . $raiz . 'notificacion/toastr.js"></script>';
	$texto .= '<link rel="stylesheet" type="text/css" href="' . $raiz . 'notificacion/toastr.css">';

	$texto .= '<script src="' . $raiz . 'js/librerias_notificacion.js"></script>';

	$movil = $conexion -> detectar_movil();
	if(@$movil == 'phone'){
		$position = "bottom-center";
	} else {
		$position = "top-center";
	}

	$texto .= '<script>
	toastr.options = {
	  "closeButton": false,
	  "debug": false,
	  "newestOnTop": false,
	  "progressBar": false,
	  "rtl": false,
	  "positionClass": "toast-' . $position . '",
	  "preventDuplicates": false,
	  "onclick": null,
	  "showDuration": 300,
	  "hideDuration": 1000,
	  "timeOut": 5000,
	  "extendedTimeOut": 1000,
	  "showEasing": "swing",
	  "hideEasing": "linear",
	  "showMethod": "fadeIn",
	  "hideMethod": "fadeOut"
	}
	</script>';

	return($texto);
}
function login_css(){
	global $raiz;
	$texto='<link rel="stylesheet" type="text/css" href="' . $raiz . 'css/login.css">';
	return($texto);
}
function jquery_validate(){
	global $raiz; 
	$texto='<script src="' . $raiz . 'js/jquery.validate.js"></script>';
	return($texto);
}
function bootstrap_table(){
	global $raiz;
	$texto='<script src="' . $raiz . 'js/bootstrap-table.js"></script>';
	$texto.='<script src="' . $raiz . 'js/locale/bootstrap-table-es-ES.js"></script>';
	$texto.='<link rel="stylesheet" type="text/css" href="' . $raiz . 'css/bootstrap-table.css">';
	return($texto);
}
function bootstrap_datepicker(){
	global $raiz;
	$texto='<script src="' . $raiz . 'js/datepicker/bootstrap-datepicker.js"></script>';
	$texto.='<link rel="stylesheet" type="text/css" href="' . $raiz . 'css/datepicker/bootstrap-datepicker.css">';
	$texto.='<script src="' . $raiz . 'js/datepicker/locales/bootstrap-datepicker.es.js"></script>';
	return($texto);
}
function estilos_iconos(){
	global $raiz;
	$texto='<link rel="stylesheet" type="text/css" href="' . $raiz . 'css/css/all.css">';
	return($texto);
}
function estilo_cargando(){
	global $raiz;
	$texto='<link rel="stylesheet" type="text/css" href="' . $raiz . 'css/cargando.css">';
	return($texto);
}
function date_format_jquery(){
	global $raiz;
	$texto='<script src="' . $raiz . 'js/jquery-dateformat.js"></script>';
	return($texto);
}
function chart(){
	global $raiz;
	$texto='<script src="' . $raiz . 'js/Chart.bundle.js"></script>';

	return($texto);
}
function tema_dashboard_lite($basic=null){
	global $raiz;
	include_once($raiz . "template/dashboard-lite/funciones_tema.php");
	$texto='<link rel="stylesheet" type="text/css" href="' . $raiz . 'css/css/all.css">';
	$texto.='<link rel="stylesheet" type="text/css" href="' . $raiz . 'vendor/twbs/bootstrap/dist/css/bootstrap.css">';
	$texto.='<link rel="stylesheet" type="text/css" href="' . $raiz . 'template/dashboard-lite/styles/shards-dashboards.1.1.0.css">';
	$texto.='<link rel="stylesheet" type="text/css" href="' . $raiz . 'template/dashboard-lite/styles/extras.1.1.0.min.css">';
	$texto.='<script src="' . $raiz . 'vendor/components/jquery/jquery.js"></script>';
	$texto.='<script src="' . $raiz . 'template/dashboard-lite/externs/popper.min.js"></script>';
	$texto.='<script src="' . $raiz . 'vendor/twbs/bootstrap/dist/js/bootstrap.js"></script>';
	if(!$basic){
		$texto.= chart();
		$texto.='<script src="' . $raiz . 'template/dashboard-lite/externs/shards.min.js"></script>';
		$texto.='<script src="' . $raiz . 'template/dashboard-lite/externs/jquery.sharrre.min.js"></script>';
		$texto.='<script src="' . $raiz . 'template/dashboard-lite/scripts/extras.1.1.0.min.js"></script>';
		$texto.='<script src="' . $raiz . 'template/dashboard-lite/scripts/shards-dashboards.1.1.0.min.js"></script>';
		$texto.='<script src="' . $raiz . 'template/dashboard-lite/scripts/app/app-blog-overview.1.1.0.js"></script>';
	}
	$texto.='<link rel="stylesheet" type="text/css" href="' . $raiz . 'template/dashboard-lite/styles/accents/success.1.1.0.css">';

	return($texto);
}
function tema_majestic_master($basic=null){
	global $raiz;
	include_once($raiz . "template/majestic-master/funciones_tema.php");
	$texto ='<link rel="stylesheet" type="text/css" href="' . $raiz . 'vendor/twbs/bootstrap/dist/css/bootstrap.css">';
	$texto.='<link rel="stylesheet" href="' . $raiz . 'template/majestic-master/vendors/mdi/css/materialdesignicons.min.css">';
	$texto.='<link rel="stylesheet" href="' . $raiz . 'template/majestic-master/vendors/base/vendor.bundle.base.css">';
	$texto.='<link rel="stylesheet" href="' . $raiz . 'template/majestic-master/vendors/datatables.net-bs4/dataTables.bootstrap4.css">';
	$texto.='<link rel="stylesheet" href="' . $raiz . 'template/majestic-master/css/style.css">';
	$texto.='<link rel="shortcut icon" href="' . $raiz . 'template/majestic-master/images/favicon.png" />';
	
	$texto.='<script src="' . $raiz . 'vendor/components/jquery/jquery.js"></script>';
	$texto.='<script src="' . $raiz . 'template/majestic-master/vendors/base/vendor.bundle.base.js"></script>';
	$texto.='<script src="' . $raiz . 'vendor/twbs/bootstrap/dist/js/bootstrap.js"></script>';
	$texto.='<script src="' . $raiz . 'template/majestic-master/vendors/datatables.net/jquery.dataTables.js"></script>';
	$texto.='<script src="' . $raiz . 'template/majestic-master/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>';
	$texto.='<script src="' . $raiz . 'template/majestic-master/js/off-canvas.js"></script>';
	$texto.='<script src="' . $raiz . 'template/majestic-master/js/hoverable-collapse.js"></script>';
	$texto.='<script src="' . $raiz . 'template/majestic-master/js/template.js"></script>';
	$texto.='<script src="' . $raiz . 'template/majestic-master/js/dashboard.js"></script>';
	$texto.='<script src="' . $raiz . 'template/majestic-master/js/data-table.js"></script>';
	$texto.='<script src="' . $raiz . 'template/majestic-master/js/jquery.dataTables.js"></script>';
	$texto.='<script src="' . $raiz . 'template/majestic-master/js/dataTables.bootstrap4.js"></script>';
	
	
	if(!$basic){
		$texto.= chart();
	}

	return($texto);
}
?>