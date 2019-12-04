<?php
require_once("Connect.php");

$db=get_db_connection();
$result = $db->query("SELECT Accounts.username, sum(quantity*price) as TotalSold FROM `Transaction` join Accounts on Accounts.accountID = sellerID group by sellerID order by TotalSold desc LIMIT 3;");

$seller_list = [];
while ($row = mysqli_fetch_assoc($result)) {
    $username = $row["username"];
    $TotalSold = $row["TotalSold"];
    $seller = [];
    $seller["username"] = $username;
    $seller["TotalSold"] = $TotalSold; 
    array_push($seller_list, $seller);
}

$ret = [];
$ret["sellers"] = $seller_list;

echo json_encode($ret);
?>

