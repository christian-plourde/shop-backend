<?php
    // define('DB_NAME', 'lrc353_1');
    // define('DB_USER', 'lrc353_1');
    // define('DB_PASSWORD', 'vamp353C');
    // define('DB_HOST', 'lrc353.encs.concordia.ca');
    //
    // $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    //
    // if(mysqli_connect_errno()) {
    //     die('Could not connect : ' . mysqli_connect_error());
    // }

    function get_db_connection() {
    $servername = "remotemysql.com";
    $username = "HQgsxOVVFA";
    $password = "QU8LU8QaqR";
    $database = "HQgsxOVVFA";
    $dbport = "3306";
    $dbh = new mysqli($servername, $username, $password, $database, $dbport);
    return $dbh;
}
    // $db_selected = get_db_connection();
?>
