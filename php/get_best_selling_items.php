<?php
require_once("Connect.php");
require_once('Admin.php');
require_once('product.php');

//for debugging - don't delete me
$data = ['num_to_return' => '3'];
// $data = json_decode(file_get_contents("php://input"), TRUE);


$echo_array = array("Accepted" => false,
                    "reason"=>"Data not set",
                    "products"=>null);
if(!isset($data))
{
  echo json_encode($echo_array);
  return;
}

//Get products (note: already encoded)
$product_id_objs = get_best_sellers_by_quantity_limit_noencode(intval($data['num_to_return']));
//Set echo array for new debug output
$echo_array = array("Accepted" => false,
                    "reason"=>"get_best_sellers_by_quantity_limit :: unsuccessful query",
                    "products"=>null);
if (!isset($product_id_objs))
{
  echo json_encode($echo_array);
  return;
}
//Else we have our list of whichever quantity of products

// echo 'product id objs<br />' . json_encode($product_id_objs) . '<br />';

//Now for each of the product ids, get me that product
$product_list = [];
foreach($product_id_objs as $index => $entry)
{
  $productID = $entry['productID'];
  $product_array = get_products_noencode($productID);
  $product_array['amount_sold'] = $entry['AMOUNT'];
  array_push($product_list, $product_array);
}
// echo 'product list:' . json_encode($product_list);

$echo_array = array("Accepted" => false,
                    "reason"=>"unsuccessful query",
                    "products"=>null);

if(isset($product_list))
{
  $echo_array["Accepted"] = true;
  $echo_array["reason"] = "";
  $echo_array["products"]= $product_list;
}
echo json_encode($echo_array, JSON_UNESCAPED_SLASHES);
?>

