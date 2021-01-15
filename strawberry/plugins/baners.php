<?php
/**
 * @package Plugins
 * @access private
 */
/*
Plugin Name:	Baners
Plugin URI: 	http://cutenews.ru/
Description:	Реклама между постами.
Version: 		0.1
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI:     http://lexa.cutenews.ru
*/

add_filter('options', 'baners_AddToOptions');

function baners_AddToOptions($options){

	$options[] = ['name' => t('Рекламные материалы'), 'url' => 'plugin=baners', 'category' => 'tools'];
    return $options;
}

add_action('plugins', 'baners_CheckAdminOptions');

function baners_CheckAdminOptions(){
	if ( isset($_GET['plugin']) and $_GET['plugin'] == 'baners'){
		baners_AdminOptions();
	}
}

function baners_AdminOptions(){

	global $categories, $config, $PHP_SELF;

	$baners = new PluginSettings('Baners');
	$template_position = [
		''     => t('Выбрать расположение'), 
		'news'          => t('В новостной ленте'), 
		'top_banner'    => t('Верхний баннер'), 
		'right_banner'  => t('Правый баннер'), 
		'modal_banner'  => t('Модальное окно'), 
		'bottom_banner' => t('Нижнний баннер')
	];

	$handle = opendir(templates_directory);
	while ( $file = readdir($handle) ) {
		if ($file != '.' and $file != '..' and is_dir(templates_directory.'/'.$file)){
		   $templates[$file] = $file;
		}
	}	

	$directory = UPLOADS.'/Banners';
	$directory = new DirectoryIterator ($directory);

	$banners     = [];
	$banners[''] = t('Имеющиеся банеры');
	
	foreach ($directory as $file) {
		
		if( !$file->isDot() ) { //$file->getExtension()
			if( $file->isFile() and in_array(strtolower($file->getExtension()), explode(', ', $config['type_images_upload']))) {
				$banners[$file->getFilename()] = $file->getFilename();
			}
		}
	}

	if ( isset($_POST['submit']) ){

		$_POST['posts'] = chicken_dick($_POST['posts'], ',');

		if ( !reset($_POST['template']) ){
			unset($_POST['template']);
		}

		if ( !reset($_POST['category']) ){
			unset($_POST['category']);
		}
		
		if ( empty($_POST['image']) ){
			unset($_POST['image']);
		}
		
		unset($_POST['submit']);

		if ($_POST['baner']){
			$baners->settings[$_POST['baner']] = $_POST;
		} elseif (!count($baners->settings)) {
			$baners->settings[1] = $_POST;
		} else {
			$baners->settings[]  = $_POST;
		}

		$baners->save();
		header('Location: '.$PHP_SELF.'?plugin=baners'.($_POST['baner']? '&baner='.$_POST['baner'] : ''));
	}

	if ($_GET['remove']){
		$baners->settings[$_GET['remove']] = null;
		$baners->save();
		header('Location: '.$PHP_SELF.'?plugin=baners');
	}

	if ($baners->settings[$_GET['baner']]){
		$name     = $baners->settings[$_GET['baner']]['name'];
		$posts    = $baners->settings[$_GET['baner']]['posts'];
		$category = $baners->settings[$_GET['baner']]['category'];
		$template = $baners->settings[$_GET['baner']]['template'];
		$position = $baners->settings[$_GET['baner']]['position'];
		$link     = $baners->settings[$_GET['baner']]['link'];
		$image    = $baners->settings[$_GET['baner']]['image'];
		$text     = $baners->settings[$_GET['baner']]['text'];
	}
	
	echoheader('options', t('Управление рекламой'));
?>

<ul id="tabs">
  <li><a href="#new_banner"><?=t(' Создать новый ') ?></a></li>
  <li><a href="#all_banner"><?=t(' Имеющиеся банеры ') ?></a></li>
</ul>

<div id="new_banner" class="tab">
<form action="<?=$PHP_SELF; ?>?plugin=baners" method="post">
 <fieldset>
  <legend style="border: none"> &nbsp; </legend>
	<dl>
		<dt><?=t('Название баннера'); ?></dt>
		<dd><input type="text" size="40" name="name" value="<?=$name; ?>"></dd>
	</dl>
	<dl>
		<dt><?=t('Ссылка на страницу'); ?></dt>
		<dd><input type="text" size="40" name="link" value="<?=$link; ?>"></dd>
	</dl>
	<dl>
		<dt><?=t('Позиция на сайте'); ?></dt>
		<dd><?=makeDropDown($template_position, 'position" onChange="if(this.value == \'news\'){$(\'templates\').show(); $(\'posts\').show(); $(\'categories\').show();} else {$(\'templates\').hide(); $(\'posts\').hide(); $(\'categories\').hide();}', $position); ?></dd>
	</dl>
	<dl id="templates" style="display: none">
		<dt><?=t('Шаблоны для банера'); ?></dt>
		<dd>
			<select size="20" name="template[]">
			<option value="">Выбрать шаблон</option>
			<? foreach ($templates as $k => $v){ ?>
			<option value="<?=$k; ?>"<?=(@in_array($k, $template) ? ' selected' : ''); ?>><?=$v; ?></option>
			<? } ?>
			</select>
		</dd>
	</dl>
   
	<dl id="posts" style="display: none">
		<dt><?=t('Каким постом'); ?></dt>
		<dd><input size="40" type="text" name="posts" value="<?=$posts; ?>">  <small><?=t('(через запятую)'); ?></small></dd>
	</dl>
  
	<dl id="categories" style="display: none">
		<dt><?=t('Категории где будет отображаться банер'); ?></dt>

		<dd>
		<select name="category[]" size="20" multiple="multiple">
		<option value="">...</option>
		<?=category_get_tree('&nbsp;', '<option value="{id}"[php]baners_category_selected({id}, '.($category ? join(',', $category) : 0).')[/php]>{prefix}{name}</option>'); ?>
		</select>
		</dd>

	</dl>
  
  <dl>
     <dt><?=t('Добавить банеры'); ?></dt>
     <dd><?=makeDropDown($banners, 'image', ($_GET['baner'] ? $image : '')); ?>
	 <!--input name="image" type="file" id="uploadbanners" style="display: none"-->
     <br>
	 <input type="checkbox" onclick="$('image').toggle(); $('uploadbanners').toggle();"> <?=t('закачать');?>
	</dd>
    </dl>
  <dl>
     <dt><?=t('Код банера / Описание'); ?></dt>
     <dd><textarea name="text" size="40" style="height:60px;"><?=$text; ?></textarea></dd>
  </dl>
  <dl>
     <dt>&nbsp;</dt>
     <dd><input type="submit" name="submit" value="   <?=($_GET['baner'] ? t('Редактировать') : t('Создать')); ?>   ">
     <input name="baner" type="hidden" value="<?=$_GET['baner']; ?>"></dd>
</dl>
</fieldset>
</form>
</div>

<div id="all_banner" class="tab">
	<br />
<?php if ($baners->settings){ ?>
	<?php foreach ($baners->settings as $k => $row){ ?>
	<li><a href="<?=$PHP_SELF; ?>?plugin=baners&baner=<?=$k; ?>"><?=$row['name']; ?></a>
	<small>(<a href="<?=$PHP_SELF; ?>?plugin=baners&remove=<?=$k; ?>"><?=t('удалить'); ?></a>)</small></li>
	<?php } ?>
<?php } ?>
<br />
</div>
<?php echofooter();
}

