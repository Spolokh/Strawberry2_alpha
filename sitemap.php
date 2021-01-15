<?php
/**
 * @package Show
 * @access private
 */

include_once "strawberry/head.php";

header('Content-type: text/xml');

$dom = new DOMDocument ("1.0", $config['charset']);
$dom ->formatOutput = true; //под вопросом

$set = $dom->createElement ('urlset');
$set ->setAttribute ('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
$dom ->appendChild ($set);

$url = $dom->createElement('url');
$set ->appendChild($url);

$loc = $dom->createElement('loc', $config['http_script_dir']);
$req = $dom->createElement('changefreq', 'daily'); 
$mod = $dom->createElement('lastmod', langdate("Y-m-d", $config['timestamp_registered_site'])); 
$pri = $dom->createElement('priority', '1.0');

$url ->appendChild($loc);
$url ->appendChild($mod);
$url ->appendChild($req);
$url ->appendChild($pri);

$query = $sql->select(['news', 
	'select' => ['id', 'url', 'date', 'author', 'category', 'type'], 
	'where'  => ['type != php', 'and', 'type != poll', 'and', 'type != page', 'and', 'hidden != 1']
]);

if ( !reset($query) ) {
	return false; // code...
}

foreach ($query as $row) { 
		
	$url = $dom->createElement('url');
	$set ->appendChild ($url);

	$loc = $dom->createElement('loc', cute_get_link($row));
	$req = $dom->createElement('changefreq', 'daily'); 
	$mod = $dom->createElement('lastmod', langdate("Y-m-d", $row['date'])); 
	$pri = $dom->createElement('priority', '0.8');

	$url ->appendChild ($loc);
	$url ->appendChild ($mod);
	$url ->appendChild ($req);
	$url ->appendChild ($pri);
}

echo $dom->saveXML();
