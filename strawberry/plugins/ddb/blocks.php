<?php

add_action('head', 'ddb_ob_start', 1);

function ddb_ob_start(){
    ob_start();
}

add_filter('options', 'ddb_AddToOptions');
function ddb_AddToOptions($options) {

	$options[] = ['name' => t('Конструктор'), 'url' => 'plugin=ddb', 'category' => 'templates'];
    return $options;
}

add_action('plugins', 'ddb_CheckAdminOptions');
function ddb_CheckAdminOptions(){
	if ( isset($_GET['plugin']) and $_GET['plugin'] == 'ddb'){
		ddb_AdminOptions();
	}
}

function ddb_AdminOptions(){
	global $PHP_SELF, $QUERY_STRING, $config;

    $self = $PHP_SELF.'?plugin=ddb'.cute_query_string($QUERY_STRING, ['action', 'block', 'category', 'plugin']);
    $arr  = explode('/', $_GET['block'].$_GET['category']);
    $name = end($arr);
    unset($arr[(count($arr) - 1)]);
    $cat  = join('/', $arr);

    ob_start();
    list_directory(blocks_directory, 0, $self);
    $list_directory = ob_get_clean();

    ob_start();
    list_categories(blocks_directory, 0, $cat);
    $list_categories = ob_get_clean();

	if ( $_POST['action'] ) {

		$self.= '&action='.$_POST['action'];

	      if ($_POST['action'] != 'category'){
	        if ($_POST['remove']){
	            @unlink(blocks_directory.'/'.$_POST['block'].'.block');
	            @header('Location: '.$self);
	        }

	        if ($_POST['save']) {

	            if ($_POST['block'] and $_POST['name']){
	                if ($_POST['block'] != ($_POST['cat'] ? $_POST['cat'].'/' : '').$_POST['name']){
	                    @rename(blocks_directory.'/'.$_POST['block'].'.block', blocks_directory.'/'.($_POST['cat'] ? $_POST['cat'].'/' : '').$_POST['name'].'.block');
	                }

	                file_write(blocks_directory.'/'.($_POST['name'] ? ($_POST['cat'] ? $_POST['cat'].'/' : '').$_POST['name'] : $_POST['block']).'.block', $_POST['content']);
	                @chmod(blocks_directory.'/'.($_POST['name'] ? ($_POST['cat'] ? $_POST['cat'].'/' : '').$_POST['name'] : $_POST['block']).'.block', 0777);
	            }

	            if (!$_POST['block']){
	                $_POST['name'] = ($_POST['name'] ? $_POST['name'] : 'noname'.count($blocks));
	                file_write(blocks_directory.'/'.($_POST['cat'] ? $_POST['cat'].'/' : '').$_POST['name'].'.block', $_POST['content']);
	                @chmod(blocks_directory.'/'.($_POST['cat'] ? $_POST['cat'].'/' : '').$_POST['name'].'.block', 0777);
	            }

	            @header('Location: '.$self.'&block='.($_POST['cat'] ? $_POST['cat'].'/' : '').$_POST['name']);
	        }
		} else {
            if ($_POST['remove']){
                remove_directory(blocks_directory.'/'.$_POST['category']);
                @rmdir(blocks_directory.'/'.$_POST['category']);
                @header('Location: '.$self);
            }

			if ($_POST['name']){
			
				if ($_POST['save']){
					if ($_POST['category'] and $_POST['name'] and $_POST['category'] != ($_POST['cat'] ? $_POST['cat'].'/' : '').$_POST['name']){
						rename(blocks_directory.'/'.$_POST['category'], blocks_directory.'/'.($_POST['cat'] ? $_POST['cat'].'/' : '').$_POST['name']);
					}

					if ($_POST['cat']){
						mkdir(blocks_directory.'/'.($_POST['cat'] ? $_POST['cat'].'/' : '').$_POST['name'], 0777);
						chmod(blocks_directory.'/'.($_POST['cat'] ? $_POST['cat'].'/' : '').$_POST['name'], 0777);
					} else {
						mkdir(blocks_directory.'/'.($_POST['cat'] ? $_POST['cat'].'/' : '').$_POST['name'], 0777);
						chmod(blocks_directory.'/'.($_POST['cat'] ? $_POST['cat'].'/' : '').$_POST['name'], 0777);
					}

					header('Location: '.$self.'&category='.($_POST['cat'] ? $_POST['cat'].'/' : '').$_POST['name']);
				}
			}
		}
	}
 	echoheader('options', 'Drag\'n\'Drop Blocks');
?>

<style type="text/css">
.CodeMirror {
	height: auto;
	border: 1px solid #999
}
.cm-mustache {color: #0ca;}
</style>

<ul id="tabs">
	<li><a href="#modules"><?=t('Модули'); ?></a></li>
	<li><a href="#constructor"><?=t('Конструктор'); ?></a></li>
</ul>

<div class="tab" id="modules" style="display: block;">

	<table style="width: 100%;" border="0" cellspacing="1" cellpadding="4">
		<tr valign="top">
			<td style="padding: 10px;">
			<b><a href="<?=$self; ?>&amp;action=category"><?=t('Новая категория'); ?></a></b><br>
			<b><a href="<?=$self; ?>"><?=t('Новый блок'); ?></a></b><br>
				<?=$list_directory; ?>
		 
			<td style="width: 86%; padding: 10px;">
				<form action="<?=$self ?>" method="post">
				<input name="name" type="text" value="<?=$name ?>">

				<?php if($list_categories){ ?>

				<select size="1" name="cat">
					<option value="">...</option>
					<?=$list_categories; ?>
				</select>

			<?php } ?>
			<img border="0" src="skins/images/help_small.gif" align="absmiddle">&nbsp;
			<a onClick="javascript:Help('ddb')" href="#"><?=t('Что такое Drag\'n\'Drop Blocks и с чем их едят?'); ?></a>
<br />
<?php if ($_GET['action'] != 'category'){ ?>
<?=t('<h4>PHP и HTML разрешены. Форматирования, как в новостях нет.</h4>'); ?>

<textarea id="code" name="content"><?= htmlspecialchars(file_read(blocks_directory.DS.$_GET['block'].'.block')) ?></textarea>
<br/>

<input name="block" type="hidden" value="<?=$_GET['block'].$_GET['category']; ?>">
<?php } else { ?>
<?=t('Категории это обычные папки.'); ?><br />
<input name="category" type="hidden" value="<?=$_GET['block'].$_GET['category']; ?>">
<?php } ?>
<input type="submit" name="save" value="<?=t('Сохранить'); ?>">
<?php if ($_GET['block'] or $_GET['category']){ ?>
<input type="submit" name="remove" value="<?=t('Удалить'); ?>">
<?php } ?>
<input name="action" type="hidden" value="<?=$_GET['action']; ?>">
	</form>
 
</table>

</div>

<div class="tab" id="constructor">
	<form id="ddbCall">
		<table border="0" cellspacing="0" cellpadding="4" width="100%">
			<?php foreach (run_filters('constructor-variables', array()) as $k => $v){ ?>
			<tr <?=cute_that(); ?>>
			<td style="padding: 6px">$<?=$k; ?>
			<td>=
			<td><?=$v[1]; ?>
			<?php } ?>
			<tr>
			<td colspan="3">
			<input type="hidden" name="ddb_call" value="1">
			<input type="submit" value=" <?=t('Сгенерировать'); ?> ">
			<tr>
			<td colspan="3">
			<div id="constructorlist"></div>
		</table>
	</form>
</div>

<script src="codemirror/lib/codemirror.js"></script>
<!--script src="codemirror/addon/edit/matchbrackets.js"></script-->
<script src="codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="codemirror/mode/javascript/javascript.js"></script>
<script src="codemirror/mode/clike/clike.js"></script>
<script src="codemirror/mode/xml/xml.js"></script>
<script src="codemirror/mode/css/css.js"></script>
<script src="codemirror/mode/php/php.js"></script>
<script>

var editor = CodeMirror.fromTextArea($('code'), 
{
	mode: "mustache",
	indentUnit: 4,
	lineNumbers: true,
	//matchBrackets: true,
	indentWithTabs: true,
	viewportMargin: Infinity,
	mode: 'application/x-httpd-php'
});

function complete(request){
	if (request.status == 200){
		$('constructorlist').update(request.responseText);
	} else {
		failure(request);
	}
}

function failure (request){
	$('constructorlist').update();
}

	function call_ajax (form) {
		new Ajax.Updater({success: 'constructorlist'}, '<?=$_SERVER['PHP_SELF']; ?>?plugin=ajax&call=ddb', {
			insertion: Insertion.Top,
			onComplete: function(request){complete(request)},
			onFailure: function(request){failure(request)},
			parameters: Form.serialize(form),
			evalScripts: true
		} );
	}

	$('ddbCall').on('submit', function(e) {
		e.stop(); call_ajax(this);
	});
</script>

<?php echofooter();
}

add_action('plugins', 'ddb_call_ajax');

function ddb_call_ajax(){
	global $config;

	//@header('Content-type: text/html; charset='.$config['charset']);

	if (isset($_POST['ddb_call'])){
		$variables = run_filters('constructor-variables', []);
		$result[] = '<?php';

	    foreach ($_POST as $k => $v) {

	        if ($v[0] and $k != 'static'){


	            if ($_POST['static']){
	                $var = 'static[\''.$k.'\']';
	            } else {
	            	$var = $k;
	            }

	            if ($variables[$k][0] == 'array'){
	                $result[] = '$'.$var.' = array(\''.$v[0].'\', \''.$v[1].'\');';
	            } elseif ($variables[$k][0] == 'string'){
	                $result[] = '$'.$var.' = \''.(is_array($v) ? join(', ', $v) : $v).'\';';
	            } elseif ($variables[$k][0] == 'bool'){
	                $result[] = '$'.$var.' = '.($v ? 'true' : 'false').';';
	            } elseif ($variables[$k][0] == 'int'){
	                $result[] = '$'.$var.' = '.$v.';';
	            }
	        }
	    }

	    $result[] = 'include rootpath.\'/show_news.php\';';
	    $result[] = '?>';
	    echo '<textarea>' .join("\r\n", $result). '</textarea>';
	}
}

add_action('head', 'ddb_save_positions');

function ddb_save_positions(){

	if (cute_get_rights('ddb', 'write') and isset($_GET['ddb']) and $_GET['ddb'] == 'save'){
	    $blocks = new PluginSettings('Blocks');
	    $blocks->settings = array_merge($blocks->settings, $_COOKIE['block']);
	    $blocks->save();
	    header('Location: '.str_replace('ddb=save', 'ddb=edit', $_SERVER['REQUEST_URI']));
	}
}

add_filter('help-sections', 'ddb_help');

function ddb_help($help_sections){
global $config;
    $help_sections['ddb'] = t('<h1>Drag\'n\'Drop Blocks</h1><p><a href="%url">см. тутачки</a></p>', array('url' => $config['http_script_dir'].'/docs/additions.html#ddb'));
    return $help_sections;
}

add_filter('constructor-functions', 'default_constructor_functions', 1);

function default_constructor_functions($functions){
return $functions;
}

add_filter('constructor-variables', 'default_constructor_variables', 1);

function default_constructor_variables($variables){
	global $sql, $users;

	$sort[''] = t('- по умолчанию -');

 	foreach ($sql->describe(['news']) as $k => $row){
	    if ($k != 'primary' and $k != 'sticky'){
	        $sort[$k] = $k;
	    }
	} 
	
	$template[''] = t('- по умолчанию -');

	$handle = opendir(templates_directory);
	while ($file = readdir($handle)){
	    if (is_file(templates_directory.'/'.$file.'/active.tpl')){
	        $template[$file] = $file;
	    }
	}

	$link[''] = t('- по умолчанию -');

	foreach (parse_ini_file(rufus_file, true) as $k => $v){
	    $link[$k] = $k;
	}

	$variables['number']   = ['int', ' <input name="number" type="text" value="" style="width: 32px;">'];
	$variables['skip']     = ['int', ' <input name="skip" type="text" value="" style="width: 32px;">'];
	$variables['sort']     = ['array', makeDropDown($sort, 'sort[0]').' '.makeDropDown(['DESC' => 'DESC', 'ASC' => 'ASC'], 'sort[1]')];
	$variables['template'] = ['string', makeDropDown($template, 'template')];
	$variables['link']     = ['string', makeDropDown($link, 'link')];

	ob_start();
?>
	<select name="category[]" size="5" multiple="multiple">
	<option value=""><?=t('- все -'); ?></option>
	<option value="none"><?=t('- новости без категории -'); ?></option>
	<?=category_get_tree('-&nbsp;', '<option value="{id}">{prefix}{name}</option>'); ?>
	</select>
<?php
	$variables['category'] = ['string', ob_get_clean()];
	//$variables['user']     = array('string', makeDropDown($user, 'user'));
	$variables['year']     = ['int', ' <input name="year" type="text" maxlength="4" style="width: 32px;">'];
	$variables['month']    = ['int', ' <input name="month" type="text" maxlength="2" style="width: 32px;">'];
	$variables['day']      = ['int', ' <input name="day" type="text" maxlength="2" style="width: 32px;">'];
	$variables['static']   = ['bool', makeDropDown([t('Нет'), t('Да')], 'static')];

	return $variables;
}

#-------------------------------------------------------------------------------
function ddb_get($output) {

	global $config, $dragdropblocks, $config;
	dragdropblocks(blocks_directory);
	$block = $dragdropblocks['block'];

	preg_match_all('/\<\!--block:(.*?)--\>/i', $output, $matches);
	$matches[1] = array_merge(['blocks'], $matches[1]);
	ob_start();
?>

<script type="text/javascript" src="<?=$config['http_script_dir']; ?>/skins/cute.js"></script>
<script type="text/javascript" src="<?=$config['http_script_dir']; ?>/plugins/ddb/core.js"></script>
<script type="text/javascript" src="<?=$config['http_script_dir']; ?>/plugins/ddb/events.js"></script>
<script type="text/javascript" src="<?=$config['http_script_dir']; ?>/plugins/ddb/tool-man/css.js"></script>
<script type="text/javascript" src="<?=$config['http_script_dir']; ?>/plugins/ddb/coordinates.js"></script>
<script type="text/javascript" src="<?=$config['http_script_dir']; ?>/plugins/ddb/drag.js"></script>
<script type="text/javascript" src="<?=$config['http_script_dir']; ?>/plugins/ddb/dragdrop.js"></script>
<script type="text/javascript" src="<?=$config['http_script_dir']; ?>/plugins/ddb/dragsort.js"></script>
<script type="text/javascript" src="<?=$config['http_script_dir']; ?>/plugins/ddb/cookies.js"></script>
<script>
var junkdrawer = ToolMan.junkdrawer();

window.onload = function(){
<? foreach ($matches[1] as $position){ ?>
	var item = $('<?=$position; ?>');
	if (item){
		DragDrop.makeListContainer(item);
	}
<? } ?>
};

function saveOrder(){
<? foreach ($matches[1] as $position){ ?>
	var item = $('<?=$position; ?>');
	if (item){
		ToolMan.cookies().set('block[<?=$position; ?>]', junkdrawer.serializeList(item), 1);
	}
<? } ?>
	window.location.href = '<?=$_SERVER['PHP_SELF']; ?>?ddb=save';
}
</script>
<style>
dfn {
	position: relative;
	cursor: move;
	font-style: normal;
}

.block {
	border: solid 2px red;
	padding: 3px;
}

.blockheader {
	color: red;
	font-size: 10px;
	font-weight: bold;
}

.dragblock {
	text-align: left;
	color: blue;
	font-size: 10px;
	font-weight: bold;
}
</style>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="block_select">
<tr>
<td>
<div id="blocks">

<?php
	//echodragdropblocks(blocks_directory, 0, $config['http_script_dir']);

	foreach ($block as $k => $v){
	    if ($v){
	        echo '<dfn itemID="'.$k.'"><div class="dragblock">['.$k.']';
	        echo  '['.t('<a href="%url">редактировать</a>', array('url' => $config['http_script_dir'].'?plugin=ddb&action=block&block='.$k)).']';
	        echo '</div></dfn>';
	    }
	}
?>

</div>
<input type="button" value="<?=t('Сохранить'); ?>" onclick="saveOrder()">
</td>
</tr>
</table>
<br />

<?php
	$blocks_header = ob_get_clean();

    if (cute_get_rights('ddb', 'read') and isset($_GET['ddb']) and $_GET['ddb'] == 'edit'){
    	$output = $blocks_header.$output;
    }

	return $output;
}

function list_directory($dir = '.', $level = 0, $self = ''){

	$level++;

	$handle = opendir($dir);
	while ($file = readdir($handle)){
		if ($file != '.' and $file != '..' and is_dir($dir.'/'.$file)){
			$files[] = $file;
        }
	}

	$handle = opendir($dir);
	while ($file = readdir($handle)){
		if ($file != '.' and $file != '..' and is_file($dir.'/'.$file)){
			$files[] = $file;
        }
	}

    if ($files){
    	$filename = preg_replace('/^'.preg_quote(blocks_directory, '/').'/', '', $dir);

	    foreach ($files as $k => $v){
	    	//echo '<ul style="list-style: none">';
	    	//echo ($level > 1 ? str_repeat(' - &nbsp; ', ($level - 1)) : '');

	    	if (is_dir($dir.'/'.$v)){
	            $menu = '<b><a href="'.$self.'&action=category&category='.chicken_dick($filename.'/'.$v).'">'.$v.'</a></b><br />';
	            echo ($_GET['category'] == chicken_dick($filename.'/'.$v) ? strip_tags($menu, '<b>, <br>') : $menu);
	            list_directory($dir.'/'.$v, $level, $self);
	        } else {
	        	$menu = '<span style="display: block; padding:5px;">'.($level > 1 ? str_repeat('-&nbsp;', ($level - 1)) : '').'<a href="'.$self.'&action=block&block='.chicken_dick($filename.'/'.substr($v, 0, -6)).'">'.substr($v, 0, -6).'</a></span>';
	        	echo ($_GET['block'] == chicken_dick($filename.'/'.substr($v, 0, -6)) ? strip_tags($menu, '<b>, <br>') : $menu);
	        }

	        //echo '</ul>';
	    }
	}
}

function list_categories($dir = '.', $level = 0, $category = ''){

	$level++;
	$handle = opendir($dir);
	while ($file = readdir($handle)){
		if ($file != '.' and $file != '..' and is_dir($dir.'/'.$file)){
			$filename = preg_replace('/^'.preg_quote(blocks_directory, '/').'/', '', $dir);
			$filename = chicken_dick($filename.'/'.$file);
			echo '<option value="'.$filename.'"'.($filename == $category ? ' selected' : '').'>'.($level > 1 ? str_repeat('-&nbsp;', ($level - 1)) : '').$file.'</option>';
			list_categories($dir.'/'.$file, $level, $category);
        }
	}
}

function remove_directory($dir = '.'){

	$handle = opendir($dir);
	while ($file = readdir($handle)){
		if ($file != '.' and $file != '..'){
	        if (is_dir($dir.'/'.$file)){
	            @rmdir($dir.'/'.$file);
	            remove_directory($dir.'/'.$file);
	        } else {
	            @unlink($dir.'/'.$file);
	        }
        }
	}
}

function dragdropblocks($dir = '.'){
	global $dragdropblocks;

    $handle = opendir($dir);
    while ($file = readdir($handle)){
        if ($file != '.' and $file != '..'){
            if (is_dir($dir.'/'.$file)){
                dragdropblocks($dir.'/'.$file);
            } elseif (substr($file, -5) == 'block'){
                $filename = preg_replace('/^'.preg_quote(blocks_directory, '/').'/', '', $dir);
                $dragdropblocks['block'][chicken_dick($filename.'/'.substr($file, 0, -6))] = $dir.'/'.$file;
            }
        }
    }
}

function echodragdropblocks($dir = '.', $level = 0, $self = ''){

	$level++;

	$handle = opendir($dir);
	while ($file = readdir($handle)){
		if ($file != '.' and $file != '..' and is_dir($dir.'/'.$file)){
			$files[] = $file;
        }
	}

	$handle = opendir($dir);
	while ($file = readdir($handle)){
		if ($file != '.' and $file != '..' and is_file($dir.'/'.$file)){
			$files[] = $file;
        }
	}

    if ($files){
    	$filename = preg_replace('/^'.preg_quote(blocks_directory, '/').'/', '', $dir);

	    foreach ($files as $k => $v){

	    	if (is_dir($dir.'/'.$v)){
	    		echo '<div class="dragblock" style="margin-left: '.(($level - 1) * 5).';">';
	    		echo '['.makePlusMinus(chicken_dick($filename.'/'.$v)).']';
	    		echo '['.$v.']['.t('<a href="%url">редактировать</a>', array('url' => $self.'/?plugin=ddb&action=category&category='.chicken_dick($filename.'/'.$v))).']';
	    		echo '</div>';
	    		echo '<dfn id="'.chicken_dick($filename.'/'.$v).'" style="display: none;">';
	            echodragdropblocks($dir.'/'.$v, $level, $self);
	            echo '</dfn>';
	        } else {
	        	echo '<dfn itemID="'.chicken_dick($filename.'/'.substr($v, 0, -6)).'" class="dragblock"><div style="margin-left: '.(($level - 1) * 7).';">';
	        	echo '['.substr($v, 0, -6).']['.t('<a href="%url">редактировать</a>', array('url' => $self.'/?plugin=ddb&action=block&block='.chicken_dick($filename.'/'.substr($v, 0, -6)))).']';
	        	echo '</div></dfn>';
	        }
	    }
	}
}
?>
