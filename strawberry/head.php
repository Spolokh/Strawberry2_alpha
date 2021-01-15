<?php
/**
 * @package Private
 * @access private
 */

session_start();
error_reporting(E_ALL & ~E_NOTICE); //  & ~E_NOTICE  E_STRICT 

$vars    = ['id','category','catalog','skip','page','cpage','action','user','PHPSESSID','title','number','template','static','year','month','day','keywords','search','sort','author','time','type','link','tpl','start_from','go'];
$default = [
	'DOCUMENT_ROOT' => dirname(__DIR__), 'HTTP_REFERER' => $_SERVER['HTTP_REFERER'], 'REQUEST_URI' => $_SERVER['REQUEST_URI'], 'PHP_SELF' => $_SERVER['PHP_SELF'], 
	'rootpath' => dirname(__FILE__),
	'phpversion' => phpversion(),
	'is_logged_in' => false, 
	'cache_uniq' => 0, 
	'categories' => [],
	'groups_arr' => [], 
	'usergroups' => [],
	'gettext' => [],
	'member' => [],
	'errors' => [],
	'values' => [],
	'config' => [],
	'array'	=> [],
	'files' => [],	   
	'users' => [], 
	'data' 	=> [],	   
	'json' 	=> [],	   
	'post'  => [],
	'tpl' 	=> [],
	'result' => ''
];  

foreach ($vars as $k => $v) {
	$$k = htmlspecialchars($v);
}

foreach ($default as $k => $v) { 
	$$k = !is_array($v)? (!empty($v)? str_replace('\\', '/', $v): ''): []; 
}    

include_once $rootpath.'/inc/defined.inc.php';
include_once includes_directory.'/functions.inc.php';
include_once includes_directory.'/plugins.inc.php';
include_once includes_directory.'/plugins.default.php';
include_once includes_directory.'/class.inc.php';
include_once includes_directory.'/cache.inc.php';
$config = include (config_file);

$zone = $config['timestamp_zone'] ?? 'Europe/Moscow';
$time = new DateTime('now', new DateTimeZone($zone));

$uploads = cute_parse_url($config['path_image_upload']);

define('time', $time->getTimestamp());
define('UPLOADS', $uploads['abs']);
define('UPIMAGE', $uploads['path']);

if (!filesize(config_file)) {
	include includes_directory.'/install.inc.php';
}

include_once languages_directory.DS.$config['language'].'/functions.php';
include_once databases_directory.DS.$config['database'].'.inc.php';
 
spl_autoload_register('AutoLoader');

$cute = new classes\CuteParser($config);
$cute->getPincode(6);
$ip = $cute->getRealIp();

$cache = new Cache($config['cache'], cache_directory);

$_GET = cute_stripslashes($_GET);
$_GET = cute_htmlspecialchars($_GET);
$_POST= cute_stripslashes($_POST);

@extract($_GET, EXTR_SKIP);
@extract($_POST, EXTR_SKIP);
@extract($_COOKIE, EXTR_SKIP);
@extract($_SESSION, EXTR_SKIP);
@extract($_ENV, EXTR_SKIP);

if (!empty($id) and !$post = $cache->unserialize('post', $id))
{ 
	$post = $sql->select(['news', 'join' => ['story', 'id'], 'where' => ["id = $id", 'or', "url = $id"]]);	
	$post = $cache->serialize(reset($post));
}

if (!$categories = $cache->unserialize('categories'))
{	
	foreach ($sql->select(['categories', 'where' => ['hidden <> 1'], 'orderby' => ['id', 'ASC']]) as $row) {
		$categories[$row['id']] = $row;
	} 	
	$categories = $cache->serialize($categories);
}

if (!$usergroups = $cache->unserialize('usergroups')) 
{
	foreach ($sql->select(['usergroups', 'orderby' => ['id', 'ASC']]) as $row){
		$row['access']      = ($row['access'] == 'full' ? $row['access'] : unserialize($row['access']));
		$row['permissions'] = unserialize($row['permissions']);
		$usergroups [$row['id']] = $row;
	}   $usergroups = $cache->serialize($usergroups);
}

if (substr($HTTP_REFERER, -1) == DS) {
	$HTTP_REFERER.= $PHP_SELF;
}

if (isset($username))
{
    $username = $_COOKIE[$config['cookie_prefix'].'username'] ? : $username;
    $password = $_COOKIE[$config['cookie_prefix'].'password'] ? : md5x($password);
	
	if ($sql->login($username, $password))
	{
		cute_setcookie('lastname', $username, (time + 1012324305),  '/');
        cute_setcookie('username', $username, (time + 3600 * 24 * 365), '/');
		cute_setcookie('password', $password, (time + 3600 * 24 * 365), '/');
		$is_logged_in = true;

	} else {
		$result = '<font color="red">'.t('Неправильное имя пользователя или пароль!').'</font>';
        $is_logged_in = false;
        cute_setcookie('username', '', (time - 3600 * 24 * 365), '/');
        cute_setcookie('password', '', (time - 3600 * 24 * 365), '/');
	}
}

if (isset($exit)) {
	$sql->logout();
}

LoadActivePlugins();
run_actions('head');
CN::start();
