<?php
require_once 'Connect.php';
require_once 'User.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// require 'vendor/autoload.php';

// USE BELOW REQUIRE WHEN UPLOADING TO GIT
require '../vendor/autoload.php'

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


$data = array("username"=>"burnsj056");//Spam Jacques
// $data = json_decode(file_get_contents("php://input"), TRUE);

if (!isset($data))
{
  echo json_encode(array("Accepted" => false, "reason" => "data not set"));
  return;
}
$username = $data['username'];

$db = get_db_connection();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$result = $db->query("SELECT email FROM Accounts WHERE username = '$username'")->fetch_assoc()['email'];

$echo_array = array("Accepted" => false, "reason" => "No results found for email");
if(isset($result))
{
  $echo_array["Accepted"] = true; $echo_array["reason"] = '';
  // the message
  $msg = "First line of text\nSecond line of text";

  // use wordwrap() if lines are longer than 70 characters
  $msg = wordwrap($msg,70);

  // send email
  send_mail($result,"My subject",$msg);
}
echo json_encode($echo_array);
?>
