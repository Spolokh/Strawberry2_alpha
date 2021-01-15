<?php
/**
 * @test.ru
 * @package Plugins
 * @access private
 */
 
//attachment
function Attachment2 ($tpl='{file}', $path = '/', $attachment = '') 
{
	global $sql, $post;
	
	if (!$query = $sql->select(['attach', 'where' => ["post = $post[id]"]]))
	{
		return false;
	}
	
	foreach ($query as $row)
	{
		$size = formatsize($row['size']);
		$find = ['{id}', '{type}', '{file}', '{comment}', '{ext}', '{size}', '{path}'];
		$repl = [$row['id'], $row['file'], $row['comment'], $row['ext'], $size, $path];
		$attachment.= str_replace($find, $repl, $tpl);
	}	
	return $attachment;
}


function videoAttachment ($id, $path = '/', $json = []) 
{
	global $sql;
	
	if (!$query = $sql->select(['attach', 'select' => ['id', 'type', 'file', 'comment'], 'where' => ["post = $id"]]))
	{
		return false;
	}
	
	foreach ($query as $row)
	{
		if ($row['type'] == 'video') {
			$json = ['m' => $row['type'], 'uid' => 'attach', 'file' => $row['file'], 'poster' => '', 'comment' => $row['comment']];
		}
	}	
	return $json ? json_encode($json, JSON_UNESCAPED_UNICODE) : '';
}


// XFields self-cleaning
add_action('deleted-single-entry', 'clean_single_xfields');
add_action('deleted-multiple-entries', 'clean_multiple_xfields');

function clean_single_xfields($hook){
	global $row, $id;
	$xfields = new XfieldsData();
	$xfields->delete(($id ? $id : $row['id']));
	$xfields->save();
}

function clean_multiple_xfields($hook) {
	global $selected_news;
	$xfields = new XfieldsData();
	foreach ($selected_news as $id){
		$xfields->delete($id);
	}   $xfields->save();
}

// sticky
add_action('new-advanced-options', 'sticky_AddEdit');
add_action('edit-advanced-options', 'sticky_AddEdit');

function sticky_AddEdit()
{
	global $post, $config;

	$return = '<fieldset id="sticky"><legend>'.t('Закрепить новость').'</legend>
					<label for="sticky_post">
						<input type="checkbox" id="sticky_post" name="sticky_post" value="on"'. ($post['sticky'] ? ' checked' : '') .'>&nbsp;'.t('Закрепить эту новость?').'
					</label>
				</fieldset>';
	if (defined('CUTEDATE')) {
		$return.= '<fieldset id="sticky"><legend>'.t('Периоды вывода новостей').'</legend>
					<label for="sticky_year">
						<input type="radio" id="sticky_year" name="period" value="year"'. ($post['period'] ? ' checked' : '') .'>&nbsp;'.t('Ежегодная новость?').'
					</label>
					<br/>
					<label for="sticky_month">
						<input type="radio" id="sticky_month" name="period" value="month"'. ($post['period'] ? ' checked' : '') .'>&nbsp;'.t('Ежемесячная новость?').'
					</label>
					<br/>
					<label for="sticky_week">
						<input type="radio" id="sticky_week" name="period" value="week"'. ($post['period'] ? ' checked' : '') .'>&nbsp;'.t('Еженедельная новость?').'
					</label>
				</fieldset>';
	} 
	return $return;
}

add_action('new-save-entry', 'sticky_Save');
add_action('edit-save-entry', 'sticky_Save');

function sticky_Save(){
	global $sql;
    $sql->update(['news', 'where' => $_POST['id'], 'values' => ['sticky' => ($_POST['sticky_post'] ? 1 : 0)]]);
}

function cn_meta($k)
{	
	global $config, $post, $row; 
	
	$pattern = ['/\"([^\"]*)\"/' => '«$1»', '/ +/' => ' ', '/[\r\n]+/' => ' ', '/{nl}/' => ' '];
	
	$result  = isset($post[$k]) ? $post[$k] : (isset($row[$k]) ? $row[$k] : $config[$k]);
	$result  = stripslashes($result);
	$result  = preg_replace(array_keys($pattern), array_values($pattern), $result);
	return trim($result);
}

