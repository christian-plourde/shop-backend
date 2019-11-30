<?php
/*
  A file to get all site earnings in a given time interval, should return a fixed numerical value with two decimal places. i.e. 10.00
Note: we have a BETWEEN keyword, if we want
  SELECT * FROM table
  WHERE timestamp BETWEEN '2012-05-05 00:00:00' AND '2012-05-05 23:59:59'
*/
require_once("User.php");
require_once("Connect.php");

/*The following for debugging*/
//SQL timestamp format is the following: '1970-01-01 00:00:01'
$data = json_decode('{"from_date":"today"}', TRUE);
// $data = json_decode(file_get_contents("php://input"), TRUE);

if (!isset($data))
{
  echo json_encode(array("Accepted" => false, "reason" => "data not set"));
  return;
}

$from_date = $data['from_date'];

// $result = get_products_sold_by_user($username);
$result = get_commission_since_date($from_date);

$echo_array = array("Accepted" => false,
                    "reason"=>"unsuccessful query",
                    "commission" => NULL);
if(isset($result))
{
    $echo_array["Accepted"] = true;
    $echo_array["reason"] = "";
    $echo_array["commission"] = $result;
}
echo json_encode($echo_array);
?>
