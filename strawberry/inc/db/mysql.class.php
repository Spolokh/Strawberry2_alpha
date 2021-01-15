<?php

namespace inc\db;

trait Users {

	public function UsersByPostIDs ($query = [])
	{
		if (!reset($query) ) {
			return false;
		}
		foreach ($query as $row) {
			$this->result[] = '"' .$row['author']. '"';
		}
		
		$query = $this->select(['users', 'select' => $this->fields, 'where' => ['`username` IN ('.join(', ', $this->result).')','and','deleted = 0']]);	
		return $this->_validUsersArray($query);
	}
	
	/* Принимаем список комментариев, выбираем авторов и запрашиваем их в БД */
	public function UsersByCommIDs($query)
	{
		if (!reset($query)) {
			return false;
		}

		foreach ($query as $row){
			$this->result[] = $row['user_id'];
		}

		$query = $this->select(['users', 'select' => $this->fields, 'where' => ['`id` IN ('.join(', ', $this->result).')','and','deleted = 0']]); 	
		return $this->_validUsersArray($query);
	}
	
	/* Валидируем массив, "складываем" его с тем, который уже был дан ранее */
	private function _validUsersArray(array $query = [], array $users = [])
	{
	    if (!reset($query)) {
			return false;
		}
		
		foreach ($query as $row) {
			$row['name'] = $row['name'] ? $row['name'] : $row['username'];
			$users[$row['username']] = $row;
		}

		return array_merge($users, $this->users);
	}
}

class MySQL extends \mysqli
{
	use Users;
	
	protected
		$author = []
		, $config = []
		, $fields = ['id', 'usergroup', 'username', 'name', 'publications', 'avatar']
		, $orderby = ['id', 'ASC']
	;

	public $users  = [];
	public $result = [];
	public $member = [];

	/**
	 * Connects a user to the database
	 * @param string $username The username of the user
	 * @param string $password The corressponding password of the user
	 * @param string $server The database server (usually "localhost")
	 * @return void
	 * @access public
	 */
	
	public function __construct($config = [])
	{
		try {
			parent::__construct(
				defined('DBHOST') ? DBHOST : 'localhost',
				defined('DBUSER') ? DBUSER : 'root',
				defined('DBPASS') ? DBPASS : '',
				defined('DBNAME') ? DBNAME : 'test'
			);

		} catch (\Exception $e) {
			die ($e->getMessage());
		}
		
		
		/*try {
			$this->connect = mysqli_connect(DBHOST, DBUSER, DBPASS);
			$this->connect->select_db (DBNAME);

		} catch (Exception $e) {
			die ($e->getMessage());
		}*/
	}
	
	public function setCharset($charset = 'utf8')
	{
		$this->set_charset($charset);
		return $this;
	}
	
	/**
	 * To select a database for mysql to use as a default
	 * @param string $database The name of the database that is to be selected
	 * @param string $prefix The table prefix (e.g.: cute_)
	 * @return void
	 * @access public
	 */
	 
	public function selectDb($db = '')
	{
		$this->select_db ($db);
	}

	/**
	 * Disconnects a user from the database
	 * @return void
	 * @access public
	 */
	function disconnect(){
		return $this->close();
	}

	/**
	* To return the last error that occurred
	* @return string $error The last error
	* @access public
	*/
	
	public function getError () {
		return $this->error();
	}
	
	/**
    * @name	get_version
    * @desc	returns a string containing the database server version
    * @return string
	* @access public
    */
	public function serverInfo()
	{
		return printf('Версия сервера: %s', $this->server_info);
	}
	
	/**
    * @name	close
	* @access public
    */
	//public function close()
	//{
	//	$this->close();
	//}
	
