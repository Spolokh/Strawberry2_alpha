<?php
$array = array (
  'uhtaccess' => '#DirectoryIndex index.php
# [user htaccess] 
ErrorDocument 404 /404.php
<IfModule mod_rewrite.c>
RewriteEngine On
#Options +FollowSymlinks
RewriteBase /

# [home] post
RewriteRule ^(page|news|article|review|art|music|art/cinema|pressa|analitics|files|yumor|news/serv_news|news/runet|clips)/(|[_0-9a-z-]+).html(/?)+$ index.php?category=$1&id=$2 [QSA,L]

# [home] blog
RewriteRule ^blog/([_0-9a-z-]+).html(/?)+$ index.php?go=blog&id=$1 [QSA,L]

# [home] category
RewriteRule ^(page|news|article|review|art|music|art/cinema|pressa|analitics|files|yumor|news/serv_news|news/runet|clips)(/?)+$ index.php?category=$1 [QSA,L]

# [home] author
RewriteRule ^author/([_0-9a-zA-Z-]+)(/?)+$ index.php?author=$1 [QSA,L]

# [home] user
RewriteRule ^users/([_0-9a-zA-Z-]+).html(/?)+$ index.php?go=users&user=$1 [QSA,L]

# [home] day
RewriteRule ^([0-9]{4})/([0-9]{2})/([0-9]{2})(/?)+$ index.php?year=$1&month=$2&day=$3 [QSA,L]
# [home] month
RewriteRule ^([0-9]{4})/([0-9]{2})(/?)+$ index.php?year=$1&month=$2 [QSA,L]
# [home] year
RewriteRule ^([0-9]{4})(/?)+$ index.php?year=$1 [QSA,L]

# [home] keywords
# [wrong rule] 
RewriteRule ^(/?)+$ index.php [QSA,L]

# [home] skip
# [wrong rule] 
RewriteRule ^(/?)+$ index.php [QSA,L]

# [home] map
RewriteRule ^(/?)+$ index.php?go=map [QSA,L]

# [home] do
#RewriteRule ^do/([_0-9a-zA-Z-]+)(/?)+$ index.php?go=do&action=$1 [QSA,L]

# [home] page
# [wrong rule] 
RewriteRule ^(/?)+$ index.php [QSA,L]
# [home] cpage
# [wrong rule] 
RewriteRule ^(/?)+$ index.php [QSA,L]

# [home] doIt
RewriteRule ^(do|search|users|video|blog|mail|map|profile|registration|fave|keywords)(/?)+$ index.php?go=$1 [QSA,L]

#Do file
RewriteRule ^do/([_0-9a-zA-Z-]+)(/?)+$ /strawberry/do.php?action=$1 [NC,L,QSA]

#RSS feed
RewriteRule rss.xml /strawberry/rss.php [NC,L,QSA]

# [rss.php] post
# [wrong rule] 
RewriteRule ^(/?)+$ /rss.php [QSA,L]
# [rss.php] category
# [wrong rule] 
RewriteRule ^(/?)+$ /rss.php [QSA,L]
# [rss.php] user
# [wrong rule] 
RewriteRule ^(/?)+$ /rss.php [QSA,L]
# [print.php] post
# [wrong rule] 
RewriteRule ^(/?)+$ /print.php [QSA,L]
# [trackback.php] post
# [wrong rule] 
RewriteRule ^(/?)+$ /trackback.php [QSA,L]
</IfModule>',
  'Blocks' => 
  array (
    'blocks' => 'content/do/archives|content/do/category|content/do/else|content/do/forgot|content/do/mail|content/do/map|content/do/registration|content/do/search|content/do/users|headlines|links|main|menu|meta|output|pages|right|submenu|users',
    'header' => 'header',
    'menu' => 'menu',
    'slider' => 'slider',
    'content' => 'submenu',
    'left' => 'left',
    'right' => 'useful|right',
    'head' => 'head',
    'footer' => 'footer',
    'jscripts' => 'js',
  ),
  'registration' => 
  array (
    'preventRegFlood' => true,
    'regCapcha' => true,
    'regSender' => false,
    'regSendSubj' => 'Администратору сайта',
    'RegDelay' => 180,
    'banOnWarns' => 4,
    'regLevel' => 5,
    'regBlocked' => 1,
    '127.0.0.1' => 
    array (
      'warns' => 0,
    ),
  ),
  'Adepto_Fastload' => 
  array (
    'delete_files' => '1',
    'deny_files' => '.cgi .pl .shtml .shtm .php .php3 .php4 .php5 .phtml .phtm .phps',
    'path_upload' => 'http://strawberry.test.ru/data/attach',
  ),
);

?>