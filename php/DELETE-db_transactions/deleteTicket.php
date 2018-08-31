<?php
// Headers HTML para prevenir que el navegador guarde en caché el contenido de la pagina
Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
// Notificar solamente errores de ejecución
error_reporting(E_ERROR);

// Incluimos los necesario para usar la Clase MeekroDB
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';
//DB::debugMode();

// Recuperamos el mensaje JSON del cuerpo de la solicitud (POST)
$deleteData = file_get_contents("php://input");

// Si hay algo, seguimos.
if($deleteData){
	// Transformamos la request a un Array Asociativo de PHP
	$request = json_decode($deleteData, true);
	//Buscamos la IP del que solicita la pagina.
	//La buscamos incluso si usa un proxy.
	//Necesario por medidas de seguridad.
	
	try{
		DB::delete('tareas', "id_tarea = %d", $request["id_tarea"]);
		DB::update('operadores', array('tareas' => 1), "tareas=%d", 0);
		echo '{"status":"success"}';
		exit();
	}
	//Si falla, lanzamos un error.
	catch(MeekroDBException $e) {
		echo '{"status":"mysqlError","code":"'.$e->getMessage().'"}';
		exit();
	}
}
//Si el cuerpo de la solicitud HTTP esta vacio, entonces salimos.
else{
	echo '{"status":"lost"}';
	exit();
}
?>
