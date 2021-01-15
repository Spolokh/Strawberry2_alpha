<?php
/**
 * @package Plugins
 */

/*
Plugin Name: 	CN functions
Plugin URI:     http://cutenews.ru
Description: 	<code>&lt;?=cn_calendar(); ?&gt;</code> - календарь.<br /><code>&lt;?=cn_archives(); ?&gt;</code> - список месяцев.<br /><code>&lt;?=cn_category(); ?&gt;</code> - список категорий.<br /><code>&lt;?=cn_title(); ?&gt;</code> - заголовки.
Version: 		2.1
Application: 	Strawberry
Author: 		Лёха zloy и красивый
Author URI:     http://lexa.cutenews.ru
*/

/**
 * @access private
 */

add_filter('constructor-functions', 'etc_constructor_functions');
function etc_constructor_functions($functions)
{
    $functions['cn_calendar'] = '';
    $functions['cn_archives'] = ['string', 'array'];
    $functions['cn_category'] = ['string', 'string', 'bool', 'int'];
    $functions['cn_title']    = ['string', 'bool', 'string'];
    return $functions;
}

/**
 * Возвращает таблицу месяца
 * @return string
 */

function cn_calendar($categid = 0){
	
	global $cache, $year, $month, $day, $PHP_SELF, $sql, $config;

	if ( empty($categid) ) { //Если категория не указана
		//$cat_cat = $sql->select(['news', 'where' => ['date < '.(time() + $config['date_adjust'] * 60)]]);
		$where = ['date < '.(time() + $config['date_adjust'] * 60)];
	}

	elseif ( !strpos($categid, ',') ) { //Если указана одна категория
		//$cat_cat = $sql->select(['news', 'where' => ['category ? ['.$categid.']', 'and', 'date < '.(time() + $config['date_adjust'] * 60)]]);
		$where = ['category ? ['.$categid.']', 'and', 'date < '.(time() + $config['date_adjust'] * 60)];
	}

	else { //Если указано несколько категорий
		$line = explode(',', $categid);
		//$cat_cat = $sql->select(['news', 'where' => ['category ? ['.$line[0].']', 'and', 'date < '.(time() + $config['date_adjust'] * 60)]]);
		
		$where = ['category ? ['.$line[0].']', 'and', 'date < '.(time() + $config['date_adjust'] * 60)];
		for ($i=1; $i < count($line); $i++){
		//	array_push($cat_cat, $sql->select(['news', 'where' => ['category ? ['.$line[$i].']', 'and', 'date < '.(time() + $config['date_adjust'] * 60)]]));
		}
	}

	if (!$query = $sql->select(['news', 'select' => ['date'], 'where' => $where])) {
		return false;
	}
	
    if (!$post_arr = $cache->get('calendar')){
        
		foreach ($query as $row){
            $save[] = $row['date'];
		}

		@rsort($save);
		$post_arr = $cache->put(@join("\r\n", $save));
    }

    $post_arr = explode("\r\n", $post_arr);
    $array = [];
	$month = ( isset($month) ? $month : $_GET['month'] );
	$year  = ( isset($year) ? $year : $_GET['year'] );
	$day   = ( isset($day) ? $day : $_GET['day'] );
	 

    if ($year and $month){
        $array['month'] = $month;
        $array['year']  = $year;
    } else {
        $array['month'] = date('m', $post_arr[0]);
        $array['year']  = date('Y', $post_arr[0]);
    }

    if ( !$calendar = $cache->get(($day ? $day.'.' : '').$array['month'].'.'.$array['year']) ){
        foreach ($post_arr as $date){
            if ($array['year'] == date('Y', $date) and $array['month'] == date('m', $date)){
                $events[date('j', $date)] = $date;
            }

            if ($array['month'].$array['year'] != date('mY', $date)){
                $prev_next[] = $date;
            }
		} 
		$calendar = $cache->put(calendar($array['month'], $array['year'], $events, $prev_next, $categid));
    }
	return $calendar;
}

/**
 * $tpl это шаблон, в котором
 * {link} это ссылка,
 * {date} - дата,
 * {count} - количество постов в категории
 * @param string $tpl
 * @param array $sort
 * @return string
 */
 
