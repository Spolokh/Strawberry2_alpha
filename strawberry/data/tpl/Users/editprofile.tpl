<style>
form#editprofile 
	{
	background:#fCfCfC;
	}
form#editprofile dl
	{ 
	background:#fCfCfC;
	}
form#editprofile dl:nth-of-type(2n+1)
	{background:#f3f3f3;}	
form#editprofile dl abbr	
	{font-size: 11.0px;}
form#editprofile dd
	{}
form#editprofile dd i.icon-question-sign
	{margin:5px; color:#3c6498; font-size:16px; cursor:pointer}
#month
	{width:95px;}
input[size="2"]
	{width:30px;}
input[size="4"]
	{width:50px;}
.member_avatar {
	width:150px; float:left; 
        background:#FFF}

.member_avatar img {
    padding:3px; border:1px solid #C7C7C7;
}
</style>
<ul id="tabs" class="tabs horizontal">
	<li><a href="#tab1" title="Личные настройки">Основное</a></li>
	<li><a href="#tab2" title="Контактная информация">Контакты</a></li>
</ul>	
<!--div id="member_avatar" class="member_avatar">
		<img id="avatar" width="100" src="<?//=get_avatar($member)?>" />
</div-->

<form id="editprofile" method="post" action="profile.php" enctype="multipart/form-data">

	<!--tab1-->
	<fieldset id="tab1" class="tab">
		<dl>
		   <dt title="Не меняется.">&nbsp; Ник <abbr>(Не меняется)</abbr>:</dt>
		   <dd><input  type="text" value="#{username}" disabled /></dd>
		</dl>
		<dl>
		   <dt title="">&nbsp; Имя:</dt>
		   <dd><input  type="text" name="name" id="name" value="#{name}" /></dd>
		</dl>
	
		<dl>
		   <dt title="">&nbsp; Новый пароль:</dt>
		   <dd><input  type="text" name="editpass" id="editpass" placeholder="Если хотите изменить текущий"/></dd>
		</dl>
		<dl>
		   <dt title="">&nbsp; E-mail:</dt>
		   <dd><input type="email" name="mail" id="mail" value="#{mail}" /></dd>
		</dl>
		<dl>
		   <dt title="">&nbsp; Дата рождения:</dt>
		   <dd>#{age}</dd>
		</dl>
		<dl>
			<dt title="">&nbsp; Аватар:</dt>
			<dd> <cite></cite> <input  type="file" id="xhrField" name="avatar" />
		        <input type="button" id="Uploat" value="OK" style="display:none;" />
				<div id="xhrStatus"></div>
			</dd>
		</dl>
		<dl>
			<dt title="">&nbsp; О себе:</dt>
			<dd><textarea name="about" onkeyup="this.style.height='22px'; this.style.height = this.scrollHeight + 8 + 'px';">#{about}</textarea> 
			</dd>
		</dl>
		
	</fieldset>
	<!--tab2-->
	<fieldset id="tab2" class="tab">
	<dl>
		<dt>&nbsp; Откуда:</dt>
		<dd><input type="text" name="contacts[city]" id="location" value="#{city}" /></dd>
	</dl>
	<dl>
		<dt>&nbsp; Skype:</dt>
		<dd><input type="text" name="contacts[skype]" id="skype" value="#{skype}" /></dd>
	</dl>
	<dl>
		<dt>&nbsp;  Контактный телефон:</dt>
		<dd><input type="text" name="contacts[phone]" id="phone" value="#{phone}" /></dd>
	</dl>
	<dl>
		<dt>&nbsp; Домашняя страница:</dt>
		<dd><input type="url" name="contacts[page]" id="homepage" value="#{page}" /></dd>
	</dl>
	<dl>
		<dt>&nbsp; Аккаунт в LJ:</dt>
		<dd><input type="text" name="ljusername" id="ljusername" value="#{ljusername}" /></dd>
	</dl>
	<dl>
		<dt>&nbsp; Пароль от LJ:</dt>
		<dd><input type="password" name="ljpassword" id="ljpassword" value="#{ljpassword}" /></dd>
	</dl>	  
   </fieldset>
    <dl>
		<dt> &nbsp; Действие:</dt>
		<dd><input type="hidden" id="action" name="action" value="editprofile" />
			<input type="submit" id="submit" accesskey="s" value="  Редактировать  " />
			<div id="result"></div>
		</dd>
	</dl>	
</form>

[group=2]
вфывфвы
[/group]

<!--textarea style="overflow: hidden" onkeyup="this.style.height='22px'; this.style.height = this.scrollHeight + 8 + 'px';"></textarea>

<form onsubmit="return false" oninput="amount.value = (principal.valueAsNumber * rate.valueAsNumber) / 100">
<fieldset>  
<legend style="font-weight:bold;">Interest Calculator</legend>
<label for="principal">Amount to invest: $</label>
<input type="number" min="0" id="principal" name="principal" value="1000">
<p><label for="rate">Interest Rate: </label>
<input type="range" min="0" max="20" id="rate" name="rate" value="0" oninput="thisRate.value = rate.value">
<output name="thisRate" for="range">0</output> <span>%</span></p>
<p>Interest Received: <strong>$<output name="amount" for="principal rate">0</output></strong></p>
</form>
</fieldset-->
 