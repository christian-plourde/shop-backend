<?
include 'Connect.php';

$TABLE_NAME = 'Accounts';#in case we change our minds
#Input fields are the name attributes of the input tags found at the very summit of the table
$INPUT_FIELDS = ["accountID", "username", "email", "password", "first_name", "last_name", "address", "Country", "isAdmin"];
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
for ($i = 1; $i < $counter - 2; $i++)
{
  if ($SQL_FIELDS[$i] != "int"){
      $ENTRY .= "'";
      $ENTRY .= $_POST[$INPUT_FIELDS[$i]];
      $ENTRY .= "'";
  }
  else{
      $ENTRY .= $_POST[$INPUT_FIELDS[$i]];
  }
  $ENTRY .= ", ";
}
$ENTRY .= "'Canada'";//Assume from Canada
$ENTRY .= ", ";
$ENTRY .= "0";//Not an admin
$ENTRY .= ");";
$QUERY .= $ENTRY;
// echo $QUERY;
$db = get_db_connection();
$result = $db->query($QUERY);

// header("Location: http://localhost:3000/");
header("Location: https://shop-354.herokuapp.com/");
?>