add_action('new-advanced-options', 'rufus_AddEdit', 2);
add_action('edit-advanced-options', 'rufus_AddEdit', 2);

function rufus_AddEdit()
{
	global $post;
	$url = $post['url'] ?? '';

	return '<fieldset id="url">
				<legend>'. t('УРЛ (при желании)') .'</legend>
				<input size="40" type="text" name="url" value="'.$url.'"/>
			</fieldset>';
}

add_action('new-advanced-options', 'image_AddEdit', 3);
add_action('edit-advanced-options', 'image_AddEdit', 3);

function image_AddEdit()
{
	global $post;
	$image = !empty($post['image']) ? $post['image'] : 'default.png';
	$image = !filter_var($image, FILTER_VALIDATE_URL) ? '/'. UPIMAGE .'/posts/'.$image : $image;

	return '<fieldset id="post_image">
				<legend>'. t('Изображение') .'</legend>
				<figure style="width:280px; height:150px;">
					<img id="srcImage" src="'. $image .'" alt="'. $image .'" />
					<output></output>
				</figure>
			</fieldset>';
}

// date
add_action('new-advanced-options', 'date_AddEdit', 1);
add_action('edit-advanced-options', 'date_AddEdit', 1);

function date_AddEdit(){
	global $post, $config;

	$months = [];
	
    for ($i = 1; $i <= 12; $i++){
        $months[date('M', mktime(0, 0, 0, $i, 1))] = ucfirst(langdate('M', mktime(0, 0, 0, $i, 1)));
    }

    $time   = isset($post['date']) ? (int)$post['date'] : time;
    $result = '<fieldset id="date">';
    $result.= '<input type="number" name="day" size="2" maxlength="2" value="'.langdate('d', $time).'" title="'.t('День').'"> ';
    $result.= makeDropDown($months, 'month', date('M', $time));
    $result.= ' <input type="number" name="year" size="4" maxlength="4" value="'.langdate('Y', $time).'" title="'.t('Год').'">';
    $result.= '@<input type="number" name="hour" size="2" maxlength="2" value="'.langdate('H', $time).'" title="'.t('Час').'">';
    $result.= ':<input type="number" name="minute" size="2" maxlength="2" value="'.langdate('i', $time).'" title="'.t('Минута').'">';
    $result.= ':<input type="number" name="second" size="2" maxlength="2" value="'.langdate('s', $time).'" title="'.t('Секунда').'">';
    $result.= '</fieldset>';
	return $result;
}

function date_AddRows($time = time, array $months = [])
{
    for($i = 1; $i <= 12; $i++) {
        $months[date('M', mktime(0, 0, 0, $i, 1))] = ucfirst(langdate('M', mktime(0, 0, 0, $i, 1)));
    }

    $result = '<input type="text" size="2" name="day" maxlength="2" class="day" value="'.langdate('d', $time).'" title="'.t('День').'"> ';
    $result.= makeDropDown($months, 'month', date('M', $time));
    $result.= ' <input type="text" size="4" name="year" maxlength="4" class="year" value="'.langdate('Y', $time).'" title="'.t('Год').'">';
    $result.= '@<input type="text" size="2" name="hour" maxlength="2" value="'.langdate('H', $time).'" title="'.t('Час').'">';
    $result.= ':<input type="text" size="2" name="minute" maxlength="2" value="'.langdate('i', $time).'" title="'.t('Минута').'">';
    $result.= ':<input type="text" size="2" name="second" maxlength="2" value="'.langdate('s', $time).'" title="'.t('Секунда').'">';
    return $result;
}

// usergroups
add_action('new-advanced-options', 'AddEdit_usergroups_check_fields', 1000000);
add_action('edit-advanced-options', 'AddEdit_usergroups_check_fields', 1000000);

function AddEdit_usergroups_check_fields(){
	global $usergroups_check_fields;
	return $usergroups_check_fields;
}

add_action('head', 'head_usergroups_check_fields');

