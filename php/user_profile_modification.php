<?php
include 'Connect.php';
$data = json_decode(file_get_contents("php://input"), TRUE);

$EMAIL = $data['email'];
$FIRST_NAME = $data['firstName'];
$LAST_NAME = $data['lastName'];
$ADDRESS = $data['address'];
$COUNTRY = $data['country'];
$USERNAME = $data['userName'];
// $OLD_USERNAME = $data['olduserName'];
$QUERY = "UPDATE Accounts SET ";
$QUERY .= "email = '$EMAIL', ";
$QUERY .= "firstName = '$FIRST_NAME', ";
$QUERY .= "lastName = '$LAST_NAME', ";
$QUERY .= "address = '$ADDRESS', ";
$QUERY .= "Country = '$COUNTRY' ";
// $QUERY .= "Country = '$COUNTRY', ";
// $QUERY .= "username = '$USERNAME'";
$QUERY .= " WHERE username = '$USERNAME';";

#Put the query into $QUERY

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