function cn_archives($tpl = '<a href="{link}">{date}</a><br />', $sort = ['date', 'DESC']) { 
	//убрали ({count}) в шаблоне
	
	global $PHP_SELF, $sql, $cache;
	static $uniqid;

	$my_year = ''; //Ввели новую переменную для хранения года

	if (!$archives = $cache->get('archives', $uniqid++)){
 
		foreach ($sql->select(['news', 'select' => ['date'], 'orderby' => $sort]) as $row){
			
			if ($arch!= date('Y/m', $row['date'])) {
				$arch = date('Y/m', $row['date']);
				$find = ['{date}', '{link}', '{count}'];
				$repl = [_etc_lang(date('n', $row['date']), 'month'), cute_get_link($row, 'month'), count_month_entry($row['date'])]; //убрали .date(' Y', $row['date'])

				if ($my_year!= date('Y', $row['date'])) { //Добавили эту конструкцию для вывода года
					$my_year = date('Y', $row['date']);
					$archives.= '<p><b>' .$my_year. '</b><br />'; //Конец добавленной конструкции
				} 
			
				$archives.= str_replace($find, $repl, $tpl);
			}
		}	$archives = $cache->put($archives);
    } 
	return $archives;
}

/**
 * @see category_get_tree()
 * @param string $prefix Префикс
 * @param string $tpl Шаблон
 * @param bool $no_prefix Не использовать префикс для категорий, чей родитель 0 (верхний уровень)
 * @param int $level ID категории детей которой показывать
 * @return string Список категорий по шаблону
 */

function cn_category($prefix = '&nbsp;', $tpl = '<a href="[php]cute_get_link($row, category)[/php]">{name} ([php]count_category_entry({id})[/php])</a><br />', $no_prefix = true, $level = 0){
	
	global $PHP_SELF, $cache;
	static $uniqid;
	
	if(!$category = $cache->get('category', $uniqid++)){
		$category = $cache->put(category_get_tree($prefix, $tpl, $no_prefix, $level));
	}   
	return $category;
}

///////////////////////////////////////////////

function cn_menu($prefix = '&nbsp;', $tpl = '<a href="[php]cute_get_link($row)[/php]">{title}</a><br />', $no_prefix = true, $id = 0, $level = 0){
	global $PHP_SELF, $cache;
	static $uniqid;

	if (!$page = $cache->get('page', $uniqid++)) {
		$page = $cache->put(page_get_tree($prefix, $tpl, $no_prefix, $id, $level));
	}   
	return $page;
}

/**
 * @param string $separator Разделитель
 * @param bool $reverse Показывать в обратном порядке
 * @return string Заголовки в указаном порядке
 */
 
