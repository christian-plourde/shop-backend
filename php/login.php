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

function get_account($user, $pass){
    $db = get_db_connection();
    $result = $db->query("SELECT * FROM Accounts WHERE Accounts.username = '$user' AND Accounts.password = '$pass'");
    return json_encode ($result->fetch_assoc());
    }

$data = json_decode(file_get_contents("php://input"), TRUE);
$username = $data['username'];
$password = $data['password'];


if(!empty($username) && !empty($password)){
    $result = get_account($username, $password);

    $echo_array = array("Accepted" => false);

    if($result == "null"){

        $echo_array["Accepted"] = false;
        echo json_encode($echo_array);
         
    } else {
        
        $echo_array["Accepted"] = true;
        echo json_encode($echo_array);
    }
}
?>
