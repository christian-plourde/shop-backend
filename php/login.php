<?php
//A function to return a connection to th database
function get_db_connection() {
  $servername = "www.remotemysql.com";
  $username = "HQgsxOVVFA";
  $password = "QU8LU8QaqR";
  $database = "HQgsxOVVFA";
  $dbport = "3306";
  $dbh = new mysqli($servername, $username, $password, $database, $dbport);
  return $dbh;
}
//A function to return a list of all successful queries in the Accounts table for the given username and password.
function get_account($user, $pass){
    $db = get_db_connection();
    $query = "SELECT * FROM Accounts WHERE username = '$user' AND password = '$pass';";
    $result = $db->query($query);

    return json_encode ($result->fetch_assoc());
    }

//Get the data we sent over via our axios.post() in the login
$data = json_decode(file_get_contents("php://input"), TRUE);
//To make our lives easier, set variables for easy access
$username = $data['username'];
$password = $data['password'];
//We assume the results are false, by default
$echo_array = array("Accepted" => false);
if(!empty($username) && !empty($password)){
    //Access the database and check validity of arguments
    $result = get_account($username, $password);
    //Set accepted to true if the values are legit, else keep to false.
    $echo_array["Accepted"] = ($result != "null");
}
//Return the result; either {"Accepted":true} or {"Accepted":false}
echo json_encode($echo_array);
?>
