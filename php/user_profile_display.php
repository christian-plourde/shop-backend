<?php
require_once("Connect.php");

function get_account($user){
    $db = get_db_connection();
    $result = $db->query("SELECT * FROM Accounts WHERE Accounts.username = '$user'");
    return json_encode ($result->fetch_assoc());
}

$data = json_decode(file_get_contents("php://input"), TRUE);
if (!isset($data))
{
  echo "Data not set";
  return array("Accepted" => false);
}

$username = $data['username'];
$result = get_account($username);

$echo_array = array("Accepted" => false, "email" => "", "firstName" => "", "lastName" => "", "country" => "", "address" => "", "isAdmin" => false);

if($result != "null")
{
    $echo_array["Accepted"] = true;
    $echo_array["email"] = json_decode($result, TRUE)["email"];
    $echo_array["firstName"] = json_decode($result, TRUE)["firstName"];
    $echo_array["lastName"] = json_decode($result, TRUE)["lastName"];
    $echo_array["country"] = json_decode($result, TRUE)["Country"];
    $echo_array["address"] = json_decode($result, TRUE)["address"];
    $echo_array["isAdmin"] = ((intval(json_decode($result, TRUE)["isAdmin"]) == 1));
}

echo json_encode($echo_array);

?>