	/**
	 * Alters a database by working with its columns
	 * @param mixed $arg The arguments in form of "[$key] => $value" where $key can be 'db', 'table', 'action', 'name', and 'values'
	 * @return void
	 * @access public
	 */
	public function alterTable($arg) {
		
		$query = 'ALTER TABLE '.PREFIX.pos ($arg). "\n";
		
		if ($arg['action'] == 'insert'){
			$query.= 'ADD '.$arg['name'].' '.$this->_values($arg);
        
		} elseif ($arg['action'] == 'rename table'){
			$query.= 'RENAME '.$arg['name'];
        
		} elseif ($arg['action'] == 'rename col' and !$arg['values']['permanent']){
        	
		 /*
			$result = [];
	        
			$list   = mysqli_query( $this->connect, 'SELECT * FROM '.PREFIX.pos($arg) );
	        
			for ($i = 0; $i < mysqli_num_fields ($list); $i++){
	        	
				if ( mysql_field_name ($list, $i) == $arg['name'] ){	
					$result[] = mysql_field_type($list, $i).' '.mysql_field_flags($list, $i);
	            }
	        }

        	$query .= 'CHANGE `'.$arg['name'].'` `'.$arg['values']['name'].'` '.join('', $result)."\n";
			*/ 
		} elseif ($arg['action'] == 'modify' and !$arg['values']['permanent']){
        	
			/*$result = [];
	        $list   = mysqli_query($this->connect, 'SELECT * FROM `'.PREFIX.pos($arg).'`');
	        
			for ( $i = 0; $i < mysqli_num_fields ($list); $i++ ){
	        	
				if ( mysqli_field_name ($list, $i) == $arg['name'] ){
	        	    $result[] = str_replace('string', 'varchar(255)', str_replace('bool', 'tinyint(1)', mysql_field_type($list, $i))).' '.mysql_field_flags($list, $i);
				}
	        }

        	$query .= 'CHANGE `'.$arg['name'].'` `'.$arg['name'].'` '.(!$arg['values']['type'] ? join('', $result).' ' : '').$this->_values($arg).' not null'."\n";
	*/
		} elseif ( $arg['action'] == 'addkey' ){
        	
			$query .= 'add primary KEY('.$arg['name'].')'."\n";
        
		} elseif ( $arg['action'] == 'drop' ) {
		
        	if ($arg['name']){
        		$query.= 'DROP '.$arg['name'] . "\n";
        	} else {
        		$query = 'DROP TABLE ' .PREFIX.pos($arg). "\n";
        	}
			
        } elseif ($arg['values']['permanent']){
        	$query.= 'ADD UNIQUE('.$arg['name'].')'."\n";
        }
		return parent::query($query);
	}
	
	/**
	 * Returns an array containing a list of the columns, and their corresponding properties
	 * @param mixed arg The arguments that are passed to the MySQL as an array.
	 * @return mixed cols An array populated with details on the fields in a table
	 * @access private
	 */
	public function describe ($arg) {

		if (!$this->select_db (DBNAME)) {
			return false;
		}

		$query = parent::query('SHOW FIELDS FROM '.PREFIX.pos($arg));

		while ( $row = $query->fetch_assoc() ) {

			$row = array_change_key_case($row);

			if (substr($row['key'], 0, 3) == 'PRI'){
            	$result['primary'] = $row['field'];
            }

			if (substr($row['key'], 0, 3) == 'UNI'){
            	$result['permanent'] = $row['field'];
			}
			
			if (substr($row['type'], 0, 4) == 'enum'){
            	$row['type'] = preg_replace('/^enum\((.*?)\)$/i', '\\1', $row['type']);
            	preg_match_all('/\'(.*?)\'/i', $row['type'], $matches);
            	$row['type']     = 'enum';
            	$row['enum_val'] = $matches[1];
            }

            $tables[] = $row;
		}

		$this->close();

		foreach ($tables as $row){
			$row['type'] = str_replace('varchar(255)', 'string', $row['type']);
			$row['type'] = str_replace('tinyint(1)','bool',$row['type']);
			$row['type'] = str_replace('int(11)','int', $row['type']);
			
			$result[$row['field']] 	= [
				'max'      => 0,
				'permanent'=> (substr($row['key'], 0, 3) == 'PRI' ? 1 : 0),
				'auto_increment' => ($row['extra'] == 'auto_increment' ? 'auto_increment' : 0),
				'type'     => $row['type'],
				'default'  => $row['default'],
				//'autocount'  => ($this->last_insert_id(pos($arg)) > 0 ? $this->last_insert_id(pos($arg)) : 0),
				'enum_val' => $row['enum_val'],
	    	];
	    }

		return $result;
	}
	
	
	/**
	 * Returns a list containing the current valid MySQL databases
	 * @return mixed $databases A list containing the databases
	 * @access public
	 */ 
	public function showDbs($arg = '', $result = [])
	{
	    $query = parent::query("SHOW DATABASES");	
		while ( $rows = $query->fetch_row() ) {
			$result[] = reset($rows);
		}
		return join ('<br/>', $result);
	}
	
