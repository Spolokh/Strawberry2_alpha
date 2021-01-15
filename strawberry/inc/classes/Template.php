<?php

/**
 * project: ultra-templates is the php compiling easy template engine
 * file: ultratpl.class.php
 * 
 * This class is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * 
 * @link http://www.wap-relax.net/templates/
 * @copyright 2008 zick
 * @author Artem Egorov aka zick <xyasse@inbox.ru>
 * @package ultra-templates
 * @version 1.0b
 */

namespace classes;
 
class Template	{

	const VARIABLE 	= '(?:\$|%|\$%)[\w]+(?:\.%?[\w]+)*';	// regexp for variables
	const OPERATOR 	= '[\s!|><&)(:^?%.\/*+=-]';			// regexp for operators
	const NUMBER 	= '[0-9.]+?';
	
	/**
	 * @desc Дирректория для шаблонов
	 * @access public
	 * @var string
	 */
	public $dir = '';
	
	/**
	 * @desc Расширение файлов для шаблонов
	 * @access public
	 * @var string
	 */

	public $ext = '.tpl';
	
	/**
	 * @desc Данные компиляции шаблонов
	 * @access public
	 * @var array
	 */

	public $result = [];
	public $values = [];
	
	/**
	 * @desc ������ ��� ������ ����������
	 * @access public
	 * @var array
	 */
	public $data = [];
	
	/**
	 * @desc �������� ���� ��������
	 * @access public
	 * @var array
	 */
	public $source = [];
	
	/**
	 * @desc ������ ��� ������ ������
	 * @access public
	 * @var array
	 */
	public $data_block = [];
	
	/**
	 * @desc ����� ������� ��� ����������
	 * @access public
	 * @var integer
	 */
	public $time_busy = 0;
	
	/**
	 * @desc ������� �������������������� ���������� �� �������?
	 * @access public
	 * @var boolean
	 */
	public $delete_tags = true;
	
	/**
	 * @desc ����������� � ����������� ���� ����������
	 * @example #{tagname}
	 * @access public
	 * @var string
	 */
	public $_openingTag = '#{';
    public $_closingTag = '}';
	
	/**
	 * @desc ����������� ��� ����������
	 * @example #{tagname}
	 * @access public
	 * @var string
	 */
	public $tag_start_delim = "#{";
	
	/**
	 * @desc ����������� ��� ����������
	 * @example {tagname}
	 * @access public
	 * @var string
	 */
	public $tag_end_delim = "}";
	
	/**
	 * @desc ����������� ��� ������������ �����
	 * @example [blockname]
	 * @access public
	 * @var string
	 */
	public $block_start_delim_1 = "[";
	
	/**
	 * @desc ����������� ��� ������������ �����
	 * @example [blockname]
	 * @access public
	 * @var string
	 */
	public $block_end_delim_1 = "]";
	
	/**
	 * @desc ����������� ��� ������������ �����
	 * @example [/blockname]
	 * @access public
	 * @var string
	 */
	public $block_start_delim_2 = "[/";
	
	/**
	 * @desc ����������� ��� ������������ �����
	 * @example [/blockname]
	 * @access public
	 * @var string
	 */
	public $block_end_delim_2 = "]";
	
	/**
	 * @deck ����� ������ ( __construct() � php5)
	 * @access public
	 * @param string $dir
	 */
	public function __construct( $dir = '' )
	{
		$this->dir = is_dir($dir) ? $dir : themes_directory;
	}
	
	/**
	 * @desc Вывод шаблона
	 * @access public
	 * @param string $template
	 */
	public function out($template) {
	
		if (isset($this->result[$template])) {
			echo $this->result[$template];
		} else {
			$this->error('out: result of template ' .$template. ' not found');
			return false;
		}
	}
	
	/**
	 * @desc �������� �������
	 * @access public
	 * @param string $template
	 * @param string $name
	 */
	public function open($template, $name = []) {
	
		if ( is_file($this->dir.DS.$template.$this->ext) ) {
		
			$this->source[$name] = file_get_contents($this->dir . DS . $template. $this->ext);
			!isset($this->data[$name])       ? $this->data[$name]       :[];
			!isset($this->data_block[$name]) ? $this->data_block[$name] :[];	
		    return $this;
		} else {
			$this->error('open: template ' . $template . ' not found');
			$this->source[$name] = '';
		}
	}
	
	/**
	 * @desc �������� ������������� ��������� � �������
	 * @access public
	 * @param string $name
	 * @param string $template
	 */
	public function isset_value($name, $template) {
		if (isset($this->source[$template])) {
			return strstr($this->source[$template], $tag_start_delim .$name. $tag_end_delim) ? true : false;
			
		} else {
			$this->error('isset: template ' .$template. ' not found');
			return false;
		}
	}
	
