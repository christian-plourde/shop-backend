<?php
require_once("Connect.php");
require_once('product.php');

$products = get_products_noencode(-1);
$echo_array = array("Accepted" => false,
                    "reason"=>"unsuccessful query",
                    "products"=>null);

if(isset($products))
{
    $echo_array["Accepted"] = true;
    $echo_array["reason"] = "";
    $echo_array["products"]= $products;
}
// echo json_encode($echo_array, JSON_UNESCAPED_SLASHES);
echo json_encode($echo_array);
?>
