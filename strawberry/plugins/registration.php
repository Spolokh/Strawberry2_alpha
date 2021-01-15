<?php
/*
Plugin Name:	User.Registration
Plugin URI:     http://cutenews.ru
Description:    <strong>Русский:</strong> плагин позволяет посетителям вашего сайта самостоятельно зарегитсрироваться в системе. Возможно подключение с разными шаблонами.<br />В ACP можно указать с каким уровнем будут регистрироваться пользователи (Админ, Редактор, Журналист и Комментатор), так же там можно включить (по умолчанию включена) и настроить защиту от флуда регистрациями.
Version:		1.1
Author:         Пашка
Author URI:     mailto:pashka.89@mail.ru
Application: 	Strawberry
*/

use classes\PHPMailer;
use classes\CuteParser;

class userRegistration extends CuteParser
{
	const MODULE = 'registration'; 
	
	private $tpl    = '';
	private $values	= [];
	private $mailer = false;
	private $plugin = "registration";
	
	public function  __construct($config)
	{
		parent::__construct($config);
	    //global $xfields;
		//if(!$xfields instanceof XFieldsData) { 
		//	$xfields = new XFieldsData();
		//}

		$this->ip    = $this->getRealIp();
		$this->lang  = cute_lang('plugins/registration'); 
		$this->mailer = $mailer;
		$this->settings = new PluginSettings('registration');

        if( !is_array($this->settings->settings) ) {
            $this->setDefSettings ();
		}
	}

	private function setDefSettings()
	{
	    $this->settings->settings = [
			'regCapcha'  => true, 
			'regSender'	 => false,
			'preventRegFlood' => true,
			'regSendSubj'=> 'Администратору сайта',
			'RegDelay'	 => 180, 
			'banOnWarns' => 3, 
			'regLevel'	 => 5,
			'regBlocked' => 0
		];	$this->settings->save();
	}

