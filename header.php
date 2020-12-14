<header>
    <h1 class="business_name"><?php if(isset($_SESSION["username"])){ echo '<a href="home.php" style="text-decoration: none; color: #01161E;">'.$GLOBALS["nombre_empresa"].'</a>'; } else { echo $GLOBALS["nombre_empresa"];} ?></h1><?php if(isset($_SESSION["username"])){ echo "<b class=\"username\"><i class=\"fas fa-user\"></i> {$_SESSION["username"]}   <a href=\"logout.php\"><i class=\"fas fa-door-open\" style=\"color: black;\"></i></a></b>"; } ?>
</header>
<div class="msg-container">
	<?php
		foreach($GLOBALS["msgs"] as $msg){
			echo $msg; 
		}
	?>
</div>