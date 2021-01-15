<?php

namespace classes;

use inc\db\MySQL;

class CuteParser extends MySQL
{
	public $member  = [];
	public $select  = [];
	public $orderby = ['id', 'ASC'];
	public $logged  = false;
	public $randkeys = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
	public $ipApiUrl = 'http://ip-api.com/json';

	public $result  = [];

	protected $Config = [];
	
	public function __construct($config)
	{	
		parent::__construct();
		$this->Config = $config;
	}
	
	public function showHeadlines(
		$tpl = '{title}',
		$where = ['hidden <> 1'],
		$limit = [],
		$order = ['date', 'DESC'],
		$select = ['date', 'title', 'author', 'id', 'image', 'category', 'url', 'type'],
		$result = PHP_EOL
	) {
		
		if (empty($limit)) {
			$limit = [0, $this->Config['news_number']];
		}
		
		$query = $this->select([
			'news', 'select' => $select, 'where' => $where, 'limit' => $limit, 'orderby' => $order
		]);
		
		if (!reset($query)) {
			return false;
		}
			 
		foreach ($query as $row)
		{
			$link = cute_get_link ($row);
			$date = langdate('d.m.Y', $row['date']);
			$image = UPIMAGE .'/posts/'. $row['image'];
			$title = replace_news('show', $row['title']);

			$find = ['{link}', '{image}', '{category}', '{title}', '{date}'];
			$repl = [$link, $image, $category, $title, $date];
			$result.= str_replace($find, $repl, $tpl);
		}	
		return sprintf ('<ul>%s</ul>', $result);
	}
	
	public function msg($title,  $text = '', $type = 'info') {
	
		ob_start();
		include themes_directory.'/'.$type.'.tpl';
		$result = ob_get_clean();
		
		$result = str_replace('{{title}}', $title, $result);

		if (is_array($text)) { 
			foreach ($text as $k => $v) {
				$result.= str_replace('{{'.$k.'}}', $v, $result);
			}
		} else {
			$result = str_replace('{{text}}', $text, $result);
		}
		echo $result;
	}
	