add_action('head', 'baners_make_array');

function baners_make_array(){

	global $baners_array;

	$baners = new PluginSettings('Baners');
	$baners_array = $baners->settings;
}

add_filter('news-show-generic', 'baner_after_news');

function baner_after_news($g_tpl){

	global $baners_array, $tpl;
	static $i;
	$i++;
    if ( $baners_array ){
	    foreach ($baners_array as $row){
	        $row['posts'] = explode(',', $row['posts']);

	        if (in_array($i, $row['posts']) and (!$row['template'] or ($row['template'] and in_array($tpl['template'], $row['template'])))){
	            if ($row['category']){
	                if ( in_array(category_get_id($_GET['category']), $row['category']) ){
	                    echo '<div class="baners news">' .$row['text']. '</div>';
	                }
	            } else {
	                echo '<div class="baners news">' .$row['text']. '</div>';
	            }
	        }
	    }
	}  return $g_tpl;
}

function baners_make_design($position, $banner = ''){
	global $config, $baners_array;
	
	if ( !$baners_array ){
		return false;
	}
		
	foreach ($baners_array as $row){
		if( $row['position'] == $position ) {
			$banner.= ($row['link'] ? '<a title="'.strip_tags($row['text']).'" href="'.$row['link'].'" target="_blank">' : '').($row['image']? '<img src="'.$config['path_image_upload'].'/Banners/'.$row['image'].'" alt="'.strip_tags($row['text']).'" />' : $row['text']).($row['link'] ? '</a>' : '');
		}       
	} 
	return $banner;
}

#-------------------------------------------------------------------------------
function baners_category_selected($id, $select){
	if (in_array($id, explode(',', $select))){
		return ' selected';
	}
}
?>