<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name: 	Emoticons Manager
Description: 	Менеджер смайлов.
Version: 		0.1
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI:     http://lexa.cutenews.ru
*/

add_filter('options', 'emoticons_AddToOptions');

function emoticons_AddToOptions($options){

	$options[] = ['name' => t('Управление смайликами'),'url'  => 'plugin=emoticons', 'category' => 'files'];

return $options;
}

add_action('plugins', 'emoticons_CheckAdminOptions');

function emoticons_CheckAdminOptions(){

	if ($_GET['plugin'] == 'emoticons'){
		emoticons_AdminOptions();
	}
}

function emoticons_AdminOptions(){
global $config, $allowed_extensions;

    $http      = $config['http_script_dir'].'/uploads/emoticons';
    $folder    = cute_parse_url($http);
	$folder    = $folder['abs'];
	$smiles    = explode(',', $config['smilies']);
	$PHP_SELF .= '?plugin=emoticons';

	foreach ($smiles as $k => $v){
		$smiles[$k] = trim($v);
	}

	if ($_FILES['image']['name']){
	    for ($i = 0; $i < count($_FILES['image']['name']); $i++){
	    	if (!$_FILES['image']['error'][$i]){
	            $ext = strtolower(end($ext = explode('.', $_FILES['image']['name'][$i])));

	            if (!file_exists($folder.'/'.$_FILES['image']['name'][$i]) and in_array($ext, $allowed_extensions)){
	                move_uploaded_file($_FILES['image']['tmp_name'][$i], $folder.'/'.$_FILES['image']['name'][$i]);
	            }
	        }
	    }

	    header('Location: '.$PHP_SELF);
	}

	if ($_POST['save']){
		$smiles = [];

		foreach ($_POST['smiles'] as $k => $v){
			$smiles[] = $k;
		}

		$config['smilies'] = @join(', ', $smiles);

        save_config($config);
	    header('Location: '.$PHP_SELF);
	}

	echoheader('images', t('Управление смайликами'));
?>

<form action="<?=$PHP_SELF; ?>" name="upload" method="post" enctype="multipart/form-data">
<b><?=t('Добавить смайликов'); ?></b>
<table border="0" cellpading="0" cellspacing="0" width="300" class="panel">
 <tr>
  <td>

<script language="javascript">
f = 0
function file_uploader(which){
if (which < f) return
    f ++
    d = document.getElementById('image_'+f)
    d.innerHTML = '<input type="file" name="image['+f+']" id="image_'+f+'" value="" onchange="file_uploader('+f+');" /><br /><span id="image_'+(f+1)+'">'
}
document.writeln('<input type="file" name="image[0]" value="" onchange="file_uploader(0);" /><br />')
document.writeln('<span id="image_1"></span>')
</script>

<input type="submit" value="<?=t('Загрузить'); ?>">
</table>
</form>

<br /><br />
<form action="<?=$PHP_SELF; ?>" method="post">
<table width="500" border="0" cellspacing="3" cellpadding="0">
<tr>

<?
	$i = 0;
	$handle = opendir($folder);
	while ($file = readdir($handle)){
		$ext  = strtolower(end($ext = explode('.', $file)));
		$name = preg_replace('/\.'.$ext.'$/i', '', $file);

	    if (in_array($ext, $allowed_extensions)){
	    	$i++;
?>

<td <?=cute_that(); ?> align="center">
<input name="smiles[<?=$name; ?>]" type="checkbox" value="on"<?=(in_array($name, $smiles) ? ' checked' : ''); ?>>
<img src="<?=$http.'/'.$file; ?>" align="absmiddle">
<?=($i%9 == 0 ? '<tr>' : ''); ?>

<?
	    }
	}
?>

<tr>
<td colspan="8">
<input type="submit" value="<?=t('Сохранить'); ?>" name="save">
</table>
</form>

<?php echofooter();
}
?>