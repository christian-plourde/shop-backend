<?php
include 'Connect.php';
//What we receive is already encrypted
//Hardcoded values for debugging
// $data = array(
//   "password" => "nbhpp",
//   "username" => "dat_magoo");

$data = json_decode(file_get_contents("php://input"), TRUE);
if (!isset($data))
{
  echo array("Accepted"=>false, "reason"=>"data null");
  return;
}
//Else data is set
$USERNAME = $data['username'];
$NEW_PASSWORD = $data['password'];
#At this point, we assume the old password has been verified and encrypted.
#2. Provide the new password
#   For this, the query will have the form
# UPDATE Accounts SET password = $new_password WHERE username = $username;
$QUERY = "UPDATE Accounts SET password='$NEW_PASSWORD' WHERE username = '$USERNAME';";

$db = get_db_connection();
$echo_array = array("Accepted" => false, "reason"=>"failed query");
$db->query($QUERY);
$err = $db->error;
if($err == NULL)
{
  $echo_array["Accepted"] = true;
  $echo_array["reason"] = '';
}
echo json_encode($echo_array);
?>
