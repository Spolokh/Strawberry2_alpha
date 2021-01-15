<?php

Class FTPClient {

    // *** переменные класса
	private $connectionId = '';
	private $isLoginOk    = false;
	private $isPassive    = false;
	private $messageArray = array();	

    public function __construct(){}
	
	public function connect($server, $ftpUser, $ftpPassword, $isPassive = false) {  

		$this->$isPassive   = $isPassive;
		// *** Установить основное соединение
		$this->connectionId = ftp_connect($server);  
	
		// *** Логин с именем пользователя и паролем
		$loginResult = ftp_login($this->connectionId, $ftpUser, $ftpPassword);  

		// *** Устанавливает пассивный режим вкл/выкл (on/off) (по умолчанию стоит off)
		ftp_pasv($this->connectionId, $isPassive);  

		// *** Проверка соединения
		if ((!$this->connectionId) || (!$loginResult)) {
			$this->logMessage('Ошибка подключения по FTP!');
			$this->logMessage('Попытка подключения к  '.$server.' для пользователя '.$ftpUser, true);
			return false;
		} else {
			$this->logMessage('Соединение к '.$server.', для пользователя '.$ftpUser);
			$this->isLoginOk = true;
			return true;
		}
	}
	
	public function makeDir($directory){

		if (ftp_mkdir($this->connectionId, $directory)) { // *** Если удалось создать директорию…

			$this->logMessage('Директория "'.$directory.'" успешно создана');
			return true; 
		} else {  // *** …Если не удалось
			$this->logMessage('Ошибка создания директории "' . $directory . '"');
			return false;

		}
	}
	
	public function uploadFile($fileFrom, $fileTo) {

		// *** Установите режим передачи
		$asciiArray = array('txt', 'csv');
		$extension = end(explode('.', $fileFrom));
		$mode = in_array($extension, $asciiArray) ? FTP_ASCII : FTP_BINARY;

		// *** Выгрузите файл.
		$upload = ftp_put($this->connectionId, $fileTo, $fileFrom, $mode); 

		if (!$upload) { 
			$this->logMessage('Не удалось загрузить файл!');
			return false; 
		} else {
			$this->logMessage('Загружен "'.$fileFrom.'" как "'.$fileTo);
			return true;

		}
	}
	
	public function changeDir($directory) {

		if (ftp_chdir($this->connectionId, $directory)){
			$this->logMessage('Текущая директория: ' . ftp_pwd($this->connectionId));
			return true;

		} else {
			$this->logMessage('Не возможно изменить директорию');
			return false;
		}
	}
	
	public function getDirListing($directory = '.', $parameters = '-la'){
	
		// получить содержимое текущей директории
		$contentsArray = ftp_nlist($this->connectionId, $parameters . '  ' . $directory);  
		return $contentsArray;
	}
	
	public function downloadFile ($fileFrom, $fileTo){  

		// *** Установите режим передачи
		$asciiArray = array('txt', 'csv');
		$extension  = end(explode('.', $fileFrom));
		$mode       = in_array($extension, $asciiArray) ? FTP_ASCII : FTP_BINARY;

		// попробуйте скачать $remote_file и сохранить его в $handle
		if (ftp_get($this->connectionId, $fileTo, $fileFrom, $mode, 0)) {  

			return true;
			$this->logMessage(' файл "'.$fileTo.'" успешно скачен');
		} else {  

			return false;
			$this->logMessage('Ошибка при скачивании файла  "' . $fileFrom . '" to "' . $fileTo . '"');
		}  
	}
	
	
	public function getMessages(){
		return $this->messageArray;
	}
	
	private function logMessage($message) {
		$this->messageArray[] = $message;
	}
	
	public function __deconstruct(){
		if ($this->connectionId) {
			ftp_close($this->connectionId);
		}
	}
}
?>