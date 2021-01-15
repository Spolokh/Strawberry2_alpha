<?php
/**
 * @package Private
 * @access private
 */

include_once databases_directory.'/txtsql.class.php';
$sql = new txtSQL(data_directory.'/db');
$sql->connect('root', '');

if ($sql->db_exists('base')){
	$sql->selectdb('base');
}