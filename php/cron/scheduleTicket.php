<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/Exception.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/SMTP.php';

require $_SERVER['DOCUMENT_ROOT'].'/php/functions/nthDayOfMonth.php';

function updateDB(){
	DB::update('operadores', array('tareas' => 1), "tareas = %d", 0);
	DB::update('operadores', array('tareas_programadas' => 1), "tareas_programadas = %d", 0);
}

$now 					  			= date("d-m-Y");
$thisDayOfWeekNumber  = date("N");
$thisDayOfWeekName		= date("l");
$thisYear							= date("Y");
$thisMonth						= date("n");
$scheduledTasks 			= DB::query("SELECT * FROM `tareas_programadas`");
$mail 								= new PHPMailer(true);

$mail->isSMTP();
$mail->Host = '	appmixtura.ddns.net';
$mail->SMTPAuth = true;
$mail->Username = 'alertas@appmixtura.ddns.net';
$mail->Password = 'KxQV?6Z~4o]&';
$mail->SMTPSecure = 'ssl';
$mail->Port = 465;

$mail->setFrom('alertas@appmixtura.ddns.net');
$mail->addAddress('luis.mixtura@gmail.com');
$mail->addAddress('alexis.mixtura@gmail.com');
$mail->addAddress('proyectos.mixtura@gmail.com');
$mail->addAddress('felipe.mixtura@gmail.com');


$mail->isHTML(true);                                  // Set email format to HTML
$mail->Subject = 'Notificacion de Tarea Programada';

foreach ($scheduledTasks as $scheduledTask){
	$insertTicket = array(
		'id_cliente'				=>	$scheduledTask["id_cliente"],
		'prioridad_tarea'		=>	$scheduledTask["prioridad_tarea"],
		'nom_solicitante'		=>	$scheduledTask["nom_solicitante"],
		'email_solicitante'	=>	$scheduledTask["email_solicitante"],
		'desc_tarea'				=>	$scheduledTask["desc_tarea"],
		'tipo'							=>	$scheduledTask["tipo"]
	);
	switch ($scheduledTask["modo"]){
		case '1':{
			if($scheduledTask["fec_calendarizacion"] == $now){
				DB::insert('tareas', $insertTicket);
				$insertId 			 = DB::insertId();
				$scheduledTaskId = $scheduledTask["id_tareaprogramada"];
				DB::delete('tareas_programadas', "id_tareaprogramada = %d", $scheduledTask["id_tareaprogramada"]);
				updateDB();
				$mail->Body = "Se notifica al Equipo Mixtura que la Tarea Programada <Codigo: $scheduledTaskId>, fue insertada exitosamente en lista de Tareas. El codigo de la nueva tarea es: #$insertId. PD: Debido a que la Tarea Programada se configuró como 'Solo una Vez', esta se borrará automaticamente de la Lista de Tareas Programadas";
				$mail->send();
			}
		}
		break;

		case '2':{
			DB::insert('tareas', $insertTicket);
			$insertId 			 = DB::insertId();
			$scheduledTaskId = $scheduledTask["id_tareaprogramada"];
			updateDB();
			$mail->Body = "Se notifica al Equipo Mixtura que la Tarea Programada Numero: $scheduledTaskId, fue insertada exitosamente en lista de Tareas. El codigo de la nueva tarea es: #$insertId. PD: Debido a que la Tarea Programada se configuró como 'Una vez al Día', esta se agregará a la Lista de Tareas todos los dias hasta que se borre de la Lista de Tareas Programadas por un Administrador";
			$mail->send();
		}
		break;

		case '3':{
			if($scheduledTask["periodo_dia"] == $thisDayOfWeekNumber){
				DB::insert('tareas', $insertTicket);
				$insertId 			 = DB::insertId();
				$scheduledTaskId = $scheduledTask["id_tareaprogramada"];
				updateDB();
				$mail->Body = "Se notifica al Equipo Mixtura que la Tarea Programada <Codigo: $scheduledTaskId>, fue insertada exitosamente en lista de Tareas. El codigo de la nueva tarea es: #$insertId. PD: Debido a que la Tarea Programada se configuró como 'Una vez a la Semana', esta se agregará a la Lista de Tareas todos las semanas en el dia configurado hasta que se borre de la Lista de Tareas Programadas por un Administrador";
				$mail->send();
			}
		}
		break;

		case '4':{
			$calculatedDayName = date('l', strtotime("Sunday +{$scheduledTask["periodo_dia"]} days"));
			$calculatedDate = date("d-m-Y", nth_day_of_month($scheduledTask["periodo_semana"], $calculatedDayName, $thisMonth, $thisYear));
			if($calculatedDate == $now){
				DB::insert('tareas', $insertTicket);
				$insertId 			 = DB::insertId();
				$scheduledTaskId = $scheduledTask["id_tareaprogramada"];
				updateDB();
				$mail->Body = "Se notifica al Equipo Mixtura que la Tarea Programada <Codigo: $scheduledTaskId>, fue insertada exitosamente en lista de Tareas. El codigo de la nueva tarea es: #$insertId. PD: Debido a que la Tarea Programada se configuró como 'Una vez al Mes', esta se agregará a la Lista de Tareas todos los meses hasta que se borre de la Lista de Tareas Programadas por un Administrador";
				$mail->send();
			}
		}
		break;
	}
}
?>