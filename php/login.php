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

if(!empty($_POST["username"]) && !empty($_POST[password])){
    $user = $_POST["username"];
    $pass = $_POST["password"];
    $result = get_account($user, $pass);

    if($result == NULL){
        header("Location: https://shop-354.herokuapp.com/login");
    } else {
        header("Location: https://shop-354.herokuapp.com/");
    }
}
?>