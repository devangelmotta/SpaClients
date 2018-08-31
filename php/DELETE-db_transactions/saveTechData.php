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
	try{
		if($request["tipo_techdata"] == "equipo"){
			//Insertamos los valores del formulario
			DB::insert('techdata_equipos', array(
				'id_cliente'   			 => $request["id_cliente"],
				'id_sucursal'  			 => $request["id_sucursal"],
				'id_ups'						 => 0,
				'id_tds'						 => $request["id_tds"],
				'responsable'  			 => $request["responsable"],
				'cargo' 			 			 => $request["cargo"],
				'nom_correo'   			 => $request["nom_correo"],
				'clave_correo' 			 => $request["clave_correo"],
				'usuario_dominio' 	 => $request["usuario_dominio"],
				'clave_dominio' 		 => $request["clave_dominio"],
				'ip_equipo' 				 => $request["ip_equipo"],
				'mac_equipo' 				 => $request["mac_equipo"],
				'acceso_remoto' 		 => $request["acceso_remoto"],
				'sesion_multicuenta' => $request["sesion_multicuenta"],
				'nom_antivirus' 		 => $request["nom_antivirus"],
				'respaldo_acronis' 	 => $request["respaldo_acronis"],
				'nom_ccleaner' 			 => $request["nom_ccleaner"],
				'nom_malware' 			 => $request["nom_malware"],
				'nom_antispyware' 	 => $request["nom_antispyware"],
				'nom_procesador' 		 => $request["nom_procesador"],
				'sistema_operativo'  => $request["sistema_operativo"],
				'cap_ram' 					 => $request["cap_ram"],
				'cap_almacenamiento' => $request["cap_almacenamiento"],
				'obs_generales' 		 => $request["obs_generales"],
				'id_anydesk' 				 => $request["id_anydesk"],
				'clave_anydesk' 		 => $request["clave_anydesk"],
				'id_teamviewer' 		 => $request["id_teamviewer"],
				'clave_teamviewer' 	 => $request["clave_teamviewer"]
			));
			DB::update('operadores', array('techdata_equipos' => 1), "techdata_equipos=%d", 0);
			echo '{"status":"success"}';
			exit();
		}
		else if($request["tipo_techdata"] == "servidor"){
			DB::insert('techdata_servidores', array(
				'id_cliente'				=> $request["id_cliente"],
				'id_sucursal'				=> $request["id_sucursal"],
				'id_ups'						=> 0,
				'nom_servidor' 			=> $request["nom_servidor"],
				'utilidad'					=> $request["utilidad"],
				'modelo'						=> $request["modelo"],
				'sistema_operativo' => $request["sistema_operativo"],
				'usuario_dominio'		=> $request["usuario_dominio"],
				'clave_dominio'			=> $request["clave_dominio"],
				'acceso_remoto'			=> $request["acceso_remoto"],
				'puerto_remoto'			=> $request["puerto_remoto"],
				'tipo'							=> $request["tipo"],
				'estado_ip'					=> $request["estado_ip"],
				'ip_publica'				=> $request["ip_publica"],
			));
			DB::update('operadores', array('techdata_servidores' => 1), "techdata_servidores = %d", 0);
			echo '{"status":"success"}';
			exit();
		}
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
