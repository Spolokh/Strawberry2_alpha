<form method="post" id="registration">
	<fieldset>
		<dl>
			<dt>{lang.Login}: <cite>*</cite></dt>
			<dd><input id="nick" maxlength="12" pattern="^[A-Za-z0-9_\.\-]{3,12}$" type="text" name="register[nick]" required placeholder="Только латиница" /></dd>
		</dl>
		<dl>
			<dt>{lang.Passw}: <cite>*</cite></dt>
			<dd><input id="pass" maxlength="50" type="password" name="register[pass]" required placeholder="Пароль" /></dd>
		</dl>
		<dl>
			<dt>{lang.Passw} <sup>({lang.Re})</sup>: <cite>*</cite></dt>
			<dd><input id="conf" maxlength="50" type="password" name="register[conf]" required placeholder="Повторите пароль" /></dd>
		</dl>
		<dl>
			<dt>{lang.EMail}: <cite>*</cite></dt>
			<dd><input id="mail" maxlength="50" type="email" name="register[mail]" required placeholder="yourMail@mail.com" /></dd>
		</dl>
		<dl>
			<dt>{lang.Nick}: <cite>*</cite></dt>
			<dd><input id="name" maxlength="50" pattern="^[А-Яа-яЁё\s\-]{3,50}$" type="text" name="register[name]" required /></dd>
		</dl>
	
		[capcha]
		<dl>
			<dt>{lang.Pin}: <cite>*</cite></dt>
			<dd>
				<input type="text" name="pincode" class="pin_check"/> 
				<img id="capcha" style="width:85px; height: 28.9px; margin:0 0 3px;" src="/registration?pincode=1" border="1" align="absmiddle" alt="" /> &nbsp; 
				<a title="Обновить" style="font-size: 1.25em;" class="icon icon-repeat" id="icon-repeat" href="#"></a>
			</dd>
		</dl>
		<script type="text/javascript">

			$('icon-repeat').on('click', function(e) {		
				var url = $H({pincode: 1}).toQueryString();
					url = '/registration?'+url;
				new Ajax.Request(url, {
					method   : 'GET', 
					onSuccess: function(data) {
						$('capcha').src = url;
					},
					onFailure: function(data) {
						alert('Error Capcha!')
					}
				}); e.stop ();
			});
		</script>
		[/capcha]
    	<dl>
			<dt>&nbsp; <cite>&nbsp;</cite></dt>
			<dd>
				<input type="hidden" name="sessid" value="{SESSID}">
			    <input type="hidden" name="step" value="1">
				<input type="submit" value="    OK    ">
				<div id="result"></div>
			</dd>
		</dl>	
 	</fieldset>
</form>
<script type="text/javascript">
	//new MaskedInput('#phone', '+7(999)999-9999'); //document.domain + 
	//new Protoform('registration', {
	//	url:'/ajax/ajax.mails.php'
	//});
	
	//new Ajax.Autocompleter('location', 'citys', '/ajax/ajax.add.citys.php', {
	//		minChars: 1, indicator: 'indicator', paramName: 'register[location]' 			  
	//});
</script>