	/**
	 * Creates a new database
	 * @param mixed $arg The arguments in form of "[$key] => $value" where $key can be 'db'
	 * @return void
	 * @access public
	 */
	public function createDb($db) {
		return parent::query ("CREATE DATABASE IF NOT EXISTS $db");
	}
	
	/**
	 * Creates a new table with the given criteria inside a database
	 * @param mixed $arg The arguments in form of "[$key] => $value"
	 * where $key can be 'db', 'table', 'columns'
	 * @return int $deleted The number of rows deleted
	 * @access public
	 */
	 
	public function createTable ($arg) {
		
		foreach ( $arg['columns'] as $column => $arg['values'] ) {
			
			$query[] = '`'.$column.'` '.$this->_values($arg);

			foreach($arg['values'] as $k => $v) {
				
				if ($k == 'primary' and $v){
					$primary = ','."\n".'primary key ('.$column.')';
				}

				if ($k == 'permanent' and $v){
					$unique  = ','."\n".'unique('.$column.')'."\n";
				}

				if ($k == 'default' and $v){
					$default = ','."\n".'default ('.$column.')'."\n";
				}
			}
		}

		$query = join(', ', $query).$primary.$unique.$default;
		$query = 'CREATE TABLE '.PREFIX.pos($arg)." (\n".$query."\n)\n";
		return parent::query($query);
	}
	
	
	/**
	 * Drops a table from a database
	 * @param mixed $arguments The arguments in form of "[$key] => $value" where $key can be 'db', 'table'
	 * @return void
	 * @access public
	 */

	public function dropTable ($table) {
		$query = 'DROP TABLE '.$table;
		return parent::query($query);
	}
	
	
	public function getTables ( $base = '', $result = [] )
	{
		$base  = $base ? : DBNAME;
		if ($query = parent::query("SHOW TABLES FROM ".$base.""))
		{
			while($row = $query->fetch_array()) {
				$result[] = reset($row);
			}

			parent::close();
			return $result;
		}
		return false;
	}
	
	/**
	 * To check whether a table exists or not
	 * @param string $table The name of the table
	 * @param string $database The name of the database the table is in
	 * @return bool Whether the db exists or not
	 * @access public
	 */
	
	public function tableExists ($table, $base = '') : bool
	{
		$base  = $base ? $base : DBNAME;
		$query = parent::query("SHOW TABLES FROM ". $base ."");
		
		while( $row = $query->fetch_array() ){
			if ( reset($row) == PREFIX.$table ){
	            return true;
	        }
		}
		return false;
	}
	
	/**
	 * To return the number of queries sent to MySQL
	 * @return int
	 * @access public
	 */
	public function queryCount(){
		//return $this->query ?: 0;
	}
	
	/**
	 * To retrieve the number of records inside of a table
	 * @param string $table The name of the table
	 * @param string $database The database the table is inside of (optional)
	 * @return int The number of records in the table
	 * @access public
	 */
	public function tableCount($table)
	{	
		$query = 'SHOW TABLE STATUS LIKE \''.PREFIX.$table.'\'';
		$query = parent::query($query);
		$row   = $query->fetch_array();
		return $row['Rows'];
	}
	
	
	/**
	 * To retrieve the last ID generated by an auto_increment field in a table
	 * @param string $table The name of the table
	 * @param string $database The database the table is inside of (optional)
	 * @return string Get the last ID generated by this column instead of the priamry key (optional)
	 * @access public
	 */
	public function lastInsertId($table = '')
	{
	    $query = "SHOW TABLE STATUS LIKE ".PREFIX.$table."";
	    $query = parent::query($query);
	    $row   = $query->fetch_array();
	    return $row['Auto_increment'];
	}
	
	
	/**
	 * Inserts a new row into a table with the given information
	 * @param mixed $arg The arguments in form of "[$key] => $value" where $key can be 'db', 'table', 'values'
	 * @return int The number of rows inserted into the table
	 * @access public
	 */
	 
