<?php
/**
 * @package Show
 * @access private
 */

include_once dirname(__DIR__).'/strawberry/head.php';

foreach ( $_POST as $k  => $v ){
	$$k = htmlspecialchars($v);
}

header("Content-type: text/html; charset=$config[charset]");

$errors            = [];
$allow_add_comment = true;
$allow_add_comment = run_filters('allow-add-comment', $allow_add_comment);

$blockip = false; // Check if IP is banned

if ($row = $sql->select(['ipban', 'where' => ["ip = $ip"]]))
{    
	$blockip = true;
    $row = reset($row);
	$sql->update(['ipban', 'where' => ["ip = $ip"], 'values' => ['count' => ($row['count'] + 1)]]);
	
	$row->close();
	unset($row);
}

if ($blockip or !cute_get_rights('comments') and $config['only_registered_comment']){
	$errors[] = t('Извините, но вам запрещено публиковать комментарии.');
}

if (!cute_get_rights('full') and $config['flood_time'] and flooder($ip, $id)){ // Check Flood Protection
	$errors[] = t('Включена защита от флуда! Подождите <b>%time</b> секунд после вашей публикации.', ['time' => $config['flood_time']]);
}

if ($config['only_registered_comment'] and !$is_logged_in){
	$errors[] = t('Извините, только зарегистрированные пользователи могут оставлять комментарии.');
}

if (!$is_logged_in){	

	if (empty($name)){
	    $errors[] = t('Введите ваше имя.');
	}
	   
    if (empty($mail) and $config['need_mail']){ // пробуем другие формы
    	$errors[] = t('Введите e-mail.');
	}

	if (strlen($name) > 50){ // Check the lenght of name
		$errors[] = t('Вы ввели слишком длинное имя.');
	}
	
	if (strlen($mail) > 50){ // Check the lenght of mail
		$errors[] = t('Вы ввели слишком длинный e-mail.');
	}
	
	if ($mail != '' and filter_var($mail, FILTER_VALIDATE_EMAIL) === false) { 	//!preg_match('/^[\.A-z0-9_\-]+[@][\.A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/', $mail)
		$errors[] = t('Извините, этот e-mail неправильный.');
 	}

	if ($page != '' and !filter_var($page, FILTER_VALIDATE_URL)){ 
		//!preg_match('/^(http|https|ftp)+\:\/\/([\.A-z0-9_\-]+)\.([A-z]{1,4})$/', $page)
		$errors[] = t('Извините, этот адрес неправильный.');
    }
	
	foreach ($sql->select(['users', 'select' => ['username', 'mail']]) as $row){
	    if ($name and (strtolower($row['username']) == strtolower($name)) or $mail and strtolower($row['mail']) == strtolower($mail)){
	    	$errors[] = t('Вы используете данные зарегистрированного пользователя, но не зашли в систему.');
	    }
	}
}

if (empty($comments)){
	$errors[] = t('Заполните поле "Комментарий".');
}

if ($config['comment_max_long'] and strlen($comments) > $config['comment_max_long']) { // Check the lenght of comment
	$errors[] = t('Вы ввели слишком длинный комментарий.');
}

if ( reset($errors) ){
    $allow_add_comment = false;
    header('HTTP/1.1 500 Internal Server Error'); //403
	exit (join ( '<br/>', array_values($errors) )) ;
}

if (!$allow_add_comment){
	return;
}

$name     = $is_logged_in ? $member['username'] : replace_comment('add', preg_replace("/\n/", '', $name));
$mail     = $is_logged_in ? $member['mail'] : replace_comment('add', preg_replace("/\n/", '', $mail));
$page     = $is_logged_in ? $member['page'] : replace_comment('add', preg_replace("/\n/", '', $page));
$comments = replace_comment('add', $comments);

//$time = (time() + $config['date_adjust'] * 60);
$values['post_id'] = intval($id);
$values['user_id'] = $is_logged_in ? $member['id'] : 0;
$values['date']    = time;
$values['type']    = isset ($type) ? $type : 'post';
$values['author']  = $name;
$values['mail']    = $mail;
$values['page']    = $page;
$values['comment'] = $comments;
$values['ip']      = $ip;

if ( $parent > 0 ) { 

	$reply  = $sql->select(['comments', 'select' => ['user_id', 'author', 'mail', 'level'], 'where' => $parent]);
	$reply  = reset($reply);
	$parent = (int) $parent;
	
	$values['parent'] = $parent;
	$values['level']  = $reply['level'] + 1;
}

