<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Template for alone news
Plugin URI:     http://cutenews.ru
Description:	Новость может быть с любым шаблоном.
Version:		1.1
Application: 	Strawberry
Author:			Лёха zloy и красивый
Author URI:		http://lexa.cutenews.ru
*/

// Cartman find you and kill you!

add_action('new-advanced-options', 'change_template');
add_action('edit-advanced-options', 'change_template');

function change_template(){
global $id;

    $xfields = new XfieldsData();
	$result  = ['' => '...'];
    $handle  = opendir(templates_directory);
    while ($file = readdir($handle)){
        if (file_exists(templates_directory.'/'.$file.'/active.tpl') and file_exists(templates_directory.'/'.$file.'/full.tpl')){
			$result[$file] = $file;
        }
    }

    $result  = '<fieldset id="news_template"><legend>'.t('Шаблон').'</legend>'.makeDropDown($result, 'template', $xfields->fetch($id, 'template')).'</fieldset>';
	return $result;
}

add_action('new-save-entry', 'save_template');
add_action('edit-save-entry', 'save_template');

function save_template(){
global $id;

	$xfields = new XfieldsData();
	$xfields->set($_POST['template'], $id, 'template');
	$xfields->save();
}

add_filter('news-show-generic', 'apply_template');

function apply_template($array){
global $tpl, $row, $xfields, $static;

	if (!is_object($xfields)){
		$xfields = new XfieldsData();
	}

	$template = $xfields->fetch($row['id'], 'template');

    if (
    $template and
    !$static and
    !strstr($_SERVER['PHP_SELF'], 'rss.php')
    and
    !strstr($_SERVER['PHP_SELF'], 'print.php')
    and
    !is_file(templates_directory.'/'.$template)
    and
    is_dir(templates_directory.'/'.$template)
    ){
		$tpl['template'] = $template;
	}

return $array;
}
?>