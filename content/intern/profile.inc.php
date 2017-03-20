<?php require_once('content/intern/header.inc.php'); ?>

<div class="container">
	<h1>Profile</h1>
	<?php
	if(isset($_GET['a'])){
		if(file_exists(CONTENT_PATH . 'profile/' . $_GET['a'] . '.inc.php')){
			require_once(CONTENT_PATH . 'profile/' . $_GET['a'] . '.inc.php');
		}else{
			require_once(ERROR_PATH . '404.html');
		}
	}
	?>
</div>