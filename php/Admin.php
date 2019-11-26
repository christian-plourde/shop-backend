<?php
require_once("Connect.php");

// gets the products that have sold the most
function get_best_sellers_by_quantity(){
    $db = get_db_connection();
    $result = $db->query("SELECT productID, sum(quantity) AS AMOUNT 
                            FROM Transaction 
                            GROUP BY productID 
                            ORDER BY AMOUNT DESC");
    $array = $result->fetch_all(MYSQLI_ASSOC);
    return json_encode($array);
}

// will only consider products that have more than 3 reviews
function get_best_sellers_by_reviews(){
    $db = get_db_connection();
    $result = $db->query("SELECT productID, Count(rating) AS ratingCount, avg(rating) as ratingAverage
                            FROM Reviews
                            GROUP BY productID
                            HAVING ratingCount > 3
                            ORDER BY ratingAverage DESC, ratingCount");
    $array = $result->fetch_all(MYSQLI_ASSOC);
    return json_encode($array);
}

?>