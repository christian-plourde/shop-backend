<?php
/*
  A file to get all site earnings in a given time interval
  SELECT * FROM table
  WHERE timestamp > $timestamp
*/
require_once("Connect.php");

function get_site_commission($timestamp){
  $db = get_db_connection();
  $result = $db->query("SELECT 0.08*sum(price) AS commission FROM Transaction where time_stamp > $timestamp")->fetch_all(MYSQLI_ASSOC);
  return json_encode($result);
}

function get_site_quantity($timestamp){
  $db = get_db_connection();
  $result = $db->query("SELECT sum(quantity) as quantity FROM Transaction where time_stamp > $timestamp")->fetch_all(MYSQLI_ASSOC);
  return json_encode($result);
}

/*The following for debugging*/
//SQL timestamp format is the following: '1970-01-01 00:00:01'
// $data = json_decode('{"from_date":"\'2019-09-03 00:00:00\'"}', TRUE);
$data = json_decode(file_get_contents("php://input"), TRUE);
// echo $data["from_date"];
// return;


if (!isset($data))
{
  echo json_encode(array("Accepted" => false, "reason" => "data not set"));
  return;
}

$from_date = $data['from_date'];

// $result = get_site_commission($from_date);
$results_array = array();
array_push($results_array, get_site_commission($from_date));
array_push($results_array, get_site_quantity($from_date));


$echo_array = array("Accepted" => false,
                    "reason"=>"unsuccessful query",
                    "results" => []);
// if(isset($result))
// {
//     $echo_array["Accepted"] = true;
//     $echo_array["reason"] = "";
//     $commission_obj = json_decode($result)[0];
//     $commission_obj["quantity"] = 0;
//     $echo_array["commission"] = $commission_obj;
// }

if(isset($results_array) and isset($results_array[0]) and isset($results_array[1]))
{
    $echo_array["Accepted"] = true;
    $echo_array["reason"] = "";
    //Commission
    array_push($echo_array["results"], json_decode($results_array[0])[0]);
    //Quantity
    array_push($echo_array["results"], json_decode($results_array[1])[0]);
}
echo json_encode($echo_array);
?>
