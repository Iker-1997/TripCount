<?php

function initGlobal(){
	$GLOBALS["nombre_empresa"] = "TripCount";
	$GLOBALS["nombre_pagina"] = ucwords(str_replace("_", " ", explode(".", explode("/", $_SERVER["PHP_SELF"])[sizeof(explode("/", $_SERVER["PHP_SELF"]))-1])[0]));
    $GLOBALS["formacion_empresa"] = 2019;
	$GLOBALS["msgs"] = array();
	$GLOBALS["js"] = "";
}

function call_db(){
    try {
        $hostname = "localhost";
        $dbname = "tripcount";
        $username = "root";
        $pw = '';
        $pdo = new PDO ("mysql:host=$hostname;dbname=$dbname","$username","$pw");
      } catch (PDOException $e) {
        die("ERROR DB: $e");
      }
      return $pdo;
}

function login(){

}

function getTitle(){
    echo "<title>{$GLOBALS["nombre_pagina"]} - {$GLOBALS["nombre_empresa"]}</title>";
}

function verify($value, $kind=false){
    if(htmlspecialchars($value) !== $value){
        return ["category" => "error", "id" => "xss"];
    }
	if($kind === "username"){
		if(!ctype_alnum($value)){
			return ["category" => "error", "id" => "username_non_alnum"];
		}
        if(strlen($value) < 4){
            return ["category" => "error", "id" => "username_too_short"];
        }
        if(strlen($value) > 32){
            return ["category" => "error", "id" => "username_too_long"];
        }
        return true;
    }
	elseif($kind === "name"){
		if(!ctype_alpha(str_replace(' ', '', $value))){
			return ["category" => "error", "id" => "name_non_alnum"];
		}
		if(strlen($value) < 2){
            //return ["category" => "error", "id" => "username_too_short"];
        }
        if(strlen($value) > 32){
            //return ["category" => "error", "id" => "username_too_long"];
        }
        return true;
    }
	elseif($kind === "lastname"){
		if(!ctype_alpha(str_replace(' ', '', $value))){
			return ["category" => "error", "id" => "lastname_non_alnum"];
		}
		if(strlen($value) < 2){
            //return ["category" => "error", "id" => "username_too_short"];
        }
        if(strlen($value) > 32){
            //return ["category" => "error", "id" => "username_too_long"];
        }
        return true;
    }
	elseif($kind === "email"){
		if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
			return ["category" => "error", "id" => "email_invalid"];
		}
        return true;
    }
	elseif($kind === "travel_name"){
		if(!ctype_alnum(str_replace(' ', '', $value))){
			return ["category" => "error", "id" => "travelname_non_alnum"];
		}
		if(strlen($value) < 2){
            //return ["category" => "error", "id" => "travelname_too_short"];
        }
        if(strlen($value) > 32){
            return ["category" => "error", "id" => "travelname_too_long"];
        }
        return true;
    }
	elseif($kind === "description"){
		if(strlen($value) < 1){
            //return ["category" => "error", "id" => "travelname_too_short"];
        }
        if(strlen($value) > 255){
            return ["category" => "error", "id" => "description_too_long"];
        }
        return true;
    }
	elseif($kind === "currency"){
		if(!ctype_alpha($value) || strlen($value) !== 3){
            return ["category" => "error", "id" => "invalid_currency_format"];
        }
        return true;
    }
	return true;
}

function loginVerifyWrap($username, $name, $lastname1, $lastname2, $email){
	
	$result = verify($username, "username");
	if(is_array($result)){
		return $result;
	}
	
	$result = verify($name, "name");
	if(is_array($result)){
		return $result;
	}
	
	$result = verify($lastname1, "lastname");
	if(is_array($result)){
		return $result;
	}
	
	$result = verify($lastname2, "lastname");
	if(is_array($result)){
		return $result;
	}
	
	$result = verify($email, "email");
	if(is_array($result)){
		return $result;
	}
	
	return true;
}

function addTravelVerifyWrap($travel_name, $description, $currency){
	
	$result = verify($travel_name, "travel_name");
	if(is_array($result)){
		return $result;
	}

	$result = verify($description, "description");
	if(is_array($result)){
		return $result;
	}
	
	$result = verify($currency, "currency");
	if(is_array($result)){
		return $result;
	}
	
	return true;
}

