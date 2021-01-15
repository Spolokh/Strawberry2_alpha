<?php
/**
 * @package Public
 * @access public
 */

final class Post extends \CuteParser
{
	const MODULE 	= 'post';
	const OFFSET 	= 0;
	const NUMBER 	= 1;

	public $fields = ['date', 'author', 'title', 'id', 'image', 'url', 'type', 'short'];

	public function __construct ($config)
	{
		parent::__construct($config);
	}

	public function show($id)
	{
		$template = new Template (themes_directory);
		$template ->open('post', self::MODULE);

		$row = parent::select([
			'news', 
			'join' 		=> ['story', 'id'],
			'select' 	=> $this->fields,  
			'where' 	=> $id, 
			'limit' 	=> [1]
		]);

		if (!$row = reset($row)) {
			return;
		}

		//var_dump($row);	
		$type  = $row['type'];
		$date  = $row['date'];
		$link  = cute_get_link($row);
		$image = $row['image'] ? UPIMAGE .'/posts/'. $row['image'] : '';
		$title = $this->value($row['title'], true);	
		$short = $this->value($row['short'], true);

		$template->set('link', $link, self::MODULE)
			->set('title', $title, self::MODULE)
			->set('short', $short, self::MODULE)
			->set('image', $image, self::MODULE)
		;

		return $template ->compile(self::MODULE, true);	
		$template ->fullClear();	 
	}
}