function cn_title($separator = ' &raquo; ', $reverse = false, $type = ''){

	global $cache, $post, $user, $config, $categories;
	static $uniqid;

	if (!$cn_title = $cache->get('title-'.str_replace(['/', '?', '&', '='], '-', CN::ChickenDick($_SERVER['REQUEST_URI'])), $uniqid++)){
        
		foreach ($_GET as $k => $v){
            $$k = htmlspecialchars ($v);
        }      

		$result[] = '<a href="'.$config['http_script_dir'].'">'.(!empty($type) ? $type : $config['home_title']).'</a>';
			 
		$go = $_GET['go'];
		
		switch ($go) {
		 
			case 'registration' :
				$title ['go'] = t('Регистрация нового пользователя'); 
			break;
			case 'profile':
				$title ['go'] = t('Личные настройки'); 
			break;
			case 'search': //search
				$title ['go'] = t('Поиск по сайту'); 
			break;

			case 'video': //video
				$title ['go'] = t('Видео плееры'); 
			break;
			
			case 'users': //search
				$title['go'] = t('Все пользователи');
				$uri  ['go'] = $go;
			break;

			case 'blog':
				$title['go'] = t('Блоги');
				$uri  ['go'] = $go;
				 
			break;
			case 'mail':
				$title['go'] = t('Обратная связь'); 
				$uri  ['go'] = $go;
			break;
		}
		
		if ( isset($category) and $category != '')
		{
			if ( !strstr($category, ',') and !is_numeric($category) ) {
				$category = CN::getId($categories, $category);
			}  

			$title['category'] = category_get_title($category, $separator);
			$uri  ['category'] = category_get_link($category);
			//$title['category'] = explode($separator, category_get_title($category, $separator));

			if ($category == 'post') {
				$title['category'] = category_get_link($category);
			}
			//echo category_get_link($category);
		}

		if (isset($catalog)) {
			$title['catalog'] = catalog_get_title($catalog, $separator);
		}

		if ( isset($user) or isset($author) ){
            
			$user = $user ?: $author;
		
            if ( is_numeric($user) ){
                
				/*foreach ($users as $row){
                    if ($row['id'] == $user){
                        $title['user'][]   = $row['name'];
                        $title['author'][] = $row['name'];
                    }
                }*/
				
            } else {
				$title['user'][]   = $user ; //$users[$user]['name'];
				$title['author'][] = $user ; //$users[$user]['name'];
            }
        }

        if (isset($year)) {
			$title['year'][] = $year;
        }

        if (isset($month)) {
			$f_num  			= ['01', '02', '03', '04', '05', '06', '07', '07', '09', '10', '11', '12'];
			$f_name 			= ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
			$replace 			= [t('Январь'), t('Февраль'), t('Март'), t('Апрель'), t('Май'), t('Июнь'), t('Июль'), t('Август'), t('Сентябрь'), t('Октябрь'), t('Ноябрь'), t('Декабрь')];
			$title['month'][] 	= (is_numeric($month) ? str_replace($f_num, $replace, $month) : str_replace($f_name, $replace, $month));
        }

        if (isset($day)){
             $title['day'][] = $day;
        }

        if (isset($id)){
	        $title['id'][] = replace_news('show', $post['title']);
		}

		foreach ($_GET as $k => $v)
		{
			if ( preg_match('/\//', $v) ){ 
	    		
				$v_arr  = explode('/', $v);
	            for ($i = 0; $i < count($v_arr); $i++){
	            	$uri_tmp.= $v_arr[$i].'/';
	                $uri[$k] = CN::ChickenDick($uri_tmp);
	            }
	    	}

	        if ( preg_match('/&/', $_SERVER['REQUEST_URI']) ){    
				$v_arr2 = explode('&', $v);
				for ($i = 0; $i < count($v_arr2); $i++){
					$uri_tmp.= $k.'='.$v_arr2[$i].'&';
					$uri[$k] = CN::ChickenDick($uri_tmp, '&');
				}
			}
			
			foreach ((array)$title[$k] as $v){
	    		$array['title'][] = $v;
	    	}

	    	foreach ((array)$uri[$k] as $v){
	    		$array['uri'][] = $v;
	    	}	
	    }

	    /*foreach ($_GET as $k => $row){
	    	foreach ((array)$title[$k] as $v){
	    		$array['title'][] = $v;
	    	}

	    	foreach ((array)$uri[$k] as $v){
	    		$array['uri'][] = $v;
	    	}
	    }*/

	    $home = parse_url($config['http_script_dir']);
	    $home = $home['scheme'].'://'.$home['host'].(isset($home['port']) ? ':'.$home['port'] : '').($home['path'] ? '/'.$home['path'] : '').'/';
		$home = $home.(preg_match('/&/', $_SERVER['REQUEST_URI']) ? '?' : '');
		
		$count = count((array)$array['title']); 

	    for ($i = 0; $i < $count; $i++){
			$result[] = '<a href="'.$array['uri'][$i].'">'.$array['title'][$i].'</a>';
	    }
	
	    $result[(count($result) - 1)] = strip_tags ($result[(count($result) - 1)]);
	    $cn_title = join ($separator, ($reverse ? array_reverse($result) : $result));
		$cn_title = $cache->put (( empty($type) ? strip_tags($cn_title) : $cn_title ));
	}   
	return $cn_title;
}

#-------------------------------------------------------------------------------

/**
 * @access private
 */
function count_month_entry($time){
	global $sql;
	$fday   = strtotime(date('m/01/Y 00:00:01', $time));
	$lday   = strtotime(date('m/t/Y 23:59:59', $time));
	$result = $sql->count(['news', 'select' => ['date'], 'where' => ['date > '.$fday, 'and', 'date < '.$lday]]);
	return $result;
}

/**
 * @access private
 */
function count_category_entry($catid){
	global $sql; 
	$result = $sql->count(['news', 'select' => ['category'], 'where' => ['category ? ['.$catid.']']]); 
	return $result;
}

/**
 * @access private
 */
 
