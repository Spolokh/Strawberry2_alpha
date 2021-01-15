<?php
/*
umask(0);
@mkdir(templates_directory, chmod);
@chmod(templates_directory, chmod);

$handle = opendir(data_directory);
while ($file = readdir($handle)){
    if (substr($file, -3) == 'tpl'){
        if (@copy(data_directory.'/'.$file, templates_directory.'/'.$file)){
            @unlink(data_directory.'/'.$file);
        }
    }
}
*/

$sql->altertable(array(
'table'  => 'categories',
'action' => 'modify',
'name'   => 'id',
'values' => array('default' => 0)
));

$sql->altertable(array(
'table'  => 'story',
'action' => 'modify',
'name'   => 'post_id',
'values' => array('default' => 0)
));

$sql->altertable(array(
'table'  => 'flood',
'action' => 'modify',
'name'   => 'post_id',
'values' => array('default' => 0)
));

$sql->altertable(array(
'table'  => 'categories',
'action' => 'addkey',
'name'   => 'id'
));

$sql->altertable(array(
'table'  => 'story',
'action' => 'addkey',
'name'   => 'post_id'
));

$sql->altertable(array(
'table'  => 'flood',
'action' => 'addkey',
'name'   => 'post_id'
));
?>