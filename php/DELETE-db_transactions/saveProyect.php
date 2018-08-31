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

$postdata = file_get_contents("php://input");
if($postdata){
	$request = json_decode($postdata, true);

	$clientInitianls					= DB::queryFirstField("SELECT `sigla_cliente` FROM `clientes` WHERE `id_cliente` = %d", $request["id_cliente"]);
	$amountProyectsPerClient 	= DB::queryFirstField("SELECT COUNT(*) FROM `proyectos` WHERE `id_cliente` = %d", $request["id_cliente"]);
	$amountProyectsPerClient 	= $amountProyectsPerClient + 1;
	$amountProyectsPerClient 	= str_pad($amountProyectsPerClient, 2, "0", STR_PAD_LEFT);
	$currentYearNumber				= date("y");

	$uniqueProyectId = $clientInitianls.$currentYearNumber.$amountProyectsPerClient;

	try{
		DB::insert('proyectos', array(
			'id_solicitante'			=> 		$request["id_solicitante"],
			'desc_proyecto'				=>		$request["desc_proyecto"],
			'fec_formalizacion'		=>		$request["fec_formalizacion"],
			'id_cliente'					=>		$request["id_cliente"],
			'cod_proyecto'				=>		$uniqueProyectId
		));

		DB::update('operadores', array('proyectos' => 1), "proyectos=%d", 0);
		echo '{"status":"success"}';
		exit();
	}
	catch(MeekroDBException $e){
		echo '{"status":"mysqlError","code":"'.$e->getMessage().'"}';
		exit();
	}
}
else{
	echo '{"status":"lost"}';
	exit();
}
?>
