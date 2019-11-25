<?php
require_once("Connect.php");
date_default_timezone_set("America/New_York");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

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
		send_mail($buyerEmail, $subject, $message);
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
		send_mail($sellerEmail, $subject, $message);
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


function send_mail($email, $subject, $message) {
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
        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        //MAIL LOGIN
        //shop.03544@gmail.com
        //COMP3544
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "shop.03544@gmail.com";
        $mail->Password = "COMP3544";
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        //Receipent
        $mail->setFrom('noreply@shop354.com', 'SHOP 354');
        $mail->addAddress($email);

        //Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        $result["msg"] = "GOOD";
        echo json_encode($result);
    } catch (Exception $e) {
        $result["msg"] = "error $mail->ErrorInfo";
        echo json_encode($result);
    }
}


if(isset($_POST['productid']) && isset($_POST['buyerid']) && isset($_POST['sellerid']) && isset($_POST['quantity']) && isset($_POST['price'])){
	insert_transcation($_POST['productid'],$_POST['buyerid'],$_POST['sellerid'],$_POST['quantity'],$_POST['price']);
}
?>