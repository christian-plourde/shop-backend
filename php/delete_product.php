<?php
/*
  A file to delete a given product from the SQL database, from product id.
  Also provide fixing functionalities, i.e. re-entry into the table, in particular for hard-coded testing values.

// -- PRODUCT 1 FIX

  INSERT INTO
  Products (productID, quantity, ownerID, productName, descriptionText, productPrice, dimensions, color, modelName)
  VALUES
  (1, 957, 1, 'Microsoft Azure Mug', 'Stunning Microsoft Azure mug to make all your coworkers jealous.', 20.0, '4 x4 ', 'White', 'B46Y9AS0GX');

  INSERT INTO
  ProductImages (productID, image_url) values (1, 'images/azure_mug.jpg');

  INSERT INTO Tags (productID, tag) values
  (1, 'kitchen'),
  (1, 'mug'),
  (1, 'white'),
  (1, 'microsoft'),
  (1, 'azure');

//No reviews for this one

  INSERT INTO
  Transaction (productID, buyerID, sellerID, time_stamp, quantity, price)
  VALUES
  (1, 12, 75, '2019-09-03 10:53:46', 81, 20.0);

// -- PRODUCT 6 FIX

INSERT INTO
Products (productID, quantity, ownerID, productName, descriptionText, productPrice, dimensions, color, modelName)
VALUES (6, 419, 4, 'Titanium Kitchen Scale', 'Digital kitchen scale for everyday use. Titanium built to last.', 15.24, '8 x1.5 ', 'Silver', 'B06X9NQ8GX');

INSERT INTO
ProductImages (productID, image_url) values (6, 'images/kitchen_scale.png');

INSERT INTO Reviews (productID, reviewerID, rating, reviewText)
 VALUES
 (6, 100, 0, 'I esteem Titanium Kitchen Scale'),
 (6, 100, 2, 'My family love Titanium Kitchen Scale'),
 (6, 19, 3, 'My children despise Titanium Kitchen Scale');

 //No review images yet

 INSERT INTO TAGS (productID, tag)
 values
 (6, 'home'),
 (6, 'kitchen'),
 (6, 'scale'),
 (6, 'electronic'),
 (6, 'titanium'),
 (6, 'cooking');

INSERT INTO
Transaction (productID, buyerID, sellerID, time_stamp, quantity, price)
VALUES
(6, 19, 83, '2019-10-11 04:49:33', 34, 518.16),
(6, 100, 38, '2019-09-29 04:19:40', 19, 289.56);

*/
require_once("Connect.php");


function fix_deletion_of_debug_product1(){
  //id of debugging product is 1
  $product_id = 1;
  $db = get_db_connection();
  $db->query("INSERT INTO
  Products (productID, quantity, ownerID, productName, descriptionText, productPrice, dimensions, color, modelName)
  VALUES
  (1, 957, 1, 'Microsoft Azure Mug', 'Stunning Microsoft Azure mug to make all your coworkers jealous.', 20.0, '4 x4 ', 'White', 'B46Y9AS0GX'); ");
  $db->query("INSERT INTO
  ProductImages (productID, image_url) values (1, 'images/azure_mug.jpg');");
  $db->query("INSERT INTO Tags (productID, tag) values
  (1, 'kitchen'),
  (1, 'mug'),
  (1, 'white'),
  (1, 'microsoft'),
  (1, 'azure');");
  $db->query("INSERT INTO
  Transaction (productID, buyerID, sellerID, time_stamp, quantity, price)
  VALUES
  (1, 12, 75, '2019-09-03 10:53:46', 81, 1620.00);");
}

function fix_deletion_of_debug_product6(){
  //id of debugging product is 1
  $product_id = 1;
  $db = get_db_connection();
  $db->query("INSERT INTO
  Products (productID, quantity, ownerID, productName, descriptionText, productPrice, dimensions, color, modelName)
  VALUES (6, 419, 4, 'Titanium Kitchen Scale', 'Digital kitchen scale for everyday use. Titanium built to last.', 15.24, '8 x1.5 ', 'Silver', 'B06X9NQ8GX');");
  $db->query("INSERT INTO
  ProductImages (productID, image_url) values (6, 'images/kitchen_scale.png');");
  $db->query("INSERT INTO Reviews (productID, reviewerID, rating, reviewText)
   VALUES
   (6, 100, 0, 'I esteem Titanium Kitchen Scale'),
   (6, 100, 2, 'My family love Titanium Kitchen Scale'),
   (6, 19, 3, 'My children despise Titanium Kitchen Scale');");
  $db->query("INSERT INTO TAGS (productID, tag)
  values
  (6, 'home'),
  (6, 'kitchen'),
  (6, 'scale'),
  (6, 'electronic'),
  (6, 'titanium'),
  (6, 'cooking');");
  $db->query("INSERT INTO
  Transaction (productID, buyerID, sellerID, time_stamp, quantity, price)
  VALUES
  (6, 19, 83, '2019-10-11 04:49:33', 34, 518.16),
  (6, 100, 38, '2019-09-29 04:19:40', 19, 289.56);");
}

function delete_product($product_id){
  $db = get_db_connection();
  $result = $db->query("DELETE FROM Products where productID = $product_id;");
  // $result = $db->query("SELECT FROM Products where productID = $product_id;");
  return json_encode($result);
}

//uncomment this to fix deletion of products 1 or 6
fix_deletion_of_debug_product1();
return;

/*The following for debugging*/
//SQL timestamp format is the following: '1970-01-01 00:00:01'
// $data = json_decode('{"id":"6"}', TRUE);//Hard coded values
$data = json_decode(file_get_contents("php://input"), TRUE);

if (!isset($data))
{
  echo json_encode(array("Accepted" => false, "reason" => "data not set"));
  return;
}
// echo $data['id'];
// return;
$product_id = $data['id'];
$result = delete_product($product_id);

$echo_array = array("Accepted" => false,
                    "reason"=>"unsuccessful query");
if(isset($result))
{
    $echo_array["Accepted"] = true;
    $echo_array["reason"] = "";
}
echo json_encode($echo_array);
?>
