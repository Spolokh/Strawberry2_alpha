<?php
/**
 * @package Private
 * @access private
 */

include_once dirname(__DIR__) . '/strawberry/head.php';

if (!$is_logged_in or !isset($member['id'])) {
	//$errors[] = t('Голосовать может только авторизированный пользователь!');
}

foreach ( $_POST AS $k => $v ) {
	$$k= htmlspecialchars($v);
}

if (empty($id)) {
	$errors[] = t('Такого поста не найдено!');
}

if (reset($errors)) {
    header('HTTP/1.1 500 Internal Server Error'); 
	exit ( join('<br/>', array_values($errors)) ) ;
}

$vote = (int) $post['votes'];

if (!$sql->count(['votes', 'where' => ["ip = $ip", 'and', "id = $id"]]))
{	
	$vote = ($vote + 1);
	$values['id'] = $id;
	$values['ip'] = $ip;

	if (isset($member['id']))
	{
		$values['user'] = $member['id'];
	}

	$sql->insert(['votes', 'values' => $values]);

} else {
	$vote = ($vote - 1);
    $sql->delete(['votes', 'where' => ["ip = $ip", 'and', "id = $id"]]);
}

$sql->update(['news', 'where' => $id, 'values' => [ 'votes' => $vote ]]);

echo ($vote > 0) ?  ' '.$vote.' ' : '';