	public function showForm($tpl = 'default')
	{
		global $sql, $is_logged_in;
		
		switch($_POST['step']) {
		
			case 1:			        	
					
				if ($this->settings->settings['preventRegFlood'] === true && (time - $this->settings->settings[$_SERVER['REMOTE_ADDR']]['LastRegTime']) < $this->settings->settings['RegDelay']){
					
					$tpl = $this->msg($this->lang['regError'], $this->lang['regErrorFlood']);
					$this->settings->settings[$this->ip]['warns']++;

					if ($this->settings->settings[$this->ip]['warns'] >= $this->settings->settings['banOnWarns'])
					{			
						if (!$sql->select(['ipban', 'where' => ["ip =  $this->ip"]]))
						{						
							$sql->insert(['ipban', 'values' => ["ip => $this->ip"]]);
						}
					}	
					break;
				}

				$_POST['register'] = array_map('trim', $_POST['register']);

				if (isset($_POST['sessid']) and $_POST['sessid'] !== session_id()) 
				{
					$this->tpl = $this->msg($this->lang['regError'], '');
					break;
				}

				foreach ($_POST['register'] as $k => $v) {
					$$k = $this->value($v, true);
				}

				if ( !preg_match('/^[A-Za-z0-9_\.\-]{3,12}$/i', $nick) )
				{
					$this->tpl = $this->msg($this->lang['regError'], 'Что то с ником');
					break;
				}

				if ( empty($pass) or $pass != $conf )
				{
					$this->tpl = $this->msg($this->lang['regError'], $this->lang['regErrorPasswords']);
					break;
				}
 	
				if (!filter_var($mail, FILTER_VALIDATE_EMAIL))
				{ 
					$this->tpl = $this->msg($this->lang['regError'], $this->lang['addErrorMail']);
					break;
				}

				if ($sql->select(['users', 'select' => ['username', 'mail'], 'where' => ["username = $nick", 'or', "mail = $mail"]]))
				{	
					$this->tpl = $this->msg($this->lang['regError'], $this->lang['regErrorName']);
					break;
				}

				//до лучших времен
				if ($this->settings->settings['regCapcha'] and $this->pinCheck())
				{
					$this->tpl = $this->msg( $this->lang['regError'], $this->lang['regErrorPin'] );			
					break;
				}

				/*if ( $sql->select(['users', 'select' => ['mail'], 'where' => ["mail = $mail"]]) )
				{
					$this->tpl = $this->msg($this->lang['regError'], $this->lang['regErrorMail']);
					break;
				}*/

				$this->settings->settings[$this->ip]['LastRegTime'] = time;
				
				$this->values['date']      = $this->settings->settings[$this->ip]['LastRegTime'];
				$this->values['usergroup'] = $this->settings->settings['regLevel'];
				$this->values['username']  = $nick;
				$this->values['password']  = md5x($pass);
				$this->values['about']     = $about;
				$this->values['name']      = $name;
				$this->values['mail']      = $mail;
				$this->values['deleted']   = $this->settings->settings['regBlocked'];
			
				/*
				$this->values['about']      = $about;
				$this->values['contacts']['location'] =$location;
				$this->values['contacts']['skype'] = $skype;
				$this->values['contacts']['phone'] = $phone;
				$this->values['contacts']   = json_encode($contacts);
				*/
			
				$id = $sql->insert(['users', 'values' => $this->values]);
			
				if ($this->settings->settings['regSender'] !== false)
				{
					$this->regSenderFromUser($tpl);
				}

				$this->tpl = $this->msg($this->lang['regOkAndLogined'], $this->lang['regOk']);
				break;

			default:
			  
				$this->tpl = GetContents(plugins_directory.'/registration/'. (file_exists(plugins_directory.'/registration/'.$tpl.'/form.tpl') ? $tpl : 'default') .'/form.tpl') ;
				
				$replaces = [
					'{lang.Re}'     => $this->lang ['regRe'],
					'{lang.Pin}'    => $this->lang ['regPin'],
					'{lang.Nick}'   => $this->lang ['regNick'],
					'{lang.EMail}'  => $this->lang ['regEmail'],
					'{lang.ESubj}'  => $this->lang ['sendSubj'],
					'{lang.Login}'	=> $this->lang ['regLogin'],
					'{lang.Passw}'     => $this->lang ['regPassw'],
					'{lang.Phone}'     => $this->lang ['regPhone'],
					'{lang.RegUser}'   => $this->lang ['regNewUser'],
					'{lang.Location}'  => $this->lang ['regLocation'],
					'{lang.AutoLogin}' => $this->lang ['regAutoLogin'],
					'{SESSID}'	=> session_id()
				];

				if (!$this->settings->settings[$this->ip])
				{
					$this->settings->settings[$this->ip] = ['warns' => 0];
				}
				
				foreach ($replaces as $from => $to) {
					$this->tpl = str_replace($from, $to, $this->tpl);
				}
				
				if (false === $this->settings->settings['regCapcha'])
				{
					$this->tpl = preg_replace('/\[capcha\](.*?)\[\/capcha\]/s', '', $this->tpl);
				}

				$this->tpl = str_replace('[capcha]', '', $this->tpl);
				$this->tpl = str_replace('[/capcha]', '', $this->tpl);
				
			break;		
		}

		$this->settings->save();
		return $this->tpl;
	}

