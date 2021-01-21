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

					// Redirect
					$_SESSION["selected_travel"] = $inserted_id;
					header("Location: invitations.php");
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
			$travels = home_travels("last_modification");
		}
		elseif($_POST["order_by"] === "creation_date_asc"){
			$travels = home_travels("creation_date_asc");
		}
		elseif($_POST["order_by"] === "last_modification_asc"){
			$travels = home_travels("last_modification_asc");
		}
	}
	else{
		$travels = home_travels();
	}
}
else{
	$travels = home_travels();
}

////////////////////////////////// GET ALL USER TRAVELS
$pdo = call_db();

$query = $pdo->prepare('SELECT travel_id FROM users_travels WHERE user_id = :userid;');
$query->bindParam(':userid', $_SESSION['user_id'], PDO::PARAM_INT);
$query->execute();

$user_travels = $query->fetchAll(\PDO::FETCH_ASSOC);

////////////////////////////////// GET ALL TRAVEL'S DATA
$travels_expenses = array();

foreach($user_travels as $travel_id){
	$query = $pdo->prepare('SELECT e.expense_id, e.travel_id, e.user_id, e.concept, round(e.quantity, 2) quantity, e.date, u.username, u.name, u.lastname1, u.lastname2 FROM `expenses` e JOIN `users` u ON e.user_id = u.user_id WHERE travel_id = :travelid ORDER BY date DESC;');
	$query->bindParam(':travelid', $travel_id["travel_id"], PDO::PARAM_INT);
	$query->execute();
	$travels_expenses[$travel_id["travel_id"]] = $query->fetchAll(\PDO::FETCH_ASSOC);
}

injectJS("travel_data", $travels_expenses, false);

injectJS("currencies", null, false);

?><!DOCTYPE html>
<html>
    <head>
        <?php getTitle(); ?>
		<link rel="stylesheet" href="css/fontawesome.min.css">
        <link rel="stylesheet" href="css/style.css">
		<link href="https://fonts.googleapis.com/css2?family=Potta+One&display=swap" rel="stylesheet">
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
		<form id="add_expense" action="add_expense.php" method="POST">
			<input type="hidden" name="travel_id">
		</form>
        <?php
            include_once("footer.php");
        ?>
    </body>
</html>