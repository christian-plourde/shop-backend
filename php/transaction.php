<?php
date_default_timezone_set("America/New_York");
function get_db_connection() {
$servername = "www.remotemysql.com";
$username = "HQgsxOVVFA";
$password = "QU8LU8QaqR";
$database = "HQgsxOVVFA";
$dbport = "3306";
$dbh = new mysqli($servername, $username, $password, $database, $dbport);
return $dbh;
}

function insert_transcation($productid,$buyerid,$sellerid,$quantity,$price){
$db = get_db_connection();
$date = date("Y-m-d H:i:s");
$currentquantity = $db->query("SELECT quantity FROM Products WHERE productid = $productid")->fetch_assoc()['quantity'];
if($currentquantity>=$quantity){
$result = $db->query("INSERT INTO Transaction(productID,buyerID,sellerID,time_stamp,quantity,price) VALUES ($productid,$buyerid,
	$sellerid,'$date',$quantity,$price)"); 
$db->query("UPDATE Products SET quantity = quantity - $quantity WHERE productID = $productid");
}
else
{
echo("Cannot sell more than available!");
}
}

if(isset($_GET['a'])){
	insert_transcation($_GET['a'],$_GET['b'],$_GET['c'],$_GET['d'],$_GET['e']);
}
?>