<?php
/**
 * @package Plugins
 */

/*
Plugin Name:Keywords
Plugin URI :http://cutenews.ru
Description:Ключевые слова новости. Выводить так: <code>&lt;?=cn_keywords(); ?&gt;</code>.
Version    :1.0
Application:Strawberry
Author     :Лёха zloy и красивый
Author URI :http://lexa.cutenews.ru
*/
add_action('head', 'keywords');

/**
 * @access private
 */
function keywords() {

	global $sql, $cache, $_keywords;

	if (!$sql->tableExists('keywords'))
	{	
		$rufus = parse_ini_file(rufus_file, true);
		$rufus['home']['keywords'] = '?keywords={keywords-id}';
		write_ini_file(rufus_file, $rufus);

		$sql->createTable(['keywords', 'columns' =>	[
			'id' 	=> ['type' => 'int', 'auto_increment' => 1, 'primary' => 1], 
			'name' 	=> ['type' => 'string'], 
			'url' 	=> ['type' => 'string', 'permanent' => 1]
	    ]]);

		$sql->alterTable(['news', 'name' => 'tags', 'action' => 'insert', 'values' => ['type' => 'string']]);

        return false;
    }

	if (!$_keywords = $cache->unserialize('_keywords'))
	{
		foreach ($sql->select(['keywords', 'orderby' => ['name', 'ASC']]) as $row){
	        $_keywords['id'][$row['name']] = $row['id'];
	        $_keywords['id'][$row['url']]  = $row['id'];
	        $_keywords['id'][$row['id']]   = $row['id'];
	        $_keywords['name'][$row['id']] = $row['name'];
	        $_keywords['url'][$row['id']]  = $row['url'];
		}   
		$_keywords = $cache->serialize($_keywords);
	}
}

add_filter('options', function ($options){
	$options[] = ['name' => t('Ключевые слова'), 'url' => 'plugin=keywords', 'category' => 'plugins'];	
	return $options;
});

/**
 * @access private
 */
 
add_filter('news-where', function ($where) {
	global $keywords, $_keywords;

    if (isset($keywords) and $keywords != 'none'){

		foreach (explode(',', $keywords) as $k){
            $keywords_tmp.= $_keywords['id'][trim($k)].',';
        }
	
	    if ( chicken_dick($keywords, ',') ){
	        $where[]  = 'tags ? ['.str_replace(',', '|', chicken_dick($keywords_tmp, ',')).']';
			$where[]  = 'and';
	    } else {
    		$where = ['id = 0', 'and'];
    	}
    }	return $where;
});

add_filter('unset', function ($var){
   $var[] = 'keywords';
   return $var;
});


/**
 * @access private
 */
add_filter('constructor-functions', function($functions){
    $functions['cn_keywords']       = ['string'];
    $functions['cute_get_keywords'] = ['string'];
    return $functions;
});

add_filter('constructor-variables', 'keywords_constructor_variables');

/**
 * @access private
 */
function keywords_constructor_variables($variables){
	global $sql;
	ob_start();
?>
	<select name="keywords[]" size="5" multiple="multiple">
	<option value="" <?=(!$_POST['keywords'][0] ? 'selected' : ''); ?>><?=t('- все -'); ?></option>
	<?php foreach ($sql->select(['keywords', 'select' => ['id', 'name'], 'orderby' => ['id', 'ASC']]) as $row){ ?>
	<option value="<?=$row['id']; ?>" <?=(isset($_POST['keywords']) and in_array($row['id'],  $_POST['keywords'])) ? 'selected' : ''?>><?=$row['name']; ?></option>
	<?php } ?>
	</select>
<?php
	$variables['keywords'] = ['string', ob_get_clean()];
	return $variables;
}


add_action('new-advanced-options', 'keywords_AddEdit');
add_action('edit-advanced-options', 'keywords_AddEdit');

/**
 * @access private
 */
