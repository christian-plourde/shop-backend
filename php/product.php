<?php
require_once("Connect.php");

//Gets Products; If input is -1, will return all products. If input is a number greater than zero, it will return a products with
//the same product id as the input
function get_products($id){
set_time_limit(300);
$db = get_db_connection();
if($id==-1){
$result = $db->query("SELECT Products.* FROM Products");
$array = $result->fetch_all(MYSQLI_ASSOC);
foreach($array as &$data){
$data["tags"] = json_decode(get_tag($data['productID']));
$data["images"] = json_decode(get_product_image($data['productID']));
}
return json_encode($array);	
}
elseif($id>0){
$result = $db->query("SELECT Products.* FROM Products WHERE productID=$id");
$array = $result->fetch_assoc();
$array["tags"] = json_decode(get_tag($id));
$array["images"] = json_decode(get_product_image($id));
return json_encode($array);		
}

}

function get_product_image($pid){
$db = get_db_connection();
$result = $db->query("SELECT image_url FROM ProductImages,Products WHERE Products.productID = ProductImages.productID AND Products.productID = $pid");
$array = array();
foreach($result as $key=>$value){
array_push($array,$value['image_url']);
}
return json_encode($array);	
}

function get_tag($id){
$db = get_db_connection();
$result = ($db->query("SELECT tag FROM Tags WHERE Tags.productID = $id"))->fetch_all(MYSQLI_ASSOC);
$array = array();
foreach($result as $key=>$value){
array_push($array,$value['tag']);
}
return json_encode($array);
}

function add_product($quantity,$ownerID,$productName,$descriptionText,$productPrice,$dimensions,$color,$modelname){
$db = get_db_connection();
$result = $db->query("INSERT INTO Products(quantity,ownerID,productName,descriptionText,productPrice,dimensions,color,modelname)VALUES($quantity,$ownerID,$productName,$descriptionText,$productPrice,$dimensions,$color,$modelname)");
}

function remove_product($id){
$db = get_db_connection();
$result = $db->query("DELETE FROM Products WHERE productID=$id");
}

if(isset($_GET['product'])){
echo get_products($_GET['product']);
}
else
if(isset($_POST['quantity']) && isset($_POST['ownerID']) && isset($_POST['productName']) && isset($_POST['descriptionText']) && isset($_POST['productPrice']) && isset($_POST['dimensions']) && isset($_POST['color']) && isset($_POST['modelname'])){
add_product($_POST['quantity'],$_POST['ownerID'],$_POST['productName'],$_POST['descriptionText'],$_POST['productPrice'],$_POST['dimensions'],$_POST['color'],$_POST['modelname']);
}
else
if(isset($_POST['remove'])){
remove_product($_POST['remove']);
}

?>
