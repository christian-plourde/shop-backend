<?php
require_once("Connect.php");

$data = json_decode(file_get_contents("php://input"), TRUE);
$data = array("ownerId" => "1");
//To make our lives easier, set variables for easy access
$ownerId = $data['ownerId'];

$db=get_db_connection();
$result = $db->query("SELECT username FROM `Accounts` where accountID = " . "$ownerId")->fetch_assoc();

echo json_encode($result);
?>

