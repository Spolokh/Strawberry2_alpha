<?php
if (!$config['gmtoffset']){
	$config['gmtoffset'] = $config['date_adjust'];
}

if (!$config['charset']){
	$_echo = cute_lang('shows');
	$config['charset'] = $_echo['charset'];
}

$sql->altertable(array(
'table'  => 'usergroups',
'action' => 'modify',
'name'   => 'access',
'values' => array('type' => 'text')
));

$sql->altertable(array(
'table'  => 'usergroups',
'action' => 'modify',
'name'   => 'permissions',
'values' => array('type' => 'text')
));

$sql->altertable(array(
'table'  => 'users',
'action' => 'insert',
'name'   => 'deleted',
'values' => array('type' => 'bool', 'default' => 0)
));

$sql->altertable(array(
'table'  => 'categories',
'action' => 'insert',
'name'   => 'level',
'values' => array('type' => 'int', 'default' => 0)
));

$sql->altertable(array(
'table'  => 'categories',
'action' => 'insert',
'name'   => 'description',
'values' => array('type' => 'text')
));

$sql->altertable(array(
'table'  => 'categories',
'action' => 'insert',
'name'   => 'usergroups',
'values' => array('type' => 'string')
));

$sql->altertable(array(
'table'  => 'news',
'action' => 'insert',
'name'   => 'sticky',
'values' => array('type' => 'bool', 'default' => 0)
));

$rufus = parse_ini_file(rufus_file, true);

if (!$rufus['home']['skip']){
	$rufus['home']['skip']  = '?skip={skip}';
	$rufus['home']['page']  = '?skip={page}';
	$rufus['home']['cpage'] = '?skip={cpage}';
	write_ini_file(rufus_file, $rufus);
}

if ($rufus['home']['page'] == '?skip={page}'){
	$rufus['home']['page']  = '?page={page}';
	$rufus['home']['cpage'] = '?cpage={cpage}';
	write_ini_file(rufus_file, $rufus);
}

include databases_directory.'/database.inc.php';

$sql->createtable(array('table' => 'money', 'columns' => $database['money']));

foreach ($sql->select(array('table' => 'categories', 'orderby' => array('id', 'ASC'))) as $row){
	$categories[$row['id']] = $row;
}

foreach ($categories as $row){
	if ($row['parent']){
        $count = explode('/', category_get_link($row['id']));
        $count = (count($count) - 1);

	    $sql->update(array(
	    'table'  => 'categories',
	    'where'  => array("id = $row[id]"),
	    'values' => array('level' => $count)
	    ));
	}
}

foreach ($sql->select(array('table' => 'usergroups')) as $row){
	if (!unserialize($row['access']) or !unserialize($row['permissions'])){
		if ($row['id'] == 2){
	        $sql->update(array(
	        'table'  => 'usergroups',
	        'where'  => array("id = $row[id]"),
	        'values' => array(
	                    'access'      => 'a:2:{s:5:"write";a:26:{s:5:"about";s:1:"1";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"1";s:5:"ipban";s:1:"1";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"1";s:10:"categories";s:1:"1";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"1";s:9:"editusers";s:1:"1";s:4:"help";s:1:"1";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"1";s:4:"main";s:1:"1";s:3:"cqt";s:1:"1";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"1";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"1";}s:4:"read";a:26:{s:5:"about";s:1:"1";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"1";s:5:"ipban";s:1:"1";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"1";s:10:"categories";s:1:"1";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"1";s:9:"editusers";s:1:"1";s:4:"help";s:1:"1";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"1";s:4:"main";s:1:"1";s:3:"cqt";s:1:"1";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"1";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"1";}}',
	                    'permissions' => 'a:6:{s:12:"approve_news";s:1:"0";s:4:"edit";s:1:"1";s:6:"delete";s:1:"1";s:8:"edit_all";s:1:"1";s:10:"delete_all";s:1:"1";s:8:"comments";s:1:"1";}'
	                    )
	        ));
	    }

        if ($row['id'] == 3){
	        $sql->update(array(
	        'table'  => 'usergroups',
	        'where'  => array("id = $row[id]"),
	        'values' => array(
	                    'access'      => 'a:2:{s:5:"write";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}s:4:"read";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}}',
	                    'permissions' => 'a:6:{s:12:"approve_news";s:1:"0";s:4:"edit";s:1:"1";s:6:"delete";s:1:"1";s:8:"edit_all";s:1:"0";s:10:"delete_all";s:1:"0";s:8:"comments";s:1:"1";}'
	                    )
	        ));
	    }

        if ($row['id'] == 4){
	        $sql->update(array(
	        'table'  => 'usergroups',
	        'where'  => array("id = $row[id]"),
	        'values' => array(
	                    'access'      => 'a:2:{s:5:"write";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"0";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"0";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"0";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"0";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}s:4:"read";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"0";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"0";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"0";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"0";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}}',
	                    'permissions' => 'a:6:{s:12:"approve_news";s:1:"0";s:4:"edit";s:1:"1";s:6:"delete";s:1:"0";s:8:"edit_all";s:1:"0";s:10:"delete_all";s:1:"0";s:8:"comments";s:1:"1";}'
	                    )
	        ));
	    }
    }
}
?>