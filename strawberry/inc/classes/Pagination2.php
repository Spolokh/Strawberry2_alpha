<?php

/*
 * Класс для генерации постраничной навигации
 */

namespace classes;
//use classes\Blitz;

class Pagination
{
	protected $total  = '';
	protected $limit  = 0;
	protected $numLinks  = 4;
	protected $currentPage  = 0;
	protected $firstLink  = 'Первая';
	protected $nextLink   = '';  //Следующая
	protected $prevLink   = '';  //Предыдущая
	protected $lastLink   = 'Последняя';
	protected $fullTagOpen  = '<ul>' ;
	protected $fullTagClose = '</ul>';
	protected $firstTagOpen = '<li>' ;
	protected $firstTagClose= '</li>'; //&nbsp;
	protected $lastTagOpen  = '<li>';  //&nbsp;
	protected $lastTagClose = '</li>';
	protected $curTagOpen   = '<li><a><b>';
	protected $curTagClose  = '</b></a></li>';
	protected $nextTagOpen  = '<li>';
	protected $nextTagClose = '</li>';
	protected $prevTagOpen  = '<li>' ;
	protected $prevTagClose = '</li>';
	protected $numTagOpen   = '<li>';
	protected $numTagClose  = '</li>';
	protected $showCount    = false;
	protected $currentOffset= 0;
	protected $queryString  = 'skip';
	protected $baseURL      = '';
	
	public function __construct(array $params = [])
	{
		if (count($params) > 0) {
			$this->initialize($params);        
		}
	}
	
	private function initialize(array $params = [])
	{
		foreach ($params as $k => $v) {
			if (isset($this->$k)){
				$this->$k = $v;
			}
		}
	}
	
	/**
	 * Генерируем ссылки на страницы
	 */    
	public function get()
	{ 
		// Если общее количество записей 0, не продолжать
		if ($this->total == 0 || $this->limit == 0){
		   return '';
		}
		// Считаем общее количество страниц
		$numPages = ceil($this->total / $this->limit);
		
		// Если страница только одна, не продолжать
		if ($numPages == 1) {
			if ($this->showCount) {
				$info = 'Showing : ' . $this->total;
				return $info;
			} else {
				return '';
			}
		}
		
		// Определяем строку запроса
		//$query_string_sep = (strpos($this->baseURL, '?') === FALSE) ? '?skip=' : '&amp;skip=';
		//$this->baseURL = $this->baseURL.$query_string_sep;
		
		// Определяем текущую страницу
		$this->currentPage = $_GET[$this->queryString];
		
		if (!is_numeric($this->currentPage) || $this->currentPage == 0){
			$this->currentPage = 1;
		}
		
		// Строковая переменная вывода контента
		$output = '';
		
		// Отображаем сообщение о ссылках на другие страницы
		if ($this->showCount)
		{
		   $currentOffset = ($this->currentPage > 1)?($this->currentPage - 1) * $this->limit : $this->currentPage;
		   $info = 'Показаны элементы с ' . $currentOffset . ' по ' ;	
		   $info.= (($currentOffset + $this->limit) < $this->total)? $this->currentPage * $this->limit: $this->total;
		 
		   /*if( ($currentOffset + $this->limit) < $this->total )
			  $info .= $this->currentPage * $this->limit;
		   else
			  $info .= $this->total;*/
		
		   $info.= ' из ' . $this->total . ' | ';
		   $output.= $info;
		}
		
		$this->numLinks = (int)$this->numLinks;
		
		// Если номер страницы больше максимального значения, отображаем последнюю страницу
		if ($this->currentPage > $this->total) {
			$this->currentPage = $numPages;
		}
		
		$uriPageNum = $this->currentPage;
		
		// Рассчитываем первый и последний элементы 
		$start = (($this->currentPage - $this->numLinks) > 0) ? $this->currentPage - ($this->numLinks - 1) : 1;
		$end   = (($this->currentPage + $this->numLinks) < $numPages) ? $this->currentPage + $this->numLinks : $numPages;
		
		// Выводим ссылку на первую страницу
		if ($this->currentPage > $this->numLinks)
		{
			$firstPageURL = strtok ($this->baseURL, '?');	
			$output.= $this->firstTagOpen.'<a href="'.$firstPageURL.'">'.$this->firstLink.'</a>'.$this->firstTagClose;
		}
		// Выводим ссылку на предыдущую страницу
		if($this->currentPage != 1)
		{
			$i = ($uriPageNum - 1);	
			if ($i == 0) {
				$i = '';
			} 

			$output.= $this->prevTagOpen.'<a class="next icon-chevron-left" href="'.cute_get_link([$this->queryString => $i], $this->queryString).'">'.$this->prevLink.'</a>'.$this->prevTagClose;
		}
		// Выводим цифровые ссылки
		for ($loop = $start-1; $loop <= $end; $loop++) {
			$i = $loop;
			if($i >= 1) {
				if ($this->currentPage == $loop){
					$output.= $this->curTagOpen .$loop. $this->curTagClose;
				} else {
					 
					$output.= $this->numTagOpen.'<a href="'.cute_get_link([$this->queryString => $i], $this->queryString).'">'.$loop.'</a>'.$this->numTagClose;
				}  
			}
		}
		// Выводим ссылку на следующую страницу
		if ($this->currentPage < $numPages) {
			$i = ($this->currentPage + 1);
			$output.= $this->nextTagOpen.'<a class="next icon-chevron-right" href="'.cute_get_link([$this->queryString => $i], $this->queryString).'">'.$this->nextLink.'</a>'.$this->nextTagClose;
		}
		// Выводим ссылку на последнюю страницу
		if(($this->currentPage + $this->numLinks) < $numPages) {
			$i = $numPages;
			$output.= $this->lastTagOpen.'<a href="'.cute_get_link([$this->queryString => $i], $this->queryString).'">'.$this->lastLink.'</a>'.$this->lastTagClose;
		}
		// Удаляем двойные косые
		$output = preg_replace("#([^:])//+#", "\1/", $output);
		// Добавляем открывающий и закрывающий тэги блока
		$output = $this->fullTagOpen .$output. $this->fullTagClose;
		return $output;        
	}
}
