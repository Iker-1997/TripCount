<?php

require_once("internal/functions.php");
initGlobal();

session_start(['name' => 'TRIPCOUNT_SESS_ID']);

if(isset($_GET["invite"])){

}

if(!isset($_SESSION["username"])){
    if(isset($_GET["invite"])){
        if(is_numeric($_GET["invite"])){
            $_SESSION["next_page"] = "accept_invite.php?invite={$_GET["invite"]}";
        }
    }
    header("Location: login.php");    
}
else{
    // destroy redirect
    unset($_SESSION["next_page"]);

    // session in ok
    if(isset($_GET["invite"])){
        // check if the invite is for me
        $pdo = call_db();
        $query = $pdo->prepare('SELECT travel_id FROM users u JOIN invitations i ON i.email = u.email WHERE u.email = :email AND i.invitation_id = :invitationid');
        $query->bindParam(':email', $_SESSION["email"], PDO::PARAM_STR, 255);
        $query->bindParam(':invitationid', $_GET["invite"], PDO::PARAM_INT);
        $query->execute();

        $a = $query->fetchAll();
		if(sizeof($a) !== 0){
            // okey, add me to the travel!
            // add user to travel
            $a = $a[0];
            $query = $pdo->prepare('INSERT INTO `users_travels` (`user_id`, `travel_id`, `association_date`) VALUES (:userid, :travelid, current_timestamp())');
            $query->bindParam(':userid', $_SESSION["user_id"], PDO::PARAM_INT);
            $query->bindParam(':travelid', $a["travel_id"], PDO::PARAM_INT);
            $query->execute();

            // delete used invitation
            $query = $pdo->prepare('DELETE FROM `invitations` WHERE invitation_id = :invitationid');
            $query->bindParam(':invitationid', $_GET["invite"], PDO::PARAM_INT);
            $query->execute();
            invokeMsgbox(["category" => "success", "id" => "invite_accepted"]);
            header("Location: home.php");
        }
        else{
            header("Location: login.php");
        }
    }
    else{
        header("Location: login.php");
    }
}

?>