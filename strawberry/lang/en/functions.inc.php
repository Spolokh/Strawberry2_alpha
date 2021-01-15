<?php
/**
 * Стандратне функции Strawberry, которые всегда доступны.
 *
 * Если написано "многоязычная" - это значит, что результат данной функции
 * зависит от языка указнного в системных настройках.
 *
 * Может случиться так, что в этом разделе нет нужной функции, то - увы - она нестандартая и
 * Вам, вероятно, придеться написать ее самому...
 *
 * @package Functions
 */

/**
 * Возвращает размер файла в "читаемом" виде.
 *
 * Возвращает размер $file_size (байт) в "читаемом" виде.
 * Многоязычная.
 *
 * @param int $file_size
 * @return string
 */
function formatsize($file_size){

    if ($file_size >= 1073741824){
    	$file_size = (round($file_size / 1073741824 * 100) / 100).' '.t('Гбайт');
    } elseif ($file_size >= 1048576){
    	$file_size = (round($file_size / 1048576 * 100) / 100).' '.t('Мбайт');
    } elseif ($file_size >= 1024){
    	$file_size = (round($file_size / 1024 * 100) / 100).' '.t('Кбайт');
    } else {
    	$file_size = $file_size.' '.t('байт');
    }

return '<nobr>'.$file_size.'</nobr>';
}

/**
 * Класс пора менять - поэтому без доков.
 * @access private
 */
class microTimer {
	/**
	 * Enter description here...
	 *
	 * @access private
	 */
    function start(){
    	global $starttime;
        $mtime     = microtime();
        $mtime     = explode (' ', $mtime);
        $mtime     = $mtime[1] + $mtime[0];
        $starttime = $mtime;
    }

    /**
     * Enter description here...
     *
     * @access private
     * @return unknown
     */
    function stop(){
    	global $starttime;
        $mtime     = microtime();
        $mtime     = explode (' ', $mtime);
        $mtime     = $mtime[1] + $mtime[0];
        $endtime   = $mtime;
        $totaltime = round (($endtime - $starttime), 5);
    	return $totaltime;
    }
}

/**
 * Проверяет соотвествие указанного хеша паролю в БД.
 *
 * Проверяет соотвествие указанного хеша пароля для пользователя $username
 * хешу пароля в БД. В качестве хеша должна быть строка полученная функцией md5x().
 *
 * Также, в случаее успеха, передает в глобальную переменную $member массив,
 * содержащий всю информацию о авторизированом пользователе.
 *
 * @see md5x()
 *
 * @param string $username
 * @param string $md5_password
 * @return bool
 */
function check_login($username, $md5_password){
global $member, $users;

    $member = array();
    $result = false;

    foreach ($users as $row){
        if (strtolower($username) == strtolower($row['username']) and $md5_password == $row['password']){
			$result = true;
            $member = $row;
        }
    }

return $result;
}

/**
 * Формирует строку для запроса.
 *
 * Формирует строку $q_string (такую как $_SERVER['QUERY_STRING'], например)
 * для запроса типа $type (POST или GET), игнорируя переменные,
 * указанные в массиве $strips.
 *
 * Прим. перев: со $strips тупо сделано - название игнорируемой
 * переменной должно быть не значением массива, а его ключем.
 *
 * @param string $q_string
 * @param array $strips
 * @param string $type
 * @return string
 */
function cute_query_string($q_string, $strips, $type = 'get'){

    foreach ($strips as $key){
    	$strips[$key] = true;
    }

    foreach(explode('&', $q_string) as $var_peace){
        $parts = explode('=', $var_peace);

        if ($parts[0] and !$strips[$parts[0]]){
            if (strtolower($type) == 'post'){
            	$my_q .= '<input type="hidden" name="'.$parts[0].'" value="'.$parts[1].'" />';
            } else {
            	$my_q .= '&'.$var_peace;
            }
        }
    }

return $my_q;
}

/**
 * Выводит строку таблицы (для ACP).
 *
 * Выводит строку таблицы с заголовком $title, описанием $description
 * и полем формы $field в панеле управления.
 *
 * @param string $title
 * @param array $description
 * @param string $field
 * @return string
 */
function showRow($title = '', $description = '', $field = ''){
global $i;

    if ($i%2 !== 0 and $title){
    	$bg = 'class="enabled"';
    } else {
    	$bg = 'class="disabled"';
    }

    echo '<tr '.$bg.'>
         	<td width="400" colspan="2" class="opt-title">&nbsp;<b>'.$title.'</b></td>
            <td width="250" rowspan="2" valign="middle" align="left" class="opt-space">'.$field.'</tr>
         <tr '.$bg.'>
         	<td width="20" class="opt-space">&nbsp;</td>
            <td width="400" valign="top" class="opt-desc">'.$description.'</td>
         </tr>';

    $bg = '';
    $i++;
}