	public function insert($arg)
	{
        foreach ($arg['values'] as $k => $v) {
        	$insert[] = '`' .$k. '`';
        	$values[] = '\''.$this->escapeString($v).'\'';
        }

		$query = 'INSERT INTO '.PREFIX.pos($arg)."\n";
		$query.= '('.join(', ', $insert).')'."\n";
		$query.= 'VALUES('.join(', ', $values).')';
		return parent::query($query) ? $this->insert_id : false;
	}
	
	public function replace($arg)
	{
        foreach ( $arg['values'] as $k => $v ) {
        	$insert [] = '`'.$k.'`';
        	$values [] = '\''.$this->escapeString($v).'\'';
        }

		$query.= 'REPLACE INTO '. PREFIX .pos($arg).' ('.join(', ', $insert).')'."\n";
		$query.= 'VALUES('.join(', ', $values).')'."\n";
		return parent::query($query) ? true: false;	
	}
	
	/**
	 * Deletes a row from a table that matches a 'where' clause
	 * @param mixed $arg The arguments in form of "[$key] => $value" where $key can be 'db', 'table', 'where', 'limit'
	 * @return int The number of rows deleted
	 * @access public
	 */
	public function delete ($arg) {
		$query = 'DELETE FROM '.PREFIX.pos($arg)."\n";
		$query.= $this->_where($arg)."\n";
		$query.= isset($arg['limit']) ? 'LIMIT '.join(', ', $arg['limit']) : '';
		return parent::query($query) ? true: false;
	}
	
	
	/**
	 * Updates a row that matches a 'where' clause, with new information
	 * @param mixed $arg The arguments in form of "[$key] => $value" where $key can be 'db', 'table', 'where', 'limit', and 'values'
	 * @return int The number of rows updated
	 * @access public
	 */
	public function update($arg)
	{
		foreach ($arg['values'] as $k => $v){
			$update[] = '`'.$k.'` = \''.$this->escapeString($v).'\'';
		}

		$query = 'UPDATE '.PREFIX.pos($arg).' SET '.join(', ', $update)."\n";
		$query.= $this->_where($arg)."\n";
		$query.= isset($arg['limit']) ? 'LIMIT '.join(', ', $arg['limit']) : "\n";
		return parent::query($query) ? true: false;
	}

	public function escapeString($v) // á
	{
		return $this->real_escape_string($v);
	}
	
	/**
	 * Selects rows of information from a selected database and a table that fits the given 'where' clause
	 * @param mixed $arg The arguments in form of "[$key] => $value" where $key can be 'db', 'table', 'select', 'where', 'limit' and 'orderby'
	 * @return mixed An array that MySQL returns that matches the given criteria
	 * @access public
	 */

	public function select (array $arg)
	{
		if ( isset($arg['orderby']) ){
			
			if ( is_array($arg['orderby'][0]) ){
			  
			  	foreach ($arg['orderby'] as $v){
					$orderby[] = (!empty($v[0]) ? '`'.$v[0].'` ' : '').(!empty($v[1]) ? ((strtolower($v[1]) == 'rand') ? $v[1].'()' : $v[1]) : '');
			  	}
			} else {
				$orderby[] = (!empty($arg['orderby'][0]) ? '`'.$arg['orderby'][0].'` ' : '').(!empty($arg['orderby'][1]) ? ((strtolower($arg['orderby'][1]) == "rand") ? $arg['orderby'][1]."()" : $arg['orderby'][1]) : "");
			}
		}
//      $this->query++;

		$result = [] ;
		$query = '';
        $query.= 'SELECT '.$this ->_select($arg).' FROM `'.PREFIX.pos($arg).'`'."\n";
		
		if ( isset($arg['join']) ) {
		    if ( isset($arg['join']['table']) and isset($arg['join']['where']) ) {
			    $query.= 'INNER JOIN `'.PREFIX.$arg['join']['table'].'` ON '.$arg['join']['where']."\n";
			} else {
				$query.= 'INNER JOIN `'.PREFIX.pos($arg['join']).'` USING(`'.end($arg['join']).'`)'."\n"; 
			}
		}
      
		$query.= isset($arg['where'])  ? $this->_where($arg)."\n" : '';
		$query.= isset($arg['groupby'])? 'GROUP BY '.join(', ', $arg['groupby'])."\n" : '';
		$query.= isset($arg['orderby'])? 'ORDER BY '.join(', ', $orderby)."\n" : '';
		$query.= isset($arg['limit'])  ? 'LIMIT '.join(', ', $arg['limit'])."\n" : '';
		$query = parent::query($query);

		$result = $query->num_rows ? $query->fetch_all(MYSQLI_ASSOC) : [];
		return $result;
	}
	
