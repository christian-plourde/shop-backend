<?php
include 'Connect.php';
// include 'Encryption.php';
$SHIFT = 1;

#What we receive is already encrypted
// $data = array(
//   #Input field => value
//   "password" => "ham",
//   "username" => "Abra");

$data = json_decode(file_get_contents("php://input"), TRUE);
#To reset your password, we have the following:
#1. provide your old password, to be verified
#   For this, the query will have the form
# SELECT * FROM Accounts where username = $username and password = $password
$USERNAME = $data['username'];
// $PASSWORD = encrypt($data['password'], $SHIFT);
$PASSWORD = $data['password'];#Uncomment for final
$QUERY = "SELECT * FROM Accounts WHERE username = '$USERNAME' AND password = '$PASSWORD';";
// echo $QUERY;
$db = get_db_connection();
$echo_array = array("Accepted" => false);

$result = $db->query($QUERY);
if ($result->num_rows != 0)#If there are results, then the password is valid
{
  $echo_array["Accepted"] = true;
}
#else, if there were no results, the password is invalid
echo json_encode($echo_array);

?>
