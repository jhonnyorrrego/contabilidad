<?php
date_default_timezone_set("America/Bogota");

if (!defined("SERVIDOR")){
	define("SERVIDOR", "127.0.0.1");
}
if (!defined("USUARIO")){
	define("USUARIO", "u292290787_conta");
}
if (!defined("CLAVE")){
	define("CLAVE", "admin123");
}
if (!defined("DB")){
	define("DB", "u292290787_conta");
}
if (!defined("MOTOR")){
	define("MOTOR", "mysql");
}
if (!defined("ALMACENAMIENTO")){
	define("ALMACENAMIENTO", "../archivos/");
}
if (!defined("PERMISO_CARPETA")){
	define("PERMISO_CARPETA", 0777);
}
if (!defined("PERMISO_ARCHIVO")){
	define("PERMISO_ARCHIVO", 0777);
}
if (!defined("LLAVE_SESION")){
	define("LLAVE_SESION", "CONTABILIDAD");
}

ini_set("display_errors",false);
ini_set("safe_mode",false);

//Constantes predefinidas MYSQL
define('MYSQL_BOTH',MYSQLI_BOTH);
define('MYSQL_NUM',MYSQLI_NUM);
define('MYSQL_ASSOC',MYSQLI_ASSOC);
?>