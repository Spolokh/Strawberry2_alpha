/*
Просмотров: <?=$tpl['post']['views'] ?>
Комментариев: <?=$tpl['post']['comments'] ?>
Голосов: <?=$tpl['post']['votes'] ?>
*/

$(document).on('mouseover', '.showtooltip', function (e, a) {

	var title = a.dataset.tooltip;
		title = title.gsub(/\\n/, '<br />');
	var tooltip = a.down('div.tooltip');

	return tooltip ? tooltip.update(title).show() : false;
});

$(document).on('mouseout', '.showtooltip', function (e, a) {

	var tooltip = a.down('div.tooltip');
	return tooltip ? tooltip.update().hide() : false;
});

function addVote (e, a) {

    var query = $H({id: a.dataset.id}).toQueryString();
    new Ajax.Request('/ajax/ajax.vote.php', { 
        parameters: query,
        onSuccess : function(data) {
            if (data.status == 200) {
                a.update(data.responseText);
            } else alert(data.responseText);
        },
        onFailure : function(data) {
	     	alert(data.responseText);
        }
    });	e.stop();
}

function message_onkeydown(textarea, n, div){
	
	if (!n || n == 0) {
		return;
	}

	var submit = $$('input[type="submit"]');
	var ccount = n - $F(textarea).length;
	$(div).update('Осталось: ' + ccount);
	//.setStyle({color:'gray'}).innerHTML = 'Осталось: ' + c;

	if ($F(textarea).length > n) { ////
		$(div).update('вы превысили количество вводимых символов!').setStyle({color:'red'});
		submit.invoke('disable');	//Modalbox.alert('вы превысили количество вводимых символов!');
		return;
	} else submit.invoke('enable');
}

function getXmlHttp() {
	var xmlhttp;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e) {
			xmlhttp = false;
		}
	}
	if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
		xmlhttp = new XMLHttpRequest();
	}   return xmlhttp;
}

function fileInfoParse(file, e) {
	var info = 'File: '+ file.name +' ('+ file.size +')' || 'No file!';
	$(e).update(info);
}

function number_format(number, decimals, point, thousands_sep) {	// Format a number with grouped thousands
	// + original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
	// + improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// + bugfix by: Michael White (http://crestidg.com)

	var i, j, kw, kd, km;
	// input sanitation & defaults
	if( isNaN(decimals = Math.abs(decimals)) ){
		decimals = 0;
	}
	if( point == undefined ){
		point = ",";
	}
	if( thousands_sep == undefined ){
		thousands_sep = " ";
	}

	i = parseInt(number = (+number || 0).toFixed(decimals)) + "";
    j = (j = i.length) > 3 ? j % 3 : 0;

	km = (j ? i.substr(0, j) + thousands_sep : "");
	kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep); //kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
	kd = (decimals ? point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");
	return km + kw + kd;
}

function changeLanguage (a) { 
			
	var attribut = a.getAttribute("data-language");
	a.toggleClassName('ru');
	 
	if (attribut == "en"){ //background: url('themes/flags/24/United-Kingdom.png')
		a.update("Russian version").setAttribute("data-language", "ru");
	} else {
		a.update("English version").setAttribute("data-language", "en");
	}

	$$(".full").invoke('toggle'); 	 
}

function setLazy(){
    lazy = document.querySelectorAll('img[loading="lazy"]');
    console.log('Found '+lazy.length+' lazy images');
} 

function lazyLoad(){
	//[].forEach.call(lazy, function(img){
	lazy.forEach(function(img)
	{
		if(isInViewport(img))
		{
			if (img.hasAttribute('data-src')){
				img.src = img.getAttribute('data-src');
				img.removeAttribute('data-src');
			}
		}
	});
    
    cleanLazy();
}

function cleanLazy(){
    lazy = Array.prototype.filter.call(lazy, function(e){ 
		return e.getAttribute('data-src');
	});
}

function isInViewport(el){
    var rect = el.getBoundingClientRect();
    return (
		rect.bottom >= 0 && 
		rect.right >= 0 && 
		rect.top <= (window.innerHeight || document.documentElement.clientHeight) && 
		rect.left <= (window.innerWidth || document.documentElement.clientWidth)
	);
}

function registerListener(ev, func) {
    if (window.addEventListener) {
        window.addEventListener(ev, func)
    } else {
        window.attachEvent('on' + ev, func)
    }
}
	
function runCarousel(carousel) {
	/*hCarousel = new UI.Carousel(carousel, {
		scrollInc: 'auto', direction: 'horizontal'
	});*/
}

function print(output){
	document.write(output);
}