function keywords_AddEdit(){
	global $id, $config, $PHP_SELF;
	
	$keywords = cute_get_keywords('<li><label class="option" for="key{id}"><input type="checkbox" [php]keywords_select({id})[/php] name="key[{id}]" id="key{id}"><span class="checkbox"></span> &nbsp; {name}</label> 
	<sup><a href="#" title="'.t('Удалить кейворд "%keyword"', ['keyword' => '{name}']).'" onclick="remove_keywords_call_ajax({id});return false;">x</a></sup>');
	ob_start();
?>

<fieldset id="keywords"><legend><?=t('Ключевые слова'); ?></legend>
<ul id="keywordslist"><?=$keywords; ?></ul>
</fieldset>
<fieldset id="add_keywords"><legend><?=t('Добавить ключевые слова'); ?></legend>
<textarea size="40" id="keywords_add" name="keywords_add" title="<?=t('Дабавить кейвордов, одна строка - один кейворд'); ?>"></textarea>
<br />
<input type="button" id="add_keywordsButton" value="  <?=t('Добавить'); ?>  " />
</fieldset>

<script>
	
	$("add_keywordsButton").on('click', add_keywords_call_ajax);
	
	function keywords_complete(data){
		if (data.status == 200){
			$('keywordslist').update(data.responseText);
			$('keywords_add').setValue('');
		} else {
			keywords_failure(data);
		}
	}

	function keywords_failure(data){
		 alert ("Заполните все нужные поля !");
	}

	function add_keywords_call_ajax(){
	
	    var pars = $H({action: 'add', keywords_add: $('keywords_add').value}).toQueryString();
		new Ajax.Updater({success: 'keywordslist'}, '<?=$PHP_SELF?>?plugin=keywords', {
			insertion : Insertion.Top,
			onComplete: function(data){keywords_complete(data)},
			onFailure :  function(data){keywords_failure(data)},
			parameters: pars,
			evalScripts: true
		});
	}

	function remove_keywords_call_ajax(id)
	{
		var parameters = $H({action: 'remove', keyid: id}).toQueryString();
		new Ajax.Updater({success: 'keywordslist'},	'<?=$PHP_SELF?>?plugin=keywords',
		{
			insertion : Insertion.Top,
			onComplete: function(request){keywords_complete(request)},
			onFailure :  function(request){keywords_failure(request)},
			parameters: parameters,
			evalScripts: true
		});
	}
</script>

<?php return ob_get_clean();
}
 
add_action('plugins', function () {
	
	if (isset($_GET['plugin']) and $_GET['plugin'] == 'keywords') {
		
		if (isset($_POST['action']) and $_POST['action'] == 'add'){
			keywords_Ajax_add();
		} elseif
		   (isset($_POST['action']) && $_POST['action'] == 'remove'){
			keywords_Ajax_remove();	
		} else {
			keywords_AdminOptions();
		}
	}
});

/**
 * @access private
 */

function keywords_AdminOptions(){
    
	global $member, $REQUEST_URI;
	echoheader('options', t('Ключевые слова'));
	
	if (!$keywords = cn_keywords('<li><span class="Id">{id}</span> 
		<a data-id="{id}" id="{url}" href="#"> &nbsp; {name}</a> 
			<span class="read"><a class="green icon icon-ok" href="#"></a></span>
	</li>')) $keywords = '<h4>'.t('-- Ключевых слов нет --').'</h4>'; ?>
	
	<ul id="list" class="list">
		<?=$keywords;?>
	</ul>
	
	<form id="keywordsAdd" method="POST">
		<input type="text" id="keyword" name="keywords_add" placeholder="<?=t('Добавить')?>"/> 
		<input type="submit" id="submit" name="tojson" value=" OK "/>
		<input type="hidden" id="action" name="action" value="add"/>
		<div id="result"></div>
	</form>

<script>
$('keywordsAdd').on('submit', function(e)
{
	var html = '';
	var form = this;
	var template = '<li><span class="Id">#{id}</span> <a data-id="#{id}" id="#{url}" href="#"> &nbsp; #{name}</a><span class="read"><a class="green icon-ok"></a></span></li>';
		template = new Template(template);	
	
	new Ajax.Request('<?=$REQUEST_URI?>', {
		onCreate   : Create
		, onSuccess: function (data) { 
			data.responseJSON.each(function (e) {
				html+= template.evaluate({id: e.id, name: e.name});
			}); 
			
			$('list').update(html); 
			$('submit').enable();
			form.reset();	 
		}
		, onFailure : Failure
		, parameters: Form.serialize(form)
	}); e.stop();
});

function Create() {
	$('submit').disable();
}

function Failure(){
	if( confirm("Заполните все нужные поля !") ) {
		$('submit').enable();
		$('keyword').focus();
	}	 
}
</script>	
<?php echofooter();
}

/**
 * @access private
 */
function keywords_Ajax_add(){
	global $sql, $config, $member;

	if (empty($_POST['keywords_add'])) {
		header("HTTP/1.0 500 Internal Server Error"); 
		exit("Заполните все нужные поля !");
	}
	
	//header("Content-type: text/html; charset=$config[charset]");
	header("Content-type: application/json; charset=$config[charset]");

	foreach ($_POST as $k => $v){
		$$k = $v;
	}

	$keywords_array = explode("\n", trim($keywords_add));
	$keywords_array = array_unique($keywords_array);
	$keywords_exist = [];

	foreach ($sql->select(['keywords', 'select' => ['name']]) as $row){
		$keywords_exist[] = strtolower($row['name']);
	}

	foreach ($keywords_array as $v){
		if (!empty($v) and !in_array(strtolower($v), $keywords_exist)){
			
			$sql->insert(['keywords', 'values' => [
				'name' => $v, 'user_id' => $member['id'], 
				'url'  => totranslit($v)? totranslit($v) : 'keyword_'.$sql->lastInsertId('keywords')
				]
			]);
		}
	}
	
	exit (cute_get_keywords('<li><label for="key{id}"><input type="checkbox" [php]keywords_select({id})[/php] name="key[{id}]" id="key{id}")"> &nbsp; {name}</label> <sup><a href="#" title="'.t('Удалить кейворд "%keyword"', ['keyword' => '{name}']).'" onclick="remove_keywords_call_ajax({id});return false;">x</a></sup>', (isset($tojson) ? true : false)));
}

/**
 * @access private
 */
function keywords_Ajax_remove($keyid = 0){
	global $sql, $PHP_SELF, $config;
	
	if ( empty($_POST['keyid']) ){   //	var_dump($_POST);
	    header("HTTP/1.0 500 Internal Server Error"); 
		exit("ID of keyword none.");
	}
	
	$keyid  = $_POST['keyid'];	//header("Content-type: text/html; charset=$config[charset]");
	$tojson = $_POST['tojson'];
	
	$sql->delete(['keywords', 'where' => $keyid]);
	
	echo cute_get_keywords('<li><label for="key{id}"><input type="checkbox" [php]keywords_select({id})[/php] name="key[{id}]" id="key{id}")"> &nbsp; {name}</label> <sup><a href="#" title="'.t('Удалить кейворд "%keyword"', ['keyword' => '{name}']).'" onclick="remove_keywords_call_ajax({id});return false;">x</a></sup>', (isset($tojson) ? true : false));
}

add_action('new-save-entry', 'keywords_save', 1);
add_action('edit-save-entry', 'keywords_save', 1);

/**
 * @access private
 */

function keywords_save(){
	global $sql, $id;

	if (!empty($_POST['key'])){
	    $values['tags'] = join(',', array_keys($_POST['key']));
	    $sql->update(['news', 'where' => $id, 'values' => $values]);
	}
}

add_filter('news-show-generic', function ($tpl){
	global $row, $_keywords;
	
    if ($key_arr = explode(',', $row['tags'])){
        $key = [];

        foreach ($key_arr as $v){
            $key['id'][]   = $v;
            $key['name'][] = $_keywords['name'][$v] ? '<a href="'.cute_get_link(['id' => $v, 'url' =>$_keywords['url'][$v]], 'keywords').'">'.$_keywords['name'][$v].'</a>' : '';
        }
    }

    $tpl['tags'] = ['name' => join(', ', $key['name']), 'id' => join(', ', $key['id'])];
    return $tpl;
});


add_filter('cute-get-link', function ($output)
{
	global $QUERY_STRING;
    $QUERY_STRING   = cute_query_string($QUERY_STRING, ['keywords']);
    $output['link'] = str_replace('{keywords}', $output['arr']['url'], $output['link']);
    $output['link'] = str_replace('{keywords-id}', $output['arr']['id'], $output['link']);
    return $output;
});

/**
 * @access private
 */

add_filter('htaccess-rules-replace', 'keywords_rules_replace');

function keywords_rules_replace($output){
global $_keywords, $config;

    if ($_POST['keywords_add']){
    	$keywords_array = explode("\r\n", $_POST['keywords_add']);
    	$keywords_array = array_unique($keywords_array);
    	$keyword_exist  = [];

        if ($_keywords['url']){
	        foreach ($_keywords['url'] as $k => $v){
	            $keyword_exist[] = strtolower($v);
	        }
    	}

	    foreach($keywords_array as $v) {
	    	if ($v and !in_array(strtolower($v), $keyword_exist)){
	    		$_keywords['url'][] = $v;
	    	}
	    }
	}

    if ($config['mod_rewrite'] and $_keywords['url']){
        foreach ($_keywords['url'] as $v){
            $keywords[] = $v;
        }

        if ($keywords){
            $keywords = join('|', $keywords);
            $keywords = '(none|'.$keywords.')';
        }
    } else {
        $keywords = '([_0-9a-z-]+)';
    }

    $output = str_replace('{keywords}', $keywords, $output);
    $output = str_replace('{keywords-id}', '([0-9]+)', $output);
	return $output;
}

/**
 * @access private
 */

add_filter('htaccess-rules-format', function ($output){
    $output = str_replace('{keywords-id', '{keywords}', $output);
    return $output;
});

add_filter('template-active', 'keywords_macros_variables');
add_filter('template-full', 'keywords_macros_variables');

/**
 * @access private
 */
function keywords_macros_variables($output){
	$output['keywords'] = t('Кейворды, к которым относится новость. Например: $tpl[\'post\'][\'keywords\'][\'name\'] выведет название кейвордов, а $tpl[\'post\'][\'keywords\'][\'id\'] - их ID');
    return $output;
}

#-------------------------------------------------------------------------------

/**
 * @access private
 */
function count_keywords_entry($keyid){
	global $sql;
	if ($result = $sql->count(['news', 'where' => ['tags ? ['.$keyid.']']])) {
		return $result;
	}
}

/**
 * @access private
 */
function keywords_select($id){
	global $post;
	return isset($_GET['id']) ? (in_array($id, explode(',', $post['tags']))? ' checked' : ''): false;
}

/**
 * @see cute_get_keywords()
 *
 * @param string $tpl Шаблон
 * @return string Список кейвордов по шаблону
 */

function cn_keywords($tpl = '<li><a href="[php]cute_get_link($row, keywords)[/php]">{name} ([php]count_keywords_entry({id})[/php])</a></li>'){
	global $cache;
	static $uniqid;
    
	if(!$output = $cache->get('keywords', $uniqid++))
	{
        $output = $cache->put(cute_get_keywords($tpl));
	}   
	return $output;
}

/**
 * Возвращает список кейвордов, используя шаблон $tpl.
 * Теги для использования в шаблоне вывода:
 * {name} - название кейворда,
 * {url} - УРЛ кейворда,
 * {id} - ID кейворда,
 * [php] и [/php] - между этими тегами указывается php-код, который будет выполнен (например: [php]function({id})[/php]).
 *
 * @param string $tpl
 * @return string
 */


function cute_get_keywords($tpl = '', $tojson = false,  $select = ['id', 'name', 'url'], $result = '')
{
	global $sql, $json, $cute;

	$query = $sql->select(['keywords', 'select' => $select, 'orderby' => ['name', 'ASC']]);

	if (!reset($query)) {
		return false;
	}

	$select = ['/{('.join('|', $select).')}/', '/\[php\](.*?)\[\/php\]/'];

	foreach ($query as $row)
	{
		$json[] = $row;
		$result.= preg_replace_callback($select, function($m) use ($row) {
			return isset($row[$m[1]]) ? $row[$m[1]] : eval('return '.$m[1].';');
		},	$tpl);
	}
	
	return !$tojson ? $result : $cute->json_encode($json);
}
?>