<?php

require_once("internal/functions.php");
initGlobal();

session_start(['name' => 'TRIPCOUNT_SESS_ID']);

if(!isset($_SESSION["username"])){
	session_destroy();
	header("Location: login.php");
}

if(isset($_POST)){
	if(isset($_POST["action"])){
		if($_POST["action"] === "add_travel"){
			if(isset($_POST["travel_name"], $_POST["description"], $_POST["currency"])){

				$result = addTravelVerifyWrap($_POST["travel_name"], $_POST["description"], $_POST["currency"]);

				if($result === true){
					$pdo = call_db();

					$query = $pdo->prepare('INSERT INTO `travels` (`travel_id`, `name`, `description`, `currency_iso`, `creation_date`, `last_modification`) VALUES (null, :travel_name, :description, :currency, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP());');

					$query->bindParam(':travel_name', $_POST['travel_name'], PDO::PARAM_STR, 32);
					$query->bindParam(':description', $_POST['description'], PDO::PARAM_STR, 255);
					$query->bindParam(':currency', $_POST['currency'], PDO::PARAM_STR, 3);

					$query->execute();
					
					$inserted_id = $pdo->lastInsertId();

					$query = $pdo->prepare('INSERT INTO `users_travels` (`user_id`, `travel_id`, `association_date`) VALUES (:user_id, :travel_id, CURRENT_TIMESTAMP());');

					$query->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
					$query->bindParam(':travel_id', $inserted_id, PDO::PARAM_INT);
					
					$query->execute();

					invokeMsgbox(["category" => "success", "id" => "travel_created_successfully"]);
				}
				else{
					invokeMsgbox($result);
				}
			}
		}
	}
}

if(isset($_POST)){
	if(isset($_POST["order_by"])){
		if($_POST["order_by"] === "creation_date"){
			$travels = home_travels("creation_date");
		}
		elseif($_POST["order_by"] === "last_modification"){
			$travels = home_travels();
		}
	}
	else{
		$travels = home_travels();
	}
}
else{
	$travels = home_travels();
}

injectJS("currencies", null, false);

?><!DOCTYPE html>
<html>
    <head>
        <?php getTitle(); ?>
		<link rel="stylesheet" href="css/fontawesome.min.css">
		<link rel="stylesheet" href="css/general.css">
        <link rel="stylesheet" href="css/home.css">
		<script src="js/util.js" async></script>
		<script src="js/home.js" async></script>
    </head>
    <body>
        <?php
            include_once("header.php");
        ?>
		<main>
			<?php
				echo $travels;
			?>
			
			<div id="dynAddTravelForm">
			</div>
		</main>
        <?php
            include_once("footer.php");
        ?>
    </body>
</html>