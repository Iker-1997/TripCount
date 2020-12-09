<?php

function initGlobal(){
    $GLOBALS["nombre_empresa"] = "TripCount";
    $GLOBALS["nombre_pagina"] = ucfirst(explode(".", explode("/", $_SERVER["PHP_SELF"])[sizeof(explode("/", $_SERVER["PHP_SELF"]))-1])[0]);
    $GLOBALS["formacion_empresa"] = 2020;
}

function call_db(){
    try {
        $hostname = "localhost";
        $dbname = "tripcount";
        $username = "tripcount";
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

function verify($value, $kind){
    if($kind === "username"){
        if(strlen($value) < 4){
            return ["category" => "error", "id" => "name_too_short"];
        }
        if(strlen($value) > 32){
            return ["category" => "error", "id" => "name_too_long"];
        }
        if(htmlentities($value) != $value){
            return ["category" => "error", "id" => "xss"];
        }
        return true;
    }
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
                return "<div class=\"msg error\">{$msg[$data["id"]]}</div>";
            }
            elseif($data["category"] == "warning"){
                return "<div class=\"msg warning\">{$msg[$data["id"]]}</div>";
            }
            elseif($data["category"] == "info"){
                return "<div class=\"msg info\">{$msg[$data["id"]]}</div>";
            }
            elseif($data["category"] == "success"){
                return "<div class=\"msg success\">{$msg[$data["id"]]}</div>";
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