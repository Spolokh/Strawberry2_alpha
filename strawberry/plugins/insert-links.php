<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name: 	InsertLinks
Plugin URI:     http://cutenews.ru
Description: 	Вставляет ссылки в текст новостей и комментариев.
Version: 		1.0
Application: 	Strawberry
*/

add_filter('news-entry-content', 'InsertLinks');
add_filter('news-comment-content', 'InsertLinks');

function InsertLinks($Text, $hook){

	$NotAnchor = '(?<!"|href=|href\s=\s|href=\s|href\s=)';
	$Protocol = '(http|ftp|https):\/\/';
	$Domain = '[\w]+(.[\w]+)';
	$Subdir = '([\w\-\.,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?';
	$Expr = '/' . $NotAnchor . $Protocol . $Domain . $Subdir . '/i';
	$Result = preg_replace( $Expr, "<a href=\"$0\" target=\"_blank\">$0</a>", $Text );
	$NotAnchor = '(?<!"|href=|href\s=\s|href=\s|href\s=)';
	$NotHTTP = '(?<!:\/\/)';
	$Domain = 'www(.[\w]+)';
	$Subdir = '([\w\-\.,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?';
	$Expr = '/' . $NotAnchor . $NotHTTP . $Domain . $Subdir . '/i';
	$nofollow = ($hook == 'news-comment-content' ? ' rel="nofollow"' : '');
	return preg_replace($Expr, "<a href=\"http://$0\" target=\"_blank\"$nofollow>$0</a>", $Result);
}
?>