<?php
require_once("Connect.php");
require_once("User.php");
$postdata = json_encode(file_get_contents('php://input'));



function get_review_average($id){
$db = get_db_connection();
$result = $db->query("SELECT Products.productName,Products.productID, avg(rating) as rating, count(*) as count from Reviews,Products where Reviews.productID = Products.productID and Reviews.productID=$id and Products.productID=$id group by productID");
return json_encode ($result->fetch_assoc());
}



function get_review($id){
$db = get_db_connection();
$result = $db->query("SELECT * FROM Reviews")->fetch_all(MYSQLI_ASSOC);
$imgsarray = json_decode(get_image($id));
$resultsarray = array();
$imagestoaddarray = array();

foreach($result as $data){
if($data['productID']==$id){
foreach ($imgsarray as $imgs) {
if($imgs['reviewID']==$data['reviewID']){
array_push($resultsarray,$imgs['image_url']);
}


}

$item = $data;
$item["images"] = $resultsarray;
array_push($imagestoaddarray,$item);
}

}
return json_encode($imagestoaddarray);
}

function get_image($rid){
$db = get_db_connection();
$result = $db->query("SELECT * FROM ReviewImages")->fetch_all(MYSQLI_ASSOC);
$array = array();
foreach($result as $data){
if($data['reviewID']==$rid){
array_push($array,$data['image_url']);
}
}
return json_encode($array);
}



function add_review($pid,$rid,$rating,$reviewerText){
$db = get_db_connection();
$result = $db->query("INSERT INTO Reviews(productID,reviewerID,rating,reviewText) VALUES ($pid,$rid,$rating,'$reviewerText')");
$err = $db->error;
$echo_array = array("Accepted" => false, "Reason" => ""); 
if($err == NULL)
{
   $echo_array["Accepted"] = true;
}
else {
  $echo_array["Reason"] = $err;
}
echo json_encode($echo_array);	
}

function remove_review($rid){
$db = get_db_connection();
$result = $db->query("DELETE FROM Reviews WHERE reviewID=$rid");
$err = $db->error;
$echo_array = array("Accepted" => false, "Reason" => ""); 
if($err == NULL)
{
   $echo_array["Accepted"] = true;
}
else {
  $echo_array["Reason"] = $err;
}
echo json_encode($echo_array);		
}

if(isset($_GET['averagereview'])){
echo get_review_average($_GET['averagereview']);
}
else
if(isset($_GET['review'])){
echo get_review($_GET['review']);
echo get_user_details(3);
}
else
if(isset($postdata['delete'])){
remove_review($postdata['delete']);
}
else
if(isset($postdata['rid'])){
$result = get_user_details($postdata['rid']);
add_review($postdata['pid'],$result['accountID'],$postdata['rating'],$postdata['text']);
}
?>
