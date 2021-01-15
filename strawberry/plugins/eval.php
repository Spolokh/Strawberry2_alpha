<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Eval
Plugin URI:     http://www.xs4all.nl/~cvdtak/phpintemplates_1.2.htm
Description:	Плагин позвол¤ет вам выполнять php в новост¤х и шаблонах. Будьте осторожны с плагином!
Application: 	Strawberry
Author:			cvdtak
Author URI:		http://www.xs4all.nl/~cvdtak/
*/

add_filter('news-entry', 'php_in_template');
function php_in_template($output){
   $output = preg_replace_callback('/<\\?php(.*?)\\?>/is', 'phpTemplateEval', $output);
   return $output;
}

#-------------------------------------------------------------------------------
function phpTemplateEval($match){
global $tpl, $users, $usergroups;
	$match[1] = replace_news('admin', $match[1]);
	$match[1] = str_replace('<br />', '', $match[1]);
	ob_start();
	if ($usergroups[$users[$tpl['post']['_']['author']]['usergroup']]['access'] == 'full'){
		eval($match[1]);
	} else {
		echo htmlspecialchars($match[1]);
	}	return ob_get_clean();
}
?>