<?php

include_once 'head.php';

$module = 'profile';
$action = $_POST['action'] ?? NULL;

use classes\Upload;
use classes\Template;

if (isset($action) and $action == 'editprofile')
{
	if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
		exit(t('Извините, этот e-mail неправильный.'));
	}

	foreach ($sql->select(['users', 'select' => ['mail'], 'where' => ["id <> $member[id]"]]) as $row)
	{
		if ($mail and strtolower($row['mail']) == strtolower($mail)) {
			exit(t('Такой e-mail уже кто-то использует.'));
		}
	}
	
	foreach ($sql->select(['users', 'select'=> ['password'], 'where'=> $member['id']]) as $row)
	{	
		if ($editpass != '') {
			$row['password'] 	= md5x($editpass);
			$_SESSION['password'] 	= $row['password'];
			cute_setcookie('password', $row['password']);
		}
    	}
	
	if(($added_time = strtotime($day.' '.$month.' '.$year)) == -1)
	{
		$added_time = time;
    	}

	$ljpassword   = $ljpassword ? : $member['lj_password'];
	$upload_image = false;
	
	if ($_FILES['avatar']['name'] and !$_FILES['avatar']['error'])
	{
		$userpics = cute_parse_url ($config['path_userpic_upload']);
		$userpics = $userpics['abs'];
	
		CN::isDir ($userpics);
	
		$handle = new Upload($_FILES['avatar']);	  
			
		if ($handle->uploaded)
		{
			$handle->allowed = ['image/*'];
			$handle->file_new_name_body = $member['username'];
			
			if ($config['avatar_w'] != '')
			{
				$handle->image_resize     = true;
				$handle->image_ratio_crop = true;
				$handle->image_x = $config['avatar_w'];
				$handle->image_y = $config['avatar_h'];
			}
			 
			if ($config['avatar_ext'] != '')
			{
				$handle->image_convert = 'jpg';
			}

			$handle->file_overwrite = true;
			$handle->process($userpics);
			  
			if ($handle->processed)  //$upload_image = true;
			{
				$values['avatar'] = $handle->file_dst_name_ext;
			}
			$handle->clean();
		}
	}
	
	$values['age']      = $added_time;
	$values['name']     = replace_comment('add', $name, true);
	$values['mail']     = replace_comment('add', $mail, true);
	$values['password'] = $row['password'];
	$values['contacts'] = json_encode($contacts, JSON_UNESCAPED_UNICODE);
 
	$values['about']    = replace_comment('add', $about);
	$values['lj_username'] = replace_comment('add', $ljusername, true);
	$values['lj_password'] = replace_comment('add', $ljpassword, true);
	
	$result = $sql->update(['users', 'where' => $member['id'], 'values' => $values]) 
	? t('Ваш профиль успешно отредактирован!') 
	: t('Ошибка запроса!');
	
	$values = [];
	exit($result);
}

$template = templates_directory . '/Users/';
$template = (new Template($template))->open('editprofile', $module);
$template->set('name', $member['name'], $module)
	->set('mail', $member['mail'], $module)
	->set('age', date_AddRows($member['age']), $module)
	->set('username', $member['username'], $module)
	->set('about', htmlspecialchars(str_replace("<br/>", NL, $member['about'])), $module)
	->set('ljusername', $member['lj_username'], $module)
	->set('ljpassword', $member['lj_password'], $module)
;

if (isset($member['contacts']) AND CN::isJson($member['contacts']))
{
	$contact = json_decode($member['contacts']);

	$template->set('city',  $contact->city,  $module)
		 ->set('page',  $contact->page,  $module)
		 ->set('skype', $contact->skype, $module)
		 ->set('phone', $contact->phone, $module)
	;
}
	
echo $template->compile($module, true);
$template ->fullClear();

 
