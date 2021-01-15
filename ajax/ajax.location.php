<?php
/**
 * @package Private
 * @access private
 */

include_once dirname(__DIR__) .'/strawberry/head.php';

foreach ($_GET as $k => $v){
	$$k = empty($v) ? $$k : htmlspecialchars($v);
}

header('Content-type: application/json; charset='. $config['charset']);

$country = $country ?? 0;
$regions = $regions ?? 0;

if ( $country > 0 ) {
   $query = $sql->select(['region', 'select' => ['region_id', 'name'], 'where' => ["id=$country"], 'orderby' => ['name', 'ASC']]);
}

if ( $region > 0 ) {
   $query = $sql->select(['city', 'select' => ['city_id', 'name'], 'where' => ["region_id=$regions"], 'orderby' => ['name', 'ASC']]);
}

if (!reset($query)) {
	header('HTTP/1.1 500 Internal Server Error');
    exit;
}

foreach($query as $k => $row){
	$json[] = $row;
}

$result = json_encode($json, JSON_UNESCAPED_UNICODE);
$json  	= []; 

exit ($result);
