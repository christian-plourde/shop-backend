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
send_buyer_email($productid, $buyerid, $sellerid, $date, $price);
send_seller_email($product, $sellerid, $date);
}
else
{
echo("Cannot sell more than available!");
}
}

function send_buyer_email($productid, $buyerid, $sellerid, $date, $price) {
	$db = get_db_connection();
	$buyerEmail = $db->query("SELECT email FROM Accounts WHERE accountID = $buyerid")->fetch_assoc()['email'];
	$buyerUser = $db->query("SELECT firstName FROM Accounts WHERE accountID = $buyerid")->fetch_assoc()['firstName'];
	$sellerName = $db->query("SELECT username FROM Accounts WHERE accountID = $sellerid ")->fetch_assoc()['username'];
	$prodName = $db->query("SELECT productName FROM Products WHERE productID = $productid")->fetch_assoc()['productName'];
	$priceAfterTax = $price * 1.15;

	$subject = "Thank you for your purchase from $sellerName";
	$message = "Hello, $buyerUser,
				Thank you for your purchase from shop354, below is your purchase summary.

				Item Bought: $prodName
				Seller: $sellerName
				Subtotal: $price
				Total: $priceAfterTax
				Date: $date
				
				Thank you for your purchase. Your item will be shipped soon!";
	$secureCheck = sanitize_email($buyerEmail);
	if($secureCheck == false) {
		echo("Invalid Email");
	} else {
		mail($buyerEmail, $subject, $message);
		echo("email sent");
	}

}

function send_seller_email($productid, $sellerid, $date) {
	$db = get_db_connection();
	$sellerEmail = $db->query("SELECT email FROM Accounts WHERE accountID = $sellerid ")->fetch_assoc()['email'];
	$sellerName = $db->query("SELECT username FROM Accounts WHERE accountID = $sellerid ")->fetch_assoc()['username'];
	$prodName = $prodName = $db->query("SELECT productName FROM Products WHERE productID = $productid")->fetch_assoc()['productName'];

	$subject = "Your product has been bought!";
	$message = "Hello, $sellerName,
				Good News! Your product, $prodName, has been bought!
				Please go to your account to confirm shipment!";
	$secureCheck = sanitize_email($sellerEmail);
	if($secureCheck == false) {
		echo ("Invalid Email");
	} else {
		mail($sellerEmail, $subject, $message);
		echo ("email sent");
	}
}


function sanitize_email($email) {
	$email = filter_var($email, FILTER_SANITIZE_EMAIL);
	if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return true;
	} else {
		return false;
	}
}



if(isset($_POST['productid']) && isset($_POST['buyerid']) && isset($_POST['sellerid']) && isset($_POST['quantity']) && isset($_POST['price'])){
	insert_transcation($_POST['productid'],$_POST['buyerid'],$_POST['sellerid'],$_POST['quantity'],$_POST['price']);
}
?>