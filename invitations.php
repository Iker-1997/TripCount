<?php

require_once("internal/functions.php");
initGlobal();

session_start(['name' => 'TRIPCOUNT_SESS_ID']);

if(!isset($_SESSION["username"])){
	session_destroy();
	header("Location: login.php");
}

if(!isset($_SESSION["selected_travel"])){
	header("Location: home.php");
}


$pdo = call_db();

$query = $pdo->prepare('SELECT * FROM travels WHERE travel_id = :selected_travel;');

$query->bindParam(':selected_travel', $_SESSION['selected_travel'], PDO::PARAM_INT);

$query->execute();

$travel = $query->fetch();

$emailHTML = '';
if(isset($_POST['email'])){
	$stats["total"] = 0;
	$stats["okey"] = 0;
	$stats["invalid"] = 0;
	$stats["already"] = 0;

	$bad_emails = array();
	foreach($_POST['email'] as $email){
		$stats["total"]++;
		if(filter_var($email, FILTER_VALIDATE_EMAIL)){
			// VALID EMAIL
			
			$pdo = call_db();
			$query = $pdo->prepare('SELECT * FROM `invitations` WHERE email = :email AND travel_id = :travelid');
			$query->bindParam(':travelid', $_SESSION['selected_travel'], PDO::PARAM_INT);
			$query->bindParam(':email', $email, PDO::PARAM_STR, 255);
			$query->execute();
			$a = $query->fetchAll();
			if(sizeof($a) === 0){
				$stats["okey"]++;
				// ADD INVITATION
				$query = $pdo->prepare('INSERT INTO `invitations` (`invitation_id`, `user_id`, `travel_id`, `email`, `date_invitation`) VALUES (NULL, :userid, :travelid, :email, current_timestamp())');
				$query->bindParam(':userid', $_SESSION["user_id"], PDO::PARAM_INT);
				$query->bindParam(':travelid', $_SESSION['selected_travel'], PDO::PARAM_INT);
				$query->bindParam(':email', $email, PDO::PARAM_STR, 255);
				$query->execute();
				$invitation_id = $pdo->lastInsertId();

				$query = $pdo->prepare('SELECT * FROM `users` WHERE email = :email LIMIT 1');
				$query->bindParam(':email', $email, PDO::PARAM_STR, 255);
				$query->execute();
				$a = $query->fetchAll();

				if(sizeof($a) > 0){
					// User exists
					$file = "already.html";
					$url = "https://tripcount.ml/accept_invite.php?invite=$invitation_id";
					$plainmessage = $_SESSION["name"] . " " . $_SESSION["lastname1"] . " " . $_SESSION["lastname2"] . " te ha invitado a " . $_SESSION['selected_travel']  . " para compartir gastos.";
				}
				else{
					// User not found
					$file = "new.html";
					$url = "https://tripcount.ml/register.php";
					$plainmessage = $_SESSION["name"] . " " . $_SESSION["lastname1"] . " " . $_SESSION["lastname2"] . " te ha invitado a " . $_SESSION['selected_travel']  . " para compartir los gastos con TripCount, la aplicación para compartir gastos. Haz clic en el enlace para crearte una cuenta https://tripcount.ml/register.php";
				}

				// Load template
				$h = fopen($file, "r");
				$HTMLmessage = fread($h, filesize($file));
				fclose($h);

				// Parse template
				$HTMLmessage = str_replace("<firstname>", $_SESSION["name"], $HTMLmessage);
				$HTMLmessage = str_replace("<lastname1>", $_SESSION["lastname1"], $HTMLmessage);
				$HTMLmessage = str_replace("<lastname2>", $_SESSION["lastname2"], $HTMLmessage);
				$HTMLmessage = str_replace("<travel_name>", $travel["name"], $HTMLmessage);
				$HTMLmessage = str_replace("<url>", $url, $HTMLmessage);
				$HTMLmessage = str_replace("<nombre_empresa>", $GLOBALS["nombre_empresa"], $HTMLmessage);
				$HTMLmessage = str_replace("<formacion_empresa>", $GLOBALS["formacion_empresa"], $HTMLmessage);
				$HTMLmessage = str_replace("<actual_year>", date("Y"), $HTMLmessage);
				
				// SEND EMAIL
				$from["name"] = "TripCount";
				$from["email"] = "noreply@tripcount.ml";
				$subject = $_SESSION["username"] . " te ha invitado a un viaje en TripCount";

				send_email($email, $from, $subject, $HTMLmessage, $plainmessage);
			}
			else{
				$stats["already"]++;
			}

		}
		else{
			$stats["invalid"]++;
			// INVALID EMAIL
			$bad_emails[] = $email;
		}

	}

	if(sizeof($bad_emails) > 0){
		//
		invokeMsgbox(["category" => "error", "id" => "error_at_sending_step"]);

		// if we have bad emails process for show at form step
		$i = 1;
		foreach($bad_emails as $bad){
			$bad = htmlspecialchars($bad);
			if($i === 1){
				$emailHTML .= '<div><input id="email1" name="email[]" type="email" placeholder="email@example.com" value="' . $bad . '" class="invalidInput"></div>' . "\n";
			}
			else{
				$emailHTML .= '<div id="emailContainer' . $i . '"><input name="email[]" type="email" placeholder="email@example.com" class="invalidInput emailInput" value=' . $bad . '><button id="delBtn' . $i . '" onclick="deleteEmailContainer(this)" class="emailRemoveBtn">-</button></div>' . "\n";
			}
			$i++;
		}
	}
	elseif($stats["total"] === $stats["already"]){
		// skip okey msg
		invokeMsgbox(["category" => "warning", "id" => "error_at_sending_step_inv_already_send"]);
		$emailHTML .= '<div><input id="email1" name="email[]" type="email" placeholder="email@example.com"></div>';
	}
	else{
		invokeMsgbox(["category" => "success", "id" => "ok_at_sending_step"]);
		$emailHTML .= '<div><input id="email1" name="email[]" type="email" placeholder="email@example.com"></div>';
	}

}
else{
	$emailHTML .= '<div><input id="email1" name="email[]" type="email" placeholder="email@example.com"></div>';
}

?><!DOCTYPE html>
<html>
    <head>
        <?php getTitle(); ?>
		<link rel="stylesheet" href="css/fontawesome.min.css">
        <link rel="stylesheet" href="css/style.css">
		<link href="https://fonts.googleapis.com/css2?family=Potta+One&display=swap" rel="stylesheet">
		<script src="js/util.js" async></script>
		<script src="js/invitations.js" async></script>
    </head>
    <body>
        <?php
            include_once("header.php");
		?>
		<main>
			<center><h1>Invitar participantes a ≪<?php echo $travel["name"]; ?>≫</h1></center>
			<button id="addParticipant">+</button>
			<form method="POST" class="emailForm">
				<div id="emails">
					<?php echo $emailHTML; ?>
				</div>
				
				<!--<div stlye="display: inline"><input type="email" placeholder="myfriend@email.com" style="margin-left: 26px;"> <button>-</button></div>-->
				<!--<input type="email" placeholder="myfriend@email.com">-->
			
				<input type="submit" value="Enviar" class="mt8">
			</form>
		</main>
        <?php
            include_once("footer.php");
        ?>
    </body>
</html>