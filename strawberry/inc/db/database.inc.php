<?php

$database = [

	'users'  => [ // users
		'date'         => ['type' => 'int'],
		'usergroup'    => ['type' => 'int', 'default' => '0'],
		'username'     => ['type' => 'string', 'permanent' => 1],
		'password'     => ['type' => 'string'],
		'name'         => ['type' => 'string'],
		'age'    	   => ['type' => 'int', 'default' => '0'],
		'mail'         => ['type' => 'string', 'permanent' => 1],
		'publications' => ['type' => 'int', 'default' => '0'],
		'avatar'       => ['type' => 'string'],
		'last_visit'   => ['type' => 'int'],
		'contacts'     => ['type' => 'string'],
		'location'     => ['type' => 'string'],
		'about'        => ['type' => 'text'],
		'lj_username'  => ['type' => 'string'],
		'lj_password'  => ['type' => 'string'],
		'id'           => [
			'type'           => 'int',
			'auto_increment' => 1,
			'primary'        => 1
		],
		'deleted'      => ['type' => 'bool', 'default' => '0']
	],

'keywords' => [ // keywords new table
	'id'   => [
		'type' => 'int',
		'auto_increment' => 1,
		'primary' => 1
	],
	'name'      => ['type' => 'string'],
	'url'       => ['type' => 'string', 'permanent' => 1],
	'user_id'   => ['type' => 'int']
],

'categories'  => [ // categories
	'id'          => ['type' => 'int', 'primary' => 1],
	'name'        => ['type' => 'string'],
	'icon'        => ['type' => 'string'],
	'url'         => ['type' => 'string', 'permanent' => 1],
	'parent'      => ['type' => 'int', 'default' => '0'],
	'level'       => ['type' => 'int', 'default' => '0'],
	'template'    => ['type' => 'string'],
	'description' => ['type' => 'text'],
	'usergroups'  => ['type' => 'string'],
	'hidden'      => ['type' => 'bool','default' => '0']
],

'comments'    => array( // comments

	'date'    => array('type' => 'int'),
	'author'  => array('type' => 'string'),
	'mail'    => array('type' => 'string'),
	'page'    => array('type' => 'string'),
	'ip'      => array('type' => 'string'),
	'comment' => array('type' => 'text'),
	'reply'   => array('type' => 'text'),
	'type'    => array('type' => 'string'),
	'post_id' => array('type' => 'string'),
	'user_id' => array('type' => 'int', 'default' => '0'),
	'parent'  => array('type' => 'int', 'default' => '0'),
	'level'   => array('type' => 'int', 'default' => '0'),
	'hidden'  => array('type' => 'bool','default' => '0'),
	'id'      => array(
		'type'          => 'int',
		'auto_increment' => 1,
		'primary'        => 1
	)
),

'news' => array( // news
	'date'     => array('type' => 'int'),
	'author'   => array('type' => 'string'),
	'title'    => array('type' => 'string'),
	'image'    => array('type' => 'string'),
	'category' => array('type' => 'string'),
	'url'      => array('type' => 'string', 'permanent' => 1),
	'id'       => array(
		'type' => 'int',
		'auto_increment' => 1,
		'primary' => 1
	),
	'views'    => array('type' => 'int', 'default' => '0'),
	'comments' => array('type' => 'int', 'default' => '0'),
	'sticky'   => array('type' => 'bool', 'default' => '0'),
	'tags'     => array('type' => 'string'),
	'parent'   => array('type' => 'int', 'default' => '0'),
	'level'    => array('type' => 'int', 'default' => '0'),
	'type'     => array('type' => 'string'),
	'hidden'   => array('type' => 'bool', 'default' => '0'),
	'password' => array('type' => 'string'),
	'rating'   => array('type' => 'int', 'default' => '0'),
	'votes'    => array('type' => 'int', 'default' => '0')
),


'attach' => array( //links
	'id' => array(
		'type'           => 'int',
		'primary'        => 1,
		'auto_increment' => 1,
	),
	'size'   => array('type' => 'int'),
	'type'   => array('type' => 'string'),
	'file'   => array('type' => 'string'),
	'thumb'  => array('type' => 'string'),
	'title'  => array('type' => 'string'),
	'folder' => array('type' => 'string'),
	'width'  => array('type' => 'string'),
	'height' => array('type' => 'string'),
	'ext'    => array('type' => 'string')
  ),


	'links' => array( //links
	'id'   => array(
			'type'           => 'int',
			'auto_increment' => 1,
			'primary'        => 1
		),
	'date'        => array('type' => 'int'),
	'name'        => array('type' => 'string'),
	'url'         => array('type' => 'string'),
	'feed'        => array('type' => 'string'),
	'banner'      => array('type' => 'string'),
	'mail'        => array('type' => 'string'),
	'catalog'     => array('type' => 'string'),
	'description' => array('type' => 'text'),
	'hidden'      => array('type' => 'bool', 'default' => '0'),
	'views'       => array('type' => 'int', 'default' => '0')
	),
 
	'catalogs' => array( //catalogs
		'id'          => array('type' => 'int', 'primary' => 1),
		'name'        => array('type' => 'string'),
		'url'    	  => array('type' => 'string'),
		'icon'    	  => array('type' => 'string'),
		'description' => array('type' => 'text'),
		'parent'      => array('type' => 'int', 'default' => '0'),
		'level'       => array('type' => 'int', 'default' => '0'),
		'usergroups'  => array('type' => 'string'),
		'publication' => array('type' => 'bool'),
		'hidden'   => array('type' => 'bool', 'default' => '0')
	),

	'ipban' => array( // ipban
		'ip'    => array('type' => 'string'),
		'count' => array('type' => 'int', 'default' => '0')
	),

	'flood' => array( // flood
		'date'    => array('type' => 'int'),
		'ip'      => array('type' => 'string'),
		'post_id' => array('type' => 'int', 'primary' => 1)
	),

	'story' => array( // story
		'id' 		  => array('type' => 'int', 'primary' => 1),
		'description' => array('type' => 'string'),
		'keywords' => array('type' => 'string'),
		'short'   => array('type' => 'text'),
		'full'    => array('type' => 'text')
	),

	'usergroups'  => array( // usergroups
		'id' => array(
			'type'           => 'int',
			'primary'        => 1,
			'auto_increment' => 1,
			),
		'name'        => array('type' => 'string'),
		'access'      => array('type' => 'text'),
		'permissions' => array('type' => 'text')
	),

	'lang' => [ //lang
		'id'   => ['type' => 'string', 'permanent' => 1],
		'name' => ['type' => 'string'],
		'text' => ['type' => 'text']
	],

		
	'rating' => [ // ipban
		'ip' 		=> ['type' => 'string'],
		'type' 		=> ['type' => 'string'],
		'user_id' 	=> ['type' => 'int', 'default' => '0'],
		'id'      	=> ['type' => 'int', 'default' => '0', 'permanent' => 1]
	]
];
