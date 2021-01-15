<?php

/**
 * Стандратне функции Strawberry, которые всегда доступны.
 * Если написано "многоязычная" - это значит, что результат данной функции
 * зависит от языка указнного в системных настройках.
 * Может случиться так, что в этом разделе нет нужной функции, то - увы - она нестандартая и
 * Вам, вероятно, придеться написать ее самому...
 * @package Functions
 */
 

 
/**
 * Возвращает размер файла в "читаемом" виде.
 * Возвращает размер $file_size (байт) в "читаемом" виде.
 * Многоязычная.
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
    }   return $file_size;
}


/**
 * Проверяет соотвествие указанного хеша паролю в БД.
 * Проверяет соотвествие указанного хеша пароля для пользователя $username
 * хешу пароля в БД. В качестве хеша должна быть строка полученная функцией md5x().
 * Также, в случаее успеха, передает в глобальную переменную $member массив,
 * содержащий всю информацию о авторизированом пользователе.
 * @see md5x()
 * @param string $username
 * @param string $md5_password
 * @return bool
 */
 
function check_login($username, $password)
{
	global $member, $users;

    $result = false;
    foreach ($users as $row){
        if (strtolower($username) == strtolower($row['username']) and $password == $row['password']){
	        $result = true;
            $member = $row;
        }
    } return $result;
}

/**
 * Формирует строку для запроса.
 * Формирует строку $q_string (такую как $_SERVER['QUERY_STRING'], например)
 * для запроса типа $type (POST или GET), игнорируя переменные, указанные в массиве $strips.
 * Прим. перев: со $strips тупо сделано - название игнорируемой переменной должно быть не значением массива, а его ключем.
 * @param string $q_string
 * @param array $strips
 * @param string $type
 * @return string
 */
 
function cute_query_string($q_string, $strips, $type = 'get', $parts = ''){
    
	$my_q = '';
    
	foreach ($strips as $key){
    	$strips[$key] = true;
    }

    foreach(explode('&', $q_string) as $var_peace){
        $parts = explode('=', $var_peace);

        if ( reset($parts) and empty($strips[$parts[0]]) ){
            if (strtolower($type) == 'post'){
            	$my_q .= '<input type="hidden" name="'.$parts[0].'" value="'.$parts[1].'" />';
            } else {
            	$my_q .= '&'.$var_peace;
            }
        }
    }   return $my_q;
}

/**
 * Выводит строку таблицы (для ACP).
 * Выводит строку таблицы с заголовком $title, описанием $description
 * и полем $field в панеле управления.
 * @param string $title
 * @param array $description
 * @param string $field
 * @return string
 */
 
function showRow($title = '', $description = '', $field = ''){
	global $i;
	
    $bg = cute_that();
	echo PHP_EOL . 
		'<tr '.$bg.'>
			<td class="opt-title">'.$title.'</td>
            <td rowspan="2" class="opt-space">'.$field.'</td>
		<tr '.$bg.'>
			<td valign="top" class="opt-desc">'.$description.'</td>';
	
    $bg = '';
    $i++;
}

/**
 * Создает выпадающее меню.
 * Создает элемент формы select с аттрибутом name = $name и
 * набором значений option из ассоц. массива $options (['key'] => 'value')
 * и аттрибутом selected для значения, если $selected = значению ключа $options.
 * @param array $options
 * @param string $name
 * @param string $selected
 * @return string
 */
 
function makeDropDown(array $options, $name, $selected = '') {

    $output  = '';
	foreach ($options as $k => $v) {
    	$output.= '<option value="'.$k.'"'.(($selected == $k) ? ' selected' : '').'>'.$v.'</option>'.PHP_EOL;
    }   
	
	return '<select size="1" id="'.$name.'" name="'.$name.'">' .PHP_EOL. $output .PHP_EOL. '</select>';
}


function makeDataList(array $options, $name, $selected = '') {
	
	$output = '<input type="text" list="'.$name.'" name="'.$name.'" />'.PHP_EOL;
	$output.= '<datalist id="'.$name.'">'.PHP_EOL;
	
	foreach ( $options as $k => $v ) {
		$output.= '<option value="'.$k.'">'.$v.'</option>'.PHP_EOL;
	}
	
	$output.= '</datalist>'; 
	return $output;
}

/**
 * Enter description here...
 * @access private
 * @param string $ip
 * @param int $id
 * @return bool
 */

function flooder($ip, $id) : boolian
{
	global $config, $sql;
	
	foreach ($sql->select(['flood', 'select' => ['date'], 'where' => ["ip = $ip", 'and', "post_id = $id"]]) as $row){
		
		if (($row['date'] + $config['flood_time']) > time){
			return true;
		} else {
			$sql->delete(['flood', 'where' => ["date = $row[date]", 'and', "ip = $row[ip]", 'and', "post_id = $row[post_id]"]]);
		}
	} 
	return false;
}

