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
 (204, 6, 100, 0, 'I esteem Titanium Kitchen Scale'),
 (205, 6, 100, 2, 'My family love Titanium Kitchen Scale'),
 (206, 6, 19, 3, 'My children despise Titanium Kitchen Scale');

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

function fix_deletion_of_review137()
{
  $db = get_db_connection();
  $db->query("INSERT INTO Reviews (reviewID, productID, reviewerID, rating, reviewText) VALUES (137, 13, 6, 0, 'My daughters hate Shrek T-Shirt');");
   $db->query("INSERT INTO ReviewImages (reviewID, image_url)
    VALUES
    (137, 'review_images/omgilovemyshrektshirt.jpg');");
}

function fix_deletion_of_review190()
{
  $db = get_db_connection();
  $db->query("INSERT INTO Reviews (reviewID, productID, reviewerID, rating, reviewText)
   VALUES
   (190, 13, 6, 3, 'My children despise Shrek T-Shirt');");
}

function fix_deletion_of_review203()
{
  $db = get_db_connection();
  $db->query("INSERT INTO Reviews (reviewID, productID, reviewerID, rating, reviewText)
   VALUES
   (203, 13, 169, 5, 'This is the best T-Shirt ever!');");
}

function fix_deletion_of_reviews_for_product13(){
  fix_deletion_of_review137();
  fix_deletion_of_review190();
  fix_deletion_of_review203();
}

function delete_review($review_id){
  $db = get_db_connection();
  $result = $db->query("DELETE FROM Reviews where reviewID = $review_id;");
  // $result = $db->query("Select FROM Reviews where reviewID = $review_id;");
  return json_encode($result);
}

//uncomment this to fix deletion of review 137
// fix_deletion_of_review137();
// return;

/*The following for debugging*/
//SQL timestamp format is the following: '1970-01-01 00:00:01'
// $data = json_decode('{"id":"137"}', TRUE);//Hard coded values
$data = json_decode(file_get_contents("php://input"), TRUE);

if (!isset($data))
{
  echo json_encode(array("Accepted" => false, "reason" => "data not set"));
  return;
}
// echo $data['id'];
// return;
$review_id = $data['id'];
$result = delete_review($review_id);

$echo_array = array("Accepted" => false,
                    "reason"=>"unsuccessful query");
if(isset($result))
{
    $echo_array["Accepted"] = true;
    $echo_array["reason"] = "";
}
echo json_encode($echo_array);
?>
