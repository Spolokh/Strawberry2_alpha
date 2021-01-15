<link type="text/css" rel="stylesheet" href="/cp/skins/css/redactor.css" />
<style>
form#editprofile 
	{
	background:#fCfCfC;
	}
form#editprofile dl
	{
	background:#fCfCfC;
	}
form#editprofile dt
	{
	width: 25%;
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
}

.redactor_story, .redactor_editor:focus {
	width: 100%;
}
</style>
<ul id="tabs" class="tabs horizontal">
	<li><a href="#tab1" title="Основное">Основное</a></li>
	<li><a href="#tab2" title="Дополнительно">Дополнительно</a></li>
	<!--li><a href="#tab3" title="Настроить Аватар">Аватар</a></li-->
</ul>	

<form id="editprofile" method="post" action="/profile.php" enctype="multipart/form-data">

	<!--tab1-->
	<fieldset id="tab1" class="tab" style="display:block;">
		<dl>
		   <dt title="Не меняется.">&nbsp; Заголовок:</dt>
		   <dd><input style="width:100%;" type="text" name="title" value="#{title}" /></dd>
		</dl>
		
		<dl>
			<dt title="">&nbsp; Иллюстрация:</dt>
			<dd><input style="width:100%;" type="file" id="xhrField" name="Filedata" /> <input type="button" id="Uploat" value="OK" style="display:none;" />
				<div id="xhrStatus"></div>
			</dd>
		</dl>

		<dl>
		   <dt title="">&nbsp; Категория:</dt>
		   <dd>
			   <select style="width:100%;" name="category"></select>
		   </dd>
		</dl>

		<dl>
			<dt title="">&nbsp; Анонс новости:</dt>
			<dd><textarea name="short" id="short" class="story" style="width:100%; height:150px">#{short}</textarea></dd>
		</dl>	
		<dl>
			<dt title="">&nbsp; Полная новость:</dt>
			<dd><textarea name="full" id="full" class="story" style="width:100%; height:150px">#{full}</textarea></dd>
		</dl>	
	 
		
		  
		
		<!--dl>
		   <dt title="">&nbsp; Новый пароль:</dt>
		   <dd><input style="width:290px;" type="text" name="editpass" id="editpass" placeholder="Если хотите изменить текущий"/></dd>
		</dl>
		<dl>
		   <dt title="">&nbsp; E-mail:</dt>
		   <dd><input style="width:290px;" type="email" name="mail" id="mail" value="#{mail}" /></dd>
		</dl>
		<dl>
		   <dt title="">&nbsp; Дата рождения:</dt>
		   <dd>#{age}</dd>
		</dl>
		
		<dl>
			<dt title="">&nbsp; О себе:</dt>
			<dd><textarea type="text" name="about" style="width:290px;height:100px;overflow-x:hidden;overflow-y:visible;">#{about}</textarea> 
			</dd>
		</dl-->	
	</fieldset>
	 
	<!--tab2-->
	<fieldset id="tab2" class="tab" style="display:none;">
	

	<dl>
		<dt title="">&nbsp; Теги:</dt>
		<dd><input style="width:100%;" type="text" name="keywords" id="keywords" value="#{keywords}" /></dd>
	</dl>
	 
	<dl>
	   <dt>&nbsp;  Контактный телефон:</dt>
	   <dd><input style="width:290px;" type="text" name="contacts[phone]" id="phone" value="#{phone}" /></dd>
	</dl>
		<dl>
		   <dt>&nbsp; Домашняя страница:</dt>
		   <dd>
		   <input style="width:290px;" type="url" name="contacts[page]" id="homepage" value="#{page}" /></dd>
		</dl>
		
		<dl>
		   <dt>&nbsp; Аккаунт в LJ:</dt>
		   <dd><input style="width:290px;" type="text" name="ljusername" id="ljusername" value="#{ljusername}" /></dd>
		</dl>
		
		<dl>
		   <dt> &nbsp; Пароль от LJ:</dt>
		   <dd><input style="width:290px;" type="password" name="ljpassword" id="ljpassword" value="#{ljpassword}" /></dd>
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

<script type="text/javascript" src="/themes/js/jquery.js"></script>
<script type="text/javascript" src="/cp/js/redactor.js"></script>
<script type="text/javascript">
$.noConflict();	

document.observe("dom:loaded", function() {	

	jQuery(".story").redactor({
		imageUpload: '/ajax/image.upload.php', fileUpload: '/ajax/files.upload.php',
		imageGetJson: '/uploads/json.images.php'
	});

	var hash;
	
	if ( window.location.hash ) {
		hash = window.location.hash.substr(1);
		$$(".tab").invoke('hide');
		$(hash).show();
	}
	
	var tabs = $$('#tabs a');
	tabs.first().addClassName('active-tab');
	tabs.invoke('on', 'click', function(e){
		e.stop();	
		tabs.invoke('removeClassName', 'active-tab');
		this.addClassName('active-tab');
		
		var tab = this.hash.substr(1);
		$$(".tab").invoke('hide');
		$(tab).show();
	}).bindAsEventListener(this);
	
	$('xhrField').on('change', function(){
		xhr_file = this.files[0]; 
		xhr_parse(xhr_file, 'xhrStatus');
	});		
});


$('editprofile').on('submit', function(e){

	var sec  = 3000;
	var form = new FormData (this);
	var ajax = getXmlHttp ();
	var file = this.getAttribute('action') ;
	ajax.onreadystatechange = function() { //
		
		if (this.readyState == 1) { // установлено соединение с сервером
			$('submit').setValue('Loading...').disable(); //Create();  // loading
		}	
		if (this.readyState == 2) { 	// запрос получен 
		}
		if (this.readyState == 3) { 	// обработка запроса
			$('submit').setValue(' Редактировать ').enable();
		}
		
		if (this.readyState == 4 && this.status == 200) {
			
			Modalbox.alert(this.responseText);
			var interval = setInterval( function(){
				Modalbox.hide(); clearInterval(interval); 
			}, sec );
		}
	};
	
	ajax.open ('POST', '/strawberry/' + file); 
	ajax.setRequestHeader ('Cache-Control', 'no-cache');
	ajax.setRequestHeader ("X-Requested-With", "XMLHttpRequest");	//ajax.setRequestHeader ("X-File-Name", xhrFile.name);
	ajax.send (form); e.stop();
});	

</script>
 