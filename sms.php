<?php
require('core/config/config.php');

$user = $_GET['cuid'];
$energy = $_GET['amount'];

$db->query("UPDATE users SET energy=energy+".$energy." WHERE id='".$user."'");


