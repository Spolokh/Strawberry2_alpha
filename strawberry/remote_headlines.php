<?php
/**
 * @package Show
 * @access private
 */

/*
Как использовать этот модуль.
Втсавьте этот код:
<script language="javascript" src="http://example.com/path/to/remote_headlines.php"></script>
Можно использовать разное кол-во новостей:
http://example.com/path/to/remote_headlines.php?number=NUMBER_OF_NEWS
Можно использовать и категории:
http://example.com/path/to/remote_headlines.php?number=NUMBER_OF_NEWS&category=CAT_ID
Шаблон для мода по умолчанию remote_headlines
*/


include_once 'head.php';

add_filter('news-where', 'hidden_news');

function hidden_news($where){

	$where[] = 'hidden = 0';
	$where[] = 'and';

return $where;
}

$template = 'remote_headlines';
$number = ($number ? $number : 7);
include root_directory.'/show_news.php';
?>