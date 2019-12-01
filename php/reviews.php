<?php
require_once("Connect.php");



function get_review_average($id){
$db = get_db_connection();
$result = $db->query("SELECT Products.productName,Products.productID, avg(rating) as rating, count(*) as count from Reviews,Products where Reviews.productID = Products.productID and Reviews.productID=$id and Products.productID=$id group by productID");
return json_encode ($result->fetch_assoc());
}

function get_review($id){
$db = get_db_connection();
$result = $db->query("SELECT Reviews.* FROM Reviews where Reviews.productID=$id");
$array = $result->fetch_all(MYSQLI_ASSOC);
foreach($array as &$data){
$data["images"] = json_decode(get_image($data['reviewID']),true);
}
return json_encode($array);
}

function get_image($rid){
$db = get_db_connection();
$result = $db->query("SELECT ReviewImages.image_url FROM Reviews,ReviewImages where ReviewImages.reviewID = Reviews.reviewID AND Reviews.reviewID=$rid");
$array = array();
foreach($result as $key=>$value){
array_push($array,$value['image_url']);
}
return json_encode($array);
}

function add_review($pid,$rid,$rating,$reviewerText){
$db = get_db_connection();
$result = $db->query("INSERT INTO Reviews(productID,reviewerID,rating,reviewerText) VALUES ($pid,$rid,'$rating','$reviewerText')");	
}

// this is separate from add_review() because we need to access ReviewImages
function add_review_image($image){
    $db = get_db_connection();
    $result = $db->query("INSERT INTO ReviewImages(image_url) VALUES ($image)");
}

// deletes the column from ReviewImages
function remove_review_image($rid){
    $db = get_db_connection();
    $result = $db->query("DELETE FROM ReviewImages WHERE reviewID=$rid");	
}

function remove_review($rid){
$db = get_db_connection();
$result = $db->query("DELETE FROM Reviews WHERE reviewID=$rid");	
}

if(isset($_GET['averagereview'])){
echo get_review_average($_GET['averagereview']);
}

if(isset($_GET['review'])){
echo get_review($_GET['review']);
}

if(isset($_GET['image'])){
echo get_image($_GET['image']);
}

if(isset($_POST['delete'])){
remove_review($_POST['delete']);
}

if(isset($_POST['pid']) && isset($_POST['rid']) && isset($_POST['rating']) && isset($_POST['reviewerText'])){
add_review($_POST['pid'],$_POST['rid'],$_POST['rating'],$_POST['reviewerText']);
}

if(isset($_POST['addreviewimage'])){
    add_review_image($_POST['addreviewimage']);
}

if(isset($_POST['deletereviewimage'])){
    remove_review_image($_POST['deletereviewimage']);
}
?>

