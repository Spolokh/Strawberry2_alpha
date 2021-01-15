<?php
/**
 * @package Private
 * @access private
 */
 
include_once dirname(__DIR__) .'/strawberry/head.php';

if ( !($member || $is_logged_in) ) {
	//return $cute->msg('Achtung!', t('Необходима авторизация на сайте'));	
	exit (t('Необходима авторизация на сайте'));
}

$where = [];

if ( !$query = $sql->select(['attach', 'select' => ['thumb', 'file', 'title', 'folder'], 'where' => $where]) ) {
	exit ();
}
	
foreach ($query as $k => $row)
{
    if ( empty($row['file']) AND !file_exists (UPLOADS .'/posts/'. $row['file']) ) {
		continue;
	}
	if ( empty($row['thumb']) ) {
		$row['thumb']  = $row['file'];
	}
	if ( empty($row['folder']) ) {
		$row['folder'] = 'Post';
	}
	if ( strpos ($row['file'], '/uploads/') === FALSE ) {
		$row['file'] = '/uploads/posts/'.$row['file'];
	}
	if ( strpos ($row['thumb'], "/uploads/thumb.php") === FALSE ) {
		$row['thumb'] = '/uploads/thumb.php?src=/uploads/posts/'.$row['thumb'].'&w=100&h=70';
	}
	$json[] = $row;
}	
	
$result = json_encode($json);
exit ($result);
