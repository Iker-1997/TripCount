<?php
    require_once("internal/functions.php");
    initGlobal();

    if(isset($_POST)){
        if(isset($_POST["username"], $_POST["password"])){
            if(!empty($_POST["username"]) && !empty($_POST["password"])){
                $result = verify($_POST["username"], "username");
                if($result){
                    // verification ok
                    // try login
                    $pdo = call_db();
                    $query = $pdo->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
                    $query->execute(["username" => $_POST["username"]]);

					if($query->rowCount() === 1){
						// Usuario encontrado
						$row = $query->fetch();
						
						//print_r($row);
						
						if(password_verify($_POST["password"], $row['password'])){
							// Login OK!
							
							//session_name("tc_sid");
							session_start(['name' => 'TRIPCOUNT_SESS_ID']);
							session_init($row);
							
                            invokeMsgbox(["category" => "success", "id" => "login_ok"]);

                            if(isset($_SESSION["next_page"])){
                                injectJS("sleep_redirect", $_SESSION["next_page"], true);
                            }
                            else{
                                injectJS("sleep_redirect", "home.php", true);
                            }
						}
						else{
							// Password erronea
							invokeMsgbox(["category" => "error", "id" => "invalid_credentials"]);
						}
					}
					else{
						// Usuario no encontrado
						invokeMsgbox(["category" => "error", "id" => "user_not_found"]);
					}
                }  
                else{
                   invokeMsgbox($result);
                }
            }
        }
    }

?><!DOCTYPE html>
<html>
    <head>
        <?php getTitle(); ?>
        <link rel="stylesheet" href="css/style.css">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Potta+One&display=swap" rel="stylesheet">
    </head>
    <body>
        <?php
            include_once("header.php");
        ?>
        <div style="text-align: center">
            <form action="" method="POST">
                <label for="username">Usuario:</label><br>
                <input type="text" id="username" name="username" placeholder="usuario82" maxlength="32" required><br>
                <label for="password">Contraseña:</label><br>
                <input type="password" id="password" name="password" placeholder="********" required><br>
                <input class="customBtn mt8" type="submit" value="Iniciar sesion">
            </form>
        </div>
        <?php
            include_once("footer.php");
        ?>
    </body>
</html>
