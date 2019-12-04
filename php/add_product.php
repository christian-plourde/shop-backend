<?php
require_once("Connect.php");
include("product.php");



function get_account($user){
    $db = get_db_connection();
    $result = $db->query("SELECT * FROM Accounts WHERE Accounts.username = '$user'");
    // echo 'Username' . $user . $result['accountID'];
    return $result->fetch_assoc();
}

function add_product($product){
  $ownerID = get_account($product['userName'])['accountID'];
  $query = "INSERT INTO Products (quantity, ownerID, productName, descriptionText, productPrice, dimensions, color, modelName) VALUES ";
  $query .= '(';
  $query .= (intval($product['quantity']) . ', ');
  $query .= (intval($ownerID) . ', ');
  $productName = $product['productName'];
  $query .= ("'$productName', ");
  $descriptionText = $product['descriptionText'];
  $query .= ("'$descriptionText', ");
  $query .= (intval($product['productPrice']) . ', ');
  $dimensions = $product['dimensions'];
  $query .= ("'$dimensions', ");
  $color = $product['color'];
  $query .= ("'$color', ");
  $modelName = 'NULL';
  $query .= ("'$modelName');");

  $db = get_db_connection();
  $result = $db->query($query);

  $query = add_tags($productName, $descriptionText, $productID, $dimensions, $color);
  return $result;
}

function add_tags_noencode($productName, $descriptionText, $dimensions, $color){
  $productID = "SELECT productID FROM Products WHERE 
                  productName = $productName AND 
                  descriptionText = $descriptionText AND 
                  dimensions = $dimensions AND 
                  color = $color";

  $tags = explode(" ", $productName);

  $query = "INSERT INTO Tags (productID, tag) VALUES ";
  $query .= implode(", ", "($id, $tags)");

  return $query;
}

function add_product__noencode($product){\
  $ownerID = get_account($product['userName'])['accountID'];
  $query = "INSERT INTO Products (quantity, ownerID, productName, descriptionText, productPrice, dimensions, color, modelName) VALUES ";
  $query .= '(';
  $query .= (intval($product['quantity']) . ', ');
  $query .= (intval($ownerID) . ', ');
  $productName = $product['productName'];
  $query .= ("'$productName', ");
  $descriptionText = $product['descriptionText'];
  $query .= ("'$descriptionText', ");
  $query .= (intval($product['productPrice']) . ', ');
  $dimensions = $product['dimensions'];
  $query .= ("'$dimensions', ");
  $color = $product['color'];
  $query .= ("'$color', ");
  $modelName = 'NULL';
  $query .= ("'$modelName');");

  return $query;
}

$data = json_decode(file_get_contents("php://input"), TRUE);
// $data = json_decode('{"product":{
//   "quantity":"100",
//   "userName":"dat_magoo",
//   "productName":"lala",
//   "descriptionText":"lala",
//   "productPrice":"14.99",
//   "dimensions":"4x4",
//   "color":"white"}}', TRUE);//Hard coded values

if (!isset($data))
{
  echo json_encode(array("Accepted" => false, "reason" => "data not set"));
  return;
}
// $product = $data['product'];
// echo $product['quantity'];
// return;

$product = $data;
$result = add_product($product);

$echo_array = array("Accepted" => false,
                    "reason"=>"unsuccessful query");
if(isset($result))
{
    $echo_array["Accepted"] = true;
    $echo_array["reason"] = "";
}
echo json_encode($echo_array);
?>