function head_usergroups_check_fields(){
	global $mod, $usergroups_check_fields;

	if ($mod and cute_get_rights($mod, 'read') and ($mod == 'addnews' or $mod == 'editnews')){
		preg_match_all('/fieldset id="(.*?)"><legend>(.*?)<\/legend>/i', run_actions('new-advanced-options'), $fields['new']);
		preg_match_all('/fieldset id="(.*?)"><legend>(.*?)<\/legend>/i', run_actions('edit-advanced-options'), $fields['edit']);

		$fields[1] = array_merge($fields['new'][1], $fields['edit'][1]);
		$fields[1] = array_unique($fields[1]);

		unset($fields[0], $fields['new'], $fields['edit']);
		ob_start();
?>
<script>
<?php foreach ($fields[1] as $k => $v) {
	if (!cute_get_rights($v, 'fields')) { ?>
		$('<?=$v ?>').hide();
	<?php }
	} ?>
</script>
<?php
		$usergroups_check_fields = ob_get_clean();
	} else {
		$usergroups_check_fields = '';
	}
}

// multicats
function multicats($that){
	global $post, $id, $usergroups, $member;

    if ($usergroups[$member['usergroup']]['permissions']['categories'] and !in_array($that, explode(',', $usergroups[$member['usergroup']]['permissions']['categories']))){
        return 'disabled';
    }

	if (isset($post['category']) and in_array($that, explode(',', $post['category']))){
	    return 'checked';
	}
}

add_action('new-advanced-options', 'multicats_AddEdit', 4);
add_action('edit-advanced-options', 'multicats_AddEdit', 4);

function multicats_AddEdit(){
	global $id, $mod, $categories;

	if (count($categories) > 50){
		$style = ' style="overflow:scroll; width:100%; height:200px;"';
	}

	if ($category = category_get_tree('&nbsp; ', '<label style="margin: 0 0 3px" class="option" for="cat{id}">{prefix}<input type="checkbox" [php]multicats({id})[/php] name="cat[{id}]" id="cat{id}"><span class="checkbox"></span> &nbsp;{name}</label><br />', true)){
		return '<fieldset id="category"><legend>'.t('Категория').'</legend>'.$category.'</fieldset>';
	}
}

add_action('new-save-entry', 'multicats_Save', 1);
add_action('edit-save-entry', 'multicats_Save', 1);

function multicats_Save(){
	global $cat, $category;
		if ($cat){
			foreach ($cat as $k => $v){
				$category_tmp[] = $k;
			}   
			$category = join(',', $category_tmp); //join(',', array_keys($cat));
		}
}

// cache_remover
add_action('head', 'cache_remover');
function cache_remover()
{
	global $cache, $id, $is_logged_in, $member;

	if (isset($_GET['action']) and $_GET['action'] == 'clearcache' and $is_logged_in)
	{
    	$cache->delete();
	} 
	elseif (isset($_POST['action']) and $is_logged_in)
	{
		$cache->delete($id);
	}
}

// rufus
add_action('head', 'rufus');
function rufus(){

	global $is_logged_in, $mod, $config;

	if (!$config['mod_rewrite'] and !$mod){
		$urls = parse_ini_file(rufus_file, true);
	    foreach ($urls as $url_k => $url_v) {
	        foreach ($url_v as $k => $v) {
	            @preg_match_all('/'.@str_replace('/', '\/', htaccess_rules_replace($v)).'/i', $_SERVER['REQUEST_URI'], $query);
	            for ($i = 0; $i < count($query); $i++){
	                if ($query[$i][0]){
	                    if ($clear = preg_replace('/(.*?)=\$([0-9]+)/i', '', str_replace('$'.$i, $query[$i][0], str_replace('?', '', htaccess_rules_format($v))))){
	                        $str[] = $clear;
	                    }
	                }
	            }
	        }
	    }

	    if ($str){
	        $str = preg_replace('/([\&]+)/i', '&', join('&', array_reverse($str)));
	        parse_str($str, $_CUTE);

	        foreach ($_CUTE as $k => $v){
	            $GLOBALS[$k] = $_GET[$k] = htmlspecialchars($v);
	        }
	    }
	}
}

add_action('head', 'make_htaccess');

