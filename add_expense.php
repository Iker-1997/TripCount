<?php

require_once("internal/functions.php");
initGlobal();

session_start(['name' => 'TRIPCOUNT_SESS_ID']);

if(!isset($_SESSION["username"])){
	session_destroy();
	header("Location: login.php");
}

if(isset($_POST['travel_id'])){
    $travel_id = str_replace("travel_", "", $_POST['travel_id']);
    if(!is_numeric($travel_id)){
        header("Location: home.php");
    }

    $pdo = call_db();
    $query = $pdo->prepare('SELECT t.name FROM `travels` t JOIN users_travels ut ON t.travel_id = ut.travel_id JOIN users u ON ut.user_id = u.user_id WHERE u.user_id = :userid and ut.travel_id = :travelid');
    $query->bindParam(':userid', $_SESSION['user_id'], PDO::PARAM_INT);
    $query->bindParam(':travelid', $travel_id, PDO::PARAM_INT);
    $query->execute();

    $travel = $query->fetchAll();

    if(sizeof($travel) > 0){
        // okey
        $travel = $travel[0];
        // set selected travel
        $_SESSION['selected_travel'] = $travel_id;
    }
    else{
        // not ok
        header("Location: home.php");
    }
}
elseif(isset($_SESSION['selected_travel'], $_POST["subject"], $_POST["concept"], $_POST["quantity"])){

    $subject = $_POST["subject"];
    $concept = $_POST["concept"];
    $quantity = $_POST["quantity"];
    $travel_id = $_SESSION['selected_travel'];

    //
    $pdo = call_db();
    $query = $pdo->prepare('SELECT name FROM `travels` WHERE travel_id = :travelid');
    $query->bindParam(':travelid', $travel_id, PDO::PARAM_INT);
    $query->execute();

    $travel = $query->fetchAll();
    $travel = $travel[0];

    if(is_numeric($subject)){
        if(is_numeric($quantity)){
            $result = verify($concept, "name");
            if($result){
                // check if subject is in travel
                $query = $pdo->prepare('SELECT * FROM `users_travels` WHERE user_id = :subject and travel_id = :travelid');
                $query->bindParam(':subject', $subject, PDO::PARAM_INT);
                $query->bindParam(':travelid', $travel_id, PDO::PARAM_INT);
                $query->execute();
                $a = $query->fetchAll();

                if(sizeof($a) > 0){
                    // insert
                    $pdo = call_db();
                    $query = $pdo->prepare('INSERT INTO `expenses` (`expense_id`, `travel_id`, `user_id`, `concept`, `quantity`, `date`) VALUES (NULL, :travelid, :subject, :concept, :quantity, current_timestamp())');
                    $query->bindParam(':travelid', $travel_id, PDO::PARAM_INT);
                    $query->bindParam(':subject', $subject, PDO::PARAM_INT);
                    $query->bindParam(':concept', $concept, PDO::PARAM_STR, 32);
                    $query->bindParam(':quantity', $quantity, PDO::PARAM_STR);

                    $query->execute();

                    invokeMsgbox(["category" => "success", "id" => "add_expense_ok"]);
                }
                else{
                    invokeMsgbox(["category" => "error", "id" => "user_does_not_belong_to_travel"]);
                }
            }
            else{
                invokeMsgbox($result);
            }
        }
        else{
            invokeMsgbox(["category" => "error", "id" => "non_numeric_value_quantity"]);
        }
    }
    else{
        invokeMsgbox(["category" => "error", "id" => "non_numeric_value_subject"]);
    }

}
else{
    header("Location: home.php");
}


// get all participants
$pdo = call_db();

$query = $pdo->prepare('SELECT u.user_id, u.username, u.name, u.lastname1, u.lastname2, u.email FROM `users_travels` ut JOIN users u ON ut.user_id = u.user_id WHERE ut.travel_id = :travelid;');
$query->bindParam(':travelid', $travel_id, PDO::PARAM_INT);
$query->execute();

$htmlselect = '<select name="subject" id="subject">' . "\n";
while($row = $query->fetch()){
    $htmlselect .= "<option value=\"{$row["user_id"]}\">{$row["username"]} | {$row["name"]} {$row["lastname1"]} {$row["lastname2"]} | {$row["email"]}</option>" . "\n";
}
$htmlselect .= "</select>" . "\n";

?><!DOCTYPE html>
<html>
    <head>
        <?php getTitle(); ?>
		<link rel="stylesheet" href="css/fontawesome.min.css">
        <link rel="stylesheet" href="css/style.css">
		<link href="https://fonts.googleapis.com/css2?family=Potta+One&display=swap" rel="stylesheet">
		<script src="js/util.js" async></script>
    </head>
    <body>
        <?php
            include_once("header.php");
		?>
		<main>
            <center><h1>Añadir gasto a ≪<?php echo $travel["name"]; ?>≫</h1></center>
            <div style="text-align: center">
                <form action="" method="POST">
                    <label for="concept">Concepto:</label><br>
                    <input id="concept" name="concept" type="text" maxlength="32" placeholder="BBQ"><br>

                    <label for="quantity">Cantidad:</label><br>
                    <input name="quantity" type="number" min="0.1" max="999999" step="0.1" placeholder="5"><br>

                    <label for="subject">Pagador:</label><br>
                    <?php echo $htmlselect; ?><br>

                    <input type="submit" value="Añadir" class="mt8">
                </form>
            </div>
		</main>
        <?php
            include_once("footer.php");
        ?>
    </body>
</html>