/**
 * Создает выпадающее меню.
 *
 * Создает элемент формы select с аттрибутом name = $name и
 * набором значений option из ассоц. массива $options (['value'] => 'description')
 * и аттрибутом selected для значения, если $selected = значению ключа $options.
 *
 * @param array $options
 * @param string $name
 * @param string $selected
 * @return string
 */
function makeDropDown($options, $name, $selected = ''){

    foreach ($options as $value => $description){
    	$output .= '<option value="'.$value.'"'.(($selected == $value) ? ' selected ' : '').'>'.$description.'</option>';
    }

return '<select size="1" id="'.$name.'" name="'.$name.'">'.$output.'</select>';
}

/**
 * Enter description here...
 *
 * @access private
 *
 * @param string $ip
 * @param int $id
 * @return bool
 */
function flooder($ip, $id){
global $config, $sql;

    foreach ($sql->select(array('table' => 'flood')) as $row){
    	if ($row['ip'] == $ip and $row['post_id'] == $id){
	        if (($row['date'] + $config['flood_time']) > time){
	           return true;
	        } else {
	            $sql->delete(array(
	            'table' => 'flood',
	            'where' => array("date = $row[date]", 'and', "ip = $row[ip]", 'and', "post_id = $row[post_id]")
	            ));
	        }
        }
    }

return false;
}

/**
 * Выводит сообщение в ACP.
 *
 * Выводит сообщение $text, с заголовком $title и картинкой $type с шаблоном панели управления,
 * прерывая дальнейшее выполнение скрипта в панеле управления.
 * Если $back = true, то будет выведена ссылка, ведущая на предыдущую страницу.
 * В $type нужно передать имя файла картинки из skins/$skin_prefix/.
 *
 * @see echoheader(), echofooter()
 *
 * @param string $type
 * @param string $title
 * @param string $text
 * @param string $back
 */
function msg($type, $title, $text, $back = ''){

	echoheader($type, $title);
	echo '<table border="0" cellpading="0" cellspacing="0" width="100%" height="100%"><tr><td>'.$text.($back ? '<br /><br /><a href="'.$back.'">'.t('Вернуться назад').'</a>' : '').'</table>';
	echofooter();
	exit;
}

/**
 * Выводит верхнюю часть шаблона ACP.
 *
 * Выводит верхнюю часть шаблона панели управления с картинкой $image
 * и заголовком $header_text.
 *
 * @param string $image
 * @param string $header_text
 */
function echoheader($image, $header_text){
global $PHP_SELF, $is_logged_in, $config, $skin_header, $skin_menu, $skin_prefix;

    if ($is_logged_in == true){
    	$skin_header = str_replace('{menu}', $skin_menu, $skin_header);
    } else {
    	$skin_header = str_replace('{menu}', ' &nbsp; '.$config['version_name'], $skin_header);
    }

    $skin_header = str_replace('{image-name}', $skin_prefix.$image, $skin_header);
    $skin_header = str_replace('{header-text}', $header_text, $skin_header);
    $skin_header = str_replace('{copyrights}', '<div style="font-size: 9px; text-transform: uppercase">Powered by <a style="font-size: 9px" href="http://strawberry.goodgirl.ru/" target="_blank">'.$config['version_name'].' '.$config['version_id'].'</a> &copy; 2006 <a style="font-size: 9px" href="http://strawberry.goodgirl.ru/" target="_blank">GoodGirl</a></div>', $skin_header);

    echo $skin_header;
}

/**
 * Выводит нижнюю часть шаблона ACP.
 *
 * @return void
 */
function echofooter(){
global $PHP_SELF, $is_logged_in, $config, $skin_footer, $skin_menu, $skin_prefix;

    if ($is_logged_in == true){
    	$skin_footer = str_replace('{menu}', $skin_menu, $skin_footer);
    } else {
    	$skin_footer = str_replace('{menu}', ' &nbsp; '.$config['version_name'], $skin_footer);
    }

    $skin_footer = str_replace('{image-name}', $skin_prefix.$image, $skin_footer);
    $skin_footer = str_replace('{header-text}', $header_text, $skin_footer);
    $skin_footer = str_replace('{copyrights}', '<div style="font-size: 9px; text-transform: uppercase">Powered by <a style="font-size: 9px" href="http://strawberry.goodgirl.ru/" target="_blank">'.$config['version_name'].' '.$config['version_id'].'</a> &copy; 2006 <a style="font-size: 9px" href="http://goodgirl.ru/" target="_blank">GoodGirl</a></div>', $skin_footer);

    echo $skin_footer;
}

