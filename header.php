<header>
    <h1 class="business_name"><?php echo $GLOBALS["nombre_empresa"]; ?></h1><?php if(isset($_SESSION["username"])){ echo "<b class=\"username\"><i class=\"fas fa-user\"></i> {$_SESSION["username"]}</b>"; } ?>
</header>
<div class="msg-container">
	<?php
		foreach($GLOBALS["msgs"] as $msg){
			echo $msg; 
		}
	?>
</div>