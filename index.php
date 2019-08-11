<?php

require_once "app/init.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<p>Please be patient</p>
	<form action="./indexing.php" method="post">
		<input type="test" name="url" value="https://www.google.com">	
		<input type="submit" name="submit">
	</form>
</body>
</html>