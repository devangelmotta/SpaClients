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
$postdata = file_get_contents("php://input");

// Si hay algo, seguimos.
if($postdata){
	// Transformamos la request a un Array Asociativo de PHP
	$request = json_decode($postdata, true);
	//Buscamos la IP del que solicita la pagina.
	//La buscamos incluso si usa un proxy.
	//Necesario por medidas de seguridad.
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) { $ip_solicitante = $_SERVER['HTTP_CLIENT_IP']; }
	else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $ip_solicitante = $_SERVER['HTTP_X_FORWARDED_FOR']; }
	else { $ip_solicitante = $_SERVER['REMOTE_ADDR']; }

	//Una vez obtenida la IP del solicitante, procedemos a insertar la solicitud en la base de datos
	try{
		//Insertamos los valores del formulario
		DB::insert('tareas_programadas', array(
			'id_cliente'					=>	$request["id_cliente"],
			'prioridad_tarea'			=>	$request["prioridad_tarea"],
			'nom_solicitante'			=>	$request["nom_solicitante"],
			'email_solicitante'		=>	$request["email_solicitante"],
			'desc_tarea'					=>	$request["desc_tarea"],
			'tipo'								=>	$request["tipo"],
			'ip_solicitante'			=>	$ip_solicitante,
			'modo'								=>	$request["modo"],
			'fec_calendarizacion' =>	$request["fec_calendarizacion"],
			'periodo_dia'					=>	$request["periodo_dia"],
			'periodo_semana'			=>	$request["periodo_semana"]
		));
		//Si todo sale bien, imprimimos "exito".
		DB::update('operadores', array('tareas_programadas' => 1), "tareas=%d", 0);
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
