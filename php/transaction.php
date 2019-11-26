<?php
require_once("Connect.php");
date_default_timezone_set("America/New_York");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function insert_transcation($productid,$buyerid,$sellerid,$quantity,$price){
var_dump("in insert_transaction");
var_dump($productid);
var_dump($buyerid);
var_dump($sellerid);
var_dump($quantity);
var_dump($price);
$db = get_db_connection();
$date = date("Y-m-d H:i:s");
$currentquantity = $db->query("SELECT quantity FROM Products WHERE productid = $productid")->fetch_assoc()['quantity'];
if($currentquantity>=$quantity){
$result = $db->query("INSERT INTO Transaction(productID,buyerID,sellerID,time_stamp,quantity,price) VALUES ($productid,$buyerid,
	$sellerid,'$date',$quantity,$price)"); 
$db->query("UPDATE Products SET quantity = quantity - $quantity WHERE productID = $productid");
send_buyer_email($productid, $buyerid, $sellerid, $date, $price);
send_seller_email($productid, $sellerid, $date);

}
else
{
var_dump("quantity error");
echo("Cannot sell more than available!");
}
var_dump("out insert_transaction");
}

function send_buyer_email($productid, $buyerid, $sellerid, $date, $price) {
	var_dump("in send_buyer_email");
	$db = get_db_connection();
	$buyerEmail = $db->query("SELECT email FROM Accounts WHERE accountID = $buyerid")->fetch_assoc()['email'];
	$buyerUser = $db->query("SELECT firstName FROM Accounts WHERE accountID = $buyerid")->fetch_assoc()['firstName'];
	$sellerName = $db->query("SELECT username FROM Accounts WHERE accountID = $sellerid ")->fetch_assoc()['username'];
	$prodName = $db->query("SELECT productName FROM Products WHERE productID = $productid")->fetch_assoc()['productName'];
	$priceAfterTax = $price * 1.15;

	var_dump($buyerEmail);
	var_dump($buyerUser);
	var_dump($sellerName);
	var_dump($prodName);
	var_dump($price);
	var_dump($priceAfterTax);


	$subject = "Thank you for your purchase from $sellerName";
	$message = "Hello $buyerUser,<br>
				Thank you for your purchase from shop354, below is your purchase summary.<br>
				<br>
				Item Bought: $prodName<br>
				Seller: $sellerName<br>
				Subtotal: \$".round($price, 2)."<br>
				Total: \$".round($priceAfterTax, 2)."<br>
				Date: $date<br>
				<br>
				Thank you for your purchase. Your item will be shipped soon!";

	$secureCheck = sanitize_email($buyerEmail);
	if($secureCheck == false) {
		echo("Invalid Email");
		var_dump("send_buyer_email invalid");
	} else {
		send_mail($buyerEmail, $subject, $message);
		var_dump("send_buyer_email sent");
		echo("email sent");
	}
	var_dump("out send_buyer_email");
}

function send_seller_email($productid, $sellerid, $date) {
	var_dump("in send_seller_email");
	$db = get_db_connection();
	$sellerEmail = $db->query("SELECT email FROM Accounts WHERE accountID = $sellerid ")->fetch_assoc()['email'];
	$sellerName = $db->query("SELECT username FROM Accounts WHERE accountID = $sellerid ")->fetch_assoc()['username'];
	$prodName = $prodName = $db->query("SELECT productName FROM Products WHERE productID = $productid")->fetch_assoc()['productName'];

	var_dump($sellerEmail);
	var_dump($sellerName);
	var_dump($prodName);


	$subject = "Your product has been bought!";
	$message = "Hello, $sellerName,<br>
				Good News! Your product, $prodName, has been bought!<br>
				Please go to your account to confirm shipment!<br>";

	$secureCheck = sanitize_email($sellerEmail);
	if($secureCheck == false) {
		echo ("Invalid Email");
		var_dump("send_seller_email invalid");
	} else {
		send_mail($sellerEmail, $subject, $message);
		var_dump("send_seller_email sent");
		echo ("email sent");
	}
	var_dump("out send_seller_email");
}


function sanitize_email($email) {
	var_dump("in sanitize_email");
	$email = filter_var($email, FILTER_SANITIZE_EMAIL);
	if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return true;
	} else {
		return false;
	}
}


function send_mail($email, $subject, $message) {
	var_dump("in send_mail");
	$mail = new PHPMailer(true);
    try {
        $mail->SMTPOptions = array(
            'ssl'=>array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
		//Server Settings
		var_dump("server settings");
        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        //MAIL LOGIN
        //shop.03544@gmail.com
        //COMP3544
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "shop.03544@gmail.com";
        $mail->Password = "COMP3544";
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

		//Receipent
		var_dump("receipent");
        $mail->setFrom('noreply@shop354.com', 'SHOP 354');
        $mail->addAddress($email);

		//Content
		var_dump("content");
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

		var_dump($mail);

        $mail->send();
		$result["msg"] = "GOOD";
		echo "good";
		var_dump("mail sent");
        echo json_encode($result);
    } catch (Exception $e) {
		$result["msg"] = "error $mail->ErrorInfo";
		var_dump("mail failed");
		var_dump($mail->ErrorInfo);
		echo json_encode($result);
	}
	var_dump("out send_mail");
}


if(isset($_POST['productid']) && isset($_POST['buyerid']) && isset($_POST['sellerid']) && isset($_POST['quantity']) && isset($_POST['price'])){
	insert_transcation($_POST['productid'],$_POST['buyerid'],$_POST['sellerid'],$_POST['quantity'],$_POST['price']);
}
?>