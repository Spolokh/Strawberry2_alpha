<form id="validateForm" method="post">
	<fieldset>	
		[if-logged]
		<dl>
		   <dt title="Введите Ваше имя">Ваше имя:  <cite>*</cite></dt>
		   <dd><input type="text" pattern="^[А-Яа-яЁё\s]{3,50}$" name="name" id="name_Req" maxlength="50" title="Введите ваше имя" required autofocus /></dd>
		</dl>
		<dl>
		   <dt title="Введите Вашу почту">Ваша почта:  <cite>*</cite></dt>
		   <dd><input type="email" name="mail" id="mail_Req_Email" maxlength="50" title="Введите ваш e-mail" required /></dd>
		</dl>
		<dl>
		   <dt title="Введите Ваш телефон">Ваш телефон: <cite>&nbsp;</cite></dt>
		   <dd><input type="tel" pattern="" name="phone" maxlength="50" id="phone"/></dd>
		</dl>
		[/if-logged]
		<dl>
		   <dt title="Не обязательно">Тема сообщения: <cite>&nbsp;</cite></dt>
		   <dd><input type="text" pattern="^[А-Яа-яЁё0-9,\s\.\-]{3,50}$" name="subject" id="subject" maxlength="50" title="Required! Please enter your subject" /></dd>
		</dl>
		<dl>
			<dt title="Введите Ваше сообщение">Cообщение: <cite>*</cite></dt>
			<dd><textarea name="comment" id="comment_Req" placeholder="Введите ваше сообщение"></textarea></dd>
		</dl>
		<dl>
			<dt>&nbsp;</dt>
			<dd><label class="option" for="rememberme">
					<input type="checkbox" id="rememberme" name="rememberme" value="on" checked>
					<span class="checkbox"></span> &nbsp; Запомнить вас?
				</label>
			</dd>  
		</dl>
		<dl>
			<dt>&nbsp;</dt>
			<dd>  
				<input type="hidden" name="template" value="callback" />
				<input type="hidden" name="session" value="[SESSID]" />
				<input type="submit" accesskey="s" value="  <?=t('Отправить'); ?>  "> &nbsp; 
				<input type="reset"  accesskey="r" value="  <?=t('Очистить'); ?>  ">
			</dd>
		</dl>
	</fieldset>
</form>