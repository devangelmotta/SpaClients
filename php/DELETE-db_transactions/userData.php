<?php
// Headers HTML para prevenir que el navegador guarde en caché el contenido de la pagina
Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
// Notificar solamente errores de ejecución
error_reporting(E_ERROR);

require $_SERVER['DOCUMENT_ROOT'].'/php/functions/utf8ize.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';
//DB::debugMode();

$admin_token 	 = $_GET["admin_token"];
$missing_table = ($_GET["missing_table"] == "missing" ? true : false);

if(!empty($admin_token) && !empty($missing_table)){
	try{
		//Buscamos el token entre la lista de operadores
		$operatorPersonalData = DB::queryFirstRow("SELECT * FROM `operadores` WHERE `admin_token` = %s", $admin_token);
		//Si la operacion devuelve mayor a 0 significa que encontro el token
		if(DB::count() > 0){
			
			$estado_tablas = DB::queryFirstRow("SELECT * FROM `operadores` WHERE `id_operador` = %d", $operatorPersonalData["id_operador"]);
			$payLoad["status"] 			 = "found";
			$payLoad["nom_operador"] = $operatorPersonalData["nom_operador"];
			/*
			*En la base de datos hay una tabla llamada "estado_tablas"
			*esta tabla contiene columnas por cada tabla de trabajo
			*si el valor de una celda es 1 entonces significa que a habido un cambio
			*en la tabla principal, por ejemplo, si estado_tablas["tareas"] es 1 entonces
			*alguien agrego o hizo un cambio en alguna tarea
			*por lo que se procede a descargar toda la tabla.
			*si fuese 0, no se descarga y se deja que Angular use la tabla almacenada en cache
			*Tambien debemos tomar en cuenta cuando el administrador borro la tabla local
			*y como ya no la tiene, y en el caso de que no se hayan actualizado las tablas, este nunca podra
			*obtener la tabla de datos, es por eso que angular verifica que las tablas existan localmente
			*sino existen envia un token mas, para avisar de esta situacion
			*/

			if($estado_tablas["tareas"] == 1 || $missing_table){
				$payLoad["tareas"] = DB::query("SELECT * FROM `tareas` ORDER BY `id_tarea` DESC");
				DB::update('operadores', array('tareas' => 0), "id_operador = %d", $operatorPersonalData["id_operador"]);
			}
			if($estado_tablas["tareas_programadas"] == 1 || $missing_table){
				$payLoad["tareas_programadas"] = DB::query("SELECT * FROM `tareas_programadas` ORDER BY `id_tareaprogramada` ASC");
				DB::update('operadores', array('tareas_programadas' => 0), "id_operador = %d", $operatorPersonalData["id_operador"]);
			}
			if($estado_tablas["proyectos"] == 1 || $missing_table){
				$payLoad["proyectos"] = DB::query("SELECT * FROM `proyectos` ORDER BY `id_proyecto`	DESC");
				DB::update('operadores', array('proyectos' => 0), "id_operador = %d", $operatorPersonalData["id_operador"]);
			}
			if($estado_tablas["operadores"] == 1 || $missing_table){
				$payLoad["operadores"] = DB::query("SELECT * FROM `operadores` ORDER BY `id_operador` ASC");
				DB::update('operadores', array('operadores' => 0), "id_operador = %d", $operatorPersonalData["id_operador"]);
			}
			if($estado_tablas["clientes"] == 1 || $missing_table){
				$payLoad["clientes"] = DB::query("SELECT * FROM `clientes` ORDER BY `id_cliente` ASC");
				DB::update('operadores', array('clientes' => 0), "id_operador = %d", $operatorPersonalData["id_operador"]);
			}
			if($estado_tablas["techdata_equipos"] == 1 || $missing_table){
				$payLoad["techdata_equipos"] = DB::query("SELECT * FROM `techdata_equipos` ORDER BY `id_cliente` ASC");
				DB::update('operadores', array('techdata_equipos' => 0), "id_operador = %d", $operatorPersonalData["id_operador"]);
			}
			
			//Encondeamos en utf8, luego JSON y pa' juera! :P
			echo json_encode(utf8ize($payLoad));
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
	//Buscamos la IP del que solicita la pagina.
	//La buscamos incluso si usa un proxy.
	//Necesario por medidas de seguridad.
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) { $outsider_ip = $_SERVER['HTTP_CLIENT_IP']; }
	else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $outsider_ip = $_SERVER['HTTP_X_FORWARDED_FOR']; }
	else { $outsider_ip = $_SERVER['REMOTE_ADDR']; }

	//Inicializamos la variable
	$matchId = 0;

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
	if ($matchId === 0) {
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
			echo json_encode(utf8ize($clientPersonalData));
			exit();
		}
	//Si falla, lanzamos un error.
		catch(MeekroDBException $e) {
			echo '{"status":"mysqlError","code":"'.$e->getMessage().'"}';
			exit();
		}
	}
}
?>