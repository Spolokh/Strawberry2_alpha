<?php
if (!$config['pages_break']){
	$config['pages_break'] = '10';
}

if (!$config['pages_section']){
	$config['pages_section'] = '3';
}

if (!$config['cpages_break']){
	$config['cpages_break'] = '10';
}

if (!$config['cpages_section']){
	$config['cpages_section'] = '3';
}

foreach ($config as $k => $v){
	if ($v == 'yes'){
		$config[$k] = '1';
	}

	if ($v == 'no'){
		$config[$k] = '0';
	}
}

/*
$handle = opendir(templates_directory);
while ($file = readdir($handle)){
    if (substr($file, -3) == 'tpl'){
    	$contents = file_read(templates_directory.'/'.$file);
    	$contents = str_replace('{link=home/post}', '{link}', $contents);
	    $contents = str_replace('[catheader]', '[category]', $contents);
	    $contents = str_replace('[/catheader]', '[/category]', $contents);
		file_write(templates_directory.'/'.$file, $contents);
    }
}
*/

if (!$sql->table_exists('usergroups')){
	include root_directory.'/inc/db/database.inc.php';

    $sql->createtable(array('table' => 'usergroups', 'columns' => $database['usergroups']));

	$sql->insert(array(
	'table'  => 'usergroups',
	'values' => array(
				'name'        => 'Администраторы',
				'access'      => 'full',
				'permissions' => ''
				)
	));

	$sql->insert(array(
	'table'  => 'usergroups',
	'values' => array(
				'name'        => 'Редакторы',
				'access'      => 'a:2:{s:5:"write";a:26:{s:5:"about";s:1:"1";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"1";s:5:"ipban";s:1:"1";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"1";s:10:"categories";s:1:"1";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"1";s:9:"editusers";s:1:"1";s:4:"help";s:1:"1";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"1";s:4:"main";s:1:"1";s:3:"cqt";s:1:"1";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"1";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"1";}s:4:"read";a:26:{s:5:"about";s:1:"1";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"1";s:5:"ipban";s:1:"1";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"1";s:10:"categories";s:1:"1";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"1";s:9:"editusers";s:1:"1";s:4:"help";s:1:"1";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"1";s:4:"main";s:1:"1";s:3:"cqt";s:1:"1";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"1";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"1";}}',
				'permissions' => 'a:5:{s:12:"approve_news";s:1:"0";s:4:"edit";s:1:"1";s:6:"delete";s:1:"1";s:8:"edit_all";s:1:"1";s:10:"delete_all";s:1:"1";}'
				)
	));

	$sql->insert(array(
	'table'  => 'usergroups',
	'values' => array(
				'name'        => 'Журналисты',
				'access'      => 'a:2:{s:5:"write";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}s:4:"read";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}}',
				'permissions' => 'a:5:{s:12:"approve_news";s:1:"0";s:4:"edit";s:1:"1";s:6:"delete";s:1:"1";s:8:"edit_all";s:1:"0";s:10:"delete_all";s:1:"0";}'
				)
	));

	$sql->insert(array(
	'table'  => 'usergroups',
	'values' => array(
				'name'        => 'Комментаторы',
				'access'      => 'a:2:{s:5:"write";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"0";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"0";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"0";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"0";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}s:4:"read";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"0";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"0";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"0";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"0";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}}',
				'permissions' => 'a:5:{s:12:"approve_news";s:1:"0";s:4:"edit";s:1:"1";s:6:"delete";s:1:"0";s:8:"edit_all";s:1:"0";s:10:"delete_all";s:1:"0";}'
				)
	));

	$sql->altertable(array(
	'table'  => 'users',
	'action' => 'insert',
	'name'   => 'usergroup',
	'values' => array('type' => 'int', 'default' => 0)
	));

	foreach ($sql->select(array('table' => 'users')) as $row){
		$sql->update(array(
		'table'  => 'users',
		'where'  => array("id = $row[id]"),
		'values' => array('usergroup' => $row['level'])
		));
	}
}
?>