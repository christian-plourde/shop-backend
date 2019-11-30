<?php
require_once 'Connect.php';
require_once 'User.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// require 'vendor/autoload.php';

// USE BELOW REQUIRE WHEN UPLOADING TO GIT
require '../vendor/autoload.php';


function send_mail($email, $subject, $message) {
	//var_dump("in send_mail");
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
		//var_dump("server settings");
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
		//var_dump("receipent");
        $mail->setFrom('noreply@shop354.com', 'SHOP 354');
        $mail->addAddress($email);

		//Content
		//var_dump("content");
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

		//var_dump($mail);

        $mail->send();
		// $result["msg"] = "GOOD";
		// echo "good";
		//var_dump("mail sent");
        echo json_encode(["Accepted"=>true, "reason"=>""]);
    } catch (Exception $e) {
		// $result["msg"] = "error $mail->ErrorInfo";
		//var_dump("mail failed");
		//var_dump($mail->ErrorInfo);
		echo json_encode(["Accepted"=>false, "reason"=>"error $mail->ErrorInfo"]);
	}
	return;
	//var_dump("out send_mail");
}


function generate_password() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

//$data = array("email"=>"burnsj056@gmail.com");//Spam Jacques
 $data = json_decode(file_get_contents("php://input"), TRUE);

if (!isset($data))
{
  echo json_encode(array("Accepted" => false, "reason" => "data not set"));
  return;
}

$email = $data['email'];

$db = get_db_connection();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$result = $db->query("SELECT email FROM Accounts WHERE email = '$email'")->fetch_assoc()['email'];


$echo_array = array("Accepted" => false, "reason" => "No results found for email");
if(isset($result))
{
  $echo_array["Accepted"] = true; $echo_array["reason"] = '';
  // the message
  $msg = "First line of text\nSecond line of text";

  // generate new password
  $newPass = generate_password();

  // encrypt pass
  $arr = str_split($newPass);
  //var_dump($arr);
  $encryptPass = "";
  foreach ($arr as $char) {
    $encryptPass .= chr(ord($char) + 1);
  }

  // update password for user
  $db->query("UPDATE Accounts SET password = '$encryptPass' WHERE email = '$email'");

  $msg = "Hello, <br>
         You requested to change your password, below is your new password. <br>
         Password: $newPass";

  // use wordwrap() if lines are longer than 70 characters
  $msg = wordwrap($msg,70);

  // send email
  send_mail($result,"Forgot Password",$msg);
	//Return from send_mail
}
echo json_encode($echo_array);
?>
