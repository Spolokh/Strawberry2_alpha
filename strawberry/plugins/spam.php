<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name: 	Фильтры плохеньих слов
Plugin URI:     http://cutenews.ru
Description: 	Чтоб можно было смело сказать, разорвав рубаху на груди: "не ругайси, дурачок!".
Version: 		0.1
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI: 	http://lexa.cutenews.ru
*/

add_filter('allow-add-comment', 'spam_filter');

add_filter('options', 'spam_AddToOptions');
add_action('plugins', 'spam_CheckAdminOptions');

function spam_filter($allow){
global $name, $mail, $comments;

    $barword = new PluginSettings('BarWord');

    if (!$barword->settings){
    	return ($allow ? true : false);
    }

	if ($comments){
	    foreach($barword->settings as $bad){
			if (preg_match('/'.preg_quote($bad, '/').'/i', strtolower($comments))){$allow = false;}
	    }
	}

return ($allow ? true : false);
}

function spam_AddToOptions($options){
global $PHP_SELF;

	$options[] = array(
	             'name'     => t('Фильтры слов'),
	             'url'      => 'plugin=spam',
	             'category' => 'tools'
	             );

return $options;
}

function spam_CheckAdminOptions(){
	if ($_GET['plugin'] == 'spam'){spam_AdminOptions();}
}

function spam_AdminOptions(){
global $PHP_SELF;

	echoheader('options', t('Фильтры слов'));

    $barword = new PluginSettings('BarWord');

	$buffer = '<table border=0 cellpading=0 cellspacing=0 width="645">
			  <table border=0 cellpading=0 cellspacing=0 width="645" >
	          <form method=post action="'.$PHP_SELF.'?plugin=spam">
	          <td width=321 height="33"><b>'.t('Ввести слово').'</b>
	          <table border=0 cellpading=0 cellspacing=0 width=379  class="panel" cellpadding="7" >
	          <tr>
	          <td width=79 height="25">&nbsp;'.t('Слово:').'
	          <td width=300 height="25">
	          <input type="text" name="add_badword">&nbsp;&nbsp;<input type="submit" value="'.t('Запретить').'">
	          </tr>
	          </form>
	          </table>

    <tr>
    <td width=654 height="11">
        <img height=20 border=0 src="skins/images/blank.gif" width=1>
    </tr><tr>
    <td width=654 height=14>
    <b>'.t('Заблокированные слова').'</b>
    </tr>
    <tr>
    <td width=654 height=1>
  <table width=641 height=100% cellspacing=2 cellpadding=2>
    <tr>
      <td width=260 class="panel"><b>Слово</b></td>
      <td width=140 class="panel">&nbsp;<b>'.t('Действие').'</b></td>
    </tr>';

    if ($words = $barword->settings){
	    foreach($words as $key => $bad){
	        $i++;
	        if ($i%2 == 0){$bg = ' class="enabled"';}
	        else {$bg = ' class="disabled"';}

	        if ($bad){$buffer .= '<tr'.$bg.'><td>'.$bad.'<td><a href="'.$PHP_SELF.'?plugin=spam&action=remove&id='.$key.'">'.t('[разблокировать]').'</a>';}
	    }
	}

	$buffer .= '</table></table>';

	if ($_POST['add_badword']){
		$barword -> settings[] = strtolower($_POST['add_badword']);
		$barword -> save();

		$buffer = t('Слово успешно заблокировано!').'<br><br><a href="'.$PHP_SELF.'?plugin=spam">'.t('Вернуться назад').'</a>';
	}

	if ($_GET['action'] == 'remove'){
		unset($barword -> settings[$_GET['id']]);
		$barword -> save();

		$buffer = 'Слово успешно разблокировано!<br><br><a href="'.$PHP_SELF.'?plugin=spam">Вернуться назад</a>';
	}

    echo $buffer;

	echofooter();
}
?>