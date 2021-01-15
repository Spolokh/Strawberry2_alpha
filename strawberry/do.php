<?php

include_once 'head.php';

switch($REQUEST_URI)
{
	case "/do/form" :
		$result = (new classes\ModalForm($config))->run(); //LoginForm
	break;
		
	case "/do/login" : 

		ob_start();
        include forms_directory.'/LoginForm.tpl';
        $result = ob_get_clean();
	break;
		
	default : 
		header('HTTP/1.1 404 Not Found');
        exit;
	break;
}

exit($result);

//exit;
$header = $_SERVER['HTTP_X_REQUESTED_WITH'];

if (!$header or strtolower($header) != 'xmlhttprequest')
{
	exit("Вам тут не надо!");
}

$module = 'guest';
$action = array_key_exists('action', $_POST) ? $_POST['action'] : null;

if (isset($action) and $action == $module)
{
	foreach ($_POST as $k => $v)
	{
		$$k= trim(htmlspecialchars($v));
	}

	if (empty($name))
	{
		$errors[] = t('Заполните ваше имя.');
	}
 
	if (empty($phone))
	{
		$errors[] = t('Заполните ваш телефон.');
	}

	if (!preg_match('/^(\s*)?(\+)?([- _():=+]?\d[- _():=+]?){10,14}(\s*)?$/', $phone))
	{
		$errors[] = t('Укажите корректный телефон.');
	}

	if (reset($errors))
	{
		echo join(' ', $errors);
		header("HTTP/1.1 500 Internal Server Error");
		exit;
	}

	$mailer = new PHPMailer; 
	$mailer->From    = 'no-reply@'.str_replace(['www.', 'http://'], '', $config['http_script_dir']);
	$mailer->CharSet = $config['charset'];
	$mailer->Subject = 'Добавляем адрес в список получателей';
	$mailer->Body    = "Сообщение с сайта $phone";
	$mailer->AddAddress($config['admin_mail']); // Добавляем адрес в список получателей

	if ($mailer->Send()) {
		$mailer->ClearAddresses();
		unset ($mailer);
	}
	
	exit("Ваше сообщение успешно отправленно!");
}

// directory
$template = templates_directory . '/Forms/';
$template = (new Template ($template)) ->open('requestForm', $module);

echo $template->compile($module, true);
$template ->fullClear();


/*

# [home] post

RewriteRule ^(page|news|article|review|art|music|art/cinema|pressa|analitics|files|yumor|news/serv_news|news/runet|clips|news/runet/novosti-es)/(|[_0-9a-z-]+).html(/?)+$ index.php?category=$1&id=$2 [QSA,L]
# [home] blog

RewriteRule ^blog/(|[_0-9a-z-]+).html(/?)+$ index.php?go=blog&id=$2 [QSA,L]
# [home] category

RewriteRule ^(page|news|article|review|art|music|art/cinema|pressa|analitics|files|yumor|news/serv_news|news/runet|clips|news/runet/novosti-es)(/?)+$ index.php?category=$1 [QSA,L]
# [home] author

RewriteRule ^author/([_0-9a-zA-Z-]+)(/?)+$ index.php?author=$1 [QSA,L]
# [home] user

RewriteRule ^users/([_0-9a-zA-Z-]+).html(/?)+$ index.php?user=$1 [QSA,L]
# [home] day

RewriteRule ^([0-9]{4})/([0-9]{2})/([0-9]{2})(/?)+$ index.php?year=$1&month=$2&day=$3 [QSA,L]
# [home] month

RewriteRule ^([0-9]{4})/([0-9]{2})(/?)+$ index.php?year=$1&month=$2 [QSA,L]
# [home] year

RewriteRule ^([0-9]{4})(/?)+$ index.php?year=$1 [QSA,L]
# [home] keywords
# [wrong rule] 
RewriteRule ^(/?)+$ index.php [QSA,L]
# [home] skip
# [wrong rule] 
RewriteRule ^(/?)+$ index.php [QSA,L]
# [home] do

RewriteRule ^do/([_0-9a-zA-Z-]+)(/?)+$ index.php?action=$1 [QSA,L]
# [home] page
# [wrong rule] 
RewriteRule ^(/?)+$ index.php [QSA,L]
# [home] cpage
# [wrong rule] 
RewriteRule ^(/?)+$ index.php [QSA,L]
# [home] doIt

RewriteRule ^(do|users|video|blog|mail|profile|registration|fave|keywords)(/?)+$ index.php?go=$1 [QSA,L]
# [do.php] action

RewriteRule ^do/([_0-9a-zA-Z-]+)(/?)+$ /do.php?action=$1 [QSA,L]
# [rss.php] post
# [wrong rule] 
RewriteRule ^(/?)+$ /rss.php [QSA,L]
# [rss.php] category
# [wrong rule] 
RewriteRule ^(/?)+$ /rss.php [QSA,L]
# [rss.php] user
# [wrong rule] 
RewriteRule ^(/?)+$ /rss.php [QSA,L]
# [rss.php] feed

RewriteRule ^rss.xml(/?)+$ /rss.php [QSA,L]
# [print.php] post
# [wrong rule] 
RewriteRule ^(/?)+$ /print.php [QSA,L]
# [trackback.php] post
# [wrong rule] 
RewriteRule ^(/?)+$ /trackback.php [QSA,L]
RewriteRule ^do/([_0-9a-zA-Z-]+)(/?)+$ /strawberry/do.php?action=$1 [QSA,L]
</IfModule>
*/