	public function getRealIp()
	{
		foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $k) 
		{
			if ( array_key_exists($k, $_SERVER) ) {	
				foreach (explode(',', $_SERVER[$k]) as $ip)
				{
					return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : FALSE;
				}
			}
		}
	}
	
	public function pagination ($count, $number, $skip)
	{
		if ($count and $number)
		{ 
			$pages_skip  = 0;
			$pages_count = ceil($count / $number);

			for ($i = 1; $i <= $pages_count; $i++) {   
				$pages[] = $pages_skip!= $skip ? '<li><a href="'.cute_get_link(['skip' => $pages_skip], 'skip').'">'.$i.'</a></li>' : '<li class="active"><a>'.$i.'</a></li>';
				$pages_skip += $number;
			}

			$pages = $pages_count > 1 ? ($skip ? '<li><a href="'.cute_get_link(['skip' => ($skip - $number)], 'skip').'">'.t('&laquo;').'</a>' : '').join(' ', $pages).((($pages_skip - $number) - $skip) ? '<li><a href="'.cute_get_link(['skip' => ($skip + $number)],'skip').'">'.t('&raquo;').'</a>' : '') : '';
			
			return $pages;
		} 
	}
	
	public function value($sourse, $clean = false)
	{
		if ($clean) {
			$pattern = ['/\"([^\"]*)\"/' => '«$1»', '/ +/' => ' ', '/[\r\n]+/' => ' ', '/{nl}/' => ' '];
			$sourse  = preg_replace(array_keys($pattern), array_values($pattern), $sourse);
		}
		else {
			$sourse  = htmlspecialchars($sourse);
		}
	
		$sourse = strip_tags($sourse);
		return stripslashes(trim($sourse));   //stripslashes
	}
	
	// условие для загрузки изображений
	public function allowed_extensions($type)
	{
		if (empty($type) or !$this->Config['type_images_upload']){
			return false;
		}
		return in_array($type, explode(', ', $this->Config['type_images_upload']));
	}
	
	// Определение формата capcha
	public function getPincode($length = 6)
	{
		if (!isset($_GET['pincode'])) {
			return false;
		}

		// первые $length символов после перемешивания (str_shuffle)
		$captcha = substr(str_shuffle($this->randkeys), 0, $length);
		
		$_SESSION['pincode'] = $captcha;
	
		$image = imagecreatefromjpeg('themes/'.$this->Config['theme'].'/images/code_bg.jpg');
		$color = imagecolorallocate($image, 200, 150, 150);
		$rotat = rand(-5, 5);
		$font  = $_SERVER['DOCUMENT_ROOT'] .'/themes/'. $this->Config['theme'].'/fonts/x.ttf';

		imagettftext ($image, 10, $rotat, 10, 15, $color, $font, $captcha);
		header("Content-type: image/jpeg");
		imagejpeg($image);
	}
	
	// Проверка pin (capcha)
	public function pinCheck()
	{
		return (extension_loaded('gd') and ($_SESSION['pincode'] !== $_POST['pincode'])) ? true : false;
	}

	public function random_keys($length = 6, $password = '')
	{	
		$randkey = $this->randkeys;

		for ($i = 0; $i < $length; ++$i) {
			$password.= substr($randkey, (mt_rand() % strlen($randkey)), 1);
		}
	  
		return $password;
	}

	public function getGeoApi($ip = '', $url = '')
	{		
		$ip  = $ip  ? $ip : $this->getRealIp();
		$url = $url ? $url: $this->ipApiUrl;
		$url = $url .'/'. $ip;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($curl);
		curl_close($curl);

		return ($output = json_decode($output)) ? $output : false;
	}
	
	public function openTable ($attrubuts = [], $caption = '')
	{
		$attrubuts['width']  = $attrubuts['width']  ?: '100%';
		$attrubuts['border'] = $attrubuts['border'] ?: '0';
		
		foreach ($attrubuts as $k => $v) {
			$this->result[] = $k .'="'.$v.'"' ;
		}
		
		return '<table '.join(' ', $this->result).'>';
	}
	
	public function closeTable() {
		return "</tr>\n</table>";
	}
	
	function json_encode($arr){

		$parts 		= [];
		$is_list 	= false;
		if (!is_array($arr)) return;
		if (count($arr) < 1) return '{}';

		//Find out if the given array is a numerical array
		$keys 		= array_keys($arr);
		$max_length = count($arr) - 1;
		if (($keys[0] == 0) and ($keys[$max_length] == $max_length)) { //See if the first key is 0 and last key is length - 1
			$is_list = true;
			for ($i = 0; $i < count($keys); $i++) { //See if each key correspondes to its position
				if ($i != $keys[$i]) { //A key fails at position check.
					$is_list = false; //It is an associative array.
					break;
				}
			}
		}

		foreach ($arr as $key => $value) {
		
			if (is_array($value)) { //Custom handling for arrays
				
				$parts[] = $is_list ? $this->json_encode($value) : '"' . $key . '":' . $this->json_encode($value);
				
			} else {
				$str = '';
				if (!$is_list) $str = '"' . $key . '":';

				//Custom handling for multiple data types
				if (is_numeric($value)) $str .= $value; //Numbers
				elseif ($value === false) $str .= 'false'; //The booleans
				elseif ($value === true) $str .= 'true';
				else { //" - remaining
					$search  = ['\\',"\x00", "\x0a", "\x0d", "\x1a", "\x09" ,'"'];
					$replace = ['\\\\','\0', '\n', '\r', '\Z', '\t' , '\"'];
					$value   = str_replace($search, $replace, $value);
					$str    .= '"' . ($value) . '"'; //All other things
				}
				// :TODO: Is there any more datatype we should be in the lookout for? (Object?)
				$parts[] = $str;
			}
		}
		$json = implode(",", $parts);
		return $is_list ? '[' . $json . ']' : '{' . $json . '}'; // Return associative JSON
	}
}
