<?php

include 'Connect.php';

$data = json_decode(file_get_contents("php://input"), TRUE);

$TABLE_NAME = 'Accounts';#in case we change our minds
#Input fields are the name attributes of the input tags found at the very summit of the table
$INPUT_FIELDS = ["accountID", "username", "email", "password", "firstName", "lastName", "address", "Country", "isAdmin"];
$SQL_FIELDS = ["accountID", "username", "email", "password", "firstName", "lastName", "address", "country", "isAdmin"];
$INPUT_FIELD_TYPES=["int", "string", "string", "string", "string", "string", "string", "string", "string", "int"];
$QUERY = 'INSERT INTO ';
$QUERY .= $TABLE_NAME;
#Gather up the $keys
$values = [];
$counter = 0;
$QUERY_VALUES = "(";
foreach($SQL_FIELDS as $field){
  array_push($values, $field);
  if ($counter > 0)
  {
      $QUERY_VALUES .= $field .= ", ";
  }
   $counter++;
}
$QUERY_VALUES = substr($QUERY_VALUES, 0, -2);
$QUERY_VALUES .= ")";
$QUERY .= " ";
$QUERY .= $QUERY_VALUES;
$QUERY .= " VALUES ";
$ENTRY = "(";
for ($i = 1; $i < $counter - 1; $i++)
{
  if ($SQL_FIELDS[$i] != "int"){
      $ENTRY .= "'";
      $ENTRY .= $data[$INPUT_FIELDS[$i]];
      $ENTRY .= "'";
  }
  else{
      $ENTRY .= $data[$INPUT_FIELDS[$i]];
  }
  $ENTRY .= ", ";
}
$ENTRY .= "0";//Not an admin
$ENTRY .= ");";
$QUERY .= $ENTRY;
$db = get_db_connection();
$echo_array = array("Accepted" => false);

$db->query($QUERY);

$err = $db->error;

if($err == NULL)
{
	$echo_array["Accepted"] = true;
}

echo json_encode($echo_array);

?>