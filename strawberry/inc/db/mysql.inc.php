<?php
/**
 * @package Private
 * @access private
 */

defined('rootpath') OR die('No direct access allowed.');
define('DBNAME', $config['dbname']);
define('DBUSER', $config['dbuser']);
define('DBHOST', $config['dbhost']);
define('DBPASS', $config['dbpass']);
define('PREFIX', $config['prefix']);

include_once databases_directory.'/mysql.class.php';

$sql = (new inc\db\MySQL())->setCharset();
//$sql->setCharset();
//define('CONFIG', [$config['dbserver'], $config['dbuser'], $config['dbpassword'],$config['dbname']]);
