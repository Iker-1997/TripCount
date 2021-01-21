<header>
    <h1 class="business_name">
		<a href="home.php">
		<?php 
		echo $GLOBALS["nombre_empresa"];
		?>
		</a>
	</h1>
	<?php
		if(isset($_SESSION["username"])){
			echo "<b class=\"username\"><i class=\"fas fa-user\"></i> {$_SESSION["username"]} <a href=\"logout.php\"><i class=\"fas fa-door-open\"></i></a></b>";
		}
	?>
</header>
<div class="msg-container">
	<?php
		foreach($GLOBALS["msgs"] as $msg){
			echo $msg; 
		}
	?>
</div>