<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Custom Quick Tags
Plugin URI:		http://cutenews.ru/cat/plugins/
Description:	Создание собственных тэгов форматирования текста для новостей и комментариев. Для вывода тегов в форме добавления комментариев используйте <code>$tpl['form']['bbcodes']</code>.
Version:		1.0
Application: 	Strawberry
Author:			David Carrington
Author URI:		http://www.brandedthoughts.co.uk
Required Framework: 1.1.2
*/

add_filter('options', 'cqt_AddToOptions');
add_action('plugins','cqt_CheckAdminOptions');

add_filter('news-entry-content','apply_cqt');
add_filter('news-comment-content','apply_cqt');

add_filter('form-show-generic', 'cqt_insertBBCodes');

add_filter('template-form','cqt_macros_variables');

function cqt_macros_variables($output){

	$output['bbcodes'] = t('������� bbCode ��� ������������ ������ � ������������');
	$output['bbcodes()'] = t('������� bbCode ��� ������������ ������ � ������������. ������ ���������� ����� ������� ���������� ������� �� ����� �����: tpl(\'bbcodes\', 1) - ���� ��� �� �����');

return $output;
}

function cqt_insertBBCodes($tpl, $br = 0){

	$cqt = new PluginSettings('Custom_Quick_Tags');

	if ($cqt->settings['tags']){
		$i = 0;
	    foreach($cqt->settings['tags'] as $cqt){
	    	$i++;        
	        $echo['cqt'] .= '<a title="'.$cqt['name'].'" id="'.$cqt['tag']. '" href="javascript:insertext(\'['.$cqt['tag'].']\', \'[/'.$cqt['tag'].']\', \'comments\')"><img src="images/blank.gif"/></a>';    
	    }   
	}

    $tpl['bbcodes'] = $echo['cqt'];
	return $tpl;
}

function BBCodes($br = 0){

	$result = cqt_insertBBCodes('', $br);
	return $result['bbcodes'];
}

function cqt_AddToOptions($options, $hook) {
	// Add a custom screen to the "options" screen
	$options[] = array(
		'name'     => t('���� ��������������'),
		'url'      => 'plugin=cqt',
		'category' => 'templates'
	);
	// return the customized options
	return $options;
}

//
function cqt_CheckAdminOptions($hook) { // chek if the user is requesting the CQT options
	if ($_GET['plugin'] == 'cqt')		// show the CQT admin screen
		cqt_AdminOptions();
}

