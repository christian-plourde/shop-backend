<?php
include 'Connect.php';
// include 'Encryption.php';
$SHIFT = 1;

// #What we receive is already encrypted
// $data = array(
//   #Input field => value
//   "new_password" => "cadabra",
//   "username" => "Abra");

$data = json_decode(file_get_contents("php://input"), TRUE);
#At this point, we assume the old password has been verified.
#2. Provide the new password
#   For this, the query will have the form
# UPDATE Accounts SET password = $new_password WHERE username = $username;
$USERNAME = $data['username'];
// $NEW_PASSWORD = encrypt($data['new_password'], $SHIFT);
$NEW_PASSWORD = $data['password'];
$QUERY = "UPDATE Accounts SET password='$NEW_PASSWORD' WHERE username = '$USERNAME';";
// echo $QUERY;
// return;
$db = get_db_connection();
$echo_array = array("Accepted" => false);
$db->query($QUERY);
$err = $db->error;
if($err == NULL)
{
  $echo_array["Accepted"] = true;
}
echo json_encode($echo_array);
?>
