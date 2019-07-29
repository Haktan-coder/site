<?php
require('core/system.php');
if(isLogged()) {
	header('Location: home.php');
} else {
	header('Location: login.php');
}
exit;
