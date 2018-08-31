<?php
// Headers HTML para prevenir que el navegador guarde en caché el contenido de la pagina
Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
// Notificar solamente errores de ejecución
error_reporting(E_ERROR);

require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';
//DB::debugMode();

// Recuperamos el mensaje JSON del cuerpo de la solicitud (POST)
$postdata = file_get_contents("php://input");

// Si hay algo, seguimos.
if($postdata){
	// Transformamos la request a un Array Asociativo de PHP
	$request = json_decode($postdata, true);
	
	try{
		$founded = DB::queryFirstField("SELECT * FROM `operadores` WHERE `superadmin_pass` = %s", $request["superadmin_pass"]);
		if(isset($founded)){
			$adminData = DB::queryFirstRow("SELECT * FROM `operadores` WHERE `id_operador` = %d", $request["admin_id"]);
			echo '{"status": "found", "admin_token":"', $adminData["admin_token"], '"}';
			exit();
		}
		else{
			echo '{"status": "lost"}';
			exit();
		}
	}
	catch(MeekroDBException $e) {
		echo '{"status":"mysqlError","code":"'.$e->getMessage().'"}';
		exit();
	}
}
?>