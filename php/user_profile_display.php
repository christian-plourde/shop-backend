<?php
function get_db_connection() {
$servername = "www.remotemysql.com";
$username = "HQgsxOVVFA";
$password = "QU8LU8QaqR";
$database = "HQgsxOVVFA";
$dbport = "3306";
$dbh = new mysqli($servername, $username, $password, $database, $dbport);
return $dbh;
}

function get_account($user){
    $db = get_db_connection();
    $result = $db->query("SELECT * FROM Accounts WHERE Accounts.username = '$user'");
    return json_encode ($result->fetch_assoc());
    }

$data = json_decode(file_get_contents("php://input"), TRUE);
$username = $data['username'];

$result = get_account($username);

$echo_array = array("Accepted" => false, "email" => "", "firstName" => "", "lastName" => "", "country" => "", "address" => "", "isAdmin" => false);

if($result == "null")
{
    $echo_array["Accepted"] = false;
}

else
{
    $echo_array["Accepted"] = true;
    $echo_array["email"] = json_decode($result, TRUE)["email"];
    $echo_array["firstName"] = json_decode($result, TRUE)["firstName"];
    $echo_array["lastName"] = json_decode($result, TRUE)["lastName"];
    $echo_array["country"] = json_decode($result, TRUE)["Country"];
    $echo_array["address"] = json_decode($result, TRUE)["address"];
    if(json_decode($result, TRUE)["isAdmin"] === 1)
    	$echo_array["isAdmin"] = true;
    else
    	$echo_array["isAdmin"] = false;
}

echo json_encode($echo_array);

?>
