<?php
Header('Content-type: text/javascript');
Header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
error_reporting(E_ERROR);

require $_SERVER['DOCUMENT_ROOT'].'/php/functions/sanitizeInput.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';



//VARIABLES////////////////////////////////////////////////
$payLoad = array();	

//METHODS///////////////////////////////////////////////////////
if($_SERVER['REQUEST_METHOD'] === 'GET'){
	try{
		$requestedId = sanitizeInput($_GET["id"]);
		if(empty($requestedId)){
			$payLoad = DB::query("SELECT `tickets`.*, `clients`.`name` AS client_name, `operators`.`name` AS operator_name FROM `tickets` INNER JOIN `clients` ON `tickets`.`client_id` = `clients`.`id` INNER JOIN `operators` ON `tickets`.`operator_id` = `operators`.`id` ORDER BY `tickets`.`id` DESC");
		}
		else{
			$payLoad = DB::query("SELECT `tickets`.*, `clients`.`name` AS client_name, `operators`.`name` AS operator_name FROM `tickets` INNER JOIN `clients` ON `tickets`.`client_id` = `clients`.`id` INNER JOIN `operators` ON `tickets`.`operator_id` = `operators`.`id` WHERE `tickets`.`id` = %d", $requestedId)[0];
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
else if($_SERVER['REQUEST_METHOD'] === 'PUT'){
	$putData = file_get_contents("php://input");
	$request = json_decode($putData, true);

	//You cannot update an entire resource on db
	if(empty($request) || empty($request["id"])){	
		http_response_code(404); //Not Found
		exit();
	}
	else{
		try{
			DB::update('tickets', array(
				'status'			=>		$request["status"],
				'time_amount'	=>		$request["time_amount"],
				'progress'		=>		$request["progress"],
				'operator_id'	=>		$request["operator_id"],
				'priority'		=>		$request["priority"],
				'closed_on'		=>		$request["closed_on"],
				'comments' 		=>		$request["comments"]), 
			"id=%d", 						$request["id"]);

			$payLoad = DB::query("SELECT `tickets`.*, `clients`.`name` AS client_name, `operators`.`name` AS operator_name FROM `tickets` INNER JOIN `clients` ON `tickets`.`client_id` = `clients`.`id` INNER JOIN `operators` ON `tickets`.`operator_id` = `operators`.`id` WHERE `tickets`.`id` = %d", $request["id"])[0];
			http_response_code(202); //Accepted
			echo json_encode($payLoad, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
			exit();
		}
		catch(MeekroDBException $e){
			http_response_code(500); //Internal Server Error
			exit();
		}
	}
}
else if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$postData = file_get_contents("php://input");
	$request = json_decode($postData, true);

	if(empty($request)){	
		http_response_code(404); //Not Found
		exit();
	}

	if(!empty($_SERVER['HTTP_CLIENT_IP'])){ 
		$ip_solicitante = $_SERVER['HTTP_CLIENT_IP']; 
	}
	else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){ 
		$ip_solicitante = $_SERVER['HTTP_X_FORWARDED_FOR']; 
	}
	else{ 
		$ip_solicitante = $_SERVER['REMOTE_ADDR']; 
	}
	try{
		//Insertamos los valores del formulario
		DB::insert('tickets', array(
			'client_id'		=> $request["client_id"],
			'priority'		=> $request["priority"],
			'name'				=> $request["name"],
			'email'				=> $request["email"],
			'description' => $request["description"],
			'type'				=> $request["type"],
			'opened_the'  => Date("d-m-Y H:i:s"),
			'ip'					=> $ip_solicitante
		));

		$payLoad = DB::query("SELECT `tickets`.*, `clients`.`name` AS client_name, `operators`.`name` AS operator_name FROM `tickets` INNER JOIN `clients` ON `tickets`.`client_id` = `clients`.`id` INNER JOIN `operators` ON `tickets`.`operator_id` = `operators`.`id` WHERE `tickets`.`id` = %d", DB::insertId())[0];
		
		http_response_code(201); //Created
		echo json_encode($payLoad, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
		exit();
	}
	//Si falla, lanzamos un error.
	catch(MeekroDBException $e) {
		http_response_code(500); //Internal Server Error
		exit();
	}
}
else{
	http_response_code(405); //405 Method not Allowed
	exit();
}
?> 