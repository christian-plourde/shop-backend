<?php
	error_reporting(-1);
	// php page which post request comes to from javascript
	require ("db_functions.php");
	//initialize return variables
	$result["error"] = "";
	$result["status"] = "";
	
	//get query
	$ID = $_POST['prodID'];
	
	//if query empty, return to javascript
	if(!is_int($ID) && $ID < 1) {
		$result["error"] = "Invalid ID passed";
		echo json_encode($result);
		exit;
	}
	
	try{
		//run the query
		$data = get_product_by_id($ID);
		//no errors, query ran successfully
        	$result["status"] = "good";
		$result["msg"] = $data;
	}
	//catch any exceptions from running the query
	catch (Exception $e) {
		error_log($e->getMessage()."\n");
		$exception = true;
		$result["error"] = $e->getMessage();
	}

    # send result back to client
    echo json_encode($result);
    
    
?>