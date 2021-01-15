<?php
/**
 * @package Install
 * @access private
 */
 
$config = [];
$handle = opendir(languages_directory);
while ($file = readdir($handle)){
    if ($file != '.' and $file != '..' and is_dir(languages_directory.'/'.$file)){
        $sys_con_lang_arr[$file] = strtoupper($file);
    }
}

$handle = opendir(databases_directory);
while ($file = readdir($handle)){
    if (substr($file, -3) != 'php' and is_file(databases_directory.'/'.$file)){
        $sys_con_database_arr[$file] = file_read(databases_directory.'/'.$file);
    }
}

$sys_con_charset_arr = ['X-MAC-ARABIC' => 'Arabic (Macintosh)', 'windows-1256' => 'Arabic (Windows)', 'iso-8859-2' => 'Central European (ISO-8859-2)', 'X-MAC-CENTRALEURROMAN' => 'Central European (MacCE)', 'windows-1250' => 'Central European (Windows-1250)', 'iso-8859-5' => 'Cyrillic (ISO-8859-5)', 'KOI8-R' => 'Cyrillic (KOI8-R)', 'x-mac-cyrillic' => 'Cyrillic (MacCyrillic)', 'windows-1251' => 'Cyrillic (Windows-1251)', 'iso-8859-7' => 'Greek (ISO-8859-7)', 'x-mac-greek' => 'Greek (MacGreek)', 'windows-1253' => 'Greek (Windows-1253)', 'X-MAC-HEBREW' => 'Hebrew (Macintosh)', 'windows-1255' => 'Hebrew (Windows)', 'Shift_JIS' => 'Japanese (Shift_JIS)', 'EUC-JP' => 'Japanese (EUC)', 'ISO-2022-JP' => 'Japanese (JIS)', 'EUC-KR' => 'Korean (EUC-KR)', 'gb2312' => 'Simplified Chinese (gb2312)', 'big5' => 'Traditional Chinese (big5)', 'X-MAC-THAI' => 'Thai (Macintosh)', 'Windows' => 'Thai (Windows)', 'iso-8859-5' => 'Turkish (Latin5)', 'X-MAC-TURKISH' => 'Turkish (Macintosh)', 'windows-1254' => 'Turkish (Windows)', 'utf-8' => 'UTF-8', 'iso-8859-1' => 'Western (Latin1)', 'macintosh' => 'Western (Macintosh)', 'windows-1252' => 'Western (Windows 1252)'];

$dateTimeZone   = ['' => t('Выбрать часовой пояс')];
$timezone_identifiers = DateTimeZone::listIdentifiers();
foreach ($timezone_identifiers as $v){
	if ( preg_match( '/^(America|Europe)\//', $v ) ){
		$dateTimeZone[$v] = $v;
	}
}


$config['database']= strtolower($_POST['database']);
$config['language']= $_POST['lang'];
$config['charset'] = $_POST['charset'];
$config['dbname']  = $_POST['dbname'];
$config['dbuser']  = $_POST['dbuser'];
$config['dbpass']  = $_POST['dbpass'];
$config['prefix']  = $_POST['prefix'];
$config['dbhost']  = $_POST['dbhost'];
$config['timestamp_zone'] = $_POST['timezone'];

$step = isset($_GET['step']) ? $_GET['step'] : 1;
$url  = explode('?', $_SERVER['HTTP_REFERER']);
$url  = reset($url);
$url  = preg_replace('/\/cp\/index.php$/i', '', $url);

include_once skins_directory.'/default.skin.php';

echoheader('options', t('Инсталяция Strawberry'));
?>

<table width="200" border="0" cellspacing="0" cellpadding="0">
<form action="<?=$_SERVER['PHP_SELF']; ?>?step=<?=($step + 1); ?>" method="post">
<input name="lang" type="hidden" value="<?=$config['language']; ?>">
<input name="charset" type="hidden" value="<?=$config['charset']; ?>">
<input name="database" type="hidden" value="<?=$config['database']; ?>">
<input name="timezone" type="hidden" value="<?=$config['timestamp_zone']; ?>">

