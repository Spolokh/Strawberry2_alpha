<?php
/**
 * @package Show
 * @access private
 */

$tpl['form']['saved']['name'] = urldecode($_COOKIE['commentname']);
$tpl['form']['saved']['mail'] = $_COOKIE['commentmail'];
$tpl['form']['saved']['page'] = $_COOKIE['commentpage'];
$tpl['form']['smilies']       = insertSmilies('comments', 0);
$tpl['form']['mail']          = $users[$member['author']]['mail'];
$tpl['form']['page']          = $users[$member['author']]['page'];
$tpl['form']['avatar']        = $config['path_userpic_upload'].'/'.$member['author'].'.'.$users[$member['author']]['avatar'];
//$tpl['form']['about']             = run_filters('news-comment-content', $users[$member['author']]['about']);
//$tpl['form']['lj-username']       = '<a href="http://'.$users[$member['author']]['lj_username'].'.livejournal.com/profile"><img src="'.$config['http_script_dir'].'/skins/images/user.gif" alt="[info]" align="absmiddle" border="0"></a><a href="http://'.$users[$member['author']]['lj_username'].'.livejournal.com">'.$users[$member['author']]['lj_username'].'</a>';
//$tpl['form']['author']            = $users[$member['author']]['name'];
//$tpl['form']['username']          = $users[$member['author']]['username'];
//$tpl['form']['location']          = $users[$member['author']]['location'];
//$tpl['form']['user-id']           = $users[$member['author']]['id'];
//$tpl['form']['icq']               = $users[$member['author']]['icq'];
$tpl['form']                  = run_filters('form-show-generic', $tpl['form']);

ob_start();
include templates_directory.'/'.$template.'/form.tpl';
$output = ob_get_clean();
?>

<div id="comment0"></div>
<form method="post" name="form" id="comment" style="margin:30px 0;">
	<?=$output ?>
	<div id="result">
		<progress style="display:none;" id="progressbar" value="0" max="100"></progress>
	</div>
</form>