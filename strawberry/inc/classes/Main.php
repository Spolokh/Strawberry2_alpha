<?php
/**
 * @package Public
 * @access public
 */

namespace classes;

use classes\Blitz;
use classes\CuteParser;

final class Main extends CuteParser
{
	const OFFSET = 0;
	const NUMBER = 6;
	const MODULE = 'main';

	private $tpl = [];
	private $category;
	protected $fields = '`id`, `date`, `author`, `title`, `image`, `category`, `url`, `type`, `views`, `comments`, `short`';

	public function __construct ($config)
	{
		parent::__construct($config);
		$this->show();
	}

	private function show()
	{
		$this->category = category_get_id($_GET['category']);

		if (!$query = $this->sql()) {
			return;
		}

		foreach ($query as $k => $row)
		{
			if ($this->category && $this->category == $row['category'])
			{
				continue;
			}

			if ($category!= $row['category'])
			{   $category = $row['category'];
				$this->tpl['row'][$k]['category'] = category_get_title($category);
			}

			$this->tpl['row'][$k]['link']  = cute_get_link($row);
			$this->tpl['row'][$k]['date']  = langdate('d.m.Y H:i:s', $row['date']);
			$this->tpl['row'][$k]['image'] = UPIMAGE.'/posts/'.($row['image'] ? $row['image'] : 'default.png');
			$this->tpl['row'][$k]['title'] = $this->value($row['title'], true);
			$this->tpl['row'][$k]['short'] = $this->value($row['short'], true);
			$this->tpl['row'][$k]['views'] = (int) $row['views'];
			$this->tpl['row'][$k]['comms'] = (int) $row['comments'];
		}
	}

	private function sql()
	{
		$query = "(SELECT $this->fields FROM `" .PREFIX. "news` JOIN `" .PREFIX. "story` USING(`id`)
				WHERE category = 2 AND hidden = 0 AND date <= ".time." ORDER BY 1 DESC LIMIT self::OFFSET, self::NUMBER)
			UNION (SELECT $this->fields FROM `" .PREFIX. "news` JOIN `" .PREFIX. "story` USING(`id`)
				WHERE category = 3 AND hidden = 0 AND date <= ".time." ORDER BY 1 DESC LIMIT self::OFFSET, self::NUMBER)
			UNION (SELECT $this->fields FROM `" .PREFIX. "news` JOIN `" .PREFIX. "story` USING(`id`)
				WHERE category = 5 AND hidden = 0 AND date <= ".time." ORDER BY 1 DESC LIMIT self::OFFSET, self::NUMBER)
			UNION (SELECT $this->fields FROM `" .PREFIX. "news` JOIN `" .PREFIX. "story` USING(`id`)
				WHERE category = 6 AND hidden = 0 AND date <= ".time." ORDER BY 1 DESC LIMIT self::OFFSET, self::NUMBER)";

		$query = $this->query($query);
		return reset($query) ? $query : false;
	}

	public function __toString()
	{
		$view = themes_directory.'/test.tpl';
		return (new Blitz($view))->parse($this->tpl);
	}
}
