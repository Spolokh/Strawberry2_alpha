<?php
/**
 * @package Private
 * @access private
 */

include_once dirname(__DIR__) .'/strawberry/head.php';

header("Content-type: application/json; charset=$config[charset]");

//$group = $_POST['usergroup'] ?? 4;
//if (!$query = $sql->select(['users', 'select' => ['mail', 'name'], 'where' => ['usergroup = '.$group]])) {
//	header('HTTP/1.0 500 Internal Server Error');
//    exit('Error');
//}


$query= ['news', 'select' => ['date', 'url', 'title'], 
		'where' => ['type = blog', 'and', 'hidden = 0'],
		'orderby' => ['date', 'DESC'],
		'limit' => [0, 20]
	];

if (!$query = $sql->select($query))
{
	header('HTTP/1.0 500 Internal Server Error');
	exit('Error');
}

foreach($query as $k => $row) {
	$json[] = $row;
}

echo json_encode($json, JSON_UNESCAPED_UNICODE); 

exit;