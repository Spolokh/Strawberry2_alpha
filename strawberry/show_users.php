<?php
/**
 * @package Show
 * @access private
 */

include_once 'head.php';

//$number = isset($number) ? (int) $number : ($config['users_number'] ? $config['users_number'] : 21);

$number = $number ?? $config['users_number'];
$offset = !empty($_GET['skip']) ? (($_GET['skip'] - 1) * $number) : 0;
 

$tpl['template']  = templates_directory.'/Users';
$allow_full_story = false;
$user   = $_GET['user'] ?? $user;
$where  = [];
$select = [];

$where[] = 'deleted = 0';

if( isset($user) ) {
	$where[] = 'and';
	$where[] = "id = $user";
	$where[] = 'or';
	$where[] = "username = $user";
	//$allow_full_story = true; 
}

$query = $sql->select(['users', 'where' => $where, 'limit' => [$offset, $number]]);

if (!reset($query)) {
	return $cute->msg('Achtung!', t('Пользователей не найдено'));
}    
	
$count = $sql->count(['users', 'where' => $where]);

foreach ($query as $row)
{
	if ($row['id'] == $user or $row['username'] == $user) {
        	$allow_full_story = true;
	} 
    	else {
		$allow_full_story = false;
	}

	if (isset($user) and !$allow_full_story) {
		continue;
	}

	if (!$rufus_file){
		$rufus_file = parse_ini_file(rufus_file, true);
	}

	foreach ($rufus_file as $type_k => $type_v){
		if (is_array($type_v)){
			foreach ($type_v as $k => $v){
				if ($type_k == 'home'){
					$tpl['user']['link'][$k] = cute_get_link($row, $k);
				}   
				$tpl['user']['link'][$type_k.'/'.$k] = cute_get_link($row, $k, $type_k);
			}
		}
	}
		
		
	$tpl['user']['member'] = $member;
		
	$avatar = $config['path_userpic_upload'].'/'.$row['username'].'.'.$row['avatar'] ;
		
	$tpl['user']['homepage'] = $homepage ?: '';
	$tpl['user']['location'] = $location ?: '<i>Город не указан</i>';
		
	$tpl['user']['avatar']      = ( $row['avatar'] and file_exists( UPLOADS .'/userpics/'. $row['username'].'.'.$row['avatar']) ) ? $config['path_userpic_upload'].'/'.$row['username'].'.'.$row['avatar'] : $config['path_userpic_upload'].'/default.png';
	//$tpl['user']['location']    = $row['location'];
	$tpl['user']['about']       = run_filters('news-entry-content', $row['about']);
	$tpl['user']['lj-username'] = ($row['lj_username'] ? '<a href="http://'.$row['lj_username'].'.livejournal.com/profile"><img style="width:17px; height17px;" src="themes/'.$config['theme'].'/images/user.gif" alt="" align="absmiddle"/></a><a href="http://'.$row['lj_username'].'.livejournal.com">'.$row['lj_username'].'</a>' : '');
	$tpl['user']['name']       = $row['name'];
	$tpl['user']['username']   = $row['username'];
	$tpl['user']['author']     = cute_get_link($row, 'author');
	$tpl['user']['usergroup']  = $usergroups[$row['usergroup']]['name'];
	$tpl['user']['id']         = $row['id'];
	$tpl['user']['date']       = langdate($config['timestamp_active'], $row['date']);
	$tpl['user']['age']        = langdate($config['timestamp_active'], $row['age']);
	$tpl['user']['mail']       = filter_var($row['mail'], FILTER_VALIDATE_EMAIL)? $row['mail']: '';
	$tpl['user']['last_visit'] = ($row['last_visit'] ? langdate($config['timestamp_active'], $row['last_visit']) : '');
	$tpl['user']['about']      = run_filters('news-entry-content', $row['about']);
	$tpl['user']['alternating'] = cute_that('cn_users_odd', 'cn_users_even');
	$tpl['user']['publications'] = $row['publications'];
	$tpl['user']['_']            = $row;

	ob_start();
	include $tpl['template'] .DS. ($allow_full_story ? 'show' : 'list').'.tpl';
	$output = ob_get_clean();

	$output = run_filters('news-entry', $output);
	$output = replace_news('show', $output);
	echo $output;       
}    