	/**
	 * @access public
	 */
	public function query($sql, $resultmode = NULL)
	{
		$result = [];
		$query = parent::query($sql);

		$result = $query->num_rows ? $query->fetch_all(MYSQLI_ASSOC) : [];
		return $result;
	}
	
	/**
	 * @access public
	 */
	public function login ($username, $password) 
	{	
		global $member, $is_logged_in;
	
		$is_logged_in = false;
		
		if (empty($username) or empty($password)) {
			return $is_logged_in;
		}

		//password_verify ( string $password , string $hash ) : bool
		
		$query = $this->select(['users', 
			'select'=> ['id', 'date', 'usergroup', 'username', 'name', 'age', 'mail', 'publications', 'contacts', 'avatar', 'about', 'lj_username', 'lj_password'], 
			'where' => ['deleted <> 1', 'and', "password = $password", 'and', "username = $username", 'or', "mail = $username"],
			'limit' => [1]
		]);
		
		if (!$member = reset($query)) {
			return $is_logged_in;  
		}

		//$query->free();
		
		if ( strtolower($username) == strtolower($member['username']) OR strtolower($username) == strtolower($member['mail']) ) {
			$is_logged_in = true;
		}
		
		$member['params'] = isset($member['params']) ? json_decode($member['params']) : '';
		return $is_logged_in;		
	}
	
	/**
	 * @access public
	 */
	public function logout(){
		
		cute_setcookie('username', '', (time() - 3600 * 24 * 365), '/');
		cute_setcookie('password', '', (time() - 3600 * 24 * 365), '/');
        cute_setcookie('login_referer', '');
		cute_setcookie(session_name(), '');
        @session_destroy();
        @session_unset();
        header("Location: /");
		exit;
	}

	
	/**
	 * @access private
	 */
    private function _select($arg){
		
		$result = [];
		
    	if ( empty($arg['select']) ){
    		$result[] = '*';
    	} else {
	        foreach ($arg['select'] as $k => $v){
	        	$result[] = '`'.$v.'`';
	        }
        }
        return join(', ', $result);
	}
	
	
	/**
	 * @access private
	 */
	private function _where($arg, $separator = ' ', $result = [])
	{	
		if ( empty($arg['where']) ) {
			return false;
		}
        
		if ( !is_array ($arg['where']) and is_numeric ($arg['where']) ){
			$result[] = 'id = '.$arg['where']."\n";
		}
		//$member[id]
		//elseif () {
		//}//
		else {
			$s = ''; 
			$v = ''; 
			$e = '';
		
			$op = '(=|!=|<|<=|>|>=|=~|!~)';

		foreach ($arg['where'] as $k => $v) {
		
			if ( preg_match('/(.*)\[(.*)\]/i', $v, $match) ){
				
				if ( preg_match('/(.*) (\?|!\?) (.*)/i', $v, $regexp) ) {
					$result[] = $regexp[1].($regexp[2] == '!?' ? ' not' : '').' regexp \'[[:<:]]('.$match[2].')[[:>:]]\''."\n";
				
				} else {
				
					foreach ( explode(',', $match[2]) as $or ){
						$where[] = $match[1].'\''.$or.'\'';
					}	
					
					$result[] = '('.join(' OR ', $where).')'."\n";
				}
			
			} elseif ($v != 'and' && $v != 'or' && $v != 'xor') {
				
				if ( substr ($v, 0, 1) == '(' ){
					$v = substr($v, 1);
					$s = true;
				}

				if ( substr ($v, (strlen($v) - 1)) == ')' ){
					$v = substr($v, 0, (strlen($v) - 1));
					$e = true;
				}

				$result[] = preg_replace('/(.*?) '.$op.' (.*)/i', '`\\1` \\2 \'\\3\'', $v);
			} else {
				$result[] = $v."\n";
			}
		}
        
		}
		return 'WHERE '.($s ? '(' : '').str_replace(['!~', '=~'], ['NOT LIKE', 'LIKE'], join($separator, $result)).($e ? ')' : '');
	}