/**
 * Возвращает кликабельные смайлы.
 *
 * Возвращает кликабельные смайлы по $break_location на строку. При клике на смайл
 * в поле $insert_location будет автоматически вставлен его синоним.
 *
 * @param string $insert_location
 * @param int $break_location
 * @return string
 */
function insertSmilies($insert_location, $break_location = 0){
global $config;

    if (!$config['smilies']){
    	return '';
    }

    $smilies = explode(',', $config['smilies']);
    foreach ($smilies as $smile){
        $i++;

        $output .= '<a href="javascript:insertext(\':'.trim($smile).':\', \'\', \''.$insert_location.'\')"><img style="border: none;" alt="'.trim($smile).'" src="'.$config['http_script_dir'].'/data/emoticons/'.trim($smile).'.gif" /></a>';

        if ($i%$break_location == 0 and $break_location){
        	$output .= '<br />';
        } else {
        	$output .= '&nbsp;';
        }
    }

return $output;
}

/**
 * Enter description here...
 *
 * @access private
 *
 * @param string $way
 * @param string $sourse
 * @return string
 */
function replace_comment($way, $sourse){
global $config;

    if ($way == 'add'){
	    $find    = array("\n", "\r");
	    $replace = array('<br />', '');
	    $sourse  = htmlspecialchars($sourse);

	    if (!get_magic_quotes_gpc()){
	    	$sourse = addslashes($sourse);
	    }

    } elseif ($way == 'show'){
	    $find    = array('&amp;');
	    $replace = array('&');
    	$sourse = stripslashes($sourse);

        foreach (explode(',', $config['smilies']) as $smile){
            $find[]    = ':'.trim($smile).':';
            $replace[] = '<img style="border: 0px; vertical-align: middle;" alt="'.trim($smile).'" src="'.$config['http_script_dir'].'/data/emoticons/'.trim($smile).'.gif" />';
        }
    } elseif ($way == 'admin'){
	    $find    = array('<br />');
	    $replace = array("\n");
	    $sourse  = unhtmlentities($sourse);
    	$sourse  = stripslashes($sourse);
    }

return str_replace($find, $replace, trim($sourse));
}

/**
 * Enter description here...
 *
 * @access private
 *
 * @param string $way
 * @param string $sourse
 * @param bool $replce_n_to_br
 * @param bool $use_html
 * @return string
 */
function replace_news($way, $sourse, $replce_n_to_br = true, $use_html = true){
global $config;

    if ($way == 'show'){
    	$find    = array('{nl}', '&#039;');
       	$replace = array('<br />', '\'');
       	$sourse  = stripslashes($sourse);

        foreach (explode(',', $config['smilies']) as $smile){
            $find[]    = ':'.trim($smile).':';
            $replace[] = '<img style="border: 0px; vertical-align: middle;" alt="'.trim($smile).'" src="'.$config['http_script_dir'].'/data/emoticons/'.trim($smile).'.gif" />';
        }
    } elseif($way == 'add'){
        $find    = array("\r", "\n");
        $replace = array('', '{nl}');

        if (!get_magic_quotes_gpc()){
        	$sourse = addslashes($sourse);
        }
    } elseif ($way == 'admin'){
    	$find    = array('{nl}');
        $replace = array("\n");
        $sourse  = stripslashes($sourse);
    }

return str_replace($find, $replace, trim($sourse));
}

/**
 * Enter description here...
 *
 * @access private
 *
 * @param array $array
 * @param bool $bool
 * @return string
 */
function echo_r($array, $bool = false){
    ob_start();
    if (is_bool($array)){
    	echo ($array ? 'true' : 'false');
    } else {
    	print_r($array);
    }

    $echo = ob_get_contents();
    ob_clean();

    if ($bool){
    	return highlight_string($echo, true);
    } else {
    	echo highlight_string($echo, true);
    }
}

/**
 * Отправляет почту.
 *
 * Отправляет почту, адресованную $to, с темой письма $subject и сообщением $message.
 * Возможно "приаттачивание" файла $filename к письму.
 *
 * @param string $to
 * @param string $subject
 * @param string $message
 * @param string $filename
 */
