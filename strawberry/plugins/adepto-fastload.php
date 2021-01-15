<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Adepto Fastload
Plugin URI: 	http://cutenews.ru/
Description:	Плагин позволяет прикреплять файлы к посту.
Version: 		0.1
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI:     http://lexa.cutenews.ru
*/


//add_filter('edit-advanced-options', 'adepto_list');
//add_filter('new-advanced-options', 'adepto_list');
//add_filter('edit-advanced-options', 'adepto_list');
add_filter('edit-adepto-list', 'adepto_list', 1);

function adepto_list($location) {

	global $id, $post, $config;

    $xfields = new PluginSettings('Adepto_Fastload');

    if(!$xfields->settings['delete_files']) {
		$xfields->settings['delete_files'] = '0';
		$xfields->save();
    }

    if (!$xfields->settings['path_upload']){
		$xfields->settings['path_upload'] = $config['http_script_dir'].'/uploads/attach';
		$xfields->save();
    }

    if (!$xfields->settings['deny_files']){
		$xfields->settings['deny_files'] = '.cgi .pl .shtml .shtm .php .php3 .php4 .php5 .phtml .phtm .phps';
		$xfields->save();
    }

    $attach_directory = cute_parse_url($xfields->settings['path_upload']);
    $attach_directory = $attach_directory['abs'];
    //if ( isset($id) and is_dir($attach_directory.'/'.$id) ) {

	$return = '<div id="adepto_fastload_p_'.$location.'"><a href="javascript:ShowOrHide(\'adepto_fastload_'.$location.'\', \'adepto_fastload_p_'.$location.'\')">'.t('Прикреплённые файлы').'</a></div>';
	$return.= '<ul id="adepto_fastload_'.$location.'" class="adepto_fastload" style="display: none;">';
	$return.= Attachment('<li><a>{path}/{file}</a></li>', $xfields->settings['path_upload']);
	$return.= '</ul>';
	return $return ;
}

add_action('del-files-entry', 'kat_del');

function kat_del(){
	global $sql, $id;

    if (empty($_POST['select_file']) or !is_array($_POST['select_file'])){
		return false;
	}
	
	$xfields = new PluginSettings('Adepto_Fastload'); //$config['http_script_dir'].'/data/attach';
    $attach_directory = cute_parse_url($xfields->settings['path_upload']);
    $attach_directory = $attach_directory['abs'];
	$deleted_files    = 0;

	foreach($_POST['select_file'] as $k){
	
		foreach($sql->select(array('table' => 'attach','where' => array("id = $k"))) as $files);
			
		if (is_dir($attach_directory.'/'.$id)) {
			@unlink($attach_directory.'/'.$id.'/'.$files['file']);
			$sql->delete(array('attach', 'where' => array("id = $k")));
			$deleted_files++;

		}
	}
}

add_action('new-advanced-options', 'adepto_uploader');
add_action('edit-advanced-options', 'adepto_uploader');

function adepto_uploader(){
	ob_start();
?>

<fieldset id="adepto_fastload"><legend><?=t('Прикрепить файлы'); ?></legend>
<script language="javascript">
f = 0
function file_uploader(which){
if (which < f) return
    f ++
    $('file_'+f).update('<input type="file" name="file['+f+']" id="file_'+f+'" value="" onchange="file_uploader('+f+');" /><br /><span id="file_'+(f+1)+'">');
}
document.writeln('<input type="file" name="file[0]" value="" onchange="file_uploader(0);" /><br />')
document.writeln('<span id="file_1"></span>')
</script>
<label for="pack"><input id="pack" name="pack" type="checkbox" value="on">&nbsp;<?=t('Упаковывать простые файлы?'); ?></label>
</fieldset>

<?php

	$return = ob_get_clean();
	return $return;
}

add_action('new-save-entry', 'adepto_save');
add_action('edit-save-entry', 'adepto_save');

