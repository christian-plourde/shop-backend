<?php
require_once("reviews.php");
require_once("product.php");

function get_user_details($uid){
$db=get_db_connection();
$result = $db->query("SELECT username,firstName,lastName,address,Country FROM Accounts WHERE Accounts.accountID = $uid")->fetch_all(MYSQLI_ASSOC);
return json_encode($result);
}

function get_user_sales($uid){
if($uid>=1){
$db = get_db_connection();
$result = $db->query("SELECT Products.* FROM Products,Accounts WHERE Accounts.accountID = Products.ownerID AND Accounts.accountID=$uid")->fetch_all(MYSQLI_ASSOC);
foreach($result as &$data){
$data['images']=json_decode(get_product_image($data['productID']));
}
return json_encode($result);}
else {
	echo "invalid input!";
}
}

function get_user_reviews($uid){
$db = get_db_connection();
$result = $db->query("SELECT Reviews.* FROM Reviews,Accounts WHERE Reviews.reviewerID = Accounts.accountID AND Accounts.accountID = $uid")->fetch_all(MYSQLI_ASSOC);
foreach($result as &$data){
$data['images']=json_decode(get_image($data['reviewID']));
}
return json_encode($result);

}

function get_user_earnings($uid){
$db = get_db_connection();
$result = $db->query("SELECT sum(price) AS sales, 0.92*sum(price) AS earnings FROM Transaction WHERE sellerid = $uid GROUP BY sellerid;")->fetch_all(MYSQLI_ASSOC);
return json_encode($result);
}

function get_site_commision(){
$db = get_db_connection();
$result = $db->query("SELECT 0.08*sum(price) AS commision FROM Transaction")->fetch_all(MYSQLI_ASSOC);
return json_encode($result);	
}

if(isset($_GET['sales'])){
	echo get_user_sales($_GET['sales']);
}
else
if(isset($_GET['reviews'])){
	echo get_user_reviews($_GET['reviews']);
}
else
if(isset($_GET['details'])){
	echo get_user_details($_GET['details']);
}
else
if(isset($_GET['earn'])){
	echo get_user_earnings($_GET['earn']);
}
else
if(isset($_GET['commision'])){
	echo get_site_commision();
}
?>