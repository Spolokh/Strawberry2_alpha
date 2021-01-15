<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name: 	Подписка на комментарии
Plugin URI:     http://cutenews.ru
Description: 	Отправляет комментария на почту комментатору (человеку оставившему комментарий и подписавшимуся на получение всех дальнейших). Используйте <code>&lt;input type="checkbox" id="sendcomments" name="sendcomments" value="on"&gt;</code> в шаблоне "Форма добавления комментария".
Version: 		1.0
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI:     http://lexa.cutenews.ru
*/

add_filter('allow-add-comment', 'comm_spy');

add_filter('options', 'comm_spy_AddToOptions');
add_action('plugins','comm_spy_CheckAdminOptions');

function comm_spy($output){
global $id, $name, $mail, $comments, $config, $sendcomments;

    $link = parse_url($config['http_home_url']);
    $link = $link['scheme'].'://'.$link['host'].$_SERVER['REQUEST_URI'];

	if ($output){
		$xfields = new XfieldsData();

    	if (preg_match("/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $mail) and !@preg_match("/$mail/i", $xfields->fetch($id, 'comm_spy')) and !@preg_match("/$mail/i", $config['admin_mail']) and $sendcomments == 'on'){
    		$xfields->set($xfields->fetch($id, 'comm_spy').($xfields->fetch($id, 'comm_spy') ? ', ' : '').$name.' <'.$mail.'>', $id, 'comm_spy');
    	   	$xfields->save();
		}

		$tpl = new PluginSettings('CommSpy');

	    if (!$tpl->settings['subj']){
	       	$tpl->settings['subj'] = t('На сайте {page-title} новый комментарий от {author}');
	       	$tpl->save();
	    }

	    if (!$tpl->settings['body']){
	       	$tpl->settings['body'] = t('Здравствуйте.{nl}{nl}Вы подписались на получение новых комментариев с сайта {page-title}. Кто-то оставил там новый комментарий.{nl}{nl}{nl}Комментарий:{nl}----------------{nl}{comment}{nl}{nl}{nl}URL: {link}');
	       	$tpl->save();
	    }

    	        $subj = replace_news('admin', $tpl->settings['subj']);
		$subj = str_replace('{page-title}', $config['home_title'], $subj);
		$subj = str_replace('{page-link}', $config['http_home_url'], $subj);
		$subj = str_replace('{link}', $link, $subj);
		$subj = str_replace('{author}', $name, $subj);
		$subj = str_replace('{mail}', $mail, $subj);
		$subj = str_replace('{mails}', $xfields->fetch($id, 'comm_spy'), $subj);

		$body = replace_news('admin', $tpl->settings['body']);
		$body = str_replace('{page-title}', $config['home_title'], $body);
		$body = str_replace('{page-link}', $config['http_home_url'], $body);
		$body = str_replace('{link}', $link, $body);
		$body = str_replace('{author}', $name, $body);
		$body = str_replace('{mail}', $mail, $body);
		$body = str_replace('{comment}', $comments, $body);
		$body = str_replace('{mails}', $xfields->fetch($id, 'comm_spy'), $body);


		if ($xfields->fetch($id, 'comm_spy')){
			cute_mail($xfields->fetch($id, 'comm_spy'), $subj, $body);
		}

		return true;
	}
}

function comm_spy_AddToOptions($options) {

	$options[] = array(
	             'name'     => t('Шаблон уведомления о комментарии'),
	             'url'      => 'plugin=comm_spy',
	             'category' => 'templates'
	             );

return $options;
}

function comm_spy_CheckAdminOptions() {

	if ($_GET['plugin'] == 'comm_spy'){
		comm_spy_AdminOptions();
	}
}

function comm_spy_AdminOptions(){

    $tpl = new PluginSettings('CommSpy');

    if (!$tpl->settings['subj']){
       	$tpl->settings['subj'] = t('На сайте {page-title} новый комментарий от {author}');
       	$tpl->save();
    }
    if (!$tpl->settings['body']){
    	$tpl->settings['body'] = t('Здравствуйте.{nl}{nl}Вы подписались на получение новых комментариев с сайта {page-title}. Кто-то оставил там новый комментарий.{nl}{nl}{nl}Комментарий:{nl}----------------{nl}{comment}{nl}{nl}{nl}URL: {link}');
       	$tpl->save();
    }

    if ($_GET['action'] == 'save'){
    	$tpl->settings['subj'] = replace_news('add', $_POST['tpl_subj']);
    	$tpl->settings['body'] = replace_news('add', $_POST['tpl_body']);
    	$tpl->save();

    	msg('info', t('Изменения сохранены'), t('Изменения были успешно сохранены'), 'javascript:history.go(-1)');
    }

    $subj = $tpl->settings['subj'];
    $body = $tpl->settings['body'];

    echoheader('options', t('Шаблон уведомления о комментарии'));
?>

<form method="post" action="?plugin=comm_spy&action=save">
<table width="300" border="0" cellspacing="2" cellpadding="2" class="panel">
<tr>
    <td width="85"><b>{page-title}</b>
    <td>&#151; <?=t('название сайта'); ?>
<tr>
    <td><b>{page-link}</b>
    <td>&#151; <?=t('ссылка на сайт'); ?>
<tr>
    <td><b>{link}</b>
    <td>&#151; <?=t('ссылка на новость'); ?>
<tr>
    <td><b>{author}</b>
    <td>&#151; <?=t('автор комментария'); ?>
<tr>
    <td><b>{mail}</b>
    <td>&#151; <?=t('ы-мэйлы автора комментария'); ?>
<tr>
    <td><b>{comment}</b>
    <td>&#151; <?=t('комментарий'); ?>
<tr>
    <td><b>{mails}</b>
    <td>&#151; <?=t('мэйлы подписавшихся'); ?>
</table>

<table width="300" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td><br />
	 <p><?=t('Заголовок:'); ?><br><input type="text" name="tpl_subj" value="<?=htmlspecialchars(replace_news('admin', $subj)); ?>" style="width: 400px;">
	 <p><?=t('Тело:'); ?><br><textarea name="tpl_body" style="width: 400px;"><?=htmlspecialchars(replace_news('admin', $body)); ?></textarea>
</table>
<p><input type="submit" name="submit" value=" Сохранить ">
</form>

<?
	echofooter();
}
?>