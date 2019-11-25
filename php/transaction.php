<?php
require_once("Connect.php");
date_default_timezone_set("America/New_York");

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

if(isset($_POST['productid']) && isset($_POST['buyerid']) && isset($_POST['sellerid']) && isset($_POST['quantity']) && isset($_POST['price'])){
	insert_transcation($_POST['productid'],$_POST['buyerid'],$_POST['sellerid'],$_POST['quantity'],$_POST['price']);
}
?>