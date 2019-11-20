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

//Gets Products; If input is -1, will return all products. If input is a number greater than zero, it will return a products with
//the same product id as the input
function get_products($id){
$db = get_db_connection();
if($id==-1){
$result = $db->query("SELECT Products.* FROM Products");
$array = $result->fetch_all(MYSQLI_ASSOC);
foreach($array as &$data){
$data["tags"] = json_decode(get_tag($data['productID']));
}
return json_encode($array);	
}
elseif($id>0){
$result = $db->query("SELECT Products.* FROM Products WHERE productID=$id");
$array = $result->fetch_assoc();
$data["tags"] = json_decode(get_tag($data['productID']));
return json_encode($array);		
}

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

if(isset($_GET['product'])){
echo get_products($_GET['product']);
}

?>