<?php if ($step == 1){ ?>

	<tr>
		<td><?=t('Язык'); ?>
		<td><?=makeDropDown( $sys_con_lang_arr, 'lang', 'ru' )?>
	<tr>
		<td><?=t('Временная зона'); ?>
		<td><?=makeDropDown( $dateTimeZone, 'timezone', 'Europe/Moscow' )?>
	<tr>
		<td><?=t('Кодировка'); ?>
		<td><?=makeDropDown( $sys_con_charset_arr, 'charset', 'utf-8' )?>
	<tr>
		<td><?=t('База данных'); ?>
		<td><?=makeDropDown( ['mysql' => 'MySql'], 'database', 'mysql' )?>

<?php
} elseif ($step == 2) {
	
	if (chmod(cache_directory, chmod) ) { 
		//chmoddir(data_directory, chmod);
		@chmod(data_directory, chmod);
		@chmod(backup_directory, chmod);
	}	
	
	echo t('Проверка на права CHMOD (если какой-то фаил будет выделен красным, то нужно зайти по FTP и проставить права как написано в ридми; или нажать &quot;Обновить&quot; у браузера, в этом случае скрипт <i>попробует</i> сам всё наладить):').'<br /><br />';
	echo '<font color="'.(is_writable('cache') ? 'green' : 'red').'">cache/</font><br />';
	echo '<font color="'.(is_writable('lang/'.$config['language']) ? 'green' : 'red').'">lang/'.$config['language'].'/</font><br />';
	
	//check_writable('data');

} elseif ($step == 3){
	
	if ($config['database'] == 'txtsql'){
		$disabled = ' disabled';
	}
?>

 <tr>
	<td><?=t('Логин'); ?>
	<td><input name="login" type="text" required />
 <tr>
	<td><?=t('Пароль'); ?>
	<td><input name="password" type="text" required />
 <tr>
	<td><?=t('Почта'); ?>
	<td><input name="mail" type="email" required />
 <tr>
	<td colspan="2"><br /><br /><b><?=t('База данных'); ?></b>:
 <tr>
	<td><?=t('Имя пользователя'); ?>
	<td><input name="dbuser" type="text" <?=$disabled; ?>>
 <tr>
	<td><?=t('Пароль'); ?>
	<td><input name="dbpassword" type="text" <?=$disabled; ?>>
 <tr>
	<td><?=t('Сервер базы данных'); ?>
	<td><input name="dbserver" type="text" value="localhost"<?=$disabled; ?>>
 <tr>
	<td><?=t('Имя базы'); ?>
	<td><input name="dbname" type="text" value=""<?=$disabled; ?>>
 <tr>
	<td><?=t('Префикс таблиц'); ?>
	<td><input name="dbprefix" type="text" value="cute_"<?=$disabled; ?>>

<?php
} elseif ($step == 4){
	
	include databases_directory.'/'.$config['database'].'.inc.php';
	include databases_directory.'/database.inc.php';

	$config = [
	'version_name'      => 'Strawberry',
	'version_id'        => 'Evo',
	'http_engine_dir'   => 'cp',
	'http_script_dir'   => $url,
	'http_home_url'     => $url.'/index.php',
	'path_image_upload' => $url.'/uploads',
	'home_title'        => 'Strawberry Evo',
	'skin'  => 'default',
	'theme' => 'default',
	'language' => $config['language'],
	'cache' => '0',
	'database' => $config['database'],
	'dbname'   => $config['dbname'],
	'dbuser'   => $config['dbuser'],
	'dbpass' => $config['dbpass'],
	'prefix' => $config['prefix'],
	'dbhost' => $config['dbhost'],
	'date_adjust' => '0',
	'mod_rewrite' => '1',
	'pages_section' => '3',
	'pages_break' => '10',
	'cpages_section' => '3',
	'cpages_break' => '10',
	'users_number' => '21',
	'poster_ext'   => 'jpg',
	'timestamp_active' => 'j M Y',
	'timestamp_zone'   => $config['timestamp_zone'], //timestamp_zone
	'use_avatar' => '1',
	'date_header' => '0',
	'newsicon' => '120',
	'type_images_upload' => 'gif, jpg, png, bmp, jpe, jpeg',
	'post_types'         => ['' => t('Тип поста'), 'page' => t('Страница'), 'blog' => t('Блог'), 'poll' => t('Опрос'), 'private' => t('Запароленый')],
    'addcomments' => '1',
	'date_headerformat' => 'l, j M Y',
	'send_mail_upon_new' => '0',
	'send_mail_upon_posting' => '0',
	'comm_moderation' => '0',
	'admin_mail' => $_POST['mail'],
	'auto_wrap'  => '50',
	'flood_time' => '0',
	'smilies' => 'angry, evil, grin, laugh, sad, smile, wink',
	'smilies_line' => '0',
	'reverse_comments' => '0',
	'cnumber' => '0',
	'only_registered_comment' => '0',
	'timestamp_comment' => 'j M Y - H:i',
	'user_avatar' => '1',
	'path_userpic_upload' => $url.'/uploads/userpics',
	'use_images_uf' => '0',
	'avatar_w'   => '150',
	'avatar_h'   => '150',
	'gmtoffset'  => '180', // московское (GMT +03:00 - Москва, Питер, Волгоград)
	'charset'    => $config['charset'],
    'site_mail'  => '',
    'site_phone' => '',
	'site_address' => '',
	'site_contacs' => '1',
	'site_accesskey'  => md5(uniqid($url)),
	'timestamp_registered_site' => time,
    'description' => '',
    'keywords' => ''
	];

	if ($config['database'] == 'txtsql'){
	    if (!$sql->db_exists('base')){
	        $sql->createdb(['db' => 'base']);
	    }

	    $sql->selectdb('base');
	} 
	else 
	{
	    $sql->createDb($config['dbname']);
		$sql->selectDb();
	}

	foreach ($database as $k => $v){
	    if (!$sql->tableExists($k)){
	        $sql->createTable(['table' => $k, 'columns' => $v]);
	    }

	    if ($sql->tableExists($k)) {
	        echo '<p color="green">' .t('Таблица "%table" создана', ['table' => $k]). '</p>';
	    }
	}

	save_config($config);

	$sql->insert(['users', 'values' => [
			'date' => time,
			'mail' => $_POST['mail'],
			'usergroup' => 1,
			'username'  => $_POST['login'],
			'password'  => md5x($_POST['password'])
		]
	]);

	$sql->insert(['usergroups', 'values' => [
			'name' => 'Администраторы',
			'access' => 'full',
			'permissions' => ''
		]
	]);
	
	$sql->insert(['usergroups', 'values' => [
			'name'        => 'Авторы',
			'access'      => 'a:2:{s:5:"write";a:26:{s:5:"about";s:1:"1";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"1";s:5:"ipban";s:1:"1";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"1";s:10:"categories";s:1:"1";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"1";s:9:"editusers";s:1:"1";s:4:"help";s:1:"1";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"1";s:4:"main";s:1:"1";s:3:"cqt";s:1:"1";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"1";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"1";}s:4:"read";a:26:{s:5:"about";s:1:"1";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"1";s:5:"ipban";s:1:"1";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"1";s:10:"categories";s:1:"1";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"1";s:9:"editusers";s:1:"1";s:4:"help";s:1:"1";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"1";s:4:"main";s:1:"1";s:3:"cqt";s:1:"1";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"1";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"1";}}',
			'permissions' => 'a:6:{s:12:"approve_news";s:1:"0";s:4:"edit";s:1:"1";s:6:"delete";s:1:"1";s:8:"edit_all";s:1:"1";s:10:"delete_all";s:1:"1";s:8:"comments";s:1:"1";}'
		]
	]);
	
	$sql->insert(['usergroups', 'values' => [
			'name'        => 'Редакторы',
			'access'      => 'a:2:{s:5:"write";a:26:{s:5:"about";s:1:"1";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"1";s:5:"ipban";s:1:"1";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"1";s:10:"categories";s:1:"1";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"1";s:9:"editusers";s:1:"1";s:4:"help";s:1:"1";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"1";s:4:"main";s:1:"1";s:3:"cqt";s:1:"1";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"1";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"1";}s:4:"read";a:26:{s:5:"about";s:1:"1";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"1";s:5:"ipban";s:1:"1";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"1";s:10:"categories";s:1:"1";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"1";s:9:"editusers";s:1:"1";s:4:"help";s:1:"1";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"1";s:4:"main";s:1:"1";s:3:"cqt";s:1:"1";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"1";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"1";}}',
			'permissions' => 'a:6:{s:12:"approve_news";s:1:"0";s:4:"edit";s:1:"1";s:6:"delete";s:1:"1";s:8:"edit_all";s:1:"1";s:10:"delete_all";s:1:"1";s:8:"comments";s:1:"1";}'
		]
	]);

	$sql->insert(['usergroups', 'values' => [
			'name'        => 'Журналисты',
			'access'      => 'a:2:{s:5:"write";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}s:4:"read";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"1";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"1";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"1";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"1";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}}',
			'permissions' => 'a:6:{s:12:"approve_news";s:1:"0";s:4:"edit";s:1:"1";s:6:"delete";s:1:"1";s:8:"edit_all";s:1:"0";s:10:"delete_all";s:1:"0";s:8:"comments";s:1:"1";}'
		]
	]);

	$sql->insert(['usergroups', 'values' => [
			'name'        => 'Комментаторы',
			'access'      => 'a:2:{s:5:"write";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"0";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"0";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"0";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"0";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}s:4:"read";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"0";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"0";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"0";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"0";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}}',
			'permissions' => 'a:6:{s:12:"approve_news";s:1:"0";s:4:"edit";s:1:"1";s:6:"delete";s:1:"0";s:8:"edit_all";s:1:"0";s:10:"delete_all";s:1:"0";s:8:"comments";s:1:"1";}'
		]
	]);

	$sql->insert(['usergroups', 'values' => [
		'name'        => 'Тестеры',
		'access'      => 'a:2:{s:5:"write";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"0";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"0";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"0";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"0";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}s:4:"read";a:26:{s:5:"about";s:1:"0";s:5:"debug";s:1:"0";s:12:"editcomments";s:1:"1";s:7:"preview";s:1:"0";s:9:"trackback";s:1:"0";s:5:"ipban";s:1:"0";s:7:"addnews";s:1:"0";s:9:"configure";s:1:"0";s:10:"categories";s:1:"0";s:8:"personal";s:1:"1";s:6:"syscon";s:1:"0";s:7:"options";s:1:"1";s:7:"plugins";s:1:"0";s:3:"snr";s:1:"0";s:9:"editusers";s:1:"0";s:4:"help";s:1:"0";s:8:"editnews";s:1:"0";s:6:"backup";s:1:"0";s:4:"main";s:1:"0";s:3:"cqt";s:1:"0";s:6:"images";s:1:"0";s:8:"comm_spy";s:1:"0";s:9:"templates";s:1:"0";s:10:"usergroups";s:1:"0";s:5:"rufus";s:1:"0";s:4:"spam";s:1:"0";}}',
		'permissions' => 'a:6:{s:12:"approve_news";s:1:"0";s:4:"edit";s:1:"1";s:6:"delete";s:1:"0";s:8:"edit_all";s:1:"0";s:10:"delete_all";s:1:"0";s:8:"comments";s:1:"1";}'
	]
]);
}
?>

 <tr>
  <td colspan="2">
  <br /><br /><input type="submit" value="<?=t('Далее (шаг %step) &raquo;&raquo;', array('step' => (($step + 1) == 5 ? t('последний') : ($step + 1)))); ?>">
</form>
</table>

<?php
echofooter();

function check_writable ($dir) {
	global $rootpath;
	$handle = opendir($rootpath.'/'.$dir);
	
	while (false !== ($file = readdir($handle))){
	    if ($file != '.' and $file != '..' and $file != '.htaccess' and substr($file, -3) != 'gif' and $file != 'tpl'){
	    	$path = $dir.'/'.$file;

	    	if (is_file($path)){
	    		echo '<font color="'.(is_writable($path) ? 'green' : 'red').'">'.$path.'</font><br />';
	    	} else {
	    		echo '<font color="'.(is_writable($path) ? 'green' : 'red').'">'.$path.'/</font><br />';
	    		check_writable($path);
	    	}
	    }
	}
}

exit;
?>
