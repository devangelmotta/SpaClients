<?php
// Headers HTML para prevenir que el navegador guarde en caché el contenido de la pagina
Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
// Notificar solamente errores de ejecución
error_reporting(E_ERROR);

require $_SERVER['DOCUMENT_ROOT'].'/php/functions/randomKey.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';
//DB::debugMode();

// Recuperamos el mensaje JSON del cuerpo de la solicitud (POST)
$postdata = file_get_contents("php://input");

// Si hay algo, seguimos.
if($postdata){
	// Transformamos la request a un Array Asociativo de PHP
	$request = json_decode($postdata, true);
	
	try{
		//Insertamos los valores del formulario
		DB::insert('operadores', array(
			'nom_operador'			=>		$request["nom_operador"],
			'rut_operador'			=>		$request["rut_operador"],
			'admin_token'				=>		randomKey(20)
		));
		$operatorId = DB::insertId();

		DB::update('operadores', array('operadores' => 1), "operadores=%d", 0);
		//Si todo sale bien, imprimimos "exito".
		echo '{"status":"success", "id":"'.$operatorId.'"}';
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
