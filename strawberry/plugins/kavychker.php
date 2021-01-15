<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Кавычкер
Plugin URI:		http://spectator.ru/technology/php/quotation_marks_stike_back
Description:	«Правильные» кавычки.
Version:		1.0
Application: 	Strawberry
Author:			Дмитрий Смирнов
Author URI:		http://nudnik.ru
*/

add_filter('news-entry-content','kavychker', 2000);
add_filter('news-comment-content','kavychker', 2000);

function kavychker($content){ // Смирновский «Кавычкер»

	// Copyright (c) Spectator.ru
	$content = stripslashes($content);

	// замена кавычек в html-тэгах на символ "¬"
	//$content = @preg_replace ( "/<([^>]*)>/es", "'<'.str_replace ('\\\"', '¬','\\1').'>'", $content);

	// замена кавычек внутри <code> на символ "¬"
	//$content = @preg_replace ( "/<code>(.*?)<\/code>/es", "'<code>'.str_replace ('\\\"', '¬','\\1').'</code>'", $content);
	//$content = @preg_replace ( "/<code>(.*?)<\/code>/es", "'<code>'.str_replace ('\\\'', '*¬','\\1').'</code>'", $content);

	// расстановка кавычек: кавычка, перед которой идет ( или > или пробел = начало слова,
	// кавычка, после которой не идет пробел = это конец слова.
	$content = preg_replace ( "[&quot;]", '"', $content);
	$content = preg_replace ( "/([>(\s])(\")([^\"]*)([^\s\"(])(\")/", "\\1«\\3\\4»", $content);

	// что, остались в тексте нераставленные кавычки? значит есть вложенные!
	if (stristr ($content, '"')) {

	// расставляем оставшиеся кавычки (еще раз).
	$content = preg_replace ( "/([>(\s])(\")([^\"]*)([^\s\"(])(\")/", "\\1«\\3\\4»", $content);

	// расставляем вложенные кавычки видим: комбинация из идущих двух подряд открывающихся кавычек без закрывающей
	// значит, вторая кавычка - вложенная. меняем ее и идущую после нее, на вложенную (132 и 147)
	while ( preg_match ("/(«)([^»]*)(«)/", $content)) {
		$content = preg_replace ( "/(«)([^»]*)(«)([^»]*)(»)/", "\\1\\2&#132;\\4&#147;", $content);
	}
	// конец вложенным кавычкам
	}

	// кавычки снаружу
	$content = preg_replace("/\<a\s+href([^>]*)\>\s*\«([^<^«^»]*)\»\s*\<\/a\>/", "&#171;<a href\\1>\\2</a>&#187;", $content);

	// расстанавливаем правильные коды, тире и многоточия

	$trans = [
		"\xAB" => '&laquo;',
		"\xBB" => '&raquo;',
		"\x93" => '&bdquo;',
		"\x94" => '&ldquo;',
		'...'  => '&hellip;',
		'(c)'  => '&copy;',
		'(C)'  => '&copy;',
		'(r)'  => '&reg;',
		'(R)'  => '&reg;',
		'(tm)' => '&trade;',
		'(TM)' => '&trade;',
		"'"    => '&#146;' #апостроф
	];

	//$content = strtr ($content, $trans);
	// тире в начале строки (диалоги)
	$content = preg_replace ('/([>|\s])- /',"\\1&#151; ", $content);

	// меняем  "¬" обратно на кавычки
	$content = str_replace ('*¬','\'', $content);
	$content = str_replace ('¬','"', $content);

	// предлоги вместе со словом (слово не переносится на другую строку отдельно от предлога)
	$content = preg_replace("/ (.) (.)/", " \\1&nbsp;\\2", $content);

	// дефисы
	$content = preg_replace("/(\s[^- >]*)-([^ - >]*\s)/", "\\1-\\2", $content);
	$content = addslashes($content);
	return $content;
}
?>
