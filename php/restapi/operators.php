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

/////////////////////////////////////////////////////////////////////////
if (!empty($_SERVER['HTTP_CLIENT_IP'])) { $ip_solicitante = $_SERVER['HTTP_CLIENT_IP']; }
else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $ip_solicitante = $_SERVER['HTTP_X_FORWARDED_FOR']; }
else { $ip_solicitante = $_SERVER['REMOTE_ADDR']; }

//VARIABLES////////////////////////////////////////////////
$userType = null;
$custumerId = null;
$payLoad = array();	
$requestedId = sanitizeInput($_GET["id"]);

//METHODS///////////////////////////////////////////////////////
if($_SERVER['REQUEST_METHOD'] === 'GET'){
	try{
		if(empty($requestedId)){
			$payLoad = DB::query("SELECT `id`, `name` FROM `operators` ORDER BY `operators`.`id` ASC");
		}
		else{
			$payLoad = DB::query("SELECT * FROM `operators` WHERE `operators`.`id` = %d", $requestedId)[0];
		}
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