<?php
    require_once("internal/functions.php");
    initGlobal();

    if(isset($_POST)){
        // tenim un POST
        if(isset($_POST["username"], $_POST["password"])){
            if(!empty($_POST["username"]) && !empty($_POST["password"])){
                $result = verify($_POST["username"], "username");
                if($result){
                    // verification ok
                    // try login
                }  
                else{
                    $msg = invokeMsgbox($result);
                }
            }
        }
    }

    /* test */
    $msg = "";
    $msg = invokeMsgbox(["category" => "success", "id" => "name_too_short"]);

?>
<!DOCTYPE html>
<html>
    <head>
        <?php getTitle(); ?>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <?php
            include_once("header.php");
        ?>
        <?php
            echo $msg;
        ?>
        <div style="text-align: center">
            <form action="" method="POST">
                <label for="username">Usuari:</label><br>
                <input type="text" id="username" name="username" placeholder="usuari82" required><br>
                <label for="password">Contrasenya:</label><br>
                <input type="password" id="password" name="password" placeholder="password" required><br>
                <input type="submit" value="Iniciar sesion">
            </form>
        </div>
        <?php
            include_once("footer.php");
        ?>
    </body>
</html>