function cute_mail($to, $subject, $message, $filename = ''){
global $config;

	$mail     = 'no-reply@'.str_replace('www.', '', $_SERVER['SERVER_NAME']);
	$uniqid   = md5(uniqid(time));
	$headers  = 'From: '.$mail."\n";
	$headers .= 'Reply-to: '.$mail."\n";
	$headers .= 'Return-Path: '.$mail."\n";
	$headers .= 'Message-ID: <'.$uniqid.'@'.$_SERVER['SERVER_NAME'].">\n";
	$headers .= 'MIME-Version: 1.0'."\n";
	$headers .= 'Date: '.gmdate('D, d M Y H:i:s', time)."\n";
	$headers .= 'X-Priority: 3'."\n";
	$headers .= 'X-MSMail-Priority: Normal'."\n";
	$headers .= 'X-Mailer: '.$config['version_name'].' '.$config['version_id']."\n";
	$headers .= 'X-MimeOLE: '.$config['version_name'].' '.$config['version_id']."\n";
	$headers .= 'Content-Type: multipart/mixed;boundary="----------'.$uniqid.'"'."\n\n";
	$headers .= '------------'.$uniqid."\n";
	$headers .= 'Content-type: text/plain;charset=windows-1251'."\n";
	$headers .= 'Content-transfer-encoding: 7bit';

    if (is_file($filename)){
	    $file     = fopen($filename, 'rb');
	    $message .= "\n".'------------'.$uniqid."\n";
	    $message .= 'Content-Type: application/octet-stream;name="'.basename($filename).'"'."\n";
	    $message .= 'Content-Transfer-Encoding: base64'."\n";
	    $message .= 'Content-Disposition: attachment;';
	    $message .= 'filename="'.basename($filename).'"'."\n\n";
	    $message .= chunk_split(base64_encode(fread($file, filesize($filename))))."\n";
	}

	mail($to, $subject, $message, $headers);
}

/**
 * Рекурсивно меняет права файлам.
 *
 * Рекурсивно меняет права всем файлам и папкам на $mod в директории $dir.
 *
 * @link http://forum.dklab.ru/php/advises/FaylovieFunktsii.html
 *
 * @param string $dir
 * @param int $mod
 * @return bool
 */
function chmoddir($dir, $mod){

	$handle = opendir($dir);
	while (false !== ($file = readdir($handle))){
	    if ($file != '.' and $file !== '..'){
	        if (is_file($file)){
	            chmod($dir.'/'.$file, $mod);
	        } else {
	            chmod($dir.'/'.$file, $mod);
	            chmoddir($dir.'/'.$file, $mod);
	        }
	    }
	}
	closedir($handle);

    if (chmod($dir, $mod)){
    	return true;
    } else {
    	return false;
	}
}

/**
 * Enter description here...
 *
 * @access private
 *
 * @param string $action
 * @param array $sort
 * @return array
 */
function c_array($action, $sort = ''){
global $sql;

	if (is_array($sort)){
		$query = array('table' => $action, 'orderby' => $sort);
	} else {
		$query = array('table' => $action);
	}

    foreach ($sql->select($query) as $k => $v){
    	$result[] = implode('|', $v);
    }

return ($result ? $result : array());
}

/**
 * Отделяет мух от супа.
 *
 * Другими словами, заменяет все повторения строки $dick
 * на одно и вырезает все повторения $dick по "бокам" строки $chicken.
 *
 * @param string $chicken
 * @param string $dick
 * @return string
 */
function chicken_dick($chicken, $dick = '/'){

	$chicken = preg_replace('/^(['.preg_quote($dick, '/').']+)/', '', $chicken);
    $chicken = preg_replace('/(['.preg_quote($dick, '/').']+)/', $dick, $chicken);
    $chicken = preg_replace('/(['.preg_quote($dick, '/').']+)$/', '', $chicken);

return $chicken;
}

/**
 * Запись данных в файл.
 *
 * Записывает строку $fwrite в файл $fopen, попутно измеяя права файлу $fopen на $chmod
 * или права по умолчанию, если аргумент $chmod = false.
 *
 * Если $clear = true, то данные будут записаны в файл без символов переноса строки и
 * возврата коретки.
 *
 * @param string $fopen
 * @param string $fwrite
 * @param bool $clear
 * @param int $chmod
 */
function file_write($fopen = '', $fwrite = '', $clear = false, $chmod = 0777){

	if ($clear){
		$fwrite = str_replace('  ', '', str_replace("\r\n", '', $fwrite));
	}

    /*
    if (get_magic_quotes_gpc()){
        $sourse = stripslashes($fwrite);
    }
    */

    /*
    $dir = explode('/', chicken_dick($fopen));

    if (count($dir) > 1){
    	for ($i = 0; $i < (count($dir) - 1); $i++){
    		$path .= $dir[$i].'/';

    		if (!is_dir($path)){
    			@mkdir($path);
    		}
    	}
    }
    */

    $fp = fopen($fopen, 'wb+');
    fwrite($fp, $fwrite);
    @chmod($fopen, $chmod);
    fclose($fp);
}

/**
 * Чтение из файла.
 *
 * Возвращает содержимое файла $filemame или false.
 *
 * @param string $filemame
 * @return string
 */
function file_read($filemame){

    if (!is_file($filemame)){
    	return false;
    }

    $fp = fopen($filemame, 'rb');
    $fo = fread($fp, filesize($filemame));
	fclose($fp);

return $fo;
}