function cqt_AdminOptions() {
	echoheader('user', t('���� ��������������'));

	$cqt = new PluginSettings('Custom_Quick_Tags');

	switch ($_GET['action']) {
		case 'edit':
			$tag = $cqt->settings['tags'][$_GET['id']];
		case 'add':
			$id = $tag ? '&id='.$_GET['id'] : '';
			$buffer = '
<table cellspacing="0" cellpadding="0">
    <tr>
      <td width="25" align=middle><img border="0" src="skins/images/help_small.gif"></td>
      <td >&nbsp;<a href="http://www.brandedthoughts.co.uk/cutewiki/index.php/Custom%20Quick%20Tags%20Plugin" target="_blank">'.t('������ �� ������ � ������').'</a></td>
    </tr>
	<tr><td>&nbsp;</tr>
   </table>
	<form method="post" action="?plugin=cqt&action=doadd'.$id.'" class="easyform">
		<div>
			<label for="txtName"><b>'.t('�������� ����').'</b></label><br />
			<input id="txtName" name="cqt[name]" value="'.$tag['name'].'" style="width: 400px;" />
		</div><br />
		<div>
			<label for="txtTag"><b>���</b></label><br />
			<input id="txtTag" name="cqt[tag]" value="'.$tag['tag'].'" style="width: 400px;" />
		</div><br />
		<div>
			<label for="txtReplace"><b>'.t('�������� ��...').'</b></label><br />
			<textarea id="txtReplace" name="cqt[replace]" style="width: 400px; height: 150px;">'.$tag['replace'].'</textarea><br />
		</div>
		<div>
			<input type="checkbox" id="txtComplex" name="cqt[complex]"'.($tag['complex'] ? ' checked="checked"' : '').' value="true" style="border: 0px;" />
	        <label for="txtComplex">'.t('�����������').'</label>
		</div><br />
		<input type="submit" value="'.t('���������').'" />
	</form>';
			break;


		case 'delete':
			$tag = $cqt->settings['tags'][$_GET['id']];
			if ($tag[name])
				$buffer = '<p>'.t('��������� ���: <strong>%tag</strong>', array('tag' => $tag[name])).'</p>';
			unset($cqt->settings['tags'][$_GET['id']]);
			$cqt->save();
			break;


		case 'doadd':
			$tag = array(
				'name'	 => stripslashes($_POST[cqt][name]),
				'tag'	 => stripslashes($_POST[cqt][tag]),
				'complex'=> stripslashes($_POST[cqt][complex]),
				'replace'=> stripslashes($_POST[cqt][replace]),
			);

			if ($_GET['id'])
				$cqt->settings['tags'][$_GET['id']] = $tag;
			else
				$cqt->settings['tags'][] = $tag;

			$buffer = '<p>'.t('����������� ���: <strong>%tag</strong>', array('tag' => $_POST[cqt][name])).'</p>';
			$cqt->save();


		default:
			$buffer .= '
		<table border=0 cellpading=2 cellspacing=2 width=100%>
			<tr>
				<td bgcolor=#F7F6F4>&nbsp;<b>'.t('��������').'</b></td>
				<td bgcolor=#F7F6F4><b>'.t('���').'</b></td>
				<td bgcolor=#F7F6F4><b>'.t('���').'</b></td>
				<td bgcolor=#F7F6F4><b>'.t('���').'</b></td>
				<td bgcolor=#F7F6F4><b>'.t('��������').'</b></td>
			</tr>';

			$tags = $cqt->settings['tags'];

			if (empty($tags)) {
				$buffer .= '<td colspan="5">'.t('��� ����� ��������������').'</td>';
			} else
				foreach ($cqt->settings['tags'] as $id => $tag) {
					$buffer .= '
			<tr>
				<td>&nbsp;'.$tag[name].'</td>
				<td>['.$tag[tag].']</td>
				<td>'.($tag[complex] ? t('�����.') : t('�����.')).'</td>
				<td>'.htmlspecialchars($tag[replace]).'</td>
				<td><a href="?plugin=cqt&action=edit&id='.$id.'" title="'.t('�������� ��� ').$tag[tag].'">'.t('���.').'</a> <a href="?plugin=cqt&action=delete&id='.$id.'" title="'.t('������� ��� ').$tag[tag].'">'.t('����.').'</a></td>
				</tr>';
				}

			$buffer .= '
		</table>
		<p><a href="?plugin=cqt&action=add">'.t('�������� ����� ���?').'</a></p>';
	}

	echo $buffer;

	echofooter();
}

function apply_cqt($content, $hook) {
        $cqt = new PluginSettings('Custom_Quick_Tags');
        $tags = $cqt->settings['tags'];
        if (!empty($tags)){ ////////////
           foreach ($tags as $tag){
             if ($tag['complex'] == 'true'){
                 $content = preg_replace('{\['.$tag['tag'].'=([^[]*)\](.*)\[\/'.$tag['tag'].'\]}i',
                 $tag[replace], $content);
             }
                else {
                $tag['arr'] = explode('$1', $tag['replace']);
                $content    = str_replace('['.$tag['tag'].']', $tag['arr'][0], $content);
                $content    = str_replace('[/'.$tag['tag'].']', $tag['arr'][1], $content);
         }
      }
    }
        return $content;
}

?>