if ($comid = $sql->insert(['comments', 'values' => $values])) // Add the Comment
{ 
	$sql->update(['news', 'where' => $id, 'values' => ['comments' => $sql->count(['comments', 'where' => ["post_id = $id"]])]]);
    $values = [];
}

if ( $config['flood_time'] ) {
    
	$values['post_id'] = $id;
	$values['date'] = time;
	$values['ip'] 	= $ip;
	$sql->insert(['flood', 'values' => $values]);
}

if ($rememberme == 'on'){ 
	$now = (time() + 3600 * 24 * 365);
	cute_setcookie('commentname', urlencode($name), $now, '/');
	cute_setcookie('commentmail', $mail, $now, '/');
	cute_setcookie('commentpage', $page, $now, '/');
} else { 
	$now = (time() - 3600 * 24 * 365);
	cute_setcookie('commentname', '', $now, '/');
	cute_setcookie('commentmail', '', $now, '/');
	cute_setcookie('commentpage', '', $now, '/');
}

//  $comid = $sql->lastInsertId('comments');
if ($parent > 0 and $sendcomments == 'on') {

	if ( $users[$reply['author']] ){
		
		$reply['author'] = $users[$reply['author']]['name'];
		$reply['mail']   = $users[$reply['author']]['mail'];
	}

	if ($reply['mail'] and $reply['mail'] != '' and $reply['author'] != $name and $reply['mail'] != $mail) {
	    
		ob_start();
		include mails_directory.'/reply.tpl';
		$tpl['body'] = ob_get_clean();
		
		preg_match('/Subject:(.*)/i', $tpl['body'], $tpl['subject']);
		preg_match('/Attachment:(.*)/i', $tpl['body'], $tpl['attachment']);
		
		$tpl['body']       = preg_replace('/Subject:(.*)/i', '', $tpl['body']);
		$tpl['body']       = preg_replace('/Attachment:(.*)/i', '', $tpl['body']);
		$tpl['body']       = trim($tpl['body']);
		$tpl['subject']    = trim($tpl['subject'][1]);
		$tpl['attachment'] = trim($tpl['attachment'][1]);
    }
}

if ($config['admin_mail'] and $config['admin_mail'] != $reply['mail'] and $config['admin_mail'] != $mail and $config['send_mail_upon_posting']){

	ob_start();
	include mails_directory.'/new_comment.tpl';
	$tpl['body'] = ob_get_clean();
	
	$tpl['body'] = str_replace('{ip}', $ip, $tpl['body']);
	$tpl['body'] = str_replace('{name}', $name, $tpl['body']);
	$tpl['body'] = str_replace('{mail}', $mail, $tpl['body']);
	$tpl['body'] = str_replace('{page}', $page, $tpl['body']);
	$tpl['body'] = str_replace('{link}', cute_get_link($post), $tpl['body']);
	$tpl['body'] = str_replace('{title}', replace_comment('show', $post['title']), $tpl['body']);
	$tpl['body'] = str_replace('{comments}', str_replace('<br />', "\n", $comments), $tpl['body']);

	preg_match('/Subject:(.*)/i', $tpl['body'], $tpl['subject']);
	preg_match('/Attachment:(.*)/i', $tpl['body'], $tpl['attachment']);

	$tpl['body'] = preg_replace('/Subject:(.*)/i', '', $tpl['body']);
	$tpl['body'] = preg_replace('/Attachment:(.*)/i', '', $tpl['body']);
	$tpl['body'] = trim($tpl['body']);

	$tpl['subject']    = trim($tpl['subject'][1]);
	$tpl['attachment'] = trim($tpl['attachment'][1]);
	
	$mailer = new PHPMailer; 
	$mailer->From    = 'no-reply@'.str_replace(['www.', 'http://'], '', $config['http_script_dir']);
	$mailer->Sender  = $config['admin_mail'];
	$mailer->CharSet = $config['charset'];
	$mailer->Subject = $tpl['subject'];
	$mailer->Body    = $tpl['body'];
	$mailer->AddAddress($config['admin_mail'], $tpl['subject']); // Добавляем адрес в список получателей
	$mailer->IsHTML (false);
//	$mailer->msgHTML($tpl['body']);

	if ($mailer->Send()) {
		$mailer->ClearAddresses();
		unset ($mailer);
	}
}

$tpl['template'] = $template;
$post['id'] = $id;

include_once substr(dirname(__FILE__), 0, -5).'/strawberry/inc/show.comments.php';