/**
 * Возвращает ассоциативный массив с элементами урла $uri.
 *
 * Возвращает ассоциативный массив, полученный функцией parse_url(),
 * + ключ abs - абсолютный путь к диретории.
 *
 * @param string $url
 * @return array
 */
function cute_parse_url($url){
global $DOCUMENT_ROOT;

    $url         = parse_url($url);
    $url['path'] = chicken_dick($url['path']);
    $url['abs']  = $DOCUMENT_ROOT.'/'.$url['path'];

    if (is_file($url['abs'])){
    	$url['file'] = end($url['file'] = explode('/', $url['path']));
    	$url['path'] = chicken_dick(preg_replace('/'.$url['file'].'$/i', '', $url['path']));
    	$url['abs']  = $DOCUMENT_ROOT.'/'.$url['path'];
    }

return $url;
}

/**
 * Возвращает урл, сформированный по указанным правилам из urls.ini.
 *
 * Возвращает урл, сформированный по правилу $type секции $format из urls.ini.
 *
 * Прим. перев: честно говоря - какая-то запутанная функция, однуму автору известно
 * как и почему это все работает.
 *
 * @param array $arr
 * @param string $type
 * @param string $format
 * @return string
 */
function cute_get_link($arr, $type = 'post', $format = ''){
global $config, $users, $link, $rufus_file, $QUERY_STRING;
static $c = array();

   	if ($type == 'skip' or $type == 'page' or $type == 'cpage'){
	    $mask = preg_replace('/(\?|&)'.$type.'\=([0-9]+)/', '', $_SERVER['REQUEST_URI']);
	    $mask = $mask.(strstr($mask, '?') ? '&' : '?').$type.'='.$arr[$type];
   		return $mask;
   	}

	# Чибурашко где-то рядом!
	if (!$rufus_file){
		$rufus_file = parse_ini_file(rufus_file, true);
	}

    if ($link and !$format){
    	$format = chicken_dick($link);
    } elseif (!$link and !$format){
    	$format = 'home';
    }

    if (!is_array($arr)){
    	global $row;

    	$string = explode('/', $arr);
    	$type   = end($string);
    	unset($string[(count($string) - 1)]);
    	$format = join('/', $string);
    	$arr    = $row;
    }

    if (!$arr['date']){
    	$arr['category'] = $arr['id'];
    }

    if (!$arr['author']){
    	$arr['author']  = $arr['username'];
    	$arr['user_id'] = $arr['id'];
    } else {
    	$arr['user_id'] = $users[$arr['author']]['id'];
    }

    if ($rufus_file[$format][$type]){
    	$rufus_file[$type] = $rufus_file[$format][$type];
    } else {
    	$rufus_file[$type] = $format;
    	$QUERY_STRING = cute_query_string($QUERY_STRING, array($type));
    }

    if (!$c){
    	$c = array('home_url' => cute_parse_url($config['http_home_url']), 'script_url' => cute_parse_url($config['http_script_dir']), 'q_string' => cute_query_string($QUERY_STRING, array('category', 'skip', 'subaction', 'id', 'ucat', 'year', 'month', 'day', 'user', 'page', 'search', 'do', 'PHPSESSID', 'title', 'time', 'start_from', 'archives', 'cpage', 'action')));
    }

    $mask     = run_filters('cute-get-link', array('arr' => $arr, 'link' => $rufus_file[$type]));
    $mask     = $mask['link'];
    $mask     = reset($mask = explode(':', $mask));
	$category = reset($cat = explode(',', $arr['category']));
    $mask     = strtr($mask, array(
    		    '{add}'         => '',
                '{year}'        => date('Y', $arr['date']),
		        '{month}'       => date('m', $arr['date']),
		        '{Month}'       => strtolower(date('M', $arr['date'])),
                '{day}'         => date('d', $arr['date']),
                '{title}'       => ($arr['url'] ? $arr['url'] : totranslit($arr['title'])),
                '{url}'         => ($arr['url'] ? $arr['url'] : totranslit($arr['title'])),
                '{user}'        => urlencode($arr['author']),
                '{user-id}'     => $arr['user_id'],
                '{category-id}' => ($category ? $category : '0'),
                '{category}'    => ($category ? end($cat = explode('/', category_get_link($category))) : 'none'),
                '{categories}'  => ($category ? category_get_link($category) : 'none')
             ));

	foreach ($arr as $k => $v){
		$mask = str_replace('{'.$k.'}', $v, $mask);
	}

    if (!$config['mod_rewrite']){
    	if ($format == 'home'){
    		$result = $c['home_url']['path'].'/'.$c['home_url']['file'];
    	} elseif (substr($format, 0, 5) == 'home/'){
    		$result = $c['home_url']['path'].'/'.substr($format, 5);
	    } else {
	    	$result = $c['script_url']['path'].'/'.$format;
	    }
   	} else {
    	if ($format == 'home'){
    		$result = $c['home_url']['path'];
    	} elseif (substr($format, 0, 5) == 'home/'){
    		$result = $c['home_url']['path'].'/'.substr($format, 5);
	    } elseif (substr($uri, 0, 1) == '?'){
	    	$result = $c['script_url']['path'].'/'.$format;
	    } else {
	    	$result = $c['home_url']['path'];
	    }
   	}

   

    $result = chicken_dick($result.'/'.$mask).$c['q_string'];
    $result = str_replace('/?', '?', $result);
    $result = htmlspecialchars($c['home_url']['scheme'].'://'.$c['home_url']['host'].($c['home_url']['port'] ? ':'.$c['home_url']['port'] : '').'/'.$result);

return $result;
}