function adepto_save() {
	global $id, $sql, $config;
	
	$xfields = new PluginSettings('Adepto_Fastload'); 
	$attach_directory = $xfields->settings['path_upload'].'/'.$id;
	$attach_directory = cute_parse_url($attach_directory);
	$attach_directory = $attach_directory['abs'];   //'folder' => $attach_directory, 

    if ( isset($_FILES['file']['name']) and $_FILES['file']['name'] != '' ){

		$count = count($_FILES['file']['name']);

		for ($i = 0; $i < $count; $i++){ /////////////////////////////////////////////////////////////

			if (!$_FILES['file']['error'][$i]){ ///////////////////////////////////////////////////////////////////////////////
					
				@mkdir($attach_directory, 0777);
					
				$ext = explode('.', $_FILES['file']['name'][$i]);
				$ext = preg_quote(end($ext));

				$filename = @preg_replace('/(.*?).'.$ext.'$/ie', "totranslit('\\1')", $_FILES['file']['name'][$i]).'.'.$ext;
				$filesize = $_FILES['file']['size'][$i]; 

				if ( FALSE !== strpos ($_FILES['file']['type'][$i], "image")) {
					$filetype = 'image';
				}
					
				if ( copy($_FILES['file']['tmp_name'][$i], $attach_directory.'/'.$filename)) {
					
					$values = [
						'post'   => $id, 
						'type'   => $filetype, 
						'file'   => $filename,
						'size'   => $filesize,
						'folder' => 'Gallery',
						'ext' => $ext
					];
					
					$sql->insert(['attach', 'values' => $values]); ////////// добавляем в БД   
				}

				if (!empty($_POST['pack']) and $_FILES['file']['type'][$i] != 'application/x-zip-compressed'){

					include_once includes_directory.'/zipbackup.inc.php';
					$zipfile = new zipfile(); 
					$zipfile->add_file(file_read($attach_directory.'/'.$id.'/'.$filename), $_FILES['file']['name'][$i]);
					unlink($attach_directory.'/'.$id.'/'.$filename);
					@file_write($attach_directory.'/'.$id.'/'.$_FILES['file']['name'][0].'.zip', $zipfile->file());
				} ////////////////////////////////////////////////////////////////////////////////////////////////////////
			}
		} ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	    //if ($_POST['pack']){
	           
	    //}
	}
}

add_filter('news-show-generic', 'adepto_parse');

function adepto_parse($tpl) {

	global $config, $adepto_xfields, $attach_directory;

    if (!is_object($adepto_xfields)){
    	$adepto_xfields = new PluginSettings('Adepto_Fastload');
    	$attach_directory = cute_parse_url($adepto_xfields->settings['path_upload']);
    	$attach_directory = $attach_directory['abs'];
    }

    if ($tpl['id'] and is_dir($attach_directory.'/'.$tpl['id'])){
            $tpl['attachment'] = '<ul class="adepto_fastload">';

    $handle = opendir($attach_directory.'/'.$tpl['id']);
	 
	while ($file = readdir($handle)){
        if ($file != '.' and $file != '..'){
            $ext  = end($ext = explode('.', $file));
            $tpl['attachment'] .= '<li class="'.$ext.'"><a href="'.$adepto_xfields->settings['path_upload'].'/'.$tpl['id'].'/'.$file.'">'.$file.'</a> ('.formatsize(filesize($attach_directory.'/'.$tpl['id'].'/'.$file)).')</li>'; 
        }
	 } 
	 $tpl['attachment'].= '</ul>';
    } return $tpl;
}

add_filter('template-short', 'adepto_vars');
add_filter('template-full', 'adepto_vars');

function adepto_vars($variables){
	$variables['attachment'] = '';
	return $variables;
}

add_filter('options', 'adepto_AddToOptions');

function adepto_AddToOptions($options){

	$options[] = ['name' => t('Adepto Fastload'), 'url' => 'plugin=adepto_fastload', 'category'=> 'files'];
	return $options;
}

add_action('plugins', 'adepto_CheckAdminOptions');

function adepto_CheckAdminOptions(){

	if ( isset($_GET['plugin']) and $_GET['plugin'] == 'adepto_fastload' ){
		adepto_AdminOptions();
	}
}

