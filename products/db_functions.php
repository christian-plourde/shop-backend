<?php
//php page containing all functions that interface with the database
error_reporting(E_ALL ^ E_WARNING);

// function that gets database connection
// INPUT: NONE
// OUTPUT: MYSQLI Connection to database which can be used to execute SQL queries in PHP
function get_db_connection() {
    $servername = "remotemysql.com";
    $username = "HQgsxOVVFA";
    $password = "QU8LU8QaqR";
    $database = "HQgsxOVVFA";
    $dbport = "3306";
    $dbh = new mysqli($servername, $username, $password, $database, $dbport);
    return $dbh;
}

// function to return a product's information based off its ID
// INPUT: Valid integer representing product ID
// OUTPUT: Assoc array containing all information about that product
function get_product_by_id($id) {
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

// finds products based off of the search term passed
// INPUT: search term
// OUTPUT: Array of products relating to search term
function find_product_by_search($searchTerm) {
    if($searchTerm === '') {
        throw new Exception("Invalid Search Term");
    }

    $dbh = get_db_connection();

    // split search term string into array of single words - ie "Hello World" becomes ["hello", "world"]
    // this is so we can find all products which match parts of the search term and not necessarily all of it
    $searchTermArray = explode(" ", $searchTerm);
    $prodIDs = array();

    // 1 - find all products with tags relating to the search term
    foreach($searchTermArray as &$word) {
        $sql = "select productID from Tags where tag = ?";
        # prepare the sql statement, die if sql statement is bad
        if (! $sth = $dbh->prepare($sql)){
            throw new Exception ("Failed to prepare at line ".__LINE__.' '.$dbh->error);
        }
        # bind the input parameters, die if not coded correctly
        if (! $sth->bind_param('s', $word)) {
            throw new Exception ("Failed to bind parameters at line ".__LINE__);//.' '.$dbh->error);
        }
        # bind the output parameters, die if not coded correctly
        if (! $sth->bind_result($prodID)) {
            throw new Exception ("Failed to bind results at line ".__LINE__);//.' '.$dbh->error);
        }
        # send request to database, die if error
        if (! $sth->execute()) {
            throw new Exception ("Failed to execute at line ".__LINE__);//.' '.$dbh->error);
        }

        while($sth->fetch()) {
            //$aRow['ID'] = $prodID;
            $prodIDs[] = $prodID;
        }
               

        $sth->close();
        
    }


    // 2 - find all products with name relating to search term
    foreach($searchTermArray as $word) {
        $sql = "select productID from Products where productName like ? or descriptionText like ? or productPrice = ? or color = ? or modelName = ?";
        $likeWord = "%".$word."%";
        # prepare the sql statement, die if sql statement is bad
        if (! $sth = $dbh->prepare($sql)){
            throw new Exception ("Failed to prepare at line ".__LINE__.' '.$dbh->error);
        }
        # bind the input parameters, die if not coded correctly
        if (! $sth->bind_param('sssss', $likeWord, $likeWord, $word, $word, $word)) {
            throw new Exception ("Failed to bind parameters at line ".__LINE__);//.' '.$dbh->error);
        }
        # bind the output parameters, die if not coded correctly
        if (! $sth->bind_result($prodID)) {
            throw new Exception ("Failed to bind results at line ".__LINE__);//.' '.$dbh->error);
        }
        # send request to database, die if error
        if (! $sth->execute()) {
            throw new Exception ("Failed to execute at line ".__LINE__);//.' '.$dbh->error);
        }

        while($sth->fetch()) {
            //$aRow['ID'] = $prodID;
            $prodIDs[] = $prodID;
        }

        $sth->close();
    }

    // if ID shows up more than once, bring it to top
    $dupIDs = array();
    foreach(array_count_values($prodIDs) as $ID => $count) {
        if($count > 1) {
            $dupIDs[] = $ID;
        }
    }
    
    $prodIDs = array_merge($dupIDs, $prodIDs);


    // remove duplicates
    $uniqueProdIDs = array();
    $uniqueProdIDs = array_unique($prodIDs, SORT_REGULAR);

    $products = array();
    // get each product with their ID and return in assoc array
    foreach($uniqueProdIDs as &$id) {
        $product = get_product_by_id($id);
        $products[] = $product[0];
    }

    return $products;
    
}


// executes given query, returns results as an array of associative arrays
// INPUT: SQL Query
// OUTPUT: Results of SQL Query in assoc array
function execute_query($sql) {
	$dbh = get_db_connection();

	if(mysqli_connect_errno()) {
        throw new Exception ("Failed to get database connection at line ".__LINE__);
	}

    if($results = mysqli_query($dbh, $sql)) {
        if(mysqli_num_rows($results) > 0 || strpos($sql, 'select') === 0 || strpos($sql, 'Select') === 0) {
            $res_return = array();
            while($row = mysqli_fetch_assoc($results)) {
                $res_return[] = $row;
            }
            $results->free();
        } else {
            $dbh->close();
            return "Query ran successfully";
        }
    
        
    } else {
         throw new Exception("Could not run query, check syntax and try again");
    }
    

    $dbh->close();
    
    return $res_return;
} 

?>