/**
 * Возвращает ссылку на категорию.
 *
 * Возвращает ссылку на категорию с id = $id, учитывая все категории-родители.
 * Можно указать суффикс урлу, передав значение переменной $link.
 *
 * @param int $id
 * @param string $link
 * @return string
 */
function category_get_link($id, $link = ''){
global $categories;

    if ($categories[$id]['url']){
    	$link = $categories[$id]['url'].($link ? '/'.$link : '');
    }

    if ($categories[$id]['parent']){
    	$link = category_get_link($categories[$id]['parent'], $link);
    }

return chicken_dick($link);
}

/**
 * Возвращает всех детей категории.
 *
 * Возвращает всех детей категории с id = $id в виде строки с id категорий, разделенных запятыми.
 *
 * @author Scip
 * @param int $id
 * @return string
 */
function category_get_children($id, $withid = true, $limit = 0){
global $categories;
static $end = 1, $result = array();

	$categories_dummy = $categories;  // u could avoid this if u RESET $categories;

    if ($id === ''){
    	return false;
    }

	if ($withid){
		$result[] = $id;
	}

    foreach ($categories_dummy as $cat_id => $row){
        if ($row['parent'] == $id){
            $result[] = $cat_id;

            if ($limit - $end){
                category_get_children($cat_id, $limit);
                //$result[] = category_get_children($cat_id, false,$limit);

            }
        }
    }

    $end++;

    $return = $result;
    $result = array();

return join(',', $return);
}

/**
 * Возвращает название категории.
 *
 * Возвращает название категории с id = $id, включая названия всех родительских категорий данной категории, названия отделяютсья друг от друга разделителем $separator.
 * Можно указать суффикс названию, передав значение переменной $title.
 *
 * @param int $id
 * @param string $separator
 * @param string $title
 * @return string
 */
function category_get_title($id, $separator = ' &raquo; ', $title = ''){
global $categories;

    if ($categories[$id]['name']){
    	$title = $categories[$id]['name'].($title ? $separator.$title : '');
    }

    if ($categories[$id]['parent']){
    	$title = category_get_title($categories[$id]['parent'], $separator, $title);
    }

return chicken_dick($title);
}

/**
 * Возвращает древо категорий.
 *
 * Возвращает древо категорий, используя шаблон $tpl и префикс (приставку) $prefix для вывода категории. Корнем древа будет категория с id = $id или будет показан список всех категорий, если $id = 0.
 *
 * Теги для использования в шаблоне вывода:
 * {name} - название категории,
 * {url} - УРЛ категории,
 * {prefix} - указаный $prefix,
 * {id} - ID категории,
 * {icon} - иконка категории,
 * {template} - шаблон категории,
 * [php] и [/php] - между этими тегами указывается php-код, который будет выполнен (например: [php]function({id})[/php]).
 *
 * Также можно избежать вывода префикса для корней древа передав переменной $no_prefix значение true.
 *
 * @param string $prefix
 * @param string $tpl
 * @param bool $no_prefix
 * @param int $id
 * @return string
 */
function category_get_tree($prefix = '', $tpl = '{name}', $no_prefix = true, $id = 0){
global $categories;

    $minus = 0;

    if ($categories){
	    foreach (sort_it($categories) as $row){
	        if ($id){
	        	if ($id == $row['id']){
                    $minus++;
                    continue;
	        	}

	            if (!in_array($row['id'], explode(',', category_get_children($id)))){
                    $minus++;
                    continue;
	            }
	        }

	        $pref = ($no_prefix ? $row['level'] : ($row['level'] + 1));
	        $pref = ($minus ? ($pref - (!$no_prefix ? ($minus - 1) : ($minus - 1))) : $pref);
	        $pref = @str_repeat($prefix, $pref);
	        $find = array('/{id}/i', '/{name}/i', '/{parent}/i', '/{url}/i', '/{icon}/i', '/{template}/i', '/{prefix}/i', '/\[php\](.*?)\[\/php\]/ie', '/{description}/i');
	        $repl = array($row['id'], $row['name'], $row['parent'], $row['url'], ($row['icon'] ? '<img src="'.$row['icon'].'" alt="'.$row['icon'].'" border="0" align="absmiddle">' : ''), $row['template'], $pref, '\\1', replace_news('show', $row['description']));
	        $johnny_left_teat .= $pref.preg_replace($find, $repl, $tpl);
	    }
	}

return $johnny_left_teat;
}

