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

//Buscamos la IP del que solicita la pagina.
//La buscamos incluso si usa un proxy.
//Necesario por medidas de seguridad.
if (!empty($_SERVER['HTTP_CLIENT_IP'])) { $outsider_ip = $_SERVER['HTTP_CLIENT_IP']; }
else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $outsider_ip = $_SERVER['HTTP_X_FORWARDED_FOR']; }
else { $outsider_ip = $_SERVER['REMOTE_ADDR']; }

//Inicializamos la variable
$matchId = -1;

//Intentamos recuperar de la base de datos el listado de DNS de nuestros Clientes
try {
	$datos = DB::query("SELECT `dns`, `id_cliente` FROM `clientes`");
} 
catch (MeekroDBException $e) {
	echo '{"status":"mysqlError","code":"'.$e->getMessage().'"}';
	exit();
}

//Recorremos los datos recibidos buscando una coincidencia.
//Se resuelve una por una las DNS para obtener la IP, luego se coteja con la de la base de datos
//Si hay coincidencia se guarda el ID del cliente en $matchId
foreach ($datos as $dato) {
	$resolved_ip = gethostbyname($dato["dns"]);
	if($outsider_ip === $resolved_ip){
		$matchId = $dato["id_cliente"];
	}
}

//Si no hay coincidencias, se escapa.
if ($matchId === -1) {
	echo '{"status":"lost"}';
	exit();
}
//Caso contrario se intenta conectar a la base de datos para
//buscar toda la informacion del cliente.
else{
	//Intentamos ejecutar la conexion a la base de datos
	try{
		//Buscamos los datos personales del cliente
		$clientPersonalData = DB::queryFirstRow("SELECT * FROM `clientes` WHERE `id_cliente` = %d", $matchId);
		//Buscamos todos los tickets relacionados al cliente
		$tickets 						= DB::query("SELECT * FROM `tareas` WHERE `id_cliente` = %d ORDER BY `id_tarea` DESC", $clientPersonalData["id_cliente"]);
		//Injectamos la variable "status"
		$clientPersonalData["status"] = "found";
		//Injectamos los tickets dentro de clientPersonalData
		$clientPersonalData["tickets"] = $tickets;
		//Encodeamos e imprimimos el JSON resultante
		echo json_encode($clientPersonalData, JSON_UNESCAPED_UNICODE);
		exit();
	}
	//Si falla, lanzamos un error.
	catch(MeekroDBException $e) {
		echo '{"status":"mysqlError","code":"'.$e->getMessage().'"}';
		exit();
	}
}
?>