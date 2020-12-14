<?php

function initGlobal(){
    $GLOBALS["nombre_empresa"] = "TripCount";
    $GLOBALS["nombre_pagina"] = ucfirst(explode(".", explode("/", $_SERVER["PHP_SELF"])[sizeof(explode("/", $_SERVER["PHP_SELF"]))-1])[0]);
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
			$out .= 'customCreateElement("option", "' . $row["currency_iso"] . ' - ' . $row["name"] . '", document.getElementById("currency"), undefined, {"value": "' . $row["currency_iso"] . '"});' . "\n";
		}
		
		$out .= "}\n";
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
	
	$GLOBALS["js"] = $out;
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
	
    $query->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR, 32);
	$query->execute();

	if($query->rowCount() === 0){
		invokeMsgbox(["category" => "info", "id" => "home_travels_empty"]);
		return "<button id=\"addTravel\" class=\"button\">Añadir viaje <i class=\"fas fa-plus\"></i></button>";
	}
	
	$out = '<form method="POST" id="utilOrderForm"><input type="hidden" name="order_by"></form>';
	
	$out .= "<button id=\"addTravel\" class=\"button\">Añadir viaje <i class=\"fas fa-plus\"></i></button>";
	
	if($order_by === "last_modification"){
		$out .= "<table id=\"travels\">
	<tr>
		<th>Nombre del viaje</th>
		<th>Descripción</th>
		<th><i class=\"fas fa-coins\"></i> Divisa principal</th>
		<th><i class=\"fas fa-clock\"></i> <a name=\"creation_date\" class=\"order\">Fecha de creacion</a></th>
		<th><i class=\"fas fa-user-clock\"></i> Ultima modificacion <i class=\"fas fa-sort-amount-down\"></i></th>
	</tr>";
	}
	elseif($order_by === "creation_date"){
		$out .= "<table id=\"travels\">
	<tr>
		<th>Nombre del viaje</th>
		<th>Descripción</th>
		<th><i class=\"fas fa-coins\"></i> Divisa principal</th>
		<th><i class=\"fas fa-clock\"></i> Fecha de creacion <i class=\"fas fa-sort-amount-down\"></i></th>
		<th><a name=\"last_modification\" class=\"order\"><i class=\"fas fa-user-clock\"></i> Ultima modificacion</a></th>
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
			$out_d_cd .= round($delta_creation_date/60) . "</b> minutos";
		}
		elseif($delta_creation_date < 86400 && $delta_creation_date >= 3600){
			$out_d_cd .= round($delta_creation_date/3600) . "</b> horas";
		}
		elseif($delta_creation_date >= 86400){
			$out_d_cd .= round($delta_creation_date/86400) . "</b> dias";
		}
		
		$creation_date = date("d/m/Y H:i", $creation_date);
		
		$last_modification = strtotime($row["last_modification"]);
		$delta_last_modification = $now-$last_modification;
		
		$out_d_lm = "Hace <b>";
		
		if($delta_last_modification < 60){
			$out_d_lm .= "$delta_last_modification</b> segundos";
		}
		elseif($delta_last_modification < 3600 && $delta_last_modification >= 60){
			$out_d_lm .= round($delta_last_modification/60) . "</b> minutos";
		}
		elseif($delta_last_modification < 86400 && $delta_last_modification >= 3600){
			$out_d_lm .= round($delta_last_modification/3600) . "</b> horas";
		}
		elseif($delta_last_modification >= 86400){
			$out_d_lm .= round($delta_last_modification/86400) . "</b> dias";
		}
		
		$last_modification = date("d/m/Y H:i", $last_modification);
		
		$out .= "<tr>
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
