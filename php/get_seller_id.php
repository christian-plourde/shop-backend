<?php
require_once("Connect.php");
require_once("User.php");

$data = json_decode(file_get_contents("php://input"), TRUE);
//To make our lives easier, set variables for easy access
$username = $data['username'];

$db=get_db_connection();
$result = $db->query("SELECT accountID FROM `Accounts` where username = " . "'$username'")->fetch_assoc();

echo json_encode($result);
?>