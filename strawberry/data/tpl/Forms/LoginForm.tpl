<form method="post" id="authForm">  
	<dl>  
		<input name="username" id="username" type="text" value="<?=$lastname?>" placeholder="Ваш логин или Email" required />
    </dl>
    <dl>
		<input name="password" id="password" type="password"  placeholder="Ваш пароль" required />
	</dl>
	<dl>
		<label class="option">
			<input name="remember" id="remember" type="checkbox" value="1" />
			<span class="checkbox"></span> &nbsp; <?=t('Запомнить вас?') ?> &nbsp; &nbsp; 
			<a href="<?=cute_get_link(['go' => 'registration'], 'go')?>"><?=t('Регистрация') ?></a>
		</label>
	</dl>
	<dl>
		<input type="submit" value="    <?=t('Войти'); ?>    " /> &nbsp;&nbsp;
		<input type="button" class="button" value=" <?=t('Отмена'); ?> " onclick="Modalbox.hide();" />
		<input type="hidden" name="action" value="dologin" />
    </dl>
	<dl id="result"><?=$result ?></dl>
</form>
<script type="text/javascript">
$('authForm').on('submit', function(e)
{
	if (!$F('username').length || !$F('password').length) {   	
		$('result').setStyle({color: 'red'}).update('Все поля обязательны для заполнения!');
		e.stop(); 
		return;
	}
	e.submit();
});	   
</script>
<style>
	form#authForm dl
		{
			border: none
		}
</style>