function make_htaccess() {

	global $mod, $PHP_SELF, $config;

	$settings         = cute_parse_url($config['http_home_url']);
	$configs          = cute_parse_url($config['http_script_dir']);
	$types            = parse_ini_file(rufus_file, true);
	$settings['path'] = ($settings['path'] ? '/'.$settings['path'].'/' : '/');
	$configs['path']  = ($configs['path']  ? '/'.$configs['path'].'/'  : '/');
	$uhtaccess        = new	PluginSettings('uhtaccess');
	$htaccess         = [];

	if ($mod and isset($_POST['catid']) and $settings['file'] and $config['mod_rewrite']) {
	    $htaccess[] = '#DirectoryIndex '.$settings['file'];
//	    $htaccess[] = '# [user htaccess] '.$uhtaccess->settings;
		$htaccess[] = 'ErrorDocument 404 /404.php';
	    $htaccess[] = '<IfModule mod_rewrite.c>';
	    $htaccess[] = 'RewriteEngine On';
	    $htaccess[] = '#Options +FollowSymlinks';
	    $htaccess[] = 'RewriteBase '.$settings['path'];

		foreach ($types as $type_k => $type_v)
		{
			foreach ($type_v as $k => $v)
			{
	            $v = preg_replace('/\{(.*?)\:(.*?)\}/i', '{\\1|>|\\2}', $v);
	            $v = parse_url($v);
	            $v = preg_replace('/\{(.*?)\|>\|(.*?)\}/i', '{\\1:\\2}', $v['path']);

	            $htaccess[] = '# ['.$type_k.'] '.$k;
	            $htaccess[] = (!$v ? '# [wrong rule] ' : '');
	            $htaccess[] = 'RewriteRule ^'.(($type_k == 'home' or substr($type_k, 0, 5) == 'home/') ? '' : '').htaccess_rules_replace($v).'(/?)+$ '.htaccess_rules_format($v, ($type_k == 'home' ? $settings['file'] : (substr($type_k, 0, 5) == 'home/' ? substr($type_k, 5).'/' : $configs['path'].$type_k))).' [QSA,L]';
	        }
	    }

		$htaccess[] = 'RewriteRule ^do/([_0-9a-zA-Z-]+)(/?)+$ /strawberry/do.php?action=$1 [QSA,L]';
	    $htaccess[] = '</IfModule>';

		if (!is_writable($settings['abs'].'/.htaccess')){
			chmod($settings['abs'].'/.htaccess', chmod);
		}

		file_write($settings['abs'].'/.htaccess', join("\r\n", $htaccess));
	}
}

add_filter('options', 'rufus_AddToOptions');

function rufus_AddToOptions($options) 
{
	$options[] = ['name' => t('Управление УРЛами'), 'url' => 'plugin=rufus', 'category' => 'options'];
	return $options;
}

add_action('plugins', 'rufus_CheckAdminOptions');

function rufus_CheckAdminOptions()
{
	if (isset($_GET['plugin']) and $_GET['plugin'] == 'rufus')
	{
		rufus_AdminOptions();
	}
}

