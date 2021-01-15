<?php
/**
 * @package Show
 * @access private
 */

include_once 'strawberry/head.php';

add_filter('allow-comment-form', 'comment_form');

function comment_form(){
	return false;
}

add_filter('unset-template', 'unset_template');

function unset_template($files){
	$files[] = basename($_SERVER['PHP_SELF']);
	return $files;
}

header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="'.$config['charset'].'" ?>';
?>

<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/"
xmlns:wfw="http://wellformedweb.org/CommentAPI/"
xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>

<?php if ( empty($id) ){ ?>
<title><?=$config['home_title']; ?></title>
<link><?=$config['http_home_url']; ?></link>
<description><?=$config['description']; ?></description>
<?php } ?>

<language><?=$config['lang']; ?></language>
<generator><?=$config['version_name'].' '.$config['version_id']; ?></generator>

<?php
$config['cnumber'] = 0;
$number   =  12;
$template = 'RSS';
include rootpath.'/show_news.php';
?>
</channel>
</rss>