/**
 * Выводит сообщение в ACP.
 * Выводит сообщение $text, с заголовком $title и картинкой $type с шаблоном панели управления,
 * прерывая дальнейшее выполнение скрипта в панеле управления.
 * Если $back = true, то будет выведена ссылка, ведущая на предыдущую страницу.
 * В $type нужно передать имя файла картинки из skins/$skin_prefix/.
 * @see echoheader(), echofooter()
 * @param string $type
 * @param string $title
 * @param string $text
 * @param string $back
 */

function msg ($type, $title, $text, $back = '') {
	
	echoheader($type, $title);
	echo '<div class="msg '.$type.'">'.$text .($back ? '<br /><br />
	<a href="'.$back.'">'.t('Вернуться назад').'</a>' : '').
	'</div>';
	echofooter();
	exit;
}

/**
 * Выводит верхнюю часть шаблона ACP.
 * Выводит верхнюю часть шаблона панели управления с картинкой $image
 * и заголовком $header_text.
 * @param string $image
 * @param string $header_text
 */

function echoheader($image, $header_text){
	global $config, $PHP_SELF,  $is_logged_in, 
	$skin_header, 
	$skin_menu, 
	$skin_prefix;

    $skin_header = $is_logged_in ? str_replace('{menu}', $skin_menu, $skin_header) : str_replace('{menu}', ''.$config['home_title'], $skin_header);
    $skin_header = str_replace('{image-name}', $skin_prefix.$image, $skin_header);
    $skin_header = str_replace('{header-text}', $header_text, $skin_header);
    echo $skin_header;
}

/**
 * Выводит нижнюю часть шаблона ACP.
 *
 * @return void
 */
function echofooter(){
	global $PHP_SELF, $is_logged_in, $config, $skin_footer, $skin_menu, $skin_prefix;

    $skin_menu   = $is_logged_in ? $skin_menu : $config['version_name'];
    $skin_footer = str_replace('{menu}', $skin_menu, $skin_footer);
    $skin_footer = str_replace('{copyrights}', 'Powered by <a  href="http://strawberry.goodgirl.ru/" target="_blank">'.$config['version_name'].' '.$config['version_id'].'</a> &copy; 2006 <a  href="http://goodgirl.ru/" target="_blank">GoodGirl</a>', $skin_footer);
    echo $skin_footer;
}

/**
 * Возвращает кликабельные смайлы.
 * Возвращает кликабельные смайлы по $break_location на строку. При клике на смайл
 * в поле $insert_location будет автоматически вставлен его синоним.
 * @param string $insert_location
 * @param int $break_location
 * @return string
 */
 
function insertSmilies($insert_location, $break_location = 0){

	global $config;
	if (!$config['smilies'] or !$smilies = explode(',', $config['smilies']))
	{
    	return '';
    }
    
    foreach ($smilies as $smile){
        $i++;
		$output.= '<a href="javascript:insertext(\':'.trim($smile).':\', \'\', \''.$insert_location.'\')"><img style="width:10px; border:none;" alt="'.trim($smile).'" src="'.UPIMAGE.'/emoticons/'.trim($smile).'.gif" /></a>'; 
		$output.= '&nbsp;';
    }
	return $output;
}

/**
 * Enter description here...
 * @access private
 * @param string $way
 * @param string $sourse
 * @return string
 */

function replace_comment($way, $sourse, $clear = false)
{
	global $config;

    if ($way == 'add') {

		$find = ["\n", "\r", '&nbsp;'];
	    $repl = ['<br />', '', ' '];
		$sourse = htmlspecialchars($sourse);
		$sourse = mb_ereg_replace('[ ]+', ' ', $sourse);

		if (!get_magic_quotes_gpc())
		{
	    	$sourse = addslashes($sourse);
	    }

    } elseif ($way == 'show') {

	    $find = ['&amp;', '&nbsp;'];
		$repl = ['&', ' '];

		$sourse = stripslashes($sourse);

        foreach (explode(',', $config['smilies']) as $smile) {
            $find[] = ':'.trim($smile).':';
            $repl[] = '<img style="width:10px; border:0; vertical-align:middle;" alt="'.trim($smile).'" src="'.$config['http_script_dir'].'/uploads/emoticons/'.trim($smile).'.gif" />';
		}
		
    } elseif ($way == 'admin'){
		
		$find = ['<br />'];
		$repl = ["\n"];

	    $sourse = unhtmlentities($sourse);
    	$sourse = stripslashes($sourse);
	}
	
	return str_replace($find, $repl, trim($sourse));
}

/**
 * Enter description here...
 * @access private
 * @param string $way
 * @param string $sourse
 * @param bool $replce_n_to_br
 * @param bool $use_html
 * @return string
 */

function replace_news($way, $sourse, $replce_n_to_br = true, $use_html = true){
	global $config;

    if ($way == 'show'){
		
    	$find = ['{nl}', '&#039;'];
       	$repl = ['', '\'']; 
		//$replace = ['<br />', '\''];
       	$sourse  = stripslashes($sourse);

        foreach (explode(',', $config['smilies']) as $smile){
            $find[] = ':'.trim($smile).':';
            $repl[] = '<img style="width:10px; border:0; vertical-align:middle;" alt="'.trim($smile).'" src="'.$config['http_script_dir'].'/data/emoticons/'.trim($smile).'.gif" />';
        }
		
    } elseif ($way == 'add') {
	
        $find = ["\r", "\n"];
        $repl = ['', '{nl}'];
        $sourse = addslashes($sourse);
		
    } elseif ($way == 'admin'){
        $find = ['{nl}'];
        $repl = ["\n"];
        $sourse  = stripslashes($sourse);
    }
       
	return str_replace($find, $repl, trim($sourse));
}

/**
 * Enter description here...
 * @access private
 * @param array $array
 * @param bool $bool
 * @return string
 */

function echo_r($array, $bool = false){
    ob_start();
    if (is_bool($array)){
    	echo $array ? 'true' : 'false';
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
 * Отправляет почту, адресованную $to, с темой письма $subject и сообщением $message.
 * Возможно "приаттачивание" файла $filename к письму.
 * @param string $to
 * @param string $subject
 * @param string $message
 * @param string $filename
 */

function cute_mail($to, $subject, $message, $filename = ''){
	global $name, $mail, $config;

	$mail    = $mail ? $mail : 'no-reply@'.str_replace('www.', '', $_SERVER['SERVER_NAME']);
	$from    = $name ? $name. ' <'.$mail.'>' : $mail;  //From: $name <$email>
	$uniqid  = md5(uniqid(time));
	$format  = $config['mail_format'] ? 'html' : 'plain';
	$headers = 'From: '.$from."\n";
	$headers.= 'Reply-to: '.$from."\n";
	$headers.= 'Return-Path: '.$from."\n";
	$headers.= 'Message-ID: <'.$uniqid.'@'.$_SERVER['SERVER_NAME'].">\n";
	$headers.= 'MIME-Version: 1.0'."\n";
	$headers.= 'Date: ' .gmdate('D, d M Y H:i:s', time). "\n";
	$headers.= 'X-Priority: 3'."\n";
	$headers.= 'X-MSMail-Priority: Normal' . "\n";
	$headers.= 'X-Mailer: '.$config['version_name'].' '.$config['version_id']."\n";
	$headers.= 'X-MimeOLE: '.$config['version_name'].' '.$config['version_id']."\n";
	$headers.= 'Content-Type: multipart/mixed;boundary="----------'.$uniqid.'"'."\n\n";
	$headers.= '------------'.$uniqid."\n";
	$headers.= 'Content-type: text/'.$format.'; charset='.$config['charset']."\n";
	$headers.= 'Content-transfer-encoding: 7bit';

	if (is_file($filename))
	{
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
 * Рекурсивно меняет права всем файлам и папкам на $mod в директории $dir.
 * @link http://forum.dklab.ru/php/advises/FaylovieFunktsii.html
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
        return chmod($dir, $mod) ? true : false;
}

/**
 * Enter description here...
 * @access privat
 * @param string $action
 * @param array $sort
 * @return array
 */
function c_array($action, $sort = ''){
	global $sql;
	
	$query = is_array($sort) ? [$action, 'orderby' => $sort] : [$action];

    foreach ($sql->select($query) as $k => $v){
    	$result[] = implode('|', $v);
    }

	return $result ? $result : [];
}

/**
 * Отделяет мух от супа.
 * Другими словами, заменяет все повторения строки $dick
 * на одно и вырезает все повторения $dick по "бокам" строки $chicken.
 * @param string $chicken
 * @param string $dick
 * @return string
 */

function chicken_dick($chicken, $dick = '/')
{
	$chicken = preg_replace('/^(['.preg_quote($dick, '/').']+)/', '', $chicken);
	$chicken = preg_replace('/(['.preg_quote($dick, '/').']+)/', $dick, $chicken);
	$chicken = preg_replace('/(['.preg_quote($dick, '/').']+)$/', '', $chicken);
	return $chicken;
}

/**
 * Запись данных в файл.
 * Записывает строку $fwrite в файл $fopen, попутно измеяя права файлу $fopen на $chmod
 * или права по умолчанию, если аргумент $chmod = false.
 * Если $clear = true, то данные будут записаны в файл без символов переноса строки и
 * возврата коретки.
 * @param string $fopen
 * @param string $fwrite
 * @param bool $clear
 * @param int $chmod
 */

function file_write($fopen = '', $fwrite = '', $clear = false, $chmod = chmod)
{
	if ($clear)
	{
		$fwrite = str_replace('  ', '', str_replace("\r\n", '', $fwrite));
    }
    if (($fp = fopen($fopen, 'wb+')) !== false) {
		fwrite($fp, $fwrite);
		@chmod($fopen, $chmod);
		fclose($fp);
	}
}

/**
 * Чтение из файла.
 * Возвращает содержимое файла $filemame или false.
 * @param string $filemame
 * @return string
 */

function file_read($filename)
{
    if (!is_file($filename)){
    	return false;
	}
	
	if(($fp = fopen($filename, 'rb')) !== false)
	{
		$fo = fread($fp, filesize($filename));
		fclose($fp);
		return $fo;
	}
}

/**
 * Возвращает ассоциативный массив с элементами урла $uri.
 * Возвращает ассоциативный массив, полученный функцией parse_url(),
 * + ключ abs - абсолютный путь к диретории.
 * @param string $url
 * @return array
 */

function cute_parse_url($url)
{
	global $DOCUMENT_ROOT;

    $url  = parse_url($url);
	$url['path'] = chicken_dick($url['path']);
	
	if (!isset($url['abs'])) {
		$url['abs']  = $DOCUMENT_ROOT.'/'.$url['path'];
	}
	
	if (is_file($url['abs']))
	{
		$url['file'] = explode('/', $url['path']);
		$url['file'] = end($url['file']);
    	$url['path'] = chicken_dick(preg_replace('/'.$url['file'].'$/i', '', $url['path']));
    	$url['abs']  = $DOCUMENT_ROOT.'/'.$url['path'];
    }   return $url;
}

/**
 * Возвращает урл, сформированный по указанным правилам из urls.ini.
 * Возвращает урл, сформированный по правилу $type секции $format из urls.ini.
 * Прим. перев: честно говоря - какая-то запутанная функция, однуму автору известно
 * как и почему это все работает.
 * @param array $arr
 * @param string $type
 * @param string $format
 * @return string
 */

function cute_get_link( array $arr, $type = 'post', $format = '' ){
	
	global $config, $link, $rufus_file, $vars, $time, $QUERY_STRING;
	static $c = [];
	
	if( $type == 'skip' or $type == 'cpage' ) {
        
		$mask = preg_replace('/(\?|&)'.$type.'\=([0-9]+)/', '', $_SERVER['REQUEST_URI']);
        $mask = $mask.(strstr($mask, '?') ? '&' : '?').$type.'='.$arr[$type];
        $mask = str_replace('?skip=0', '', $mask); // нах ссылки на нулевую страницу!
		return $mask;
    }
	
	if( $type == 'go' ) {
		$mask = preg_replace('/\//', '', $arr[$type]);
        //$mask = $mask.(strstr($mask, '?') ? '&' : '?').$type.'='.$arr[$type];
		return $mask;
	}

    if(!$rufus_file){ //Чибурашко где-то рядом!
        $rufus_file = parse_ini_file(rufus_file, true);
    }

    if($link and !$format){
    	$format = chicken_dick($link);
    } elseif(!$link and !$format){
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

    if ( !isset($arr['date']) ) {
		$arr['category'] = $arr['id'];
    }

    if ( !isset($arr['author']) ) {
		$arr['author']  = $arr['username'];
		$arr['user_id'] = $arr['id'];
    }

    if ($rufus_file[$format][$type]){
    	$rufus_file[$type] = $rufus_file[$format][$type];
    } else {
		$rufus_file[$type] = $format;
		$QUERY_STRING = cute_query_string($QUERY_STRING, [$type]);
    }

    if (!$c){
		$c = ['home_url' => cute_parse_url($config['http_home_url']), 'script_url' => cute_parse_url($config['http_script_dir']), 'q_string' => cute_query_string($QUERY_STRING, $vars)];
    }
	
    $mask = run_filters('cute-get-link', ['arr' => $arr, 'link' => $rufus_file[$type]]);
    $mask = $mask['link'];
	$mask = explode(':', $mask);
    $mask = reset($mask);
    
	$category = explode(',', $arr['category']);
	$category = reset($category);
	$mask     = strtr($mask, [
		'{add}'   => '',
		'{type}'  => isset($arr['type']) ? $arr['type'] : '',
		'{year}'  => isset($arr['date']) ? date('Y', $arr['date']) : '',
		'{month}' => isset($arr['date']) ? date('m', $arr['date']) : '',
        '{day}'   => isset($arr['date']) ? date('d', $arr['date']) : '',
		
        '{title}'   => $arr['url'] ?? totranslit($arr['title']),
        '{url}'     => $arr['url'] ?? totranslit($arr['title']),
        '{user}'    => urlencode($arr['author']),
        '{user-id}' => $arr['id'],
        '{category-id}' => ($category ? $category : '0'),
        '{category}'    => ($category ? category_get_link($category) : (!empty($arr['type']) ? $arr['type'] : 'post')),
        '{categories}'  => ($category ? category_get_link($category) : (!empty($arr['type']) ? $arr['type'] : 'post')),
    ]);

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
	    //} elseif (substr($uri, 0, 1) == '?'){
	    //	$result = $c['script_url']['path'].'/'.$format;
	    } else {
	    	$result = $c['home_url']['path'];
	    }
   	}
	
	if ($arr['type'] == 'page' and $arr['url'] == 'main'){
		$result = DS;
    } else {
		$result = chicken_dick($result.'/'.$mask).$c['q_string'];
		$result = str_replace('/?', '?', $result);
		$result = htmlspecialchars($c['home_url']['scheme'].'://'.$c['home_url']['host'].(!empty($c['home_url']['port']) ? ':'.$c['home_url']['port'] : '').'/'.$result);
	} 	
	
	return $result;
}

 
function orderBy($data, $field = 'order')
{
    $code = "return strnatcmp(\$a['$field'], \$b['$field']);";
    usort($data, create_function('$a, $b', $code));
    return $data;
}

/**
 * Возвращает ссылку на категорию.
 * Возвращает ссылку на категорию с id = $id, учитывая все категории-родители.
 * Можно указать суффикс урлу, передав значение переменной $link.
 * @param int $id
 * @param string $link
 * @return string
 */
 
function category_get_link($id, $link = '')
{
	global $categories;

    if (isset($categories[$id]['url'])) {
    	$link = $categories[$id]['url'].($link ? '/'.$link : '');
    }

    if (isset($categories[$id]['parent'])) {
    	$link = category_get_link($categories[$id]['parent'], $link);
	}   
	return chicken_dick($link);
}


/**
 * Возвращает всех детей из таблиц 
 * категорий и каталогов c полями `id` и `parent`.
 * @author Yury D. Spolokh
 * @param int $id
 * @return string
 */
 
function getChildren( array $query = [], int $id = 0, int $limit = 0 ) : string {

	static
		$end = 1, 
		$result = [];

	if (!reset($query)) {
		return []; 
	}

	if (!empty($id)) {
		$result[] = $id; 
	}
	
	foreach($query as $row) {
		if ($row['parent'] == $id ){
			$result[] = $row['id'];
		}
	}	
	
	$end++;
    $return = $result;
    $result = [];
	return reset($return) ? join(',', $return) : [];
}

/**
 * Возвращает всех детей категории.
 * Возвращает всех детей категории с id = $id в виде строки с id категорий, разделенных запятыми.
 * @author Scip
 * @param int $id
 * @return string
 */

function category_get_children($id, $withid = true, $limit = 0)
{
	global $categories;
	static $end = 1, $result = [];

    $categories_dummy = $categories;  // u could avoid this if u RESET $categories;

    if ($id === ''){
    	return false;
    }

    if ($withid){
		$result[] = $id;
    }

	foreach ($categories_dummy as $k => $row)
	{
        if ($row['parent'] == $id) {
            $result[] = $k;

            if ($limit - $end){
				$result[] = category_get_children($k, false, $limit);
            }
        }
    }

    $end++;
    $return = $result;
    $result = [];
    return $return;
}

/**
 * Возвращает название категории.
 * Возвращает название категории с id = $id, включая названия всех родительских категорий данной категории, названия отделяютсья друг от друга разделителем $separator.
 * Можно указать суффикс названию, передав значение переменной $title.
 * @param int $id
 * @param string $separator
 * @param string $title
 * @return string
 */

function category_get_title($id, $separator = ' &raquo; ', $title = '')
{
	global $categories;

	if (empty($categories))
	{
		return false;
	}

    if ($categories[$id]['name']) {
    	$title = $categories[$id]['name'].($title ? $separator.$title : '');
    }

    if ($categories[$id]['parent']) {
    	$title = category_get_title($categories[$id]['parent'], $separator, $title);
    }   
	  
	return chicken_dick($title);
}

/**
 * Возвращает древо категорий.
 * Возвращает древо категорий, используя шаблон $tpl и префикс (приставку) $prefix для вывода категории. Корнем древа будет категория с id = $id или будет показан список всех категорий, если $id = 0.
 * Теги для использования в шаблоне вывода:
 * {name} - название категории,
 * {url} - УРЛ категории,
 * {prefix} - указаный $prefix,
 * {id} - ID категории,
 * {icon} - иконка категории,
 * {template} - шаблон категории,
 * [php] и [/php] - между этими тегами указывается php-код, который будет выполнен (например: [php]function({id})[/php]).
 * Также можно избежать вывода префикса для корней древа передав переменной $no_prefix значение true.
 * @param string $prefix
 * @param string $tpl
 * @param bool $no_prefix
 * @param int $id
 * @return string
 */

function category_get_tree($prefix = '', $tpl = '{name}', $no_prefix = true, $id = 0)
{
	global $categories;

    $minus  = 0;
	$result = '';

	if (empty($categories)) {
		return false;
	}

	$find = ['/{(id|name|url|parent|level|icon|prefix)}/', '/\[php\](.*?)\[\/php\]/'];
    
	foreach (build_tree($categories) as $k => $row)
	{
	    if ($id) {
	        if ($id == $row['id']){
                $minus++; continue;
	       	}

	        if (!in_array($row['id'], category_get_children($id))) {
                $minus++; continue;		
	        }
	    }

		$pref = $no_prefix ? $row['level'] : ($row['level'] + 1);
		$pref = $minus ? ($pref - (!$no_prefix ? ($minus - 1) : ($minus - 1))) : $pref;
		$pref = str_repeat($prefix, $pref);
		$row['prefix'] = $pref;
	
		$result.= preg_replace_callback($find, function($m) use ($row) {
			return isset($row[$m[1]]) ? $row[$m[1]] : eval('return ' .$m[1]. ';');
		}, $tpl);
	}
  
  	return $result;
}

/**
 * Возвращает id категории.
 * Аргументом должен передоваться урл категории, аналогичный
 * урлу, полученному функцией category_get_link();
 * @see category_get_link()
 * @param string $cat
 * @return int
 */

function category_get_id ($cat) : int
{
	global $categories;
	
	if (empty($categories))
	{
		return false;
	}

	$cat = chicken_dick($cat);
	
	foreach ($categories as $row)
	{
		if ($cat == category_get_link($row['id'])) {
    		$catId = $row['id'];
    	} elseif ($cat == category_get_title($row['id'], '/')) {
    		$catId = $row['id'];
    	} elseif ($cat == $row['url']) {
    		$catId = $row['id'];
    	} elseif ($cat == $row['id']) {
    		$catId = $row['id'];
    	} elseif ($cat == 'none') {
    		$catId = 0;
    	}
	}
	return (int) $catId;
}

////////////////////////////////////////////////////////////////////////
function catalog_get_title($id, $separator = ' &raquo; ', $title = '') {
	global $catalogs;

    if ($catalogs[$id]['name']){
    	$title = $catalogs[$id]['name'].($title ? $separator.$title : '');
    }

    if ($catalogs[$id]['parent']){
    	$title = catalog_get_title($catalogs[$id]['parent'], $separator, $title);
    }   return chicken_dick($title);
}

/////////////////////////////////////////////
function catalogs_get_tree($prefix = '', $tpl = '{name}', $no_prefix = true, $id = 0){
	global $catalogs;

    $minus  = 0;
    $result = '';
    
    if (empty($catalogs)){
		return false;
	}

	$find = ['/{(id|name|url|parent|level|icon|prefix)}/', '/\[php\](.*?)\[\/php\]/'];
		
	foreach ($catalogs as $row) {
	        
		if ($id){
			if ($id == $row['id']){
				$minus++; continue;
			}
			
			if ($id != $row['parent']){
				$minus++; continue;
			}
		}

		$pref = $no_prefix ? $row['level'] : ($row['level'] + 1);
		$pref = $minus ? ($pref - (!$no_prefix ? ($minus - 1) : ($minus - 1))) : $pref;
		$pref = str_repeat($prefix, $pref);
		$row['prefix'] = $pref;
		
		$result.= preg_replace_callback($find, function($m) use ($row) {
			return isset($row[$m[1]]) ? $row[$m[1]] : eval('return ' .$m[1]. ';');
		}, $tpl);
	} 	
	
	return $result;
}


/**
 * Enter description here...
 * @access private
 * @param string $return1
 * @param string $return2
 * @param int $every
 * @return string
 */
 
function cute_that($odd = 'class="enabled"', $even = 'class="disabled"', $every = 2){
	static $i = 0;
	$i++;
	return ($i%$every == 0) ? $odd : $even;
}

/**
 * Возвращает строки из языкового файла.
 * Возвращает строки из языковых файлов global.ini и $module.ini, если таковой существует, в виде массива.
 * Данной функцией для перевода пользоваться не стоит. Используйте t().
 * @see t()
 * @param string $module
 * @return array
 */
function cute_lang($module = ''){
	global $config;

	$module = explode('/', $module);
	$module = end($module);

	if (!file_exists($local = languages_directory.'/'.$config['language'].'/'.$module.'.ini')){
		$local = languages_directory.'/ru/'.$module.'.ini';
	}

	if (file_exists($local)){
		$lang = parse_ini_file($local, true);
	}
	return $lang;
}

/**
 * Шифрует строку.
 * @param string $str
 * @return string
 */
function md5x($str){
	$str = md5(md5($str));
	return $str;
}

/**
 * Функция выполняет действие обратое функции htmlentities()
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
 * @access private
 * @param string $str
 * @return string
 */
 
function mb_namespace($str, $table = 'news'){
	global $sql, $mod;

	foreach ($sql->select([$table, 'select' => ['id', 'url']]) as $row){
		if ( preg_match("/$str([0-9]+)?/i", $row['url']) ){
			$result[] = $row['id'];
		}

		if ( preg_match("/$str([0-9]+)?/i", $row['id']) ){
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
 * @access private
 * @param array $array
 * @param int $id
 * @param array $field
 * @return array
 */

function build_tree (array $array, int $id = 0, $field = ['parent', 'id'], array $result = [])
{
	foreach($array as $k => $row)
	{
        if ($row[$field[0]] == $id) {   
			$result[$k] = $row;
			$result = build_tree($array, $row[$field[1]], $field, $result);
		}
    }  
    return $result;
}

/**
 * Enter description here...
 * @access private
 * @param string $mod
 * @param string $section
 * @param array $arr
 * @return bool
 */
 
function cute_get_rights($mod = '', $section = 'permissions'){
	global $usergroups, $member;

	$return = false;

	if (!isset($member['usergroup'])) {
		return false;
	}
	
	$group = $usergroups[$member['usergroup']];
	 
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
 * @param string $filename
 * @param array $content
 * @return bool
 */
function write_ini_file($filename, $content){

	foreach ($content as $k => $v){
		if (is_array($v)){
			$result.= '['.$k.']'."\n";

			foreach ($v as $key => $value){
				$result.= $key.' = "'.$value.'"'."\n";
			}
		} else {
			$result.= $key.' = "'.$value.'"'."\n";
		}
	}

	return file_write($filename, $result) ? true : false;
}

/**
 * Делает плюс-минус, как в "Группах пользователей"
 * @param string $name
 * @return string
 */
 
function makePlusMinus($name){
    $result = '<a href="javascript:ShowOrHide(\''.$name.'\', \''.$name.'-plus\')" id="'.$name.'-plus" onclick="javascript:ShowOrHide(\''.$name.'-minus\')">+</a><a href="javascript:ShowOrHide(\''.$name.'\', \''.$name.'-minus\')" id="'.$name.'-minus" style="display: none;" onclick="javascript:ShowOrHide(\''.$name.'-plus\')">-</a>';
    return $result;
}

/**
 * Делает невидимые поля
 * @param string $name
 * @return string
 */
 
function makeHiddenDiv($name, $plus, $minus){
    $result = '<a href="javascript:ShowOrHide(\''.$name.'\', \''.$name.'-plus\')" id="'.$name.'-plus" onclick="javascript:ShowOrHide(\''.$name.'-minus\')">'.$plus.'</a><a href="javascript:ShowOrHide(\''.$name.'\', \''.$name.'-minus\')" id="'.$name.'-minus" style="display: none;" onclick="javascript:ShowOrHide(\''.$name.'-plus\')">'.$minus.'</a>';
    return $result;
}

/**
 * Вторая попытка организовать мультиязычность.
 * Данная функция что-то вроде замены _() из библиотеки GetText.
 * Вы ведь не поверите, если я скажу, что идея сделать так посетила меня прежде, чем я залез в сурсы Drupal`а? :)
 * @param string $text
 * @param string $array
 * @return string
 */

function t($text, $array = []){
    global $mod, $plugin, $config, $gettext;

	if (!$text){
    	return;
    }

    $file = languages_directory.'/'.$config['language'].'/pack.txt';
			
	if (file_exists($file)) {
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

	//if ($config['charset'] != 'utf-8'){
		//$text = iconv('windows-1251', $config['charset'], $text);
	//}
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
		$item = get_magic_quotes_gpc() ? stripslashes($item) : $item;
	}   return $item;
}

function cute_htmlspecialchars(&$item){

	if (is_array($item)){
		array_walk($item, 'cute_htmlspecialchars');
	} else {
		$item = htmlspecialchars($item);
	}   return $item;
}

function array_save($filename, $array, $name = 'array'){
	$contents = "<?php\r\n";
	$contents.= '$'.$name.' = ';
	$contents.= var_export($array, true);
	$contents.= ";\r\n";
	$contents.= "\r\n?>";
	return file_put_contents($filename, $contents);
}

function save_config($array){
	$contents = "<?php return ".var_export($array, true).";\r\n"; 
	return file_put_contents(config_file, $contents);
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

function tmp_selected ($id, $parent) {
	return ($id == $parent) ? ' selected' : '';
}

function function_help($func, $text = ''){
	$result = '<a href="http://strawberry.goodgirl.ru/docs/function/'.$func.'" onclick="window.open(\'http://strawberry.goodgirl.ru/docs/function/'.$func.'\', \'_FunctionHelp\', \'height=420,resizable=yes,scrollbars=yes,width=410\');return false;">'.($text ? $text : $func).'</a>';
	return $result;
}

function cropstr($string, $size, $symb)
{	
	$size = (int) $size;
    // if (mb_strlen($string) > $size)
    //    $string = mb_substr($string, 0, mb_strrpos(mb_substr($string, 0, $size, 'utf-8'),' ', utf-8), 'utf-8');
    if (mb_strlen($string) > $size)
        $string = mb_substr($string);
    if (mb_strlen($string) > mb_strlen($string))
		$string = $string . $symb;
		
    //return $string;
}

//////функция вывода текстовых блоков//////
function get_block($block, $firstText = '')
{
	$firstFile  = textdata.'/'.$block;
    
	if ($handle = fopen($firstFile, "rb"))
	{
		do {
			$data = fread($handle, 8192);
			if (strlen($data) == 0) {
				break;
			}

            $firstText.= $data;
		} while (true);
		
		fclose($handle);
   	}    
   
   	echo str_replace("\n", "<br/>", $firstText);
}



function sp_number($n, $titles)
{
  $cases = [2, 0, 1, 1, 1, 2];
  return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
}

function AutoLoader($class)
{
	$file = __DIR__ .DS. str_replace('\\', '/', $class) . '.php';
	//echo $file;	
	if (file_exists($file))
	{
		include_once $file;
		
		if (class_exists($class))
		{
			return true;
		}
	}
	return false;
}

function writeTextOnImage($filename, $text)
{   
	$size_img = getimagesize($filename);
	 
	if ($size_img[2]==2)      
		$src_img = imagecreatefromjpeg($filename);  
	else if ($size_img[2] == 1) 
		$src_img = imagecreatefromgif($filename);  
	else if ($size_img[2] == 3)
		$src_img = imagecreatefrompng($filename);    
    
	// устанавливаем цвет нашей надписи и прозрачность (тут он будет синий и полностью прозрачный)
    $color = imagecolorallocatealpha($src_img, 0, 0, 255, 0);  
    $font_file = "x.ttf";  // шрифт, которым пишем надпись (будьте внимательны с путем к шрифту)
    $img_x = imagesx($src_img); 
    $img_y = imagesy($src_img);     
    $height_font = 24; // размер шрифта 
    $angle = 0;  // наклон надписи
     
    // Запись текста поверх изображения  
    $box = imagettftext($src_img, $height_font, $angle, $img_x - 230, $img_y - 10, $color, $font_file, $text); 
 
    // Вывод изображения в браузер  
    if ($size_img[2]==2)  
    {  
        header ("Content-type: image/jpeg");  
        imagejpeg($src_img);  
    }  
    else if ($size_img[2]==1)  
    {  
        header ("Content-type: image/gif");  
        imagegif($src_img);  
    }  
    else if ($size_img[2]==3)  
    {  
        header ("Content-type: image/png");  
        imagepng($src_img);  
    }  
    return true;  
} 
// использование  
//$img = 'img.jpg'; // путь к изображению
//writeTextOnImage($img, "SnipCode.ru"); // тут "SnipCode.ru" - это наш текст, который будет поверх картинки

if (!function_exists('iconv')) {
	include includes_directory.'/iconv.inc.php';
}

if(!function_exists('json_decode'))
{
    function json_decode($json)
    {
        $comment = false;
        $out = '$x=';
        for ($i=0; $i < strlen($json); $i++)
        {
            if (!$comment)
            {
                if (($json[$i] == '{') || ($json[$i] == '['))
                    $out.= '[';
                else if (($json[$i] == '}') || ($json[$i] == ']'))
                    $out.= ']';
                else if ($json[$i] == ':')
                    $out.= '=>';
                else
                    $out.= $json[$i];
            }
            else
                $out.= $json[$i];
            if ($json[$i] == '"' && $json[($i-1)]!="\\")
                $comment = !$comment;
        }
        eval($out . ';');
        return $x;
    }
}
