<?php
/**
 * @package Private
 * @access private
 */
 
include_once dirname(__DIR__).'/strawberry/head.php';

$search = $_POST['search'] ?? null;

if ( isset($search) )
{
	if (!$query = $sql->select(['news', 'select' => ['id', 'title'], 'where' => ['hidden = 0']])) {
		exit ('<span></span>') ;
	}

	foreach ($query as $row) {
		$values[] = replace_news('show', $row['title']); //Фильтруем $pets в промежуточный массив $values
	}
}

array_walk($values, function($item, $k) { 
	global $data, $search; 

	if (strtolower(substr($item, 0, strlen($search))) == strtolower($search)) {
		$data[] = $item;
	}
});	

$result = !empty($data) ? '<ul><li>'.implode('</li><li>', $data).'</li></ul>' : '<span></span>';
exit ($result) ;

/*function str_srch($item, $k) { 
	global $data, $request; 
	
	if(strtolower(substr($item, 0, strlen($request))) == $request){
		$data[] = $item;
	}
}*/ 
