<?php
require_once("Connect.php");
require_once("User.php");
date_default_timezone_set("America/New_York");
$postdata = file_get_contents('php://input');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

//Return 8% of all transactions since from_date
function get_commission_since_date($from_date){
	$query = "SELECT sum(price) as total_price from Transaction WHERE time_stamp > $from_date and time_stamp < (SELECT current_timestamp)";
	$db = get_db_connection();
	$price = $db->query($query)->fetch_assoc()['total_price'];
	return $price * 0.08;
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

//function send_buyer_email($productid, $buyerid, $sellerid, $date, $price) {
	/*$db = get_db_connection();
	$buyerEmail = $db->query("SELECT email FROM Accounts WHERE accountID = $buyerid")->fetch_assoc()['email'];
	$buyerUser = $db->query("SELECT firstName FROM Accounts WHERE accountID = $buyerid")->fetch_assoc()['firstName'];
	$sellerName = $db->query("SELECT username FROM Accounts WHERE accountID = $sellerid ")->fetch_assoc()['username'];
	$prodName = $db->query("SELECT productName FROM Products WHERE productID = $productid")->fetch_assoc()['productName'];
	$priceAfterTax = $price * 1.15;

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
				Thank you for your purchase. Your item will be shipped soon!";*/

function send_buyer_email($buyerEmail, $subject, $message) {	
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
	$message = "Hello, $sellerName,<br>
				Good News! Your product, $prodName, has been bought!<br>
				Please go to your account to confirm shipment!<br>";

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
        $mail->setFrom('noreply@shop354.com', 'SHOP 354');
        $mail->addAddress($email);

		//Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

		var_dump($mail);

        $mail->send();
		$result["msg"] = "GOOD";
		echo "good";
        echo json_encode($result);
    } catch (Exception $e) {
		$result["msg"] = "error $mail->ErrorInfo";
		echo json_encode($result);
	}
	var_dump("out send_mail");
}


if(isset($postdata)){
$productarray = json_decode($postdata,TRUE)['products'];
$date = date("Y-m-d H:i:s");
$body = "Thank you for your purchase from Shop354 on $date. <br> Below is your purchase summary. <br><br>";
$subtotal = 0;
$db = get_db_connection();
$buyerUser = json_decode(get_user_details(json_decode($postdata,TRUE)['username']),TRUE)['username'];
echo "Buyer username: " . $buyerUser . "/n";
$buyerEmail = $db->query("SELECT email FROM Accounts WHERE username = '$buyerUser'")->fetch_assoc()['email'];
echo "Buyer e-mail: " . $buyerEmail . "/n";
for($i = 0; $i<count($productarray); $i = $i + 1){
$productid = json_decode(json_decode($postdata,TRUE)['products'][$i],TRUE)['productID'];
$buyerid = json_decode(get_user_details(json_decode($postdata,TRUE)['username']),TRUE)['accountID'];
$sellerid = json_decode(json_decode($postdata,TRUE)['products'][$i],TRUE)['ownerID'];
$quantity = json_decode((json_decode($postdata,TRUE))['products'][$i],TRUE)['cartQuantity'];
$price = json_decode((json_decode($postdata,TRUE))['products'][$i],TRUE)['productPrice'];
insert_transcation($productid,$buyerid,$sellerid,$quantity,$price);

$sellerName = $db->query("SELECT username FROM Accounts WHERE accountID = $sellerid ")->fetch_assoc()['username'];
$prodName = $db->query("SELECT productName FROM Products WHERE productID = $productid")->fetch_assoc()['productName'];

$price = $price * $quantity;

$body .= "Item Bought: $prodName<br>
		  Quantity: $quantity<br>
		  Seller: $sellerName<br>
		  Price: \$".round($price, 2)."<br>";
		  $subtotal += $price;

send_seller_email($productid, $sellerid, $date);

}

$totalPrice = $subtotal * 1.15;
$body .= "Total Price: \$".round($totalPrice, 2)."<br>";

$subject = "Thank you for your purchase!";

send_buyer_email($buyerEmail, $subject, $body);

}

if(isset($_POST['fromdate'])) {
	echo get_commission_since_date($_POST['fromdate']);
}
?>
