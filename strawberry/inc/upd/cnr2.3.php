<?php
$sql->altertable(array(
'table'  => 'category',
'action' => 'insert',
'name'   => 'template',
'values' => array('type' => 'string')
));

$sql->altertable(array(
'table'  => 'category',
'action' => 'rename table',
'name'   => 'categories'
));
?>