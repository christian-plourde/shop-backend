<?php
require_once("Connect.php");

$db = get_db_connection();
$query = "SELECT * FROM Products;";
$result = $db->query($query)->fetch_all(MYSQLI_ASSOC);

$echo_array = array("Accepted" => false,
                    "reason"=>"unsuccessful query",
                    "products"=>null);
if(isset($result))
{
    $echo_array["Accepted"] = true;
    $echo_array["reason"] = "";
    $echo_array["products"]=array();
    foreach($result as $key => $value)
    {
      array_push($echo_array["products"], $value);
    }
}
echo json_encode($echo_array);
?>
