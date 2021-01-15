<?php
if (!$config['mod_rewrite']){
	$config['mod_rewrite'] = ($config['rufus'] ? $config['rufus'] : 'no');
	unset($config['rufus']);
}

if (!$config['cnumber']){
	$config['cnumber'] = '0';
}

$sql->altertable(array(
'table'  => 'comments',
'action' => 'insert',
'name'   => 'homepage',
'values' => array('type' => 'string')
));

$sql->altertable(array(
'table'  => 'comments',
'action' => 'insert',
'name'   => 'parent',
'values' => array('type' => 'int', 'default' => 0)
));

$sql->altertable(array(
'table'  => 'comments',
'action' => 'insert',
'name'   => 'level',
'values' => array('type' => 'int', 'default' => 0)
));

/*
$sql->altertable(array(
'table'  => 'users',
'action' => 'insert',
'name'   => 'deleted',
'values' => array('type' => 'bool', 'default' => 0)
));*/

$sql->altertable(array(
'table'  => 'comments',
'action' => 'insert',
'name'   => 'user_id',
'values' => array('type' => 'int', 'default' => 0)
));

$contents = file_read(rufus_file);
$contents = str_replace('[rss]', '[rss.php]', $contents);
$contents = str_replace('[print]', '[print.php]', $contents);
$contents = str_replace('[trackback]', '[trackback.php]', $contents);
file_write(rufus_file, $contents);

/*
$handle = opendir(templates_directory);
while ($file = readdir($handle)){
    if (substr($file, -3) == 'tpl'){
    	$contents = file_read(templates_directory.'/'.$file);
	    $contents = str_replace('{link=rss/', '{link=rss.php/', $contents);
	    $contents = str_replace('{link=print/', '{link=print.php/', $contents);
	    $contents = str_replace('{link=trackback/', '{link=trackback.php/', $contents);
		file_write(templates_directory.'/'.$file, $contents);
    }
}
*/
?>