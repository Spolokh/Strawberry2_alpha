document.observe("dom:loaded", function() { 
	
	$$('#comment textarea').each(function(el) {
		el.setStyle({height: 'auto'});
		el.on('input', function (e) {
			el.style.height = 'auto';
			el.style.height = el.scrollHeight + 2 + 'px';
		});
	});
	
	$('comment').on('submit', call_ajax);

	$(document).on('click', '.full-icon', showImage);
});

function changeFontSize(inc){
  
  $$('.post p', '.post h2').each(function(p){
    if(p.style.fontSize) 
       var size = parseInt(p.style.fontSize.replace("px", ""));
    else  
       var size = 12; 
       p.style.fontSize = size+inc + 'px';
  	});
}

function message_onkeydown(n, text){ //var s = $('message').value; 
 
	var count = n - $F('comment').length;
	$('countlabel').innerText = 'Осталось: ' + count;
 
	if($F('comment').length > n){
		$('countlabel').update(text).setStyle({color:'red'});
		return;
	}
}

function quickreply(formName, comment_id){
    if (comment_id == $('parent').value || comment_id == 0){
    	$('parent').setValue(0);
		$(formName + 0).insert($(formName).setStyle({margin:'30px 0'}));
    } else {
    	$('parent').setValue(comment_id);
        $(formName + comment_id).insert($(formName).setStyle({margin:'30px 0 30px 65px'}));
    }   return false;
}
	
function Complete(request) {  
	if (request.status == 200) {
		quickreply('comment', 0);
		$('result').update();
		$('commentslist').update(request.responseText);
		$('submit').enable().setValue('Добавить');
		$('comment').reset();
	} else Failure(request);
}

function Failure(request) { 

	if (request.status == 403) {
		$('comment').reset();
	}

	$('result').update(request.responseText).setStyle({color:'red'});
	$('submit').enable().setValue('Добавить');
}

function Loading() {
 
	$('submit').disable().setValue('Подождите');
	
	var progressbar = $('progressbar').show(),
		max  = progressbar.getAttribute('max'),
		val  = progressbar.value,
		time = (1000 / max) * 5;

	var loading = function () {
		val += 1;
		progressbar.value = val;	//$('progressvalue').update(value+' %');
		
		if (val == max) {
			clearInterval(animate);			           
		}
	};

	var animate = setInterval (function() {
		loading();
	},  time);
}

function call_ajax (e) {
	var form = this;
	new Ajax.Updater({success: 'commentslist'}, '/ajax/ajax.add.comment.php',
	{
		onLoading:  Loading,
		onFailure:  Failure,
		onComplete: Complete,
		parameters: Form.serialize(form),
		insertion:  Insertion.Top,
		evalScripts: true
	}); e.stop();
}

function showImage (e, a) {

	var image = a.href  || a.src;
	var title = a.title || a.alt;

	var template = Builder.node('div', {className: 'fullscreen-pic-inner'},
	[
		Builder.node('img', {id: 'fullscreen-pic-img', src: image}),
		Builder.node('div', {className: 'fullscreen-pic-caption'}, title),
		Builder.node('a', 	{className: 'fullscreen-pic-close', title: 'Закрыть'}, '×')
	]);

	$('showOverlay').show().update(template);

	$(document).on('keyup', function (e)
	{
		var keycode = (typeof e.keyCode != 'undefined' && e.keyCode) ? e.keyCode : e.which;
		if (keycode == 'Esc' || keycode == 27) {
			$('showOverlay').update().hide();
		}
	});

	$(document).on('click', function (e) 
	{
		var picInner = $$('.fullscreen-pic-inner').first();
		var picClose = $$('.fullscreen-pic-close').first();

		if (e.target == picInner || e.target == picClose) {
			$('showOverlay').update().hide();
		}
	});	e.stop();
}

function insertext(open, close, area){
    
	var msgfield = $(area); //msgfield = document.forms['comment'].elements['comments']; 

    // IE support
    if (document.selection && document.selection.createRange){
        msgfield.focus();
        sel = document.selection.createRange();
        sel.text = open + sel.text + close;
        msgfield.focus();
    } else if (msgfield.selectionStart || msgfield.selectionStart == '0'){  // Moz support
        var startPos = msgfield.selectionStart;
        var endPos = msgfield.selectionEnd;

        msgfield.value = msgfield.value.substring(0, startPos) + open + msgfield.value.substring(startPos, endPos) + close + msgfield.value.substring(endPos, msgfield.value.length);
        msgfield.selectionStart = msgfield.selectionEnd = endPos + open.length + close.length;
        msgfield.focus();
    } else { // Fallback support for other browsers
        msgfield.value += open + close;
        msgfield.focus();
    }

    return;
}	
/*
(function() {
	
	if (window.pluso)if (typeof window.pluso.start == "function") return;
  
	if (window.ifpluso==undefined) {
		window.ifpluso = 1;
		var d = document, 
			s = d.createElement('script'), 
			g = 'getElementsByTagName';
			
			s.type = 'text/javascript'; 
			s.charset='UTF-8'; 
			s.async = true;
			s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
		
			var h = d[g]('body')[0];
			h.appendChild(s);
	}
  })();*/
  /*
<div class="pluso" data-background="#ebebeb" data-options="big,square,line,horizontal,counter,theme=04" data-services="vkontakte,odnoklassniki,facebook,twitter,google,moimir,email,print" data-user="1471053828"></div>
*/
//window.onload = function () {
    //if($('vk_groups')){
       //VK.Widgets.Group('vk_groups', {mode:1, width:'270', height:'240'}, 17842512); //17842512  // 833563
   // }
     //VK.init({apiId: 17842512, onlyWidgets: true});
     //VK.Widgets.Comments('vk_comments', {width: 500, limit: 15});
//}