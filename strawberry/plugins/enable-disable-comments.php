<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Enable/Disable Comments
Plugin URI:		http://cutenews.ru/
Description:	Возможность включения/отключения/остановки комментариев для каждой отдельной новости.
Version:		2.0
Application: 	Strawberry
Author: 		&#216;ivind Hoel
Author URI: http://appelsinjuice.org/
*/

# get saved value for an entry (or set default if new)
function edc_getsavedvalue($id){
global $xfields, $endiscomments, $stopcomments;

	$xfields       = $xfields ? $xfields : new XfieldsData();
	$endiscomments = $xfields->fetch($id, 'comments');
	$endiscomments = ($endiscomments != 'no' ? true : false);
	$stopcomments  = $xfields->fetch($id, 'commentsstop');
	$stopcomments  = ($stopcomments == 'on' ? false : true);
	$return        = ['allow' => $endiscomments, 'stop' => $stopcomments, 'edit' => $id];
	return $return;
}

add_action('edit-advanced-options', 'edc_checkbox', 10);
add_action('new-advanced-options', 'edc_checkbox', 10);

function edc_checkbox($hook) {
	global $id, $endiscomments, $stopcomments;

	$value = edc_getsavedvalue($id);

	if ($value['allow']){$checked  = 'checked="checked"';}
	if (!$value['stop']){$checked2 = 'checked="checked"';}

	return '<fieldset id="edcomments"><legend>'.t('Комментарии').'</legend>
			<label for="endiscomments"><input type="checkbox" id="endiscomments" name="endiscomments" value="on" '.$checked.' />&nbsp;'.t('Разрешить комментарии?').'</label>
            <br />
            <label for="stopcomments">
            <input type="checkbox" id="stopcomments" name="stopcomments" value="on" '.$checked2.' />&nbsp;'.t('Остановить комментарии?').'</label>
            </fieldset>';

}

add_action('new-save-entry', 'edc_save');
add_action('edit-save-entry', 'edc_save');

function edc_save(){
global $id;

	$xfields = new XfieldsData();
	$xfields->set(($_POST['endiscomments'] != 'on' ? 'no' : 'on'), $id, 'comments');
	$xfields->set(($_POST['stopcomments'] != 'on' ? 'no' : 'on'), $id, 'commentsstop');
	$xfields->save();
}

add_filter('news-show-generic', 'edc_display');

function edc_display($tpl){
    $cfg = edc_getsavedvalue($tpl['id']);
    if (!$cfg['allow']){
        unset($tpl['comments']);
    }   return $tpl;
}

add_filter('allow-comments', 'edc_comments');
add_filter('allow-comment-form', 'edc_comments');

function edc_comments($comments){
global $post;
    if ($comments){
    	$value    = edc_getsavedvalue($post['id']);
	    $comments = $value['allow'];
	}   return $comments;
}

add_filter('allow-comment-form', 'edc_stopcomments');
function edc_stopcomments($comments){
    if ($comments){
    	$value    = edc_getsavedvalue($_GET['id']);
	    $comments = $value['stop'];
	}   return $comments;
}
?>