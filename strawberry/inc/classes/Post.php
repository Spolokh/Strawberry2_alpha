<?php
/**
 * @package Public
 * @access public
 */


namespace classes;

use classes\Template;
use classes\PHPMailer;
use classes\CuteParser;

final class Post extends CuteParser
{
	const OFFSET = 0;
	const NUMBER = 1;
	const MODULE = 'post';

	protected $fields = ['date', 'author', 'title', 'id', 'image', 'url', 'type', 'short'];

	public function __construct ($config)
	{
		parent::__construct($config);
	}

	public function show($id)
	{
		$query = parent::select([
			'news',
			'join' 	 => ['story', 'id'],
			'select' => $this->fields,
			'where'  => ['id IN ('.$id.')'], 
			'limit'  => [1]
		]);

		if (!$row = reset($query)) {
			return;
		}

		$link  = cute_get_link($row);
		$date  = date("d.m.Y", $row['date']);
		$image = $row['image'] ? UPIMAGE .'/posts/'. $row['image'] : '';
		$title = $this->value($row['title'], true);	
		$short = $this->value($row['short'], true);

		$template = new Template (themes_directory);
		$template ->open('post', self::MODULE)
			->set('link', $link, self::MODULE)
			->set('date', $date, self::MODULE)
			->set('title', $title, self::MODULE)
			->set('short', $short, self::MODULE)
			->set('image', $image, self::MODULE)
		;
		return $template ->compile(self::MODULE, true);	
		$template ->fullClear();	 
	}

	public function Image($row)
	{
		if (!isset($row['image']))
		{
			return false;
		}
		return filter_var($row['image'], FILTER_VALIDATE_URL) ? $row['image'] : $this->Config['path_image_upload'] .'/posts/'. $row['image'];
	}
}