function loadMsg(){
    $h = fopen("internal/msg.json", "r");
    $data = fread($h, filesize("internal/msg.json"));
    fclose($h);
    return json_decode($data, true);
}

function invokeMsgbox($data){
    $msg = loadMsg();

    if(is_array($data)){
        if(!empty($data["category"]) && !empty($data["id"])){
            if($data["category"] == "error"){
                $GLOBALS["msgs"][] = "<div class=\"msg error\">{$msg[$data["id"]]}</div>";
            }
            elseif($data["category"] == "warning"){
                $GLOBALS["msgs"][] = "<div class=\"msg warning\">{$msg[$data["id"]]}</div>";
            }
            elseif($data["category"] == "info"){
                $GLOBALS["msgs"][] = "<div class=\"msg info\">{$msg[$data["id"]]}</div>";
            }
            elseif($data["category"] == "success"){
                $GLOBALS["msgs"][] = "<div class=\"msg success\">{$msg[$data["id"]]}</div>";
            }
            else{  
                die("die!");
            }
        }
        else{
            die("Malformed argument recived in 'invokeMsgbox' function!");
        }
    }
    else{
        die("Invalid usage of 'invokeMsgbox' function!");
    }
}

function injectJS($kind, $argument, $base64){
	
	$out = "";
	
	if($kind == "sleep_redirect"){
		$out = "setTimeout(function(){window.location.replace(\"$argument\");},3000);";
	}
	elseif($kind == "currencies"){
		$pdo = call_db();

		$query = $pdo->prepare('SELECT * FROM `currencies`');

		$query->execute();

		$out .= "function createCurrenciesOptions(){\n";

		while($row = $query->fetch()){
			$out .= 'customCreateElement("option", "' . $row["currency_iso"] . ' - ' . $row["name"] . ' - ' . $row["currency_char"] . '", document.getElementById("currency"), undefined, {"value": "' . $row["currency_iso"] . '"});' . "\n";
		}
		
		$out .= "}\n";
	}
	elseif($kind == "travel_data"){
		$out .= "var travel_data = {" . "\n";
		$q1 = 0;
		foreach($argument as $travel_id => $travel_data){
			$q1++;
			$out .= '"travel_' . $travel_id . '": [' . "\n";
			$q2 = 0;
			foreach($travel_data as $expense_id => $expense_data){
				$q2++;
				//$out .= '"' . $expense_id . '": {';
				$out .= '{';
				$q3 = 0;
				foreach($expense_data as $expense_name => $expense_value){
					$q3++;
					$out .= '"' . $expense_name . '": "' . $expense_value . '"';

					if($q3 !== sizeof($expense_data)){
						$out .= ',';
					}
					$out .= "\n";
				}

				if($q2 !== sizeof($travel_data)){
					$out .= '},';
				}
				else{
					$out .= '}';
				}
				$out .= "\n";
			}

			if($q1 !== sizeof($argument)){
				$out .= '],';
			}
			else{
				$out .= ']';
			}
			$out .= "\n";
		}

		$out .= "};" . "\n";
	}

	if($base64){
		$out = base64_encode($out);
		$out = "<script>
		eval(atob(\"$out\"));
		</script>";
	}
	else{
		$out = "<script>
		$out
		</script>";
	}
	
	$GLOBALS["js"] .= $out;
}

function session_init($user){
	$_SESSION["user_id"] = $user["user_id"];
	$_SESSION["username"] = $user["username"];
	$_SESSION["name"] = $user["name"];
	$_SESSION["lastname1"] = $user["lastname1"];
	$_SESSION["lastname2"] = $user["lastname2"];
	$_SESSION["email"] = $user["email"];
}

function home_travels($order_by = "last_modification"){
	$pdo = call_db();
	
	if($order_by === "last_modification"){
		$query = $pdo->prepare('SELECT t.travel_id, t.name, t.description, t.currency_iso, t.creation_date, t.last_modification FROM `travels` t JOIN users_travels ut ON t.travel_id = ut.travel_id JOIN users u ON u.user_id = ut.user_id  WHERE u.username = :username ORDER BY t.last_modification DESC;');
	}
	elseif($order_by === "creation_date"){
		$query = $pdo->prepare('SELECT t.travel_id, t.name, t.description, t.currency_iso, t.creation_date, t.last_modification FROM `travels` t JOIN users_travels ut ON t.travel_id = ut.travel_id JOIN users u ON u.user_id = ut.user_id  WHERE u.username = :username ORDER BY t.creation_date DESC;');
	}
	elseif($order_by === "last_modification_asc"){
		$query = $pdo->prepare('SELECT t.travel_id, t.name, t.description, t.currency_iso, t.creation_date, t.last_modification FROM `travels` t JOIN users_travels ut ON t.travel_id = ut.travel_id JOIN users u ON u.user_id = ut.user_id  WHERE u.username = :username ORDER BY t.last_modification ASC;');
	}
	elseif($order_by === "creation_date_asc"){
		$query = $pdo->prepare('SELECT t.travel_id, t.name, t.description, t.currency_iso, t.creation_date, t.last_modification FROM `travels` t JOIN users_travels ut ON t.travel_id = ut.travel_id JOIN users u ON u.user_id = ut.user_id  WHERE u.username = :username ORDER BY t.creation_date ASC;');
	}
	
    $query->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR, 32);
	$query->execute();

	if($query->rowCount() === 0){
		invokeMsgbox(["category" => "info", "id" => "home_travels_empty"]);
		// <button id=\"addTravel\">Añadir viaje <i class=\"fas fa-plus\"></i></button>
		return "<div id=\"addTravel\" class=\"customBtn mt8\">Añadir viaje <i class=\"fas fa-plus\"></i></div>";
	}
	
	$out = '<form method="POST" id="utilOrderForm"><input type="hidden" name="order_by"></form>';
	

	//$out .= "<button id=\"addTravel\">Añadir viaje <i class=\"fas fa-plus\"></i></button>";
	$out .= "<div id=\"addTravel\" class=\"customBtn mt8\">Añadir viaje <i class=\"fas fa-plus\"></i></div>";
	
	if($order_by === "last_modification"){
		$out .= "<table id=\"travels\">
	<tr>
		<th>Nombre del viaje</th>
		<th>Descripción</th>
		<th><i class=\"fas fa-coins\"></i> Divisa principal</th>
		<th><a name=\"creation_date\" class=\"order\" style=\"margin-right: 22px\"><i class=\"fas fa-clock\"></i> Fecha de creacion</a></th>
		<th><a name=\"last_modification_asc\" class=\"order\"><i class=\"fas fa-user-clock\"></i> Ultima modificacion <i class=\"fas fa-sort-amount-down\"></i></a></th>
	</tr>";
	}
	elseif($order_by === "creation_date"){
		$out .= "<table id=\"travels\">
	<tr>
		<th>Nombre del viaje</th>
		<th>Descripción</th>
		<th><i class=\"fas fa-coins\"></i> Divisa principal</th>
		<th><a name=\"creation_date_asc\" class=\"order\"><i class=\"fas fa-clock\"></i> Fecha de creacion <i class=\"fas fa-sort-amount-down\"></i></a></th>
		<th><a name=\"last_modification\" class=\"order\" style=\"margin-right: 22px\"><i class=\"fas fa-user-clock\"></i> Ultima modificacion</a></th>
	</tr>";
	}
	elseif($order_by === "last_modification_asc"){
		$out .= "<table id=\"travels\">
	<tr>
		<th>Nombre del viaje</th>
		<th>Descripción</th>
		<th><i class=\"fas fa-coins\"></i> Divisa principal</th>
		<th><a name=\"creation_date\" class=\"order\" style=\"margin-right: 22px\"><i class=\"fas fa-clock\"></i> Fecha de creacion</a></th>
		<th><a name=\"last_modification\" class=\"order\"><i class=\"fas fa-user-clock\"></i> Ultima modificacion <i class=\"fas fa-sort-amount-up\"></i></a></th>
	</tr>";
	}
	elseif($order_by === "creation_date_asc"){
		$out .= "<table id=\"travels\">
	<tr>
		<th>Nombre del viaje</th>
		<th>Descripción</th>
		<th><i class=\"fas fa-coins\"></i> Divisa principal</th>
		<th><a name=\"creation_date\" class=\"order\"><i class=\"fas fa-clock\"></i> Fecha de creacion <i class=\"fas fa-sort-amount-up\"></i></a></th>
		<th><a name=\"last_modification\" class=\"order\" style=\"margin-right: 22px\"><i class=\"fas fa-user-clock\"></i> Ultima modificacion</a></th>
	</tr>";
	}
	
	$now = time();
	
	while($row = $query->fetch()){
		$creation_date = strtotime($row["creation_date"]);
		$delta_creation_date = $now-$creation_date;
		
		$out_d_cd = "Hace <b>";
		
		if($delta_creation_date < 60){
			$out_d_cd .= "$delta_creation_date</b> segundos";
		}
		elseif($delta_creation_date < 3600 && $delta_creation_date >= 60){
			if(round($delta_creation_date/60) == 1){
				$out_d_cd .= round($delta_creation_date/60) . "</b> minuto";
			}
			else{
				$out_d_cd .= round($delta_creation_date/60) . "</b> minutos";
			}
		}
		elseif($delta_creation_date < 86400 && $delta_creation_date >= 3600){
			if(round($delta_creation_date/3600) == 1){
				$out_d_cd .= round($delta_creation_date/3600) . "</b> hora";
			}
			else{
				$out_d_cd .= round($delta_creation_date/3600) . "</b> horas";
			}
		}
		elseif($delta_creation_date >= 86400){
			if(round($delta_creation_date/86400) == 1){
				$out_d_cd .= round($delta_creation_date/86400) . "</b> dia";
			}
			else{
				$out_d_cd .= round($delta_creation_date/86400) . "</b> dias";
			}
			
		}
		
		$creation_date = date("d/m/Y H:i", $creation_date);
		
		$last_modification = strtotime($row["last_modification"]);
		$delta_last_modification = $now-$last_modification;
		
		$out_d_lm = "Hace <b>";
		
		if($delta_last_modification < 60){
			$out_d_lm .= "$delta_last_modification</b> segundos";
		}
		elseif($delta_last_modification < 3600 && $delta_last_modification >= 60){
			if(round($delta_last_modification/60) == 1){
				$out_d_lm .= round($delta_last_modification/60) . "</b> minuto";
			}
			else{
				$out_d_lm .= round($delta_last_modification/60) . "</b> minutos";
			}
		}
		elseif($delta_last_modification < 86400 && $delta_last_modification >= 3600){
			if(round($delta_last_modification/3600) == 1){
				$out_d_lm .= round($delta_last_modification/3600) . "</b> hora";
			}
			else{
				$out_d_lm .= round($delta_last_modification/3600) . "</b> horas";
			}
		}
		elseif($delta_last_modification >= 86400){
			if(round($delta_last_modification/86400) == 1){
				$out_d_lm .= round($delta_last_modification/86400) . "</b> dia";
			}
			else{
				$out_d_lm .= round($delta_last_modification/86400) . "</b> dias";
			}
		}
		
		$last_modification = date("d/m/Y H:i", $last_modification);
		
		$out .= "<tr class=\"clickeable\" id=\"travel_" . $row["travel_id"] . "\" onclick=\"ddd('travel_" . $row["travel_id"] . "')\">
			<td>{$row["name"]}</td>
			<td>{$row["description"]}</td>
			<td>{$row["currency_iso"]}</td>
			<td>$creation_date ($out_d_cd)</td>
			<td>$last_modification ($out_d_lm)</td>
		</tr>";
	}
	
	$out .= "</table>";
	
	return $out;
}

function send_email($email, $from, $subject, $htmlmessage, $plainmessage, $headers = null)
{
	// Unique boundary
	$boundary = md5( uniqid() . microtime() );

	// If no $headers sent
	if (empty($headers))
	{
		// Add From: header
		$headers = "From: " . $from["name"] . " <" . $from["email"] . ">\r\n";

		// Specify MIME version 1.0
		$headers .= "MIME-Version: 1.0\r\n";

		// Tell e-mail client this e-mail contains alternate versions
		$headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n\r\n";
	}

	// Plain text version of message
	$body = "--$boundary\r\n" .
	   "Content-Type: text/plain; charset=UTF-8\r\n" .
	   "Content-Transfer-Encoding: base64\r\n\r\n";
	$body .= chunk_split( base64_encode( strip_tags($plainmessage) ) );

	// HTML version of message
	$body .= "--$boundary\r\n" .
	   "Content-Type: text/html; charset=UTF-8\r\n" .
	   "Content-Transfer-Encoding: base64\r\n\r\n";
	$body .= chunk_split( base64_encode( $htmlmessage ) );

	$body .= "--$boundary--";

	// Send Email
	if (is_array($email))
	{
		foreach ($email as $e)
		{
			mail($e, $subject, $body, $headers);
		}
	}
	else
	{
		mail($email, $subject, $body, $headers);
	}
}