<?php
require_once("Connect.php");

function get_ads(){
    $db = get_db_connection();
    $result = $db->query("SELECT url FROM Ads");
    return $result;
}

$result = get_ads();

if($result != "null")
{
    $rows = array();
    while($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
    }

    echo stripslashes(json_encode($rows));
}

?>
