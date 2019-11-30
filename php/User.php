<?php
require_once("reviews.php");
require_once("product.php");

function get_user_details($uid){
$db=get_db_connection();
$result = $db->query("SELECT username,firstName,lastName,address,Country FROM Accounts WHERE Accounts.accountID = $uid")->fetch_all(MYSQLI_ASSOC);
return json_encode($result);
}

// //Get a list of items currently being sold by the used
// function get_products_sold_by_user($uid){
// if($uid>=1){
// $db = get_db_connection();
// $result = $db->query("SELECT Products.* FROM Products,Accounts WHERE Accounts.accountID = Products.ownerID AND Accounts.accountID=$uid")->fetch_all(MYSQLI_ASSOC);
// foreach($result as &$data){
// $data['images']=json_decode(get_product_image($data['productID']));
// }
// return json_encode($result);}
// else {
// 	echo "invalid input!";
// }
// }

//Get a list of items currently being sold by the used
/*function get_products_sold_by_user($username){
	if(isset($username)){
	$db = get_db_connection();
	$query = "SELECT Products.* FROM Products,Accounts WHERE Accounts.accountID = Products.ownerID AND Accounts.accountID=(SELECT Accounts.accountID from Accounts where Accounts.username='$username');";
	$result = $db->query($query);
	return $result;
	}
	else {
		echo "invalid input!";
	}
}*/

function get_products_sold_by_user($username) {
	if(isset($username)) {
		$db = get_db_connection();
		//get account id of username
		$userID = $db->query("SELECT accountID FROM Accounts WHERE username = '$username'")->fetch_assoc()['accountID'];
		// get ids of products sold by username
		$result = $db->query("SELECT productID FROM Products WHERE ownerID = $userID");

		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()['productID']) {
				$ids[] = $row;
			}
			$products = array();
			// for each id, get product info
			foreach($ids as $id) {
				$products[] = get_product($id);
			}
			return $products;
		} else {
			echo "No products found";
		}
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

// function get_user_reviews($username){
// $db = get_db_connection();
// $result = $db->query("SELECT Reviews.* FROM Reviews,Accounts WHERE Reviews.reviewerID = Accounts.accountID AND Accounts.accountID = $uid")->fetch_all(MYSQLI_ASSOC);
// foreach($result as &$data){
// $data['images']=json_decode(get_image($data['reviewID']));
// }
// return json_encode($result);
// }

// function get_user_earnings($uid){
// $db = get_db_connection();
// $result = $db->query("SELECT sum(price) AS sales, 0.92*sum(price) AS earnings FROM Transaction WHERE sellerid = $uid GROUP BY sellerid;")->fetch_all(MYSQLI_ASSOC);
// return json_encode($result);
// }

function get_user_earnings($username){
$db = get_db_connection();
$result = $db->query("SELECT sum(price) AS sales, 0.92*sum(price) AS earnings FROM Transaction WHERE sellerid = (select accountID from Accounts where username='$username') GROUP BY sellerid;")->fetch_all(MYSQLI_ASSOC);
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
