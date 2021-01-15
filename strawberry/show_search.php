<?php
/**
 * @package Show
 */

include_once 'head.php';

$sday[] = t('день');
for ($i = 1; $i < 32; $i++) {
	$sday[$i] = $i;
}

$smonth[] = t('месяц');
for ($i = 1; $i <= 12; $i++) {
	$smonth[$i] = langdate('M', mktime(0, 0, 0, $i));
}

$syear[] = t('год');
for ($i = 1999; $i < (date('Y') + 1); $i++) {
	$syear[$i] = $i;
}

$ids = "1, 2, 3, 4, 88";

//$query = $sql->query("SELECT `id`, `date`, `title` FROM `".$sql->prefix."news` WHERE `id` IN ($ids) ORDER BY `date` DESC LIMIT 0, 12");

//$query = $sql->select(array('news', 'where' => array('id = ['.$ids.']')));
//foreach ($query as $row){
//echo '<li><a href="' .cute_get_link($row). '">'.replace_news('show', $row['title']).'</a>';
//}
//$query = mysql_query("SELECT * FROM `".$sql->prefix."users` WHERE `id`=1");
//$row = mysql_fetch_object($query);
//echo '<li>'.$row->name.'</li>';
?>

<style>

#SearchForm
	{
	width: 100%; padding: 8px; margin: 0 0 20px; background: #EDEFF5; position: relative;
	}
#SearchForm:before
	{
	top:19px; left:20px; bottom:20px; content:'\f002'; font:normal 18px 'FontAwesome'; position: absolute; color:#CCC
	}
#SearchForm input[type="search"]
	{
	width:100%; margin:4px 0; font-size:15px; padding:6px 5px 6px 35px;
	}
#search_result
	{
	list-style-type:square; background:#f7f7f7;	
	}
#search_result
	{
	padding: 1px 1px 1px 25px; background:#f7f7f7;	
	}
#search_result li
	{
	padding: 4px; font-size:14px; border-bottom: 1px #FFF solid;
	}	
</style>

<form id="SearchForm" action="/search">
  <input id="search" type="search" name="search" value="<?=$search?>" placeholder="Не более 3-х символов" />
</form>

<!-- КОД ПОИСКА / КОНЕЦ / МОЖНО ЗАКОНЧИТЬ ВЫДЕЛЕНИЕ И КОПИРОВАТЬ -->

<?php

add_filter('news-where', 'search');

/**
 * @access private
 */
function search ($where){
	
	global $search, $sql;
	
	$search = strtolower($search);
	$search = htmlspecialchars($search);
	$search = addcslashes($search, '%_');

	if (strlen($search) >= 3 )
	{   
		foreach ($sql->select(['news', 'select' => ['id'], 'join' => ['story', 'id'], 'where' => ["title =~ %$search%", 'or', "short =~ %$search%",'or',"full =~ %$search%"]]) as $row)
		{
	        $select[] = $row['id'];
	    }
	}

    if ($select){
		$where[] = '`id` IN (' .join(', ', $select). ')'; 
		$where[] = 'and';
	} else {
    	$where = ['id = 0', 'and'];
    	echo '<div class="error">Ничего не найдено</div>';
	}
	
    return $where;
}

add_filter('news-entry-content', 'Highlight_Search', 999);

/**
 * @access private
 */
function Highlight_Search($output){
	global $search;
	$output = formattext($search, $output);
	return $output;
}

/**
 * Подсвечивает $whatfind в $text
 * @link http://forum.dklab.ru/php/heap/AllocationOfResultInNaydenomAPieceOfTheText.html
 * @param string $whatfind Искомое слово
 * @param string $text Текст, в котором проводится поиск
 * @return string
 */

function formattext($whatfind, $text){

	$pos    = @strpos(strtoupper($text), strtoupper($whatfind));
	$otstup = 200; // кол-во символов при выводе результата
	$result = '';

	if ($pos !== false){ 		//если найдена подстрока
	    if ($pos < $otstup){ 	//если встречается раньше чем первые N символов
	        $result = substr($text, 0, $otstup * 2); //то результат подстрока от начала и до N-го символа
	        $result = preg_replace("/$whatfind/i", '<span class="hilite">' .$whatfind. '</span>', $text); // выделяем
	    } else {
	        $start  = $pos-$otstup;	      
	        $result = '...'.substr($text, $pos-$otstup, $otstup * 2).'...'; //то результат N символов  от совпадения и N символов вперёд
	        $result = preg_replace("/$whatfind/i", '<span class="hilite">' .$whatfind. '</span>', $text); // выделяем
	    }
	
	} else {
		$result = substr($text, 0, $otstup * 2);
	}

	return $result;
}


if (isset($search)){
	//$search   = iconv('utf-8', $config['charset'], $_GET['search']);
	//$template = 'Headlines';
	include rootpath.'/show_news.php';
}

/**
 * @access private
 */
 
function category_selected($id){
	global $category;
	return ($category == $id) ? ' selected' : '';
}

/**
 * @access private
 */
function search_this_cat($id){
	global $category;
	return ($id == $category) ? ' selected' : '';
}

?>


<!--form id="SearchForm" method="get" action="<?//=$PHP_SELF; ?>">
	<input name="go" type="hidden" value="search">
	<div class="border-box">
	<i class="icon-search" style="dislay:block; padding: 0 3px 0 8px;"></i>
	<input type="search" name="search" value="<?//=$search; ?>" placeholder="Найти">
	</div>
	
	<div class="toggle-box">
		<dl>
			<dt><?//=t('Џоиск в категории:'); ?></dt>
			<dd><select size="1" name="category">
			<option value="">во всех</option>
			<?//=category_get_tree('&nbsp;', '<option value="{id}"[php]search_this_cat({id})[/php]>{prefix}{name}</option>'); ?></select></dd>
		</dl>
		<dl>
			<dt><?//=t('Поиск по тегам:'); ?></dt>
			<dd><?//=makeDropDown($smonth, 'month', $month); ?></dd>
		</dl>
	</div>
</form>

<table width="400" border="0">
		<td>В категории
		<td><select size="1" name="category"><option value="">во всех</option><?//=category_get_tree('&nbsp;', '<option value="{id}"[php]search_this_cat({id})[/php]>{prefix}{name}</option>'); ?></select>
	<tr>
		<td><nobr>За дату </nobr>&nbsp;
		<td><?//=makeDropDown($syear, 'year', $year); ?>/<?//=makeDropDown($smonth, 'month', $month); ?>/<?//=//makeDropDown($sday, 'day', $day); ?>
	<tr>
		<td width="1">
		<td><input type="submit" value=" поиск ">
</table-->