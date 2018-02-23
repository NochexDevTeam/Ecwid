<?php
echo "<h4>Success Page</h4>";

$xml = simplexml_load_file("orders.xml") or die("Error: Cannot create object");
$order_number = $_GET['order_id'];
$order = $xml->xpath("//orders/order/id[.='$order_number']/parent::*");

if($order[0]){
	echo $order[0]->response;
}
?>
