<?php
$sql->createtable(array(
'table'   => 'lang',
'columns' => array(
             'id'   => array('type' => 'string', 'permanent' => 1),
             'name' => array('type' => 'string'),
             'text'  => array('type' => 'text')
             )
));
?>