	private function regSenderFromUser(PHPMailer $mailer, $tpl = 'default')
	{
		ob_start();
		include plugins_directory .'/'. $this->plugin .'/'. $tpl .'/sender.tpl';
		$Body = ob_get_clean();
		$Body = str_replace( '{{name}}',  $this->values['name'], $Body );
		$Body = str_replace( '{{mail}}',  $this->values['mail'], $Body );
		$Body = str_replace( '{{phone}}', $this->value($phone), $Body );
		$Body = str_replace( '{{skype}}', $this->value($skype), $Body );
		$Body = str_replace( '{{about}}', $this->values['about'], $Body );
		$Body = str_replace( '{{ip}}', $this->ip, $Body );

		/*$template->dir = plugins_directory .'/'. self::MODULE .'/'. $tpl .'/';
		$template->open('sender', self::MODULE);
		$template->set('name',  $this->values['name'],  self::MODULE);
		$template->set('mail',  $this->values['mail'],  self::MODULE);
		$template->set('phone', $this->values['phone'], self::MODULE);
		$template->set('skype', $this->values['skype'], self::MODULE);
		$template->set('ip', $this->ip, self::MODULE);

		$Body = $template->compile(self::MODULE, true);
		$template->fullClear();*/

		$this->mailer->From     = $this->values['mail'];
		$this->mailer->FromName = $this->values['name'];
		$this->mailer->CharSet  = $this->Config['charset'];
		$this->mailer->Sender   = $this->Config['admin_mail'];
		$this->mailer->Subject  = $this->settings->settings['regSendSubj'];
		$this->mailer->AddAddress($this->Config['admin_mail'], $this->settings->settings['regSendSubj']);
		$this->mailer->AddReplyTo($this->values['mail'], $this->values['name']);
			
		$this->mailer->Body    = $Body; 
		$this->mailer->AltBody = 'Добавляем адрес или альтернативный текст';
		$this->mailer->IsHTML (true);
		$this->mailer->Send();
	}

	public function __toString() 
    {

    }
}

add_action('head', 'userpanel'); ///////// //////////////////
add_action('head', 'get_avatar');

function userpanel () {
    global $PHP_SELF, $is_logged_in, $result, $member;
    // &nbsp; <i class="icon-caret-down"></i>
    $userpanel = '<li><a class="profile" href="/profile"><i class="icon-user"></i>&nbsp; ' .(isset($member['username']) ? $member['username'] : '').'!</a>
		<ul>
			<li><a href="/profile"><i class="icon-cog"></i>  Настройки</a></li>
			<li><a href="/post"><i class="icon-pencil"></i>  Добавить</a></li>
			<li><a href="'.$PHP_SELF.'?exit"><i class="icon-signout"></i>  Выйти</a></li>
		</ul>
	</li>';
  
	$return = $is_logged_in ? $userpanel : '<li><a href="/do/login" accesskey="l" onclick="Modalbox.show(this.href, {title: \'Авторизация на сайте\', height: 240}); return false;" title="Авторизация"><i class="icon-signin"></i> Вход</a></li><li><a href="/registration">Регистрация</a>'; 
    return $return;
} 

function get_avatar(){
    global $member, $config;
    $path_userpic = $config['path_userpic_upload'];
    $myavatar = (isset($member['avatar']) ? '<img src="'.$path_userpic.'/'.$member['username'].'.'.$member['avatar'].'">' : '<img src="'.$path_userpic.'/noavatar.gif">'); 
    return $myavatar;
}

add_action('plugins', function () {
	if ( isset($_GET['plugin']) and $_GET['plugin'] == 'regmod' ){
		registration_AdminOptions();
	}
});