/**
 * Возвращает id категории.
 *
 * Аргументом должен передоваться урл категории, аналогичный
 * урлу, полученному функцией category_get_link();
 *
 * @see category_get_link()
 *
 * @param string $cat
 * @return int
 */
function category_get_id($cat){
global $categories;

    $cat = chicken_dick($cat);

    foreach ($categories as $row){
    	if ($cat == category_get_link($row['id'])){
    		$cat_id = $row['id'];
    	} elseif ($cat == category_get_title($row['id'], '/')){
    		$cat_id = $row['id'];
    	} elseif ($cat == $row['url']){
    		$cat_id = $row['id'];
    	} elseif ($cat == $row['id']){
    		$cat_id = $row['id'];
    	} elseif ($cat == '0' or $cat == 'none'){
    		$cat_id = '0';
    	}
    }

return $cat_id;
}

/**
 * Enter description here...
 *
 * @access private
 *
 * @param string $return1
 * @param string $return2
 * @param int $every
 * @return string
 */
function cute_that($return1 = 'class="enabled"', $return2 = 'class="disabled"', $every = 2){
static $i = 0;

	$i++;

	if ($i%$every == 0){
		return $return1;
	} else {
		return $return2;
	}
}

/**
 * Возвращает строки из языкового файла.
 *
 * Возвращает строки из языковых файлов global.ini и $module.ini, если таковой существует, в виде массива.
 *
 * Данной функцией для перевода пользоваться не стоит. Используйте t().
 *
 * @see t()
 *
 * @param string $module
 * @return array
 */
function cute_lang($module = ''){
global $config;

	$module = end($module = explode('/', $module));

	if (!file_exists($local = languages_directory.'/'.$config['lang'].'/'.$module.'.ini')){
		$local = languages_directory.'/ru/'.$module.'.ini';
	}

	if (file_exists($local)){
		$lang = @parse_ini_file($local, true);
	}

return $lang;
}

/**
 * Шифрует строку.
 *
 * @param string $str
 * @return string
 */
function md5x($str){

	$str = md5(md5($str));

return $str;
}

/**
 * Функция выполняет действие обратое функции htmlentities()
 *
 * @param string $string
 * @return string
 */
function unhtmlentities($string){

	$trans_tbl = get_html_translation_table(HTML_ENTITIES);
	$trans_tbl = array_flip($trans_tbl);

return strtr($string, $trans_tbl);
}

/**
 * Enter description here...
 *
 * @access private
 *
 * @param string $str
 * @return string
 */
function namespace($str){
global $sql, $mod;

	foreach ($sql->select(array('table' => 'news')) as $row){
		if (@preg_match("/$str([0-9]+)?/i", $row['url'])){
			$result[] = $row['id'];
		}

		if (@preg_match("/$str([0-9]+)?/i", $row['id'])){
			$result[] = $row['id'];
		}
	}

    $count = count($result);

    if ($mod == 'addnews'){
    	$count++;
    }

return totranslit($str.(($count and $count != 1) ? ' '.$count : ''));
}

/**
 * Enter description here...
 *
 * @access private
 *
 * @param array $array
 * @param int $id
 * @param array $field
 * @return array
 */
function sort_it($array, $id = 0, $field = array('parent', 'id'), $johnny_left_teat = ''){

	foreach ($array as $k => $row){
		if ($row[$field[0]] == $id){
			$johnny_left_teat[$k] = $row;
			$johnny_left_teat = sort_it($array, $row[$field[1]], $field, $johnny_left_teat);
		}
	}

return $johnny_left_teat;
}

/**
 * Enter description here...
 *
 * @access private
 *
 * @param string $mod
 * @param string $section
 * @param array $arr
 * @return bool
 */
function cute_get_rights($mod = '', $section = 'permissions', $array = ''){
global $usergroups, $member;

    $array  = ($array ? $array : $member);
    $return = false;
    $group  = $usergroups[$array['usergroup']];

    if ($group['access'] == 'full'){
    	$full = true;
    } else {
    	$full = false;
    }

	if ($section == 'read' or $section == 'write'){
	    if ($full or $group['access'][$section][$mod]){
	        $return = true;
	    }
	} elseif ($section == 'permissions'){
	    if ($full){
	    	$group[$section][$mod] = true;
	        $group[$section]['approve_news'] = false;
	        $group[$section]['categories'] = false;
	    }

	    if ($group[$section][$mod]){
	    	$return = true;
	    }
	} elseif ($section == 'fields'){
		if ($full or $group['permissions'][$section][$mod] !== '0'){
			$return = true;
		}
	}

    if ($mod == 'full'){
    	if ($group['access'] == 'full'){
    		$return = true;
    	} else {
    		$return = false;
    	}
    }

return $return;
}

