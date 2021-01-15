<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Old Style Tags
Plugin URI: 	http://cutenews.ru/
Description:	Старые теги типа {imagepath}, [if-logged] и [not-logged].
Version: 		0.1
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI:     http://lexa.cutenews.ru
*/

add_filter('news-entry','CuteNews2LJ_tags');
add_filter('news-comment','CuteNews2LJ_tags');
add_filter('news-entry', 'old_style_tags');

function old_style_tags(){
	global $output, $config, $is_logged_in;

	$output  = str_replace('{imagepath}', $config['path_image_upload'], $output);
	//$output  = str_replace('{imagepath}', $config['path_image_upload'], $output);

	if ($is_logged_in){
		$output  = str_replace('[if-logged]', '', $output);
		$output  = str_replace('[/if-logged]', '', $output);
		$output  = preg_replace('/\[not-logged\](.*?)\[\/not-logged\]/is', '', $output);
		$output  = preg_replace('/\[not-logged\](.*?)\[\/not-logged\]/is', '', $output);
	} else {
		$output  = str_replace('[not-logged]', '', $output);
		$output  = str_replace('[/not-logged]', '', $output);
		$output  = preg_replace('/\[if-logged\](.*?)\[\/if-logged\]/is', '', $output);
		$output  = preg_replace('/\[if-logged\](.*?)\[\/if-logged\]/is', '', $output);
	}

	return $output;
}

function CuteNews2LJ_tags(){
	global $output, $row, $config, $allow_full_story, $xfields;

	if (!is_object($xfields)){
		 $xfields = new XfieldsData();
	}

	$itemid = $xfields->fetch($row['id'], 'itemid');
	$anum   = $xfields->fetch($row['id'], 'anum');
	$itemid = ($anum ? ($itemid * 256 + $anum) : $itemid);
	$output = str_replace('{lj-itemid}', ($itemid ? $itemid : ''), $output);
	$output = preg_replace('/\[lj-link( user=(\\\"|"|\'{0,1})(.*?)(\\2))?\](.*?)\[\/lj-link\]/i', ($itemid ? '<a href="http://\\3.livejournal.com/'.$itemid.'.html">\\5</a>' : ''), $output);
	$output = preg_replace('/<lj (.*?)=(\\\"|"|\'{0,1})(.*?)(\\2)>/i', '<a href="http://\\3.livejournal.com/profile/"><img style="width:17px; height:17px" height="17" width="17" src="'.$config['http_script_dir'].'/skins/images/\\1.gif" align="absmiddle" border="0" alt="[info]"></a><a href="http://\\3.livejournal.com/">\\3</a>', $output);

	preg_match_all('/(<(lj-cut)( text=(\\\"|"|\'{0,1})(.*?)(\\4))?>)(.*?)(<\/\\2>)/i', $output, $matches);

	if ($allow_full_story){
		for ($i = 0; $i < count($matches[7]); $i++){
			$output = str_replace($matches[7][$i], '<a name="cutid'.($i+1).'"></a>'.$matches[7][$i], $output);
		}
	} else {
        for ($i = 0; $i < count($matches[7]); $i++){
        	$output = str_replace($matches[7][$i], '<span class="cutid">(&nbsp;[link=#cutid'.($i+1).']'.($matches[5][$i] ? $matches[5][$i] : 'Read More...').'[/link]&nbsp;)</span>', $output);
        }
	}

	$output = preg_replace('/<lj-cut(.*?)>(.*?)<\/lj-cut>/i', '\\2', $output);
    return $output;
}
?>