	/**
	 * @desc �������� ������� � �������� �������
	 * @access public
	 * @param string $text
	 * @param string $name
	 */
	public function insert($text, $name) {
		
		if ($text && $name) {
			
			$this->source[$name]     = $text;
			$this->data[$name]       = $this->data [$name]?: [];
			$this->data_block[$name] = $this->data [$name]?: [];
			
		} else {
			$this->error('insert: template '.$name.' not create');
		}
	}
	
	/**
	 * @desc ��������� ����������
	 * ���� ���������� �� �������, �� ��� ���������� �� ������� ��� ����������
	 * @access public
	 * @param $name
	 */
	public function set($name = '', $value, $template = '') {	
		$this->data[$template][$name] = $value;
		return $this;
	}
	
	/**
	 * @desc ������� ����� � ��������� �������
	 * @access public
	 * @param string $name
	 * @param string $value
	 * @param string $template
	 */
	public function set_block($name, $value, $template) {
		$this->data_block[$template][$name] = $value;
		return $this;
	}
	
	/**
	 * @desc ���������� ob � ������ ���������� � ��������� ������
	 * @access public
	 * @param string $name
	 * @param string $template
	 * @param boolean $clear
	 */
	public function ob_get_content($name, $template, $clear = true){
		$this->set($name, ob_get_contents(), $template);
		
		if ($clear) {
			ob_get_clean();
		}
	}
	
	
	public function output ($file)  {	
		
		ob_start();
		include $this->dir .DS .$file. $this->ext;
		$return = ob_get_clean();
		return $return;
	}
	
	/**
	 * @desc ������� �������� ��� ���������� �������
	 * @access public
	 * @param string $template
	 */
	function clear($template) {
		$this->data[$template]       = [];
		$this->data_block[$template] = [];
	}
	
	/**
	 * @desc ������ ������� ��������, ����������, �����������
	 * @access public
	 */
	function full_clear() {
		$this->data       = [];
		$this->data_block = [];
		$this->result     = [];
		$this->values     = [];
		$this->source     = [];
	}
	
	/**
	 * @desc ������ ���������� ����� �� ��������� �������
	 * @access public
	 * @param string $name
	 * @param string $template
	 */
	function read_block($name, $template) {
	
		if ( !$name ) {
			$this->error('read: blockname is invalid');
			return false;
		}
		
		if (isset($this->source[$template])) {
			if (is_numeric($start = mb_strpos($this->source[$template], $this->block_start_delim_1 . $name . $this->block_end_delim_1))) {
				if (is_numeric($end = mb_strpos($this->source[$template], $this->block_start_delim_2 . $name . $this->block_end_delim_2))) {
					$lenght = mb_strlen($name);
					
					$mb_start = $start + $lenght + strlen($this->block_start_delim_1) + strlen($this->block_end_delim_1);
					$mb_end = $end - ($start + $lenght + strlen($this->block_start_delim_2) + strlen($this->block_end_delim_2));
					
					return mb_substr($this->source[$template], $mb_start, $mb_end);
				} else {
					$this->error('read: end of blockname ' . $this->block_start_delim_1 . $name . $this->block_end_delim_1 . ' does not exist');
					return false;
				}
			} else {
				$this->error('read: end of blockname ' . $this->block_start_delim_2 . $name . $this->block_end_delim_2 . ' does not exist');
				return false;
			}
		} else {
			$this->error('read: ' . $template . ' not open');
			return false;
		}
	}
	
	/** 
	 * @desc ���������� �������
	 * @access public
	 * @param string $template
	 * @param boolean $return
	 */
	public function compile($template, $return = false) {
	
		if (isset($this->source[$template])) {
		
			$replace       = isset($this->data[$template]) ? $this->data[$template] : [];
			$block_replace = isset($this->data_block[$template]) ? $this->data_block[$template] : [];
			$time          = microtime();
			$result        = $this->source[$template];
			
			$result = preg_replace_callback('#'.preg_quote($this->block_start_delim_1).'(.*?)'.preg_quote($this->block_end_delim_1). '(.*?)' .preg_quote($this->block_start_delim_2).'\\1'. preg_quote($this->block_end_delim_2).'#siU',
				
				function ($m) use ($block_replace) { 
					return isset($block_replace[$m[1]]) ? ($block_replace[$m[1]] ? $block_replace[$m[1]] : $m[2]) : '';
				}, $result);
			
			$result = preg_replace_callback('|'.preg_quote($this->tag_start_delim).'(.*?)'.preg_quote($this->tag_end_delim).'|', 
				function ($m) use ($replace) { 
					return isset($replace[$m[1]]) ? $replace[$m[1]] : ($this->delete_tags ? '' : '{${m[1]}');
				}, $result);
			
            
			if (strpos ( $result, "[group=" ) !== false) {
				$result = preg_replace_callback("#\\[group=(.+?)\\](.*?)\\[/group\\]#is", function($m) {
					return $this->checkGroup($m[1], $m[2]);
				}, $result);
			}

			if ( strpos ( $result, "[not-groups=" ) !== false ) {
				$result = preg_replace_callback("#\\[not-groups=(.+?)\\](.*?)\\[/not-groups\\]#is", 
					function($m) {
						return $this->checkGroups($m[1], $m[2], false);
					},
				$result);
			}
			
			
			/*if ( strpos ( $result, "[if" ) !== false ) {
				$result = preg_replace_callback('/\\[(else)?if\s+(('.self::OPERATOR.'|'.self::NUMBER.'|'.self::VARIABLE.')+)\\]/is', 
					array($this, 'callbackIf'), 
						$result); // parse {if}, {elseif}
			}*/

			//$result = preg_replace('/\\[\/(foreach|if|for)\s*\\]/', '\';' . PHP_EOL . '}' . PHP_EOL, $result); // parse closing tags

			 
			$result = stripslashes($result); // ������� \", ������������ �� ����� ������
			
			list ($msec1, $sec1) = explode(' ', $time);
			list ($msec2, $sec2) = explode(' ', microtime());
			
			$this->time_busy = $this->time_busy + (($msec2 + $sec2) - ($msec1 + $sec1));
			$this->result[$template] = $result /*eval($result)*/
			 ;
			$this->clear($template);
			
			return $return ? $result : false;
			
		} else {
			$this->error('compile: template ' . $template . ' not open');
			$this->result[$template] = false;
			
			if ($return) {
				return false;
			}
		}
	}
	