/**
 * Сохраняет многомерный массив в ini-фаил.
 *
 * @param string $filename
 * @param array $content
 * @return bool
 */
function write_ini_file($filename, $content){

	foreach ($content as $k => $v){
		if (is_array($v)){
			$result .= '['.$k.']'."\n";

			foreach ($v as $key => $value){
				$result .= $key.' = "'.$value.'"'."\n";
			}
		} else {
			$result .= $key.' = "'.$value.'"'."\n";
		}
	}

	if (@file_write($filename, $result)){
		return true;
	}

return false;
}

/**
 * Делает плюс-минус, как в "Группах пользователей"
 *
 * @param string $name
 * @return string
 */
function makePlusMinus($name){

	$result = '<a href="javascript:ShowOrHide(\''.$name.'\', \''.$name.'-plus\')" id="'.$name.'-plus" onclick="javascript:ShowOrHide(\''.$name.'-minus\')">+</a><a href="javascript:ShowOrHide(\''.$name.'\', \''.$name.'-minus\')" id="'.$name.'-minus" style="display: none;" onclick="javascript:ShowOrHide(\''.$name.'-plus\')">-</a>';

return $result;
}

/**
 * Вторая попытка организовать мультиязычность.
 *
 * Данная функция что-то вроде замены _() из библиотеки GetText.
 *
 * Вы ведь не поверите, если я скажу, что идея сделать так посетила меня прежде, чем я залез в сурсы Drupal`а? :)
 *
 * @param string $text
 * @param string $array
 * @return string
 */
function t($text, $array = array()){
global $mod, $plugin, $config, $sql, $gettext;

    if (!$text){
    	return;
    }

    $file  = languages_directory.'/'.$config['lang'].'/pack.txt';

    if (file_exists($file)){
    	if (!$gettext){
    		$gettext = unserialize(file_read($file));
    	}

	    if ($gettext[md5($text)]){
	        $text = $gettext[md5($text)];
	    } else {
	    	$gettext[md5($text)] = $text;
	    	file_write($file, serialize($gettext));
	    }
	}

    foreach ($array as $k => $v){
        $text = str_replace('%'.$k, $v, $text);
    }

	if ($config['charset'] != 'windows-1251'){
		$text = iconv('windows-1251', $config['charset'], $text);
	}

return $text;
}

function cute_setcookie($name, $value = '', $expire = '', $path = '', $domain = '', $secure = ''){
global $config;

	$return = @setcookie($config['cookie_prefix'].$name, $value, $expire, $path, $domain, $secure);

return $return;
}

function cute_stripslashes(&$item){

	if (is_array($item)){
		array_walk($item, 'cute_stripslashes');
	} else {
		$item = (get_magic_quotes_gpc() ? stripslashes($item) : $item);
	}

return $item;
}

function cute_htmlspecialchars(&$item){

	if (is_array($item)){
		array_walk($item, 'cute_htmlspecialchars');
	} else {
		$item = htmlspecialchars($item, ENT_QUOTES);
	}

return $item;
}

function array_save($filename, $array, $name = 'array'){

	$contents  = "<?\r\n";
	$contents .= '$'.$name.' = ';
	$contents .= var_export($array, true);
	$contents .= ";\r\n";
	$contents .= "\r\n?>";

return file_write($filename, $contents);
}

function save_config($array){

	$contents  = "<?\r\n";
	$contents .= '$config = ';
	$contents .= var_export($array, true);
	$contents .= ";\r\n";
	$contents .= '$allowed_extensions = array(\'gif\', \'jpg\', \'png\', \'bmp\', \'jpe\', \'jpeg\');';
	$contents .= "\r\n?>";

return file_write(config_file, $contents);
}

function tpl($func){

	if (!function_exists($func)){
		return false;
	} else {
		$args = func_get_args();
		array_shift($args); // возвраает имя функции, но мы имя и так знаем
		return call_user_func_array($func, $args);
	}
}

function function_help($func, $text = ''){

	$result = '<a href="http://strawberry.goodgirl.ru/docs/function/'.$func.'" onclick="window.open(\'http://strawberry.goodgirl.ru/docs/function/'.$func.'\', \'_FunctionHelp\', \'height=420,resizable=yes,scrollbars=yes,width=410\');return false;">'.($text ? $text : $func).'</a>';

return $result;
}

if (!function_exists('iconv')){
	include includes_directory.'/iconv.inc.php';
}
?>