<?php
function get_db_connection() {
    $servername = "remotemysql.com";
    $username = "HQgsxOVVFA";
    $password = "QU8LU8QaqR";
    $database = "HQgsxOVVFA";
    $dbport = "3306";
    $dbh = new mysqli($servername, $username, $password, $database, $dbport);
    return $dbh;
}

?>
