<?php
/**
 * @package Show
 * @access private
 */

defined('rootpath') or die('No direct access allowed.');

foreach ($_GET as $k => $v){
	$$k = empty($v) ? $$k : htmlspecialchars($v);
}

foreach ($_POST as $k => $v) {
	$$k = empty($v) ? $$k : htmlspecialchars($v);
}

if (!empty($static) and is_array($static))
{
	foreach ($vars as $k => $v)
	{
		if ($v != 'id' and $v != 'static') {
			unset($$v);
		}
	}

	foreach ($static as $k => $v){
	    $$k = $v;
	}
}

if (empty($sort[0]) || !strstr($sort[1], 'SC')) {
	$sort = ['date', 'DESC'];
}

$link   = $link ?? 'home';
// теперь настраивается из панели (syscon.mdu)
$number = $number ?? $number ?? $config['news_number'] ?? 7;

if (isset($category)) {
	
	$cat_tmps = '';
	if (!strstr($category, ',') and !is_numeric($category)){
		$category = CN::getId($categories, $category);
	}

	foreach (explode(',', str_replace(' ', '', $category)) as $k) {
	    $cat_tmps.= !empty($k) ? CN::GetChildren($categories, $k): '';
	}

	$cat_tmps = CN::ChickenDick($cat_tmps, ',');
	$category = $cat_tmps ?: $category;
}

if ( empty($template) or strtolower($template) == 'default' or is_file( templates_directory .'/'. $template ) or !is_dir( templates_directory .'/'. $template) ){
	$template = 'Default';
}

$cache_uniq 		= md5($cache->touch_this().$REQUEST_URI.$member['usergroup'].$id);

$allow_active_news  = true;
$allow_full_story   = false;
$allow_comment_form = false;
$allow_comments     = false;

if ( empty($static) and isset($post['id']) )
{
	$allow_full_story   = true;
	$allow_active_news  = false;
	$allow_comment_form = true;
	$allow_comments     = true;
	
	if ($post['type'] != '' and $post['type'] != 'blog'){
		$allow_comments  = false;
		$allow_comment_form = false;
   	}
}

if (!$output = $cache->get('show', $cache_uniq)){
	ob_start();
	include includes_directory.'/show.inc.php';
	$output = $cache->put(ob_get_clean());
}

echo $output;

if ($vars = run_filters('unset', $vars))
{
	foreach ($vars as $var){
		unset($$var);
	}
}

unset($cat_tmps, $parent, $no_prev, $no_next, $prev, $var);