	/**
	 * @access private
	 */
    private function _values($arg, $separator_in = ' ', $separator_out = ' '){
        
		foreach ($arg['values'] as $k => $v){
            if ($k!= 'primary' && $v!= 'enum' && $k!= 'permanent' && $k!= 'max') {
                if ($k == 'type' || ($k == 'name' && $arg['action'])){
                    $result[] = $v.' not null';
                } elseif ($k == 'auto_increment'){
                    $result[] = $k;
                } else {
                    if ($k == 'enum_val' && is_array($v)){
                        foreach ($v as $enum){
                            $enum_var[] = '\''.$enum.'\'';
                        }

                        $result[] = 'enum('.join(', ', $enum_var).') not null';
                    } else {
                        $result[] = $k.$separator_in.'\''.$this->escapeString($v).'\'';
                    }
                }
            }
        }

		$result = join($separator_out, $result);
		$result = str_replace('string', 'varchar(255)', $result);
		$result = str_replace('bool', 'tinyint(1)', $result);
        return $result;
	}
	

	function sum($arg) {
		//$this->query++;
		$query = 'SELECT SUM('.$this->_select($arg).')'."\n";
		$query.= 'FROM `'.PREFIX.pos($arg).'`'."\n";
		$query.= $this->_where($arg)."\n";
		$query.= isset($arg['limit']) ? 'LIMIT '.join(', ', $arg['limit']) : ''."\n";
		$query = parent::query($query);
		$result = $query->fetch_row();
        return $result ? reset($result) : 0;
    }
	
	function count($arg)
	{
		$query = 'SELECT COUNT('.$this->_select($arg).')'."\n";
		$query.= 'FROM `'.PREFIX.pos($arg).'`'."\n";
		$query.= $this->_where($arg)."\n";
		$query.= isset($arg['limit']) ? 'LIMIT '.join(', ', $arg['limit']) : ''."\n";
		$query = parent::query($query);
		$result = $query->fetch_row();
		return $result ? reset($result) : 0;
	}
	
	/*
	// CuteFields Needed Queries
	function table_num_fields($table, $database = ''){
		$query    = "SELECT * FROM " .PREFIX.$table. "";
		$query    = mysqli_query($this->connect, $query);
		$fieldnum = mysqli_num_fields($query);
		return $fieldnum;
	}
	
	function table_num_rows($table, $database = ''){
		
		$sql  = "SELECT * FROM ".PREFIX.$table."";	 
		$query  =  mysqli_query ($this->connect, $sql);
		$rownum =  mysqli_num_rows($query);
		return $rownum;
	}
	
	function table_field_direct($table, $i, $database = ''){
		
		$sql    = "SELECT * FROM ".PREFIX.$table."";
		$result = mysqli_query ($this->connect, $sql);
		$field  = mysqli_fetch_field_direct($result, $i);
		return $field;
	}

	function drop_column($table, $field, $database = ''){
		$sql 	= "ALTER TABLE ".PREFIX.$table." DROP COLUMN ".$field."";
		$result = mysqli_query($this->connect, $sql);
		return $result;
	}

	function rename_column($table, $oldfield, $newfield, $database = ''){
		$sql = "ALTER TABLE ". PREFIX .$table." CHANGE ".$oldfield." ".$newfield." varchar(255)";
		return mysqli_query($this->connect, $sql);
	}
	
	function distinct_column($column, $table, $database = ''){
		$sql = "SELECT DISTINCT ".$column." FROM ".PREFIX.$table."";
		return mysqli_query($this->connect, $sql);
	}
	
	function truncate ($table) {
		$sql = "TRUNCATE TABLE ".$table."";
		return mysqli_query($this->connect, $sql);
	}
	
	function __destruct()
	{
		$this->result = [];
	}*/

	 /**
     * Защищаем от создания через клонирование
     */
    private function __clone() {}
  
	/**
	 * Защищаем от создания через unserialize
	 */
	private function __wakeup() {}
		 
	//////////////////////////////////////////////////////////////////////////
	/*public function __call($name, $arg) {
		
		$method = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
		if (method_exists($this, $method)) {
			return call_user_func_array([$this, $method], $arg);
		} else {
			throw new Exception("Method $name() does not exist in class " . get_class($this));
		}
	}*/
}
