<?php
include_once("utils/utils.php");
include_once("utils/migrate.php");
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Session Migration Simple Example</title>
<link rel="stylesheet" href="css/base.css"/>

<!--  Unblu JS API MIN -->
<!-- Please adjust the line below if it is required -->
<script src="https://unblu.cloud/unblu/js-api/v2/visitor/visitor-api.min.js"></script>

<!-- Please replace the below scripts, snippet, with your Unblu snippet JS  -->
<!-- Begin of Unblu snippet -->
<script type="text/javascript" defer="defer" src="https://unblu.cloud/unblu/visitor.js?x-unblu-apikey=your-api-key-here"></script>
<!-- End of Unblu snippet -->

<!-- Scripts that call backend-bridge and use Unblu JS API -->
<script type="text/javascript" src="js/unbluintegration.js"></script>

</head>
<body>
<div class="container">
	<div class="header">
		<h3>A simple example of session migration</h3>
	</div>
	<div class="content">
		<div class="content-1">
		<div class="button buttonCart" id="updateCart">Add Item</div>
		</div>
		<div class="content-2">
			<div class="cartFull">
				<span >Your Shopping Cart:</span><br><br>
				<img style="margin:0px 80px auto" id="cartImage" src="">
			</div>
		</div>
	</div>
	<div class="footer">
		<span class="button buttonUnblu" id="activate_live_support">Live Support</span>
	</div>
</div>
</body>
</html>