function rufus_AdminOptions(){
	global $PHP_SELF, $config;

	if ($_POST){
		header('Location: '.$PHP_SELF.'?plugin=rufus');
	}

	$settings         = cute_parse_url($config['http_home_url']);
	$configs          = cute_parse_url($config['http_script_dir']);
	$types            = parse_ini_file(rufus_file, true);
	$settings['path'] = $settings['path'] ? '/'.$settings['path'].'/' : '/';
	$configs['path']  = $configs['path']  ? '/'.$configs['path'] .'/' : '/';
	$uhtaccess        = new PluginSettings('uhtaccess');
	$htaccess		  = [];

	if (!$settings['file']){
		msg('error', t('Управление УРЛами'), t('Извините, но Вы не указали файла, в котором будут отображаться новости или указали неверно. Сделайте это в настройке системы.'));
	}

	echoheader('user', t('Управление УРЛами'));

    if ( !is_writable($settings['abs'].'/.htaccess') ){
        chmod($settings['abs'].'/.htaccess', chmod);
    }

	if (ini_get('safe_mode') and $config['mod_rewrite'] and !is_writable($settings['abs'].'/.htaccess')){
		echo '<div class="panel">'.t('<b style="color: red;">Возможна ошибка</b><br />На Вашем сервере включён Safe Mode. Возможно, не удастся создать фаил .htaccess. На всякий случай, создайте сами .htaccess в директории <b>%directory</b> и поставьте ему права <b>0666</b><br /><br />Затем проверти проставлены ли права на запись для файла <b>data/urls.ini</b>.', array('directory' => $settings['abs'])).'</div><br />';
	}

	$htaccess[] = '#DirectoryIndex '.$settings['file'];
	$htaccess[] = '# [user htaccess] '.$uhtaccess->settings;
	$htaccess[] = '<IfModule mod_rewrite.c>';
	$htaccess[] = 'RewriteEngine On';
	$htaccess[] = '#Options +FollowSymlinks';
	$htaccess[] = 'RewriteBase '.$settings['path'];

    foreach ($types as $type_k => $type_v){
        foreach ($type_v as $k => $v){
        	$v = preg_replace('/\{(.*?)\:(.*?)\}/i', '{\\1|>|\\2}', $v);
	    	$v = parse_url($v);
	    	$v = preg_replace('/\{(.*?)\|>\|(.*?)\}/i', '{\\1:\\2}', $v['path']);

            $htaccess[] = '# ['.$type_k.'] '.$k;
            $htaccess[] = (!$v ? '# [wrong rule] ' : '');
            $htaccess[] = 'RewriteRule ^'.(($type_k == 'home' or substr($type_k, 0, 5) == 'home/') ? '' : '').htaccess_rules_replace($v).'(/?)+$ '.htaccess_rules_format($v, ($type_k == 'home' ? $settings['file'] : (substr($type_k, 0, 5) == 'home/' ? substr($type_k, 5).'/' : $configs['path'].$type_k))).' [QSA,L]';
        }
    }

    $htaccess[] = '</IfModule>';

	echo '<h3 class="panel">'.t('Окно "urls.ini" показывает и даёт возможность настроить вид УРЛов. <a onClick="javascript:Help(\'rufus\')" href="#">О тегах, хитростях и само Писание :) смотрите в тут</a>. После редактирования нажмите "%save".', array('save' => t('Сохранить urls.ini'), 'make' => t('Создать .htaccess'))).'</h3>';
?>

<form action="<?=$PHP_SELF; ?>?plugin=rufus" method="post">
<h3>.htaccess:</h3>
<textarea size="100" rows="10" name="uhtaccess" onkeydown="$('urls').disabled = true;$('htaccess').disabled = false;"><?=$uhtaccess->settings; ?></textarea>
<h3>urls.ini:</h3>
<textarea size="100" rows="10" name="ini_file" onkeydown="$('urls').disabled = false;$('htaccess').disabled = true;"><?=file_read(rufus_file); ?></textarea>
<br /><br />

<input type="submit" name="urls" id="urls" value="  <?=t('Сохранить urls.ini'); ?>  " disabled>
<input type="submit" name="htaccess" id="htaccess" value=" <?=t('Создать .htaccess'); ?> ">
</form>

<?php
	if (isset($_POST['urls']))
	{
		if (!is_writable(rufus_file)) {
			chmod(rufus_file, chmod);
		}

        $uhtaccess->settings = trim($_POST['uhtaccess']);
        $uhtaccess->save();
		file_write(rufus_file, replace_news('admin', $_POST['ini_file']));
	}

	if (isset($_POST['htaccess']))
	{
		if (!is_writable($settings['abs'].'/.htaccess')) {
			chmod($settings['abs'].'/.htaccess', chmod);
		}

		$uhtaccess->settings = trim($_POST['uhtaccess']);
		$uhtaccess->save();
		file_write($settings['abs'].'/.htaccess', $uhtaccess->settings);
	}

	echofooter();
}

