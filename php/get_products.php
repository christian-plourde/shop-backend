<?php
require_once("Connect.php");

$db = get_db_connection();
$products_query = "SELECT * FROM Products;";
$products_result = $db->query($products_query)->fetch_all(MYSQLI_ASSOC);


$echo_array = array("Accepted" => false,
                    "reason"=>"unsuccessful query",
                    "products"=>null);
if(isset($products_result))
{
    $echo_array["Accepted"] = true;
    $echo_array["reason"] = "Stopped at products";
    $echo_array["products"]=array();

    //For every product, we can get that product's id.
    foreach($products_result as $index => $product)
    {
      $product_id = $product['productID'];

      $images_query = "Select image_url from ProductImages where productID = $product_id;";
      $picture = $db->query($images_query)->fetch_assoc()['image_url'];
      // echo 'picture ' . $picture . '<br />';

      $tags_query = "Select tag from Tags where productID = $product_id;";
      $tags_result = $db->query($tags_query)->fetch_all(MYSQLI_ASSOC);
      $tags = array();
      foreach($tags_result as $t_index => $tag)
      {

        // echo 'product id ' . $product_id;
        // echo  ' tag ' . $tag['tag'] . '<br />';
        array_push($tags, $tag['tag']);
      }

      $tmp_product = (array)$product;
      $tmp_product['picture'] = './ressources/img/' . $picture;
      $tmp_product['tags'] = $tags;
      $tmp_product = (object)$tmp_product;

      array_push($echo_array["products"], $tmp_product);
      // echo json_encode($echo_array);
      // return;
    }

}
echo json_encode($echo_array, JSON_UNESCAPED_SLASHES);
?>
