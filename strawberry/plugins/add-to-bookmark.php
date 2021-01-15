<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name: 	Bookmarks
Plugin URI:     http://cutenews.ru
Description: 	Добавляет новость в закладки. Используйте <code>$bookmark = true;</code> перед инклудом show_news.php.
Version: 		2.0
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI:     http://lexa.cutenews.ru
*/

add_action('head', 'bookmark');

function bookmark(){
	global $xfields;

    // подключаем "хранилисче" плагинных настроек можно сделать это и в bookmark_check(),
    // но ресурсов сожрётся куда больше ведь это будет делаться для каждого поста
    // А так проверяем: если кем-то другим не вызван - то вызываемсами

    if (!is_object($xfields)){
    	$xfields = new XfieldsData();
    }

    if (isset($_GET['bookmark'])){ 
	// если в УРЛе кто-то указывает bookmark=что-то, то подобная херня идёт лесом
    	$_GET['bookmark'] = '';
    }
}

// добавляем в "конструктор"
add_filter('constructor-variables', 'bookmark_constructor');
function bookmark_constructor($variables){

	$variables['bookmark'] = ['bool', makeDropDown([t('Нет'), t('Да')], 'bookmark')];
	return $variables;
}

// добавляем филтр постов
add_filter('news-where', 'bookmark_check');

function bookmark_check($where){
	global $sql, $xfields, $bookmark;

    if ($bookmark){
	
	    $query = $sql->tableCount('news');
	    
		for ($id = 0; $id < $query; $id++){
	        if ($xfields->fetch($id, 'bookmark') == 'on'){
	        	$found   = true;
	            $where[] = "id = $id";
	            $where[] = 'or';
	        }
	    }

	    if ($found){
	    	$where[sizeof($where) - 1] = 'and';
	    }
	}

return $where;
}

// трём значение переменной для
// избежания проблем с другими
// инклудами show_news.php
add_filter('unset', 'bookmark_unset');

function bookmark_unset($var){

    // имя переменной без знака доллара ($),
    // это важный момент!
	$var[] = 'bookmark';
	return $var;
}

// добавляем форму к добавлению и редактированию постов
add_action('new-advanced-options', 'bookmark_AddEdit', 5);
add_action('edit-advanced-options', 'bookmark_AddEdit', 5);

function bookmark_AddEdit(){
	global $id;
    $xfields = new XfieldsData();
	return '<fieldset id="bookmark"><legend>'.t('Закладки').'</legend><label for="bookmark"><input type="checkbox" id="bookmark" name="bookmark" value="on"'.($xfields->fetch($id, 'bookmark') == 'on' ? ' checked="checked"' : '').'>&nbsp;'.t('Добавить в закладки').'</label></fieldset>';
}

// записываем настройки
add_action('new-save-entry', 'add2bookmark');
add_action('edit-save-entry', 'add2bookmark');

function add2bookmark(){
	global $id;

	$xfields = new XfieldsData(); // Сохраняем настройки

	if ($_POST['bookmark']){ // если $_POST['bookmark'] не пустой - записываем
		$xfields->set($_POST['bookmark'], $id, 'bookmark');
	} else { // если пустой, то удаляем следы
		$xfields->deletefield($id, 'bookmark');
	}

	$xfields->save();
}

// всю жизнь пройдя до половины, я очутился в сумрачном лесу
?>