<?php 
 
include_once 'strawberry/head.php';

empty($config['closed_site']) or exit($cute->msg('Achtung!', $config['closed_text']));

$is_main = empty($_GET) ? true : false;

ob_start();
include themes_directory.DS.$config['theme'].($is_main ? '.index' : '.inner').'.tpl';
$content = ob_get_clean();

$content = str_replace('<!--config:breadcrumbs-->', cn_title(' &raquo; ', false, ($is_main ? 'Первая полоса' : 'Главная')), $content);
$content = str_replace('<!--config:background-->', $config['background']?: '#FFF', $content);
$content = str_replace('<!--banner:bottom-->', plugin_enabled('baners.php')? baners_make_design('bottom_banner'): '', $content); 

if (empty($is_main)) {
	$content = preg_replace('/\[main\](.*?)\[\/main\]/s', '', $content);
}

$content = str_replace('[breadcrumbs]', '', $content);
$content = str_replace('[/breadcrumbs]', '', $content);
$content = str_replace('[main]', '', $content);
$content = str_replace('[/main]', '', $content);

echo $content;

include plugins_directory.DS.'ddb'.DS.'foot.php';
