<?php
@session_start();
include_once("config.php");

$conexion=new lib_gym();
class lib_gym{
	public $con;
	public function __construct(){
		$this->con = mysqli_connect(SERVIDOR,USUARIO,CLAVE) or die ('Error en la conexion');
		$db = mysqli_select_db($this->con,DB) or die ('Error en la DB');
	}
	public function __destruct(){
		mysqli_close($this->con);
	}
	/*Lista en una matriz los resultados de una consulta
	$consulta=sql del select
	*/
	public function listar_datos($consulta,$inicio=false,$cantidad=false){
		$retorno=array();
		if($cantidad){
			$consulta = $this -> listar_datos_limite($consulta,$inicio,$cantidad);
		}
		$res=mysqli_query($this->con,$consulta);
		$cantidad=0;
		while($result = mysqli_fetch_array($res,MYSQL_ASSOC)){
			array_push($retorno,$result);
			$cantidad++;
		}
		$retorno["sql"]=$consulta;
		$retorno["cant_resultados"]=$cantidad;
		mysqli_free_result($res);
		return($retorno);
	}
	/*Inserta en base de datos segun los parametros recibidos
	$tabla=Nombre de la tabla a insertar
	$campos=array con el nombre de los campos a insertar
	$valores=array con los valores a insertar, debe estar en el mismo orden del arreglo de campos
	*/
	public function insertar($tabla,$campos,$valores){
		$sql = "insert into ".$tabla."(".implode(",",$campos).")values(".htmlentities(implode(",",$valores)).")";
		mysqli_query($this->con,$sql) or die($sql);
		$id=mysqli_insert_id($this->con);
		return($id);
	}
	/*Modificar
	$tabla = Nombre de la tabla a modificar
	$valor = Arreglo del(os) Valor(es) nuevo(s)
	$condicion_update = filtro para realizar el update
	*/
	public function modificar($tabla,$valor,$condicion_update,$id=Null){
		$sql = "update " . $tabla . " set " . implode(",", $valor) . " where " . $condicion_update;
		mysqli_query($this->con,$sql);
		return(true);
	}
	/*Detecta si el dispositivo en el que se abrio la aplicacion es movil o computador
	*/
	public function detectar_movil($session=false){
		$detect = new Mobile_Detect;
		$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

		if($session){
		  @$_SESSION["dispositivo"] = $deviceType;
		}
		return($deviceType);
	}
	/*
	funcion pensada a futuro cuando se necesite realizar limite a las consultas sobre otros motores
	La funcion retorna el SQL convertido para ejecutar los limites.
	*/
	private function listar_datos_limite($consulta,$inicio,$cantidad){
		$consulta = $consulta . " LIMIT " . $inicio . "," . $cantidad;
		return($consulta);
	}
	public function obtener_datos_usuario($idusu){
		$consulta = "select * from usuario where idusu=" . $idusu;
		$datos = $this -> listar_datos($consulta);
		
		return($datos);
	}
	public function obtener_idusu_usuario($identificacion){
		$consulta = "select idusu from usuario where identificacion='" . $identificacion . "'";
		$datos = $this -> listar_datos($consulta);
		$idusu = "";

		if($datos["cant_resultados"]){
			$idusu = $datos[0]["idusu"];
		} else {
			$idusu = false;
		}
		
		return($idusu);
	}
	public function obtener_imagen_usuario($idusu){
		$consulta = "select b.ruta from usuario a, anexo b where idusu=" . $idusu . " and a.imagen=b.idane and b.estado=1";
		$datos = $this -> listar_datos($consulta);

		if($datos["cant_resultados"]){
			return($datos[0]["ruta"]);
		} else {
			return(false);
		}
	}
	/*
	Funcion encargada en obtener opciones de listas desplegables de campos que esten en formatos

	$campo = Nombre de las opciones a obtener

	Retorna matriz con las opciones del campo.
	*/
	public function obtener_opciones_campo($campo){
		$retorno = array();
		switch ($campo) {
			case 'medir':
				$consulta = "select a.idmed_cor as id, a.etiqueta as nombre from medida_corporal a where a.estado=1";
				$retorno = $this -> listar_datos($consulta);
				break;
			
			default:
				# code...
				break;
		}

		return($retorno);
	}
	public function ingreso_usuario($idusu){
		$hoy = date('Y-m-d');
		$retorno = array();

		$datos_usuario = $this -> obtener_datos_usuario($idusu);
		if($datos_usuario[0]["estado"] == 2){//Si el usuario esta inactivo no debe dejar ingresar
			$retorno["exito"] = 0;
			$retorno["mensaje"] = "inactivo";

			return($retorno);
		}
		$consultar_ingreso = "select * from ingreso a where a.idusu=" . $idusu . " and date_format(a.fecha,'%Y-%m-%d')='" . $hoy . "' and a.estado=1";
		$ingresos_array = $this -> listar_datos($consultar_ingreso);
		if($ingresos_array["cant_resultados"]){
			$retorno["exito"] = 0;
			$retorno["mensaje"] = "insertado";
			$retorno["iding"] = $ingresos_array[0]["iding"];

			return($retorno);
		}

		if($datos_usuario[0]["tipo_mensualidad"] == 1){//Si el tipo de mensualidad es de rango de fechas
			if(!($hoy >= $datos_usuario[0]["fechai"] && $hoy <= $datos_usuario[0]["fechaf"])){//Si el usuario esta fuera del rango de la mensualidad
				$retorno["exito"] = 0;
				$retorno["mensaje"] = "fuera_rango";

				return($retorno);
			}

			$iding = $this -> insertar_ingreso($idusu);

			if($iding){//Se registra correctamente
				$retorno["exito"] = 1;
				$retorno["iding"] = $iding;
			} else {//Si la DB arrojo error al violar restriccion de usuario y fecha
				$retorno["exito"] = 0;
				$retorno["mensaje"] = "insertado";
			}
			
		} else if($datos_usuario[0]["tipo_mensualidad"] == 2){//Si el tipo de pago es de dantidad de dias
			$consultaDatosMensualidad = "select date_format(a.fecha, '%Y-%m-%d') as x_fecha,a.dias_faltantes,a.cantidad_dias,a.idmen from mensualidad a where idusu=" . $idusu . " and a.estado=1 order by idmen desc";
			$datosMensualidad = $this -> listar_datos($consultaDatosMensualidad);

			$diasUsados = $this -> cantidadDiasUtilizados($datosMensualidad[0]["x_fecha"],$idusu);
			$diasUsados = $diasUsados["diasUtilizados"];

			if($diasUsados > $datos_usuario[0]["cantidad_dias"]){
				$retorno["exito"] = 0;
				$retorno["mensaje"] = "dias_agotados";

				return($retorno);
			}

			if($datos_usuario[0]["dias_faltantes"] <= 0 || !$datos_usuario[0]["dias_faltantes"]){//si la cantidad de días se agotaron, no dejar ingresar.
				$retorno["exito"] = 0;
				$retorno["mensaje"] = "dias_agotados";

				return($retorno);
			}

			$tabla = 'mensualidad';
			$valores = array();
			$condicion = '';

			$resultadoDiasFaltantes = ($datosMensualidad[0]["cantidad_dias"] - $diasUsados) - 1;

			$valores[] = "dias_faltantes='" . $resultadoDiasFaltantes . "'";
			$condicion = "idmen=" . $datosMensualidad[0]["idmen"] . "";
			$this -> modificar($tabla,$valores,$condicion);//Se calcula dias faltantes y se actualiza en la tabla mensualidad

			$tabla = 'usuario';
			$condicion = "idusu=" . $idusu . "";
			$this -> modificar($tabla,$valores,$condicion);//Se calcula dias faltantes y se actualiza en la tabla usuario

			$iding = $this -> insertar_ingreso($idusu);
			if($iding){//Se registra correctamente
				$retorno["exito"] = 1;
				$retorno["iding"] = $iding;
			} else {//Si la DB arrojo error al violar restriccion de usuario y fecha
				$retorno["exito"] = 0;
				$retorno["mensaje"] = "insertado";
			}
		}

		return($retorno);
	}
	/*
	*/
	public function cantidadDiasUtilizados($fechaInicial,$idusu){
		global $conexion;
		$consultaDiasUtilizados = "select count(*) as cant_dias_utilizados from ingreso a where date_format(a.fecha_ingreso, '%Y-%m-%d')>='" . $fechaInicial . "' and a.idusu=" . $idusu . " and a.estado=1";
		$diasUtilizados = $this -> listar_datos($consultaDiasUtilizados);

		$retorno = array();
		$retorno["diasUtilizados"] = $diasUtilizados[0]["cant_dias_utilizados"];

		return($retorno);
	}
	/*
	Inserciones a la base de datos sobre ingreso

	@$idusu = id del usuario a registrar ingreso
	*/
	private function insertar_ingreso($idusu){
		$hoy = date('Y-m-d');
		$fecha = date('Y-m-d H:i:s');

		$tabla = "ingreso";
		$campos = array('idusu', 'fecha', 'fecha_ingreso', 'estado');
		$valores = array($idusu, "date_format('" . $hoy . "','%Y-%m-%d')", "date_format('" . $fecha . "','%Y-%m-%d %H:%i:%s')", 1);
		$iding = $this -> insertar($tabla,$campos,$valores);

		return($iding);
	}
	/*
	Funcion encargada de realizar la estructuración de las carpetas en el servidor
	*/
	public function parsear_ruta_almacenamiento($idusu,$atras){
		$consulta = "select date_format(a.fecha, '%Y-%m-%d') as x_fecha from usuario a where idusu=" . $idusu;
		$resultados = $this -> listar_datos($consulta);

		$datos_fecha = explode("-", $resultados[0]["x_fecha"]);
		$ruta = ALMACENAMIENTO . $idusu . "/" . $datos_fecha[0] . "/" . $datos_fecha[1] . "/" . $datos_fecha[2] . "/";

		$this -> crear_carpetas($atras . $ruta);

		return $ruta;
	}
	/*
	funcion encargada de obtener arreglo $_FILES y realizar el proceso de guardado sobre las carpetas del servidor
	*/
	public function procesar_imagen_usuario($idusu, $atras, $validarExtension=array()){
		$retorno = array();
		$retorno["exito"] = 1;

		$idane = array();

		$campos = array('fecha', 'estado', 'etiqueta', 'tamano', 'tipo', 'ruta');
		$hoy = "date_format('" . date('Y-m-d H:i:s') . "', '%Y-%m-%d %H:%i:%s')";
		$estado = 1;

		if($validarExtension){
			foreach ($_FILES as $key => $value) {
				$info_anexo = pathinfo($value["name"]);
				$extension = strtolower($info_anexo["extension"]);

				if(!(in_array($extension, $validarExtension))){
					$retorno["exito"] = 0;
					$retorno["error"] = 'error_extension';
					return $retorno;
				}
			}
		}

		foreach ($_FILES as $key => $value) {
			$info_anexo = pathinfo($value["name"]);

			$nombre = $value["name"];
			$nuevo_nombre = rand(0,9999999) . "." . $info_anexo["extension"];
			$tamano = $value["size"];
			$ruta_temporal = $value["tmp_name"];
			$extension = $info_anexo["extension"];

			$ruta = $this -> parsear_ruta_almacenamiento($idusu, $atras) . $nuevo_nombre;

			if($this -> resizeImage($ruta_temporal, $atras . ALMACENAMIENTO . $ruta)){
				unlink($ruta_temporal);
				$idanexo = $this -> insertar('anexo',$campos,array($hoy,$estado,"'" . $nombre . "'",$tamano,"'" . $extension . "'","'" . $ruta . "'"));
				$idane[] = $idanexo;

				$camposAneUsu = array('fk_idane', 'fk_idusu');
				$this -> insertar('anexo_usuario',$camposAneUsu, array($idanexo,$idusu));
			}
		}
		$retorno["idanexos"] = implode(",", $idane);

		return($retorno);
	}
	/*
	funcion encargada de obtener arreglo $_FILES y realizar el proceso de guardado sobre las carpetas del servidor
	*/
	public function procesar_anexos($idusu, $atras, $validarExtension=array()){
		$retorno = array();
		$retorno["exito"] = 1;

		$idane = array();

		$campos = array('fecha', 'estado', 'etiqueta', 'tamano', 'tipo', 'ruta');
		$hoy = "date_format('" . date('Y-m-d H:i:s') . "', '%Y-%m-%d %H:%i:%s')";
		$estado = 1;

		if($validarExtension){
			foreach ($_FILES as $key => $value) {
				$info_anexo = pathinfo($value["name"]);
				$extension = strtolower($info_anexo["extension"]);

				if(!(in_array($extension, $validarExtension))){
					$retorno["exito"] = 0;
					$retorno["error"] = 'error_extension';
					return $retorno;
				}
			}
		}

		foreach ($_FILES as $key => $value) {
			$info_anexo = pathinfo($value["name"]);

			$nombre = $value["name"];
			$nuevo_nombre = rand(0,9999999) . "." . $info_anexo["extension"];
			$tamano = $value["size"];
			$ruta_temporal = $value["tmp_name"];
			$extension = $info_anexo["extension"];

			$ruta = $this -> parsear_ruta_almacenamiento($idusu, $atras) . $nuevo_nombre;

			if(copy($ruta_temporal, $atras . ALMACENAMIENTO . $ruta)){
				unlink($ruta_temporal);
				$idanexo = $this -> insertar('anexo',$campos,array($hoy,$estado,"'" . $nombre . "'",$tamano,"'" . $extension . "'","'" . $ruta . "'"));
				$idane[] = $idanexo;

				$camposAneUsu = array('fk_idane', 'fk_idusu');
				$this -> insertar('anexo_usuario',$camposAneUsu, array($idanexo,$idusu));
			}
		}
		$retorno["idanexos"] = implode(",", $idane);

		return($retorno);
	}
	public function eliminar_anexo_usuario($idane){
		$tabla = "anexo";
		$valor[] = "estado='0'";
		$condicion = "idane=" . $idane;

		$this -> modificar($tabla,$valor,$condicion,$idane);

		return(true);
	}
	public function crear_carpetas($destino){
		$arreglo=explode("/",$destino);
	  	if(is_array($arreglo)){
	   		$cont=count($arreglo);
	   		for($i=0;$i<$cont;$i++){
		    	if(!is_dir($arreglo[$i])){
			      	chmod($arreglo[$i-1],PERMISO_CARPETA);
			      	if(!mkdir($arreglo[$i],PERMISO_CARPETA)){
			        	die("no es posible crear la carpeta ".$destino);
			    	} else if(isset($arreglo[($i+1)])){
		        		$arreglo[($i+1)]=$arreglo[$i]."/".$arreglo[($i+1)];
			    	}
	      		} else if(isset($arreglo[($i+1)])){
	        		$arreglo[($i+1)]=$arreglo[$i]."/".$arreglo[($i+1)];
	    		}
	    	}
	  	}
	 	return($destino);
	}
	/*public function resizeImage($original_image_data, $original_width, $original_height, $new_width, $new_height){
	    $dst_img = ImageCreateTrueColor($new_width, $new_height);
	    imagecolortransparent($dst_img, imagecolorallocate($dst_img, 0, 0, 0));
	    imagecopyresampled($dst_img, $original_image_data, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
	    return $dst_img;
	}*/
	public function resizeImage($nombreorig,$nombredest,$nwidth=640,$nheight=640){
		$ext='jpg';
		// Se obtienen las nuevas dimensiones
		list($width, $height) = getimagesize($nombreorig);
		if ($nwidth && ($width < $height)) {
			//$nwidth = ($nheight / $height) * $width;
		} else {
			//$nheight = ($nwidth / $width) * $height;
		}
		$image_p = imagecreatetruecolor($nwidth,$nheight);
		imagecolorallocate ($image_p, 255, 255, 255);

		if($ext == 'gif'){
			$image = imagecreatefromgif($nombreorig);///nombre del archivo origen
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $nwidth, $nheight, $width, $height);
			imagegif($image_p, $nombredest);///nombre del destino
			imagedestroy($image_p);
			imagedestroy($image);
			return($nombredest);
		} else {
			$image = imagecreatefromjpeg($nombreorig);
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $nwidth, $nheight, $width, $height);
			imagejpeg($image_p, $nombredest, 80);///nombre del destino
			imagedestroy($image_p);
			imagedestroy($image);
			return($nombredest);
		}

		imagedestroy($image_p);
		imagedestroy($image);
		return(true);
	}
	/*
	obtener_texto_mensualidad
	Recibe el idusu
	Retorna una cadena
	funcion encargada de mostrar el rango de la mensualidad asignada
	*/
	public function obtener_texto_mensualidad($idusu){
		$consulta = "select tipo_mensualidad, cantidad_dias,date_format(fechai, '%Y-%m-%d') as x_fechai, date_format(fechaf, '%Y-%m-%d') as x_fechaf from usuario where idusu=" . $idusu;
		$datos = $this -> listar_datos($consulta,0,1);

		$texto = "";

		if($datos[0]["tipo_mensualidad"] == 1){
			if($datos["cant_resultados"] && $datos[0]["x_fechai"] && $datos[0]["x_fechaf"]){
				$texto = "<span class='badge badge-success'>Desde " . $datos[0]["x_fechai"] . " hasta " . $datos[0]["x_fechaf"] . "</span>";
			} else {
				$texto = "<span class='badge badge-danger'>Usuario pendiente por mensualidad</span>";
			}
		} else if($datos[0]["tipo_mensualidad"] == 2){
			if($datos["cant_resultados"] && $datos[0]["cantidad_dias"]){
				$texto = "<span class='badge badge-success'>" . $datos[0]["cantidad_dias"] . "</span>";
			} else {
				$texto = "<span class='badge badge-danger'>Usuario pendiente por mensualidad</span>";
			}
		}

		return($texto);
	}
	/*
	ultimo_acceso_usuario
	Recibe idusu (entero)
	Retorna fecha hora
	funcion encargada de retornar el ultimo acceso que el usuario registro su entrada
	*/
	public function ultimo_acceso_usuario($idusu){
		$consulta = "select date_format(a.fecha_ingreso,'%Y-%m-%d %H:%i') as x_fecha_ingreso from ingreso a where a.idusu=" . $idusu . " and a.estado=1 order by iding desc";
		$datos = $this -> listar_datos($consulta, 0, 1);
		if($datos["cant_resultados"]){
			return($datos[0]["x_fecha_ingreso"]);
		} else {
			return('');
		}
	}
	/*
	obtener_texto_estado_usuario
	Recibe el idusu
	Retorna una cadena
	funcion encargada de mostrar el estado del usuario
	*/
	public function obtener_texto_estado_usuario($idusu){
		$hoy = date('Y-m-d');

		$consulta = "select date_format(a.fechai, '%Y-%m-%d') as x_fechai, date_format(a.fechaf, '%Y-%m-%d') as x_fechaf, b.estado,b.tipo_mensualidad,b.cantidad_dias,b.dias_faltantes from mensualidad a, usuario b where a.idusu=b.idusu and a.idusu=" . $idusu . " order by idmen desc";
		$datos = $this -> listar_datos($consulta,0,1);

		$texto = "<span class='badge badge-success'>Activo para ingresar al GYM</span>";

		if(!$datos["cant_resultados"]){
			$texto = "<span class='badge badge-danger'>Usuario pendiente por asignar pago</span>";
			return($texto);
		}

		if(@$datos[0]["estado"] == 2){//Usuario inactivo
			$texto = "<span class='badge badge-danger'>Usuario inactivo</span>";
			return($texto);
		}

		if($datos[0]["tipo_mensualidad"] == 1){
			if(!($hoy >= $datos[0]["x_fechai"] && $hoy <= $datos[0]["x_fechaf"])){
				$texto = "<span class='badge badge-danger'>Fuera de rango de mensualidad</span>";
				return($texto);	
			}
		} else if($datos[0]["tipo_mensualidad"] == 2){
			if($datos[0]["dias_faltantes"] == 0){
				$texto = "<span class='badge badge-danger'>D&iacute;as cumplidos</span>";
				return($texto);	
			}
		} else {
			$texto = "<span class='badge badge-danger'>Registre el tipo de pago</span>";
				return($texto);	
		}

		return($texto);
	}
	/*
	obtener_texto_valor
	recibe idusu
	retorna una cadena
	Funcion encargada de retornar el valor de la ultima mensualidad asignada
	*/
	public function obtener_texto_valor($idusu){
		$consulta = "select a.valor from usuario a where a.idusu=" . $idusu;
		$datos = $this -> listar_datos($consulta,0,1);

		$valor = 0;
		if($datos["cant_resultados"]){
			$valor = "<span class='badge badge-success'>" . number_format($datos[0]["valor"],0,",",".") . '</span>';
		}
		return($valor);
	}
	/*
	total_ingresados
	recibe fecha en formato Y-m-d
	Retorna un entero
	Funcion encargada de retornar la cantidad de usuarios ingresados en la fecha recibida
	*/
	public function total_ingresados($fecha){
		$consulta = "select count(*) as cantidad from ingreso where date_format(fecha, '%Y-%m-%d')='" . $fecha . "' and estado=1";
		$datos = $this -> listar_datos($consulta);

		return(@$datos[0]["cantidad"]);
	}
	public function eliminar_mensualidad_usuario($idusu){
		$condicion1 = "";
		$condicion2 = "";

		$valores = array();
		$valores2 = array();

		$valores[] = 'fechai=null';
		$valores[] = 'fechaf=null';
		$valores[] = 'valor=null';
		$valores[] = 'tipo_mensualidad=null';
		$valores[] = 'cantidad_dias=null';
		$valores[] = 'dias_faltantes=null';

		$condicion1 = "idusu=" . $idusu;

		$this -> modificar('usuario',$valores,$condicion1,$idusu);

		$consultarHistorico = "select idmen from mensualidad where idusu=" . $idusu . " order by idmen desc";
		$datosUsuario = $this -> listar_datos($consultarHistorico,0,1);

		if($datosUsuario["cant_resultados"]){
			$valores2[] = 'estado=0';

			$condicion2 = "idmen=" . $datosUsuario[0]["idmen"];

			$this -> modificar('mensualidad', $valores2, $condicion2, $datosUsuario[0]["idmen"]);
		}

		return(true);
	}
	public function obtener_dias_faltantes($idusu){
		$datos = $this -> obtener_datos_usuario($idusu);

		if($datos[0]["tipo_mensualidad"] == 1){
			if($datos[0]["fechaf"]){
				$dias_faltantes = $this -> restar_fecha($datos[0]["fechaf"],date('Y-m-d'));

				$clase = "";

				if($dias_faltantes > 5){
					$clase = "success";
				} else if($dias_faltantes <= 5 && $dias_faltantes > 0){
					$clase = "warning";
				}else {
					$clase = "danger";
				}

				$cadena = '<span class="badge badge-' . $clase . '">' . $dias_faltantes . '</span>';
			} else {
				$cadena = '<span class="badge badge-danger">0</span>';
			}
		} else if($datos[0]["tipo_mensualidad"] == 2){
			if($datos[0]["cantidad_dias"]){
				$dias_faltantes = $datos[0]["dias_faltantes"];

				$clase = "";

				if($dias_faltantes > 5){
					$clase = "success";
				} else if($dias_faltantes <= 5 && $dias_faltantes > 0){
					$clase = "warning";
				}else {
					$clase = "danger";
				}

				$cadena = '<span class="badge badge-' . $clase . '">' . $dias_faltantes . '</span>';
			} else {
				$cadena = '<span class="badge badge-danger">0</span>';
			}
		}
		return($cadena);
	}
	/*
	procesar_filtro_grafico
	funcion encargada de recibir arreglo con filtros y retornar los datos necesarios para generar grafico
	*/
	public function procesar_filtro_medida_grafico($datos){
		$where = array();
		if(@$datos["fk_idusu"]){
			$where[] = "a.fk_idusu=" . $datos["fk_idusu"];
		}
		if(@$datos["fechai"] && @$datos["fechaf"]){
			$where[] = "(date_format(a.fecha, '%Y-%m-%d')>='" . $datos["fechai"] . "' and date_format(a.fecha, '%Y-%m-%d')<='" . $datos["fechaf"] . "')";
		}
		if(@$datos["medida_corporal"]){
			$where[] = "a.medida_corporal=" . $datos["medida_corporal"];
		}

		$consulta1 = "select a.fecha, a.valor_medida from medida a where " . implode(" and " , $where) . " group by a.fecha, a.valor_medida";
		$datos = $this -> listar_datos($consulta1);

		return($datos);
	}
	/*
	obtener_filtro_medida_grafico
	recibe un array
	retorna un array
	Funcion encargada de retornar las medidas corporales y la cantidad de dias asistidos segun el filtro aplicado del array recibido
	*/
	public function obtener_filtro_medida_grafico($datos){
		$retorno = array();

		$where = array();
		$where2 = array();
		if(@$datos["fk_idusu"]){
			$where[] = "a.fk_idusu=" . $datos["fk_idusu"];
			$where2[] = "a.idusu=" . $datos["fk_idusu"];
		}
		if(@$datos["fechai"] && @$datos["fechaf"]){
			$where[] = "(date_format(a.fecha, '%Y-%m-%d')>='" . $datos["fechai"] . "' and date_format(a.fecha, '%Y-%m-%d')<='" . $datos["fechaf"] . "')";
			$where2[] = "(date_format(a.fecha, '%Y-%m-%d')>='" . $datos["fechai"] . "' and date_format(a.fecha, '%Y-%m-%d')<='" . $datos["fechaf"] . "')";
		}
		if(@$datos["medida_corporal"]){
			$where[] = "a.medida_corporal=" . $datos["medida_corporal"];
		}

		$consulta1 = "select b.idmed_cor as id, b.etiqueta as nombre from medida a, medida_corporal b where a.medida_corporal=b.idmed_cor and " . implode(" and " , $where) . " group by b.idmed_cor, b.etiqueta";
		$datos = $this -> listar_datos($consulta1);

		$consulta2 = "select count(*) as cantidad from ingreso a where a.estado=1 and " . implode(" and " , $where2) . "";
		$datos2 = $this -> listar_datos($consulta2);

		$retorno["datos_medida_corporal"] = $datos;
		$retorno["datos_dias_asistidos"] = $datos2[0]["cantidad"];

		return($retorno);
	}
	/*
	procesar_filtro_gestion
	funcion encargada de recibir arreglo con filtros y retornar los datos necesarios para generar grafico
	*/
	public function procesar_filtro_gestion($datos){
		$retorno = array();
		$where1 = array();
		$where2 = array();

		if(@$datos["fechai"] && @$datos["fechaf"]){
			$where1[] = "(date_format(a.fecha, '%Y-%m-%d')>='" . $datos["fechai"] . "' and date_format(a.fecha, '%Y-%m-%d')<='" . $datos["fechaf"] . "')";
			$where2[] = "(date_format(a.fecha, '%Y-%m-%d')>='" . $datos["fechai"] . "' and date_format(a.fecha, '%Y-%m-%d')<='" . $datos["fechaf"] . "')";
		}

		$consulta1 = "select date_format(a.fecha, '%Y-%m') as fecha, SUM(a.valor) as valor from mensualidad a where " . implode(" and " , $where1) . " and a.estado=1 group by date_format(a.fecha, '%Y-%m')";
		$datos1 = $this -> listar_datos($consulta1);

		$consulta2 = "select date_format(a.fecha, '%Y-%m-%d') as fecha, count(*) as cantidad from ingreso a where " . implode(" and " , $where2) . " and a.estado=1 group by a.fecha";
		$datos2 = $this -> listar_datos($consulta2);

		$retorno["datos_mensualidad"] = $datos1;
		$retorno["datos_ingreso"] = $datos2;

		return($retorno);
	}
  public function recibirGet(){
    $nuevoArray = array();
    
    $separador = "|";
    
    $cadena = base64_decode($_REQUEST["q"]);
    $datoArray = explode("|",$cadena);
    foreach($datoArray as $llave => $valor){
      $llave = explode("=",$valor);
      
      $nuevoArray[$llave[0]] = $llave[1];
    }
    
    return($nuevoArray);
  }
	/*
	sumar_fecha
	funcion encargada de sumar o restar a una fecha x dias
	retorna la nueva fecha
	*/
	public function sumar_fecha($fecha,$cantidad,$tipo,$formato){
		$actual = strtotime($fecha);
  		$nuevo = date($formato, strtotime($cantidad . " " . $tipo, $actual));
  		return($nuevo);
	}
	public function restar_fecha($fechai,$fechaf){
		if(!is_integer($fechai)){
			$date1 = strtotime($fechai);
		}
		if(!is_integer($fechaf)){
			$date2 = strtotime($fechaf);
		}
		return floor(($date1 - $date2) / 60 / 60 / 24);
	}
  public function mayuscula($texto){
    $texto_nuevo=strtoupper($texto);
    $texto_nuevo=str_replace("ACUTE;","acute;",$texto_nuevo);
    $texto_nuevo=str_replace("TILDE;","tilde;",$texto_nuevo);
    $texto_nuevo=str_replace("&IQUEST;","&iquest;",$texto_nuevo);
    $texto_nuevo=str_replace("UML;","uml;",$texto_nuevo);
    return($texto_nuevo);
  }
	public function mes($mes) {
		switch($mes) {
			case 1:
				return "enero";
			case 2:
				return "febrero";
			case 3:
				return "marzo";
			case 4:
				return "abril";
			case 5:
				return "mayo";
			case 6:
				return "junio";
			case 7:
				return "julio";
			case 8:
				return "agosto";
			case 9:
				return "septiembre";
			case 10:
				return "octubre";
			case 11:
				return "noviembre";
			case 12:
				return "diciembre";
		}
	}
	public function iniciar_variables_sesiones($datos){
		$_SESSION["usuario" . LLAVE_SESION] = $datos[0]["identificacion"];
		$_SESSION["tipo"] = $datos[0]["tipo"];
		$_SESSION["idusu"] = $datos[0]["idusu"];
		$_SESSION["nombres"] = $datos[0]["nombres"];
		$_SESSION["apellidos"] = $datos[0]["apellidos"];
	}
	public function validar_acceso_sesion(){
		global $atras;
		if(!@$_SESSION["idusu"] || @$_SESSION["tipo"] == 1){
			?>
			<script>
			window.location = '<?php echo($atras); ?>ventanas/ingreso/salir.php';
			</script>
			<?php
			die();
		}
	}
	public function validar_acceso_consulta_sesion(){
		global $atras;
		if(!@$_SESSION["idusu"]){
			?>
			<script>
			window.location = '<?php echo($atras); ?>ventanas/ingreso/salir.php';
			</script>
			<?php
			die();
		}
	}
	public function cerrar_sesion(){
		@session_unset();
		@session_destroy();
	}
}
?>