function htaccess_rules_replace($output){
	
	global $categories, /*$catalogs,*/ $config;

	if (isset($_POST['catid']) and isset($_POST['categories']))
	{
		$categories[$_POST['catid']]['url']    = $_POST['url'] ? $_POST['url'] : totranslit($_POST['name']);
		$categories[$_POST['catid']]['parent'] = $_POST['parent'];
	}
	
	//if ($_POST['catid'] and $_POST['catalogs']){
	//	$catalogs[$_POST['catid']]['url']    = $_POST['url'] ? $_POST['url'] : totranslit($_POST['name']);
	//	$catalogs[$_POST['catid']]['parent'] = $_POST['parent'];
	//}

	if ($categories and $config['mod_rewrite'])
	{
		foreach ($categories as $k => $row)
		{
			$cat [] = $row['url'];
			$cats[] = !$row['parent'] ? $row['url'] : category_get_link($k);

	        /*if (!$row['parent']){
	            $cats[] = $row['url'];
	        } else {
	            $cats[] = category_get_link($k);
	        }*/
	    }

	    if ($cats){
	        $cats = join('|', $cats);
	        $cats = '(page|'.$cats.')';
	    }
	    if ($cat){
	        $cat = join('|', $cat);
	        $cat = '(page|'.$cat.')';
	    }
		
	} else {
		$cat  = '([_0-9a-z-]+)';
		$cats = '([/_0-9a-z-]+)';
	}
	
	/*if ($catalogs and $config['mod_rewrite']){
	    
		foreach ($catalogs as $k => $row){
	    	$cat_cat[] = $row['url'];

	        if (!$row['parent']){
	            $cats_cat[] = $row['url'];
	        } else {
	            $cats_cat[] = catalog_get_link($k);
	        }
	    }

	    if ($cats_cat){
	        $cats_cat = join('|', $cats_cat);
	        $cats_cat = '(page|'.$cats_cat.')';
	    }

	    if ($cat_cat){
	        $cat_cat = join('|', $cat_cat);
	        $cat_cat = '(page|'.$cat_cat.')';
	    }
		
	} else {
		$cat_cat  = '([_0-9a-z-]+)';
		$cats_cat = '([/_0-9a-z-]+)';
	}*/

    $output = preg_replace('/{(.*?):(.*?)}/i', '{\\1}', $output);
    $output = run_filters('htaccess-rules-replace', $output);
	$output = strtr($output, [
		'{id}'     => '([0-9]+)',
		'{year}'   => '([0-9]{4})',
		'{month}'  => '([0-9]{2})',
//		'{Month}'  => '([0-9a-z]{2,3})',
		'{day}'    => '([0-9]{2})',
		'{title}'  => '([_0-9a-z-]+)',
		'{url}'    => '([_0-9a-z-]+)',
		'{user}'   => '([_0-9a-zA-Z-]+)',
		'{author}' => '([_0-9a-zA-Z-]+)',
		'{action}' => '([_0-9a-zA-Z-]+)',
		'{user-id}' => '([0-9]+)',
		'{cat-id}'  => '([0-9]+)',
		'{category}'   => $cat,
		'{categories}' => $cats,
		'{skip}'  => '([0-9]+)',
		'{type}'  => '(blog|page|poll)',
		'{page}'  => '([0-9]+)',
		'{cpage}' => '([0-9]+)',
		'{add}'	  => ''
	]);

	return $output;
}


function htaccess_rules_format ($output, $result = false)
{
	$output = run_filters('htaccess-rules-format', $output);
//	$output = str_replace('{Month', '{month', $output);
	$output = str_replace('{title}', '{id}', $output);
	$output = str_replace('{url}', '{id}', $output);
	$output = str_replace('{categories', '{category', $output);
	$output = str_replace('{cat-id', '{category', $output);
	$output = str_replace('{type', '{go', $output);
	$output = preg_replace('/{(.*?):(.*?)}/i', '{\\1}{\\2}', $output);
	$output = str_replace('{add}', '', $output);

	preg_match_all('/\{(.*?)\}/i', $output, $array);

	for ($i = 0; $i < count($array[1]); $i++)
	{ 		
		$result .= (!empty($i) ? '&' : '?').(!preg_match('/=/',  $array[1][$i]) ? $array[1][$i].'=$'.($i + 1) : $array[1][$i]);
	}
	return $result;
}

// etc
add_filter('new-advanced-options', 'advanced_options_empty');
add_filter('edit-advanced-options', 'advanced_options_empty');

function advanced_options_empty($story){
	if ($story != 'short' and $story != 'full'){
		return $story;
	}
}

add_filter('news-where', 'hide_open_post');

function hide_open_post($where) {
	global $id, $static;
	
	if (isset($id) and $static['hide_open_post'])
	{
		$where[] = "id != $id";
		$where[] = 'and';
	}   
	return $where;
}
?>
