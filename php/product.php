<?php
require_once ("Connect.php");
//Gets Products; If input is -1, will return all products. If input is a number greater than zero, it will return a products with
//the same product id as the input
function get_products($id) {
    $db = get_db_connection();
    if ($id == - 1) {
        $productimgs = $db->query("SELECT Products.productID AS pid, image_url FROM ProductImages,Products WHERE Products.productID = ProductImages.productID")->fetch_all(MYSQLI_ASSOC);
        $producttags = $db->query("SELECT productID, tag FROM Tags")->fetch_all(MYSQLI_ASSOC);
        $result = $db->query("SELECT Products.* FROM Products");
        $array = $result->fetch_all(MYSQLI_ASSOC);
        foreach ($array as & $data) {
            $tagsarray = array();
            $productsarray = array();
            foreach ($producttags as $tags) {
                if ($tags['productID'] == $data['productID']) {
                    array_push($tagsarray, $tags['tag']);
                }
            }
            foreach ($productimgs as $images) {
                if ($images['pid'] == $data['productID']) {
                    array_push($productsarray, './ressources/img/' . $images['image_url']);
                }
            }
            $data['tags'] = $tagsarray;
            $data['images'] = $productsarray;
        }
        return json_encode($array);
    } elseif ($id > 0) {
        $result = $db->query("SELECT Products.* FROM Products WHERE productID=$id");
        if(mysqli_num_rows($result)==1){
        $array = $result->fetch_assoc();
        $array["tags"] = get_tag($array['productID']);
        $array["images"] = get_product_image($array['productID']);
        return json_encode($array);}
        else{
        	echo "Item not found!";
        }
    }
}

function get_products_noencode($id) {
    $db = get_db_connection();
    if ($id == - 1) {
        $productimgs = $db->query("SELECT Products.productID AS pid, image_url FROM ProductImages,Products WHERE Products.productID = ProductImages.productID")->fetch_all(MYSQLI_ASSOC);
        $producttags = $db->query("SELECT productID, tag FROM Tags")->fetch_all(MYSQLI_ASSOC);
        $result = $db->query("SELECT Products.* FROM Products");
        $array = $result->fetch_all(MYSQLI_ASSOC);
        foreach ($array as & $data) {
            $tagsarray = array();
            $productsarray = array();
            foreach ($producttags as $tags) {
                if ($tags['productID'] == $data['productID']) {
                    array_push($tagsarray, $tags['tag']);
                }
            }
            foreach ($productimgs as $images) {
                if ($images['pid'] == $data['productID']) {
                    array_push($productsarray, './ressources/img/' . $images['image_url']);
                }
            }
            $data['tags'] = $tagsarray;
            $data['picture'] = $productsarray[0];
        }
        return $array;
    } elseif ($id > 0) {
        $result = $db->query("SELECT Products.* FROM Products WHERE productID=$id");
        if(mysqli_num_rows($result)==1){
        $array = $result->fetch_assoc();
        $array["tags"] = get_tag($array['productID']);
        $array["images"] = get_product_image($array['productID']);
        return $array;}
        else{
        	echo "Item not found!";
        }
    }
}

function get_product($id) {
    if($id < 1 && !is_int($id)) {
        throw new Exception("ID passed is invalid");
    }

    $dbh = get_db_connection();

    if(mysqli_connect_errno()) {
        throw new Exception("Failed to get database connection at line ".__LINE__);
    }

    $sql = "select * from Products where productID = ?";
    # prepare the sql statement, die if sql statement is bad
    if (! $sth = $dbh->prepare($sql)){
        throw new Exception ("Failed to prepare at line ".__LINE__.' '.$dbh->error);
    }
    # bind the input parameters, die if not coded correctly
    if (! $sth->bind_param('i', $id)) {
        throw new Exception ("Failed to bind parameters at line ".__LINE__);//.' '.$dbh->error);
    }
    # send request to database, die if error
    if (! $sth->execute()) {
        throw new Exception ("Failed to execute at line ".__LINE__);//.' '.$dbh->error);
    }

    // get results, check a product was found before moving on
    $result = $sth->get_result();
    if($result-> num_rows === 0){
        throw new Exception("No Products Found");
    }

    // get the returned product in an assoc array
    $product = $result->fetch_all(MYSQLI_ASSOC);

    $sth->close();

    $sql = "select image_url from ProductImages where productID = ?";
    # prepare the sql statement, die if sql statement is bad
    if (! $sth = $dbh->prepare($sql)){
        throw new Exception ("Failed to prepare at line ".__LINE__.' '.$dbh->error);
    }
    # bind the input parameters, die if not coded correctly
    if (! $sth->bind_param('i', $id)) {
        throw new Exception ("Failed to bind parameters at line ".__LINE__);//.' '.$dbh->error);
    }
    # bind the output parameters, die if not coded correctly
    if (! $sth->bind_result($imageURL)) {
        throw new Exception ("Failed to bind results at line ".__LINE__);//.' '.$dbh->error);
    }
    # send request to database, die if error
    if (! $sth->execute()) {
        throw new Exception ("Failed to execute at line ".__LINE__);//.' '.$dbh->error);
    }

    $imageURLs = array();
    while($sth->fetch()) {
        $imageURLs[] = $imageURL;
    }

    // add array of all images to the products assoc array i.e. now contains 'image_url' index
    $product[0] += ['image_url' => $imageURLs];

    $sth->close();

    return $product;
}

function get_product_image($pid) {
    $db = get_db_connection();
    $result = $db->query("SELECT Products.productID as productid, image_url FROM ProductImages,Products WHERE Products.productID = ProductImages.productID")->fetch_all(MYSQLI_ASSOC);
    $array = array();
    foreach ($result as $key => $value) {
        if ($value['productid'] == $pid) {
            array_push($array, $value['image_url']);
        }
    }
    return $array;
}

function get_tag($id) {
    $db = get_db_connection();
    $result = ($db->query("SELECT productID,tag FROM Tags"))->fetch_all(MYSQLI_ASSOC);
    $array = array();
    foreach ($result as $key => $value) {
        if ($value['productID'] == $id) {
            array_push($array, $value['tag']);
        }
    }
    return $array;
}

function add_product($quantity, $ownerID, $productName, $descriptionText, $productPrice, $dimensions, $color, $modelname) {
    $db = get_db_connection();
    $result = $db->query("INSERT INTO Products(quantity,ownerID,productName,descriptionText,productPrice,dimensions,color,modelname)VALUES($quantity,$ownerID,$productName,$descriptionText,$productPrice,$dimensions,$color,$modelname)");
}

function remove_product($id) {
    $db = get_db_connection();
    $result = $db->query("DELETE FROM Products WHERE productID=$id");
}

if (isset($_GET['product'])) {
    echo get_products($_GET['product']);
} else if (isset($_POST['quantity'], $_POST['ownerID'], $_POST['productName'], $_POST['descriptionText'], $_POST['productPrice'], $_POST['dimensions'], $_POST['color'], $_POST['modelname'])) {
    add_product($_POST['quantity'], $_POST['ownerID'], $_POST['productName'], $_POST['descriptionText'], $_POST['productPrice'], $_POST['dimensions'], $_POST['color'], $_POST['modelname']);
} else if (isset($_POST['remove'])) {
    remove_product($_POST['remove']);
}
?>
