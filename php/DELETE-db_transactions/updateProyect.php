<?php
/**
 * Script PHP para actualizar la información de un Proyecto
 *
 * @author    @devangelmotta <devangelmotta@hotmail.com>
 * @copyright Pyme.
**/

Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
error_reporting(E_ERROR);

require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';
//DB::debugMode();


$postdata = file_get_contents("php://input");
$request  = json_decode($postdata, true);

try{
	DB::update('proyectos', array(
		'estado_proyecto'			=>		$request["estado_proyecto"],
		'cant_horas'					=>		$request["cant_horas"],
		'porcentaje_avance'		=>		$request["porcentaje_avance"],
		'id_ejecutor1'				=>		$request["id_ejecutor1"],
		'id_ejecutor2'				=>		$request["id_ejecutor2"],
		'fec_formalizacion'		=>		$request["fec_formalizacion"],
		'fec_ejecucion'				=>		$request["fec_ejecucion"],
		'obs_operador' 				=>		$request["obs_operador"]
		),"id_proyecto=%d", 				$request["id_proyecto"]
	);

	DB::update('operadores', array('proyectos' => 1), "proyectos=%d", 0);
	echo '{"status":"success"}';
}
catch(MeekroDBException $e) {
	echo '{"status":"mysqlError","code":"'.$e->getMessage().'"}';
	exit();
}
?>