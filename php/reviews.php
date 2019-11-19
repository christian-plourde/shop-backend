<?php
function get_db_connection() {
$servername = "www.remotemysql.com";
$username = "HQgsxOVVFA";
$password = "QU8LU8QaqR";
$database = "HQgsxOVVFA";
$dbport = "3306";
$dbh = new mysqli($servername, $username, $password, $database, $dbport);
return $dbh;
}



function get_review_average($id){
$db = get_db_connection();
$result = $db->query("SELECT Products.productName,Products.productID, avg(rating) as rating, count(*) as count from Reviews,Products where Reviews.productID = Products.productID and Reviews.productID=$id and Products.productID=$id group by productID");
return json_encode ($result->fetch_assoc());
}

function get_review($id){
$db = get_db_connection();
$result = $db->query("SELECT Reviews.* FROM Reviews,ReviewImages where Reviews.productID=$id"); 
return json_encode($result->fetch_all(MYSQLI_ASSOC),JSON_PRETTY_PRINT,2);
}

function get_image($id){
$db = get_db_connection();
$result = $db->query("SELECT ReviewImages.image_url FROM Reviews,ReviewImages where ReviewImages.reviewID = Reviews.reviewID AND Reviews.productID=$id");
return json_encode($result->fetch_all(MYSQLI_ASSOC),JSON_PRETTY_PRINT,2);
}

function get_products(){
$db = get_db_connection();
$result = $db->query("SELECT Products.* FROM Products");
$array = $result->fetch_all(MYSQLI_ASSOC);
foreach($array as &$data){
$data["tags"] = json_decode(get_tag($data['productID']));
}
return json_encode($array);	
}


function get_tag($id){
$db = get_db_connection();
$result = ($db->query("SELECT tag FROM Tags where Tags.productID = $id"))->fetch_all(MYSQLI_ASSOC);
$array = array();
foreach($result as $key=>$value){
array_push($array,$value['tag']);
}
return json_encode($array);
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

if(isset($_GET['product'])){
echo get_products();
	 
}
?>

