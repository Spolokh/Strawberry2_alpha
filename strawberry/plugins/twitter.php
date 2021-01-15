<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name: 	Twitter
Plugin URI:     http://strawberry.goodgirl.ru
Description: 	Постит анонс новости в Twitter в формате "Заголовок URL". Вместо заголовка будет использовано содержимое текстового поля, если оно заполнено. Аутенификация через oAuth.
Version: 		1.0
Application: 	Strawberry
Author: 	Zomb1e
Author URI:     http://www.xcnews.ru
*/

// config
define('POST_URL_LENGTH', 38); // длина URL'a поста; для ЧПУ необходимо использовать сервисы укорачивания ссылок
define('USEFUL_TWIT_LENGTH', 139-POST_URL_LENGTH); // 139 ибо пробел перед ссылкой

// добавляем форму к только к добавлению поста; при редактировании нах не нужно
add_action('new-advanced-options', 'twitter_AddEdit', 3);

function twitter_AddEdit(){
  return '<fieldset id="twitter"><legend>'.t('Запостить в Twitter').'</legend><input type="checkbox" id="twitter" name="twitter">&nbsp;<input type="text" id="twit" name="twit" maxlength="'.USEFUL_TWIT_LENGTH.'"></fieldset>';
}

// записываем настройки
add_action('new-save-entry', 'add2twitter');

function add2twitter(){
	global $config, $id, $title;
	
	if ($_POST['twitter']){ // если $_POST['twitter'] не пустой - постим анонс
		//if ($_POST['twit']){
			$twit_text = $_POST['twit'] ? $_POST['twit'] : $title;
		//}
		//else $twit_text = $title;
		$post_url = $config['http_home_url'].'?id='.$id; // URL поста
		$twit_text = $twit_text.' '.$post_url;

		include_once plugins_directory.'/twitter/EpiCurl.php';
		include_once plugins_directory.'/twitter/EpiOAuth.php';
		include_once plugins_directory.'/twitter/EpiTwitter.php';
		$consumer_key 		= 'jdv3dsDhsYuJRlZFSuI2fg';
		$consumer_secret 	= 'NNXamBsBFG8PnEmacYs0uCtbtsz346OJSod7Dl94';
		$token 				= '25451974-uakRmTZxrSFQbkDjZnTAsxDO5o9kacz2LT6kqEHA';
		$secret				= 'CuQPQ1WqIdSJDTIkDUlXjHpbcRao9lcKhQHflqGE8';
		
		$twitterObj = new EpiTwitter($consumer_key, $consumer_secret, $token, $secret);
		try {
		$status = $twitterObj->post('/statuses/update.json', array('status' => iconv('cp1251', 'utf-8', $twit_text))); }
		catch (EpiTwitterForbiddenException $twiex403) {
		// ничего не делаем; new-save-entry вызывается дважды, на повторе бросаем исключение
		}
	} 
}

// fuck yeah!
?>