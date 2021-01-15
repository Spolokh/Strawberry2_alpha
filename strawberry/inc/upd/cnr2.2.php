<?php
$sql->altertable(array(
'table'  => 'users',
'action' => 'insert',
'name'   => 'categories',
'values' => array('type' => 'string')
));

$sql->altertable(array(
'table'  => 'news',
'action' => 'insert',
'name'   => 'hidden',
'values' => array('type' => 'bool', 'default' => 0)
));
?>