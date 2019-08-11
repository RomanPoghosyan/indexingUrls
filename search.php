<?php

require_once "app/init.php";

if(isset($_GET['q']) && !empty($_GET['q'])){
	$q = $_GET['q'];

	$query = $es->search([
		'body' => [
			'query' => [
				'bool' => [
					'should' => [
						'match' => ['title' => $q]
						'match' => ['url' => $q]
						'match' => ['text' => $q]
					]
				]
			]
		]
	]);

	echo "<pre>";
	print_r($query);
	// die;

	if($query['hits']['total'] >= 1){
		$results = $query['hits']['hits'];
	}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<form action="search.php" method="get">
		<input type="text" name="q">
	</form>
</body>
</html>