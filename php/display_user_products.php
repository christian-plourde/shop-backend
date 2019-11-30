<?php
require_once("Connect.php");
require_once("User.php");

/*The following for debugging*/
// $data = json_decode('{"username":"wniaves6"}', TRUE);
$data = json_decode(file_get_contents("php://input"), TRUE);

if (!isset($data))
{
  echo "Data not set";
  echo json_encode(array("Accepted" => false, "reason"=>"data not set"));
  return;
}

$username = $data['username'];

$result = get_products_sold_by_user($username);

$echo_array = array("Accepted" => false, "products" => NULL, "reason"=>"query failed");
if(isset($result))
{
    $products = array();

  // output data of each row
    /*while($row = $result->fetch_assoc()) {
        // echo "id: " . $row['productID'] . "<br />";
        array_push($products, $row);
    }*/

    foreach($result as $prod) {
      array_push($products, $prod[0]);
    }

    // var_dump($products);//Causes response not to be accepted :/

    $echo_array["Accepted"] = true;
    $echo_array["reason"] = "";
    $echo_array["products"] = $products;
}
echo json_encode($echo_array);
?>