	function check_module($aviable, $block, $action = true) {
    
	    global $mod;
		
		$aviable = explode('|', $aviable);
		
		$block = str_replace('\"', '"', $block);
		
		if( $action ) {
			
			if( ! (in_array( $mod, $aviable )) ) return "";
			else return $block;
		
		} else {
			
			return in_array( $mod, $aviable ) ? $block : "";
		}	
	}
	
	
	private function check_group($groups, $block, $action = true) {

		global $member, $is_logged_in;
		
		$groups = explode(',', $groups);
		
		if ( !$is_logged_in or !$action or !isset($member['usergroup']) or !in_array($member['usergroup'], $groups) ) {
			return '';
		}
		
		$block = (isset($member['usergroup']) and in_array($member['usergroup'], $groups)) ? str_replace( '\"', '"', $block ) : '';

		return $block;
	}
	
	private function callback_if ($param){
		$result =  PHP_EOL;
		$result.= ($param[1]=='else'?'}else':'').'if('.preg_replace_callback('/'.self::VARIABLE.'/', array($this, 'callbackExpressionVariables'), $param[2]).'){' . PHP_EOL;
		$result.= 'echo \'';
		return $result;
	}

	private function callbackExpressionVariables($param){
		return $this->getVariableStr($param[0]);
	}

	private function getVariableStr($s) {

		$global = $s{0} == '$' ? true : false;

		if ($global) $s = substr($s, 1);

		$result = $global ? '$this->data[$template]' : '';
		$p = explode ('.', $s);

		for($i=0; $i < count($p); $i++) {
			$result .= $p[$i]{0} == '%' ? (!$i && !$global ? '$_'.substr($p[$i], 1) : '[$_'.substr($p[$i], 1).']') : "['".$p[$i]."']";		
		}
	
		return $result;
	}
	
	/* ����� tpl-�����, � ������� ������������� ��� ������ ��� ������ */ 
	public function msg($file, $values = []) {	//$template = $this->dir.$template.".tpl";
		
		$this->values = $values;
		
		ob_start();
		include $this->dir .DS. $file . $this->ext;
		$return = ob_get_clean();
		
		foreach ($this->values as $k => $v) {
			$return = str_replace($_openingTag.$k.$_closingTag, $v, $return);
		}	return $return;
	}


	public function start_table($attrubuts = [], $caption = '')
	{	
		$attrubuts['width']  = $attrubuts['width']  ? : '100%';
		$attrubuts['border'] = $attrubuts['border'] ? : '0';
		
		foreach ($attrubuts as $k => $v) {
			$this->result[] = $k.'="'.$v.'"' ;
		}
		
		$return  = "<table ".join(' ', $this->result).">\n";	//return $return;
		$return .= !empty($caption) ? " <caption>".$caption."</caption>" : "\n"; 
		echo $return;
	}
	
	public function close_table (){
		echo "</tr>\n</table>";
	}
	
	
	public function start_form ($attrubuts = []){
		
		$attrubuts['method'] = $attrubuts['method']?: 'GET';
		
		foreach ($attrubuts as $k => $v) {
			$this->result[] = $k .'="'.$v.'"' ;
		}	
		
		echo "<form ". join(' ', $this->result) .">\n";	//return $return;
		$this->result = [];
	}
	
	
	public function close_form (){
		echo "\n</form>\n";
	}
	
	/**
	* @desc ����� ������
	* @param string $error_msg
	* @param integer $error_type
	*/
	private function error($error_msg, $error_type = E_USER_WARNING) {
		trigger_error('Template error: ' . $error_msg, $error_type);
	}
	
	public function __call($name, $arg) {
		$method = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
		return call_user_func_array([$this, $method], $arg);
	}
}
