<?php
    require_once("internal/functions.php");
    initGlobal();

    if(isset($_POST)){
		
        // tenim un POST
        if(isset($_POST["username"], $_POST["name"], $_POST["lastname1"], $_POST["lastname2"], $_POST["email"], $_POST["password"], $_POST["password2"])){
            if(!empty($_POST["username"]) && !empty($_POST["name"]) && !empty($_POST["lastname1"]) && !empty($_POST["lastname2"]) && !empty($_POST["email"]) && !empty($_POST["password"]) && !empty($_POST["password2"])){
                $result = loginVerifyWrap($_POST["username"], $_POST["name"], $_POST["lastname1"], $_POST["lastname2"], $_POST["email"]);
                if($result){
					// verification ok
					if($_POST["password"] == $_POST["password2"]){
						$pdo = call_db();
						$query = $pdo->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
						$query->execute(["username" => $_POST["username"]]);

						if($query->rowCount() == 1){
							// El usuario ya existe
							invokeMsgbox(["category" => "error", "id" => "user_already_exists"]);
						}
						else{
							$pdo = call_db();
							$query = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
							$query->execute(["email" => $_POST["email"]]);
							
							if($query->rowCount() === 1){
								// Ya existe una cuenta con el mismo correo
								invokeMsgbox(["category" => "error", "id" => "email_already_in_use"]);
							}
							else{
								/*
								TODO: Tenemos que comprovar que si el email del usuario se encuentra en la table de invitaciones,
								si es asi tenemos que hacer un inster para que este dentro de todos los viajes que fue invitado a su correo
								*/
								
								// Creamos el nuevo usuario
								$query = $pdo->prepare('INSERT INTO `users` (`user_id`, `username`, `name`, `lastname1`, `lastname2`, `email`, `password`) VALUES (NULL, :username, :name, :lastname1, :lastname2, :email, :password)');
								
								$query->bindParam(':username', $_POST['username'], PDO::PARAM_STR, 32);
								$query->bindParam(':name', $_POST['name'], PDO::PARAM_STR, 32);
								$query->bindParam(':lastname1', $_POST['lastname1'], PDO::PARAM_STR, 32);
								$query->bindParam(':lastname2', $_POST['lastname2'], PDO::PARAM_STR, 32);
								$query->bindParam(':email', $_POST['email'], PDO::PARAM_STR, 255);
								
								// Obtener la hash de la password de manera segura
								$tmp = password_hash($_POST["password"], PASSWORD_BCRYPT);
								$query->bindParam(':password', $tmp, PDO::PARAM_STR, 255);
								
								$query->execute();
								invokeMsgbox(["category" => "success", "id" => "user_created_successfully"]);
								injectJS("sleep_redirect", "login.php", true);
							}
						}
					}
					else{
						// Las contraseñas no coinciden
						invokeMsgbox(["category" => "error", "id" => "passwords_ne"]);
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
    </head>
    <body>
        <?php
            include_once("header.php");
        ?>
        <div style="text-align: center">
            <form action="" method="POST">
                <label for="username">Usuario:</label><br>
                <input type="text" id="username" name="username" placeholder="usuario82" maxlength="32" required><br>
				
				<label for="name">Nombre:</label><br>
                <input type="text" id="name" name="name" placeholder="Rodrigo" maxlength="32" required><br>
				
				<label for="lastname1">Primer apellido:</label><br>
                <input type="text" id="lastname1" name="lastname1" placeholder="Perez" maxlength="32" required><br>
				
				<label for="lastname2">Segundo apellido:</label><br>
                <input type="text" id="lastname2" name="lastname2" placeholder="Soles" maxlength="32" required><br>
				
				<label for="email">Segundo apellido:</label><br>
                <input type="email" id="email" name="email" placeholder="usuerio82@email.com" maxlength="255" required><br>
				
                <label for="password">Contraseña:</label><br>
                <input type="password" id="password" name="password" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" required><br>
				
				<label for="password2">Verificar contraseña:</label><br>
                <input type="password" id="password2" name="password2" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" required><br>
				
                <input type="submit" value="Registrarse">
            </form>
        </div>
        <?php
            include_once("footer.php");
        ?>
    </body>
</html>
