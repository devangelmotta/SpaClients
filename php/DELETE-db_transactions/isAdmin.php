<?php
// Headers HTML para prevenir que el navegador guarde en caché el contenido de la pagina
Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
// Notificar solamente errores de ejecución
error_reporting(E_ERROR);


require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/sanitizeInput.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/getIdFromIp.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';
//DB::debugMode();

$admin_token 	 = sanitizeInput($_GET["admin_token"]);
$missing_table = sanitizeInput($_GET["missing_table"]);
$missing_table = ($missing_table == "missing" ? true : false);


if(!empty($admin_token)){
	try{
		//Buscamos el token entre la lista de operadores
		$operatorPersonalData = DB::queryFirstRow("SELECT * FROM `operadores` WHERE `admin_token` = %s", $admin_token);
		//Si la operacion devuelve mayor a 0 significa que encontro el token
		if(DB::count() > 0){
			
			$estado_tablas = DB::queryFirstRow("SELECT * FROM `operadores` WHERE `id_operador` = %d", $operatorPersonalData["id_operador"]);
			$payLoad["status"] 			 = "found";
			$payLoad["idFromIp"]		 = getIdFromIp();
			$payLoad["nom_operador"] = $operatorPersonalData["nom_operador"];

			$arr = array(
				"tareas" 						  => "SELECT `tareas`.* , `clientes`.`nom_cliente`, opu.`nom_operador` AS nom_ejecutor1, opd.`nom_operador` AS nom_ejecutor2 FROM `tareas` INNER JOIN `clientes` ON `tareas`.`id_cliente` = `clientes`.`id_cliente` INNER JOIN `operadores` AS opu ON `tareas`.`id_ejecutor1` = opu.`id_operador` INNER JOIN `operadores` AS opd ON `tareas`.`id_ejecutor2` = opd.`id_operador` ORDER BY `tareas`.`id_tarea` DESC",
				"tareas_programadas"  => "SELECT `tareas_programadas`.* , `clientes`.`nom_cliente` FROM `tareas_programadas` INNER JOIN `clientes` ON `tareas_programadas`.`id_cliente` = `clientes`.`id_cliente` ORDER BY `tareas_programadas`.`id_tareaprogramada` DESC",
				"proyectos"					  => "SELECT `proyectos`.* , `clientes`.`nom_cliente`, opu.`nom_operador` AS nom_ejecutor1, opd.`nom_operador` AS nom_ejecutor2, opt.`nom_operador` AS nom_solicitante FROM `proyectos` INNER JOIN `clientes` ON `proyectos`.`id_cliente` = `clientes`.`id_cliente` INNER JOIN `operadores` AS opu ON `proyectos`.`id_ejecutor1` = opu.`id_operador` INNER JOIN `operadores` AS opd ON `proyectos`.`id_ejecutor2` = opd.`id_operador` INNER JOIN `operadores` AS opt ON `proyectos`.`id_solicitante` = opt.`id_operador` ORDER BY `proyectos`.`id_proyecto` DESC",
				"operadores"				  => "SELECT * FROM `operadores` ORDER BY `id_operador` ASC",
				"clientes"					  => "SELECT * FROM `clientes` ORDER BY `id_cliente` ASC",
				"sucursales"				  => "SELECT `sucursales`.*, `clientes`.`nom_cliente` FROM `sucursales` INNER JOIN `clientes` ON `sucursales`.`id_cliente` = `clientes`.`id_cliente`",
				"techdata_equipos"	  => "SELECT `techdata_equipos`.*, `clientes`.`nom_cliente`, `sucursales`.`nom_sucursal` FROM `techdata_equipos` INNER JOIN `clientes` ON `techdata_equipos`.`id_cliente` = `clientes`.`id_cliente` INNER JOIN `sucursales` ON `techdata_equipos`.`id_sucursal` = `sucursales`.`id_sucursal` ORDER BY `techdata_equipos`.`id_cliente` ASC",
				"techdata_servidores" => "SELECT `techdata_servidores`.*, `clientes`.`nom_cliente`, `sucursales`.`nom_sucursal` FROM `techdata_servidores` INNER JOIN `clientes` ON `techdata_servidores`.`id_cliente` = `clientes`.`id_cliente` INNER JOIN `sucursales` ON `techdata_servidores`.`id_sucursal` = `sucursales`.`id_sucursal` ORDER BY `techdata_servidores`.`id_cliente` ASC"
			);

			foreach ($arr as $key => $value){
				if($estado_tablas[$key] == 1 || $missing_table){
					$payLoad[$key] = DB::query($value);
					DB::update('operadores', array($key => 0), "id_operador = %d", $operatorPersonalData["id_operador"]);
				}
			}
			unset($key); 
			unset($value);
			echo json_encode($payLoad, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
			exit();
		}
		else{
			echo '{"status":"lost"}';
			exit();
		}
	}
	//Si falla, lanzamos un error.
	catch(MeekroDBException $e) {
		echo '{"status":"mysqlError","code":"'.$e->getMessage().'"}';
		exit();
	}
}
else{
	echo '{"status":"lost"}';
	exit();
}
?>