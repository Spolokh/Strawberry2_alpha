<?php
/**
 * @package Private
 * @access private
 */

include_once dirname(__DIR__) . '/strawberry/head.php';

header('Content-type: text/html; charset='.$config['charset']);
header('X-Robots-Tag: noindex');


$request = new classes\Request();

$sessid = $request->Post('session')->toString();
// $sessid = $request->Session($sessid)->toString();


$header = $request->Server('HTTP_X_REQUESTED_WITH')->toString();
$header = strtolower($header)

if (!$header || $header != 'xmlhttprequest')
{
	exit;
}

if (!$sessid || $sessid !== session_id())
{
	exit;
}


$name = $is_logged_in ? $member['name'] : $request->Post('name')->toString();
$mail = $is_logged_in ? $member['mail'] : $request->Post('mail')->toString();
$subject = $subject ? $request->Post('subject')->toString() : t('Cообщение с сайта.');
$comment = $request->Post('comment')->toString();

if ( !$is_logged_in )
{
	if (empty($name))
	{ 
		$errors[] = t('Введите ваше имя.');
	}

	if (empty($mail) or !filter_var($mail, FILTER_VALIDATE_EMAIL))
	{
		$errors[] = t('Извините, этот E-mail неправильный.');
	}

	if (strlen($name) > 50) 
	{ 
		$errors[] = t('Вы ввели слишком длинное имя.');
	}
	
	if (strlen($mail) > 50)
	{
		$errors[] = t('Вы ввели слишком длинный e-mail.');
	}
}

if (empty($comment))
{
	$errors[] = t('Заполните поле "Комментарий".');
}

if ($config['comment_max_long'] and strlen($comment) > $config['comment_max_long'])
{ 	
	$errors[] = t('Вы ввели слишком длинный комментарий.'); // Check the lenght of comment
}

if (reset($errors))
{
    $allow_add_comment = false;
	header("HTTP/1.1 500 Internal Server Error");
	echo join(' ', array_values($errors));
	exit;
}

exit($sessid);



if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) or strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
{
	exit('Ето не наш запрос!');
}

if (!isset($_POST['session'], $_POST['template']))
{
	exit;
}

foreach ($_POST as $k => $v) {
    $$k=htmlspecialchars($v);
}

$errors     = [];
$allow_add_comment = true;

if ($session !== session_id())
{
	exit('Вам тут не надо !');
}

//FILTER_SANITIZE_NUMBER_INT Удаляет все символы, кроме цифр и знаков плюса и минуса.
if ( !$is_logged_in ) {
	
	if (empty($name)) {  //filter_var($name, FILTER_SANITIZE_STRING);
		$errors[] = t('Введите ваше имя.');
	}

	if (empty($mail) or !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
		$errors[] = t('Извините, этот E-mail неправильный.');
	}

	if (strlen($name) > 50) 
	{ 	// Check the lenght of name
		$errors[] = t('Вы ввели слишком длинное имя.');
	}
	
	if (strlen($mail) > 50 )
	{ 	// Check the lenght of mail
		$errors[] = t('Вы ввели слишком длинный e-mail.');
	}
}

//if ($phone and !preg_match('/^[0-9+-]{6,10}$/is', $phone)){
//	$error_message[] = t('Ваш телефон написан неверно.');
//}

if (empty($comment))
{
	$errors[] = t('Заполните поле "Комментарий".');
}

if ($config['comment_max_long'] and strlen($comment) > $config['comment_max_long'])
{ 	
	$errors[] = t('Вы ввели слишком длинный комментарий.'); // Check the lenght of comment
}

if (reset($errors))
{
    $allow_add_comment = false;
	header("HTTP/1.1 500 Internal Server Error");
	echo join(' ', array_values($errors));
	exit;
}

$allow_add_comment or die();

$name    = $is_logged_in ? $member['name'] : replace_comment('add', preg_replace("/\n/", '', $name));
$mail    = $is_logged_in ? $member['mail'] : replace_comment('add', preg_replace("/\n/", '', $mail));
$subject = $subject ? filter_var(preg_replace("/\n/", '', $subject), FILTER_SANITIZE_STRING) : t('Cообщение с сайта.');

//replace_comment('add', preg_replace("/\n/", '', $subject)) : t('Cообщение с сайта.');
$comment = replace_comment('add', $comment);

$template = isset($template) ? $template : 'callback';

//$phone   = replace_comment('add', preg_replace("/\n/", '', $phone));
//http://sms.ru/sms/send?api_id=a0d6135e-5440-0e14-014f-eca334d5665e&to=79037494477&text=hello+world
/*
$ch = curl_init('http://sms.ru/sms/send');
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
curl_setopt ($ch, CURLOPT_POSTFIELDS, [
	'api_id' =>	'a0d6135e-5440-0e14-014f-eca334d5665e',
	'to'	 =>	'79037494477',
	'text'	 =>	iconv($config['charset'], 'utf-8', 'Привет!');
$sms = curl_exec($ch);
curl_close($ch);
*/

ob_start();
include mails_directory.'/'.$template.'.tpl';
$Body = ob_get_clean();

$mailer = new PHPMailer; 
$mailer->From     = $mail;
$mailer->FromName = $name;
$mailer->CharSet  = $config['charset'];
$mailer->Sender   = $mail;
$mailer->Subject  = $subject;
$mailer->Body     = $Body;
//$mailer->AltBody  = 'Добавляем адрес и альтернативный текст'; // Добавляем адрес в список получателей
$mailer->AddAddress( $config['admin_mail'], "Администратору сайта" );
$mailer->AddReplyTo( $mail, $name );
//$mailer->AddCC( $config['admin_mail'], 'Первый копия' );
//$mailer->ConfirmReadingTo = $config['admin_mail']; 
$mailer->IsHTML(true);
$result = $mailer->Send() ? t('Ваше сообщение успешно отправленно!'): $mailer->ErrorInfo; 
$mailer->ClearAddresses(); 
$mailer->ClearAttachments();

unset($mailer);	
exit ($result);
