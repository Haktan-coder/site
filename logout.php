<?php
session_start();
if(!isset($_SESSION['mobileapp'])) {
	session_destroy();
	header("Location: ../index.php");	
} else {
	session_destroy();
	header("Location: login.php?mobileapp");
}
exit;