function registration_AdminOptions()
{
	global $usergroups;
	
	$lang['regmod'] = cute_lang('plugins/registration');

	echoheader('users', $lang['regmod']['regPluginName']);
	$settings = new PluginSettings('registration');

	if(!is_array($settings->settings)){
		$settings->settings   = [
			'preventRegFlood' => true,
			'regCapcha' 	  => false,
			'regSender' 	  => false,
			'regSendSubj' 	  => 'Администратору сайта',
			'RegDelay'        => 180,
			'banOnWarns'      => 3,
			'regLevel'        => 4,
			'regBlocked'	  => 0
		];	$settings->save();
	}

	if( isset($_GET['save']) ) {
		$settings->settings = [
			'preventRegFlood' => $_POST['regmod']['noflood']? true : false,
			'regCapcha' 	  => $_POST['regmod']['capcha'] ? true : false,
			'regSender' 	  => $_POST['regmod']['sender'] ? true : false,
			'regSendSubj' 	  => $_POST['regmod']['sendSubj'],
			'RegDelay'        => (int)$_POST['regmod']['noregtime'],
			'banOnWarns'      => (int)$_POST['regmod']['warnstoban'],
			'regLevel'        => (int)$_POST['regmod']['reglevel'],
			'regBlocked'	  => (int)$_POST['regmod']['blocked']
		];	
		
		$settings->save();
	}

	$usergroupslist = [];

	foreach ($usergroups as $row){
		$usergroupslist[$row['id']] = $row['name'];
	}
?>
<form method="post" action="?plugin=regmod&amp;t=<?=time?>&amp;save=true">
	<table class="panel">
		<tr>
			<td width="350">&nbsp;<?=$lang['regmod']['regPreventRegFlood']?>
			<td><?=makeDropDown([$lang['regmod']['regNo'], $lang['regmod']['regYes']], "regmod[noflood]", $settings->settings['preventRegFlood'])?>		
		<tr>
			<td width="350">&nbsp;<?=$lang['regmod']['regCapcha']?>
			<td><?=makeDropDown([$lang['regmod']['regNo'], $lang['regmod']['regYes']], "regmod[capcha]", $settings->settings['regCapcha'])?>	
		<tr>
			<td width="">&nbsp;<?=$lang['regmod']['regBlocked']?>
			<td><?=makeDropDown([$lang['regmod']['regYes'], $lang['regmod']['regNo']], "regmod[blocked]", $settings->settings['regBlocked'])?>	
		<tr>
			<td width="350">&nbsp;<?=$lang['regmod']['regSender']?>
			<td><?=makeDropDown([$lang['regmod']['regNo'], $lang['regmod']['regYes']], "regmod[sender]", $settings->settings['regSender'])?>
		<tr>
			<td width="">&nbsp;<?=$lang['regmod']['sendSubj']?>
			<td><input type="text" name="regmod[sendSubj]" value="<?=$settings->settings['regSendSubj']?>" />
		<tr>		
			<td>&nbsp;<?=$lang['regmod']['regDelayTime']?>
			<td><input type="text" size="4" name="regmod[noregtime]" value="<?=$settings->settings['RegDelay']?>" />	
		<tr>
			<td>&nbsp;<?=$lang['regmod']['regWarns2Ban']?>
			<td><input type="text" size="2" name="regmod[warnstoban]" value="<?=$settings->settings['banOnWarns']?>" />	
		<tr>
			<td>&nbsp;<?=$lang['regmod']['regLevel']?>:
			<td><?=makeDropDown($usergroupslist, 'regmod[reglevel]', $settings->settings['regLevel']); ?>
		<tr>
		    <td>&nbsp;
			<td>
				<input type="submit" value="  <?=$lang['regmod']['regSave']?>  " /> 
			&nbsp; &nbsp; &nbsp; &nbsp;
					<img align="ABSmiddle" src="skins/images/help_small.gif"> &nbsp; 
					<a Title="Модуль регистрации пользователей" href="index.php?mod=help&amp;section=regmod" role="button" data-target="myModal" data-toggle="modal">
					<?=$lang['regmod']['regHelpTitle']?>
					</a>
	</table>
</form>
<?php echofooter(); }

add_filter('options', function ($options) { 	//global $PHP_SELF;
	$lang['regmod'] = cute_lang('plugins/registration');
	$options[] = ['name' => $lang['regmod']['regPluginName'], 'url' => 'plugin=regmod', 'category' => 'plugins'];	
	return $options;
});

add_filter('help-sections', function ($help_sections) {
    $lang['regmod'] = cute_lang('plugins/registration');
    $help_sections['regmod'] = $lang['regmod']['regHelp'];
	return $help_sections;
});
?>