function calendar($cal_month, $cal_year, $events, $prev_next, $categid){
	global $year, $month, $day, $PHP_SELF;

    $first_of_month  = mktime(0, 0, 0, $cal_month, 7, $cal_year);
    $maxdays         = date('t', $first_of_month) + 1; 	// 28-31
    $cal_day         = 1;
    $weekday         = date('w', $first_of_month); 		// 0-6

    if (is_array($prev_next)){
        sort($prev_next);
        foreach ($prev_next as $key => $value){
            if ($value < $first_of_month){
                $prev_of_month = $prev_next[$key];
            }
        }

        rsort($prev_next);
        foreach ($prev_next as $key => $value){
            if ($value > $first_of_month){
                $next_of_month = $prev_next[$key];
            }
        }
    }

    if ($prev_of_month){
        $tomonth['prev'] = '<a href="'.cute_get_link(['date' => $prev_of_month], 'month').($categid != '0' ? '&category='.$categid : '').'" title="'._etc_lang(date('n', $prev_of_month), 'month').date(' Y', $prev_of_month).'">&laquo;</a> ';
    }

    if ($next_of_month){
        $tomonth['next'] = '<a href="'.cute_get_link(['date' => $next_of_month], 'month').($categid != '0' ? '&category='.$categid : '').'" title="'._etc_lang(date('n', $next_of_month), 'month').date(' Y', $next_of_month).'">&raquo;</a>';
    }

    $buffer = '<table id="calendar" cellspacing="3" cellpadding="7" align="center">
		<tr>
			<td colspan="7" class="month">'.$tomonth['prev'].'<a href="'.cute_get_link(['date' => $first_of_month], 'month').'" title="'._etc_lang(date('n', $first_of_month), 'month').date(' Y', $first_of_month).'">'._etc_lang(date('n', $first_of_month), 'month').' '.$cal_year.$tomonth['next'].'</a>
		<tr>
			<th class="weekday">'._etc_lang(1, 'weekday').'
			<th class="weekday">'._etc_lang(2, 'weekday').'
			<th class="weekday">'._etc_lang(3, 'weekday').'
			<th class="weekday">'._etc_lang(4, 'weekday').'
			<th class="weekday">'._etc_lang(5, 'weekday').'
			<th class="weekend">'._etc_lang(6, 'weekday').'
			<th class="weekend">'._etc_lang(7, 'weekday').'
		<tr>';

    if ($weekday > 0){
        $buffer .= '<td colspan="'.$weekday.'">&nbsp;';
    }

    while ($maxdays > $cal_day){
        if ($weekday == 7){
            $buffer .= '<tr>';
            $weekday = 0;
        }

        if ($events[$cal_day]){ //В данный день есть новость
        
            $date['title'] = langdate('l, d M Y', $events[$cal_day]);
            $link = cute_get_link(array('date' => $events[$cal_day]), 'day').($categid != '0' ? '&category='.$categid : '');

            if ($weekday == '5' or $weekday == '6'){ // Если суббота и воскресенье. Слава КПСС!!!
                if ($day == $cal_day){
                    $buffer .= '<td class="weekend"><a href="'.$link.'" title="'.$date['title'].'"><b>'.$cal_day.'</b></a>';
                   } else {
                       $buffer .= '<td class="endday"><a href="'.$link.'" title="'.$date['title'].'">'.$cal_day.'</a>';
                   }
            } else { // Рабочии дни. Вперёд, стахановцы!!!
                if ($day == $cal_day){ // активный
                    $buffer .= '<td class="weekday"><a href="'.$link.'" title="'.$date['title'].'"><b>'.$cal_day.'</b></a>';
                } else {  // пассивный, дурашка
                    $buffer .= '<td class="day"><a href="'.$link.'" title="'.$date['title'].'">'.$cal_day.'</a>';
                }
            }
        } else { // В данный день новостей нет. Хуйовый день...
            if ($weekday == '5' or $weekday == '6'){ // дни, когда по телеку нихуя нет :(
                $buffer .= '<td class="endday">'.$cal_day;
            } else { // работяги хлещат водку после труда
                $buffer .= '<td class="day">'.$cal_day;
            }
        }

        $cal_day++;
        $weekday++;
    }

    if ($weekday != 7){
        $buffer .= '<td colspan="'.(7 - $weekday).'">&nbsp;';
    }
       return $buffer.'</table>';
}

/**
 * @access private
 */
function _etc_lang($num, $set){
    $lang = [
        'month'   => [t('Январь'), t('Февраль'), t('Март'), t('Апрель'), t('Май'), t('Июнь'), t('Июль'), t('Август'), t('Сентябрь'), t('Октябрь'), t('Ноябрь'), t('Декабрь')],
        'weekday' => [t('Пн'), t('Вт'), t('Ср'), t('Чт'), t('Пт'), t('Сб'), t('Вс')]
    ];  return $lang[$set][($num - 1)];
}
?>