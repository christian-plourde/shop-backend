//function is called when Submit button is clicked
function querySubmit() {
	var query = document.getElementById("query").value;
	document.getElementById("table").innerHTML = "";
	document.getElementById("error").innerHTML = "";
	if(query) {
		//if query is not null, undefined, empty, etc. post request to handler php page
		$.post("doQuery.php", {query: query}, queryPost);
	} else {
		document.getElementById("error").innerHTML = "client side empty";
	}
}

//function is called for searching for product by ID
function prodQuerySubmit() {
	var ID = document.getElementById("prodID").value;
	document.getElementById("table").innerHTML = "";
	document.getElementById("prodError").innerHTML = "";
	if(ID) {
		//If ID is not null, undefined, empty, etc. post request to handler php page
		$.post("findProdByID.php", {prodID: ID}, queryPost);
	} else {
		document.getElementById("prodError").innerHTML = "client side empty";
	}
}

//function is called when searching for products
function searchQuerySubmit() {
	var searchTerm = document.getElementById("searchTerm").value;
	document.getElementById("table").innerHTML = "";
	document.getElementById("searchError").innerHTML = "";
	if(searchTerm) {
		//If searchTerm is not null, undefined, empty, etc. post request to handler php page
		$.post("findProdsBySearch.php", {searchTerm: searchTerm}, queryPost);
	} else {
		document.getElementById("searchError").innerHTML = "client side empty";
	}
}

//function called when ajax post request is complete
function queryPost(data, status) {
	//convert results from php page to json
	var results = JSON.parse(data);
	
	//if there was any error php side, display it
	if(results.error) {
		document.getElementById("error").innerHTML = results.error;
	}
	
	// no errors
	if(results.status) {
		//if query brought back 0 results, say so
		if(results.msg === undefined || results.msg.length == 0) {
			document.getElementById("error").innerHTML = "Query returned 0 results";
		}
		else if(typeof results.msg === 'string' || results.msg instanceof String) {
			document.getElementById("table").innerHTML = results.msg;			
		}
		// create table with those results
		else {
			var value;
			var tableString = "<table border='1' width='200'>";
			tableString += "<tr>";
			//populates head of table with names of attributes from table
			for(var strName in results.msg[0]) {
				tableString += "<th>"+strName+"</th>";
			}
			tableString += "</tr>";
			
			//display each row of result as new row in table
			for(var i = 0; i < results.msg.length; i++) {
				tableString += "<tr>";
				for(var index in results.msg[i]) {
					value = results.msg[i][index];
					tableString += "<td>"+value+"</td>";
				}
				tableString += "</tr>";
			}
			
			tableString += "</table>";
			
			//display table on html page
			document.getElementById("table").innerHTML = tableString;
		}
	}
		
		

}
