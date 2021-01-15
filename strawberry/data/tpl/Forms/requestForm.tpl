<style>#orderForm fieldset {
	padding: 15px; box-shadow: #a7a7a7 0 0 2px;
	*box-shadow: 0 4px 10px 0 rgba(0,0,0,0.2), 0 4px 20px 0 rgba(0,0,0,0.19);
}#orderForm input, #orderForm textarea {
    margin: 5px; width: 98.5%
}</style>  

<form id="orderForm" action="/do/form" method="post">
	<fieldset>
		<input name="name" type="text" maxlength="50" placeholder="Ваше имя" pattern="^[А-Яа-яЁё\s]{3,50}$" required /><br />
		<input name="phone" type="tel" maxlength="50" placeholder="Ваш телефон" required /><br />
		<textarea name="comment" id="comments" placeholder="Ваше сообщение"></textarea>
		<input name="action" type="hidden" value="orderform"/>
		<div id="orderFormResult" style="padding:4px;"></div>
	</fieldset>
</form>
<script>
	$('orderForm').on('submit', function(e)
	{ 
		var file = this.getAttribute('action');
		var form = new FormData(this);
		var ajax = getXmlHttp();
		var time = 3000;

		ajax.onreadystatechange = function()
		{
			if (this.readyState != 4) //alert(this.responseText);
			{
				return;
			}

			if (this.status == 200)
			{
				$('resultModal').update(this.responseText).setStyle({color: 'green'});
				var interval = setInterval(function()
				{
					$('resultModal').update();
					$$('[data-dismiss="modal"]').invoke('click');
					clearInterval(interval); 
				}, time);
			}
		};
		
		ajax.open('POST', file);
		ajax.setRequestHeader("Cache-Control", "no-cache");
		ajax.setRequestHeader("X-Requested-With", "XMLHttpRequest");	
		ajax.send(form); e.stop();
	});
</script>
