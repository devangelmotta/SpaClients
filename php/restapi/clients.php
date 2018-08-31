<?php
// Headers HTML para prevenir que el navegador guarde en caché el contenido de la pagina
Header('Content-type: text/javascript');
Header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
// Notificar solamente errores de ejecución
error_reporting(E_ERROR);

require $_SERVER['DOCUMENT_ROOT'].'/php/functions/sanitizeInput.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';
//DB::debugMode();

//Necesario por medidas de seguridad.
if (!empty($_SERVER['HTTP_CLIENT_IP'])) { $ip_solicitante = $_SERVER['HTTP_CLIENT_IP']; }
else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $ip_solicitante = $_SERVER['HTTP_X_FORWARDED_FOR']; }
else { $ip_solicitante = $_SERVER['REMOTE_ADDR']; }
//VARIABLES////////////////////////////////////////////////
$userType           			= null;
$custumerId 			  			= null;
$payLoad									= array();
$dbResourcesForAdminGetRequest = array(
	"tareas_programadas"  => "SELECT `tareas_programadas`.* , `clientes`.`nom_cliente` FROM `tareas_programadas` INNER JOIN `clientes` ON `tareas_programadas`.`id_cliente` = `clientes`.`id_cliente` ORDER BY `tareas_programadas`.`id_tareaprogramada` DESC",
	"proyectos"					  => "SELECT `proyectos`.* , `clientes`.`nom_cliente`, opu.`nom_operador` AS nom_ejecutor1, opd.`nom_operador` AS nom_ejecutor2, opt.`nom_operador` AS nom_solicitante FROM `proyectos` INNER JOIN `clientes` ON `proyectos`.`id_cliente` = `clientes`.`id_cliente` INNER JOIN `operadores` AS opu ON `proyectos`.`id_ejecutor1` = opu.`id_operador` INNER JOIN `operadores` AS opd ON `proyectos`.`id_ejecutor2` = opd.`id_operador` INNER JOIN `operadores` AS opt ON `proyectos`.`id_solicitante` = opt.`id_operador` ORDER BY `proyectos`.`id_proyecto` DESC",
	"operadores"				  => "SELECT * FROM `operadores` ORDER BY `id_operador` ASC",
	"sucursales"				  => "SELECT `sucursales`.*, `clientes`.`nom_cliente` FROM `sucursales` INNER JOIN `clientes` ON `sucursales`.`id_cliente` = `clientes`.`id_cliente`",
	"techdata_equipos"	  => "SELECT `techdata_equipos`.*, `clientes`.`nom_cliente`, `sucursales`.`nom_sucursal` FROM `techdata_equipos` INNER JOIN `clientes` ON `techdata_equipos`.`id_cliente` = `clientes`.`id_cliente` INNER JOIN `sucursales` ON `techdata_equipos`.`id_sucursal` = `sucursales`.`id_sucursal` ORDER BY `techdata_equipos`.`id_cliente` ASC",
	"techdata_servidores" => "SELECT `techdata_servidores`.*, `clientes`.`nom_cliente`, `sucursales`.`nom_sucursal` FROM `techdata_servidores` INNER JOIN `clientes` ON `techdata_servidores`.`id_cliente` = `clientes`.`id_cliente` INNER JOIN `sucursales` ON `techdata_servidores`.`id_sucursal` = `sucursales`.`id_sucursal` ORDER BY `techdata_servidores`.`id_cliente` ASC"
);

//METHODS///////////////////////////////////////////////////////
if($_SERVER['REQUEST_METHOD'] === 'GET'){
	try{
		$payLoad = DB::query("SELECT * FROM `clients` ORDER BY `id` ASC");
		http_response_code(200); //200 OK
		echo json_encode($payLoad, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
		exit();
	}
	catch(MeekroDBException $e){
		http_response_code(500); //500 Internal Server Error
		exit();
	}
}
else{
	http_response_code(405); //405 Method not Allowed
	exit();
}
?> 