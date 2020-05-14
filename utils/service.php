<?php
include_once("utils.php");

if (isset($_GET["addItem"]) && $_GET["addItem"] == "OK") {
	$isCartEmpty = CartHandler::getInstance()->addItem();
	echo "full";
} else if (isset($_GET["removeItem"]) && $_GET["removeItem"] == "OK") {
	$isCartEmpty = CartHandler::getInstance()->removeItem();
	echo "empty";	
} else if ( isset($_GET["status"]) ) {
	$isCartEmpty = CartHandler::getInstance()->isEmpty();	
	if ( $isCartEmpty ) {
		echo "empty";
	} else {
		echo "full";
	} 
}	 
?>