function adepto_AdminOptions() {

	global $config, $PHP_SELF;

    $xfields = new PluginSettings('Adepto_Fastload');
    $content = "Order Deny,Allow\r\nAllow from all\r\n\r\nAddType text/plain ";

    if ($_POST['save_con']){
    	$htaccess = cute_parse_url($_POST['save_con']['path_upload']);

    	if ($htaccess['abs']){
	    	$xfields->settings = $_POST['save_con'];
	    	$xfields->save();
	    	file_write($htaccess['abs'].'/.htaccess', $content.$_POST['save_con']['deny_files']);
	    }

    	header('Location: '.$PHP_SELF.'?plugin=adepto_fastload');
    }

    if (!$xfields->settings['delete_files']){
       $xfields->settings['delete_files'] = '0';
       $xfields->save();
    }

    if (!$xfields->settings['path_upload']){
       $xfields->settings['path_upload'] = $config['http_script_dir'].'/data/attach';
       $xfields->save();
    }

    if (!$xfields->settings['deny_files']){
       $xfields->settings['deny_files'] = '.cgi .pl .shtml .shtm .php .php3 .php4 .php5 .phtml .phtm .phps';
       $xfields->save();
    }

    $htaccess = cute_parse_url($xfields->settings['path_upload']);
    $htaccess = $htaccess['abs'].'/.htaccess';

    echoheader('options', t('Adepto Fastload')); //onclick="OpenTab('stat', 0); return false;"
    echo '<ul id="tabs">';
    echo '<li><a href="javascript:OpenTab(\'modules\',0)">'.t('настройка модуля').'</a></li>
		  <li><a href="javascript:OpenTab(\'configs\',1)">'.t('конфигурация').'</a></li>';
    echo '</ul>';

    echo '<div class="" id="modules" style="display: block";>'; //open
    echo '<form action="'.$PHP_SELF.'?plugin=adepto_fastload" method="post">';
    echo '<table cellspacing="0" cellpadding="0" width="100%" border="0">';
    showRow(t('Путь к директории загрузки файлов'), t('например: http://example.com/news/data/attach'), '<input type="text" name="save_con[path_upload]" value="'.$xfields->settings['path_upload'].'" size="40">');
    showRow(t('Удаление'), t('удалять файлы при удалении новости'), makeDropDown(array(t('Нет'), t('Да')), 'save_con[delete_files]', $xfields->settings['delete_files']));
    showRow(t('Запрещённые расширения'), t('эти файлы будут интрепритироваться сервером, как обычные текстовые файлы (text/plain); проверьте, есть ли у файла <b><small>%htaccess</small></b> права 0666 или 0777', array('htaccess' => $htaccess)), '<input type="text" name="save_con[deny_files]" value="'.$xfields->settings['deny_files'].'" size="40">');
    echo '</table>';
    echo '<br /><input type="submit" value="'.t('Сохранить').'"></form>';
    echo '</div>'; //closed
    echo '<div class="" id="config" style="";>'; //open
  ?> 
       <!--iframe id="cont" src="<?//=$config['http_script_dir']; ?>/strawberry/plugins/quixplorer/index.php" width="100%" height="0" allowtransparency="true" frameborder="0"></iframe-->
  <?php
    echo '</div>'; //closed
     echofooter();
}

add_action('mass-deleted', 'adepto_delete');

function adepto_delete(){
	global $selected_news;

    $xfields = new PluginSettings('Adepto_Fastload');
    $attach_directory = cute_parse_url($xfields->settings['path_upload']);
    $attach_directory = $attach_directory['abs'];

	if ($xfields->settings['delete_files']){
		
		foreach ($selected_news as $select) {

		    if (is_dir($attach_directory.'/'.$select)){
	            $handle = opendir($attach_directory.'/'.$select);
	            while ($file = readdir($handle)){
	                if ($file != '.' and $file != '..'){
	                    @unlink($attach_directory.'/'.$select.'/'.$file);
	                }
	            }
	        } @rmdir($attach_directory.'/'.$select);
	    }
    }
}
?>