
var mod = getUrlVars();
    mod = mod["mod"];

var CKConfig = {
	//extraPlugins: 'embed, autoembed',
	//height: 250,
	 //contentsCss: [CKEDITOR.basePath + 'contents.css', 'http://sdk.ckeditor.com/samples/assets/css/widgetstyles.css'],
	// Configure your file manager integration. This example uses CKFinder 3 for PHP.
	//filebrowserBrowseUrl: 	   'ckfinder/ckfinder.html',
	//filebrowserImageBrowseUrl: 'ckfinder/ckfinder.html?type=Images',
	//filebrowserUploadUrl: 	   'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
	//filebrowserImageUploadUrl: 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images'
};

if ( mod == 'addnews' ){
	
	//$$("textarea.story").each( function (area) {
	//	CKEDITOR.replace(area, CKConfig);
	//});
	
	/*$('addnews').on('submit', function(e){
	    if( !$F('TitleVal') ){
			alert('Поле "Заголовок" обязательное для заполнения'); 
			e.stop();
		}
	});*/
}
	//alert(mod);


var field = 0;

function addFile(name){
     field++;
     var otherField = new Element('input', {type:'text', name: 'desc['+ field +']'});
     var otherFile = new Element('input', {type:'file', name: name +'['+ field +']'});
     $('othersFiles').appendChild(otherField);
     $('othersFiles').appendChild(otherFile);
     otherFile = new Element('br');
     $('othersFiles').appendChild(otherFile);  //if(f > 3) $('add_file_link').hide();
}


function Effect_toggle(obj, type, duration_number){
	var type = type ? type : 'blind';
	var duration_number = duration_number ? durationduration_number : 0.5;
	return Effect.toggle(obj, type, {duration: duration_number});
}

function OpenTab(obj,n){ ////////////////// open tabs 
/*	var menu = $A($$('#tabs a'));
	
	menu.each(function(tab){tab.setStyle({color:'#446488', background:'#f3f3f3'})});
	menu[n].setStyle({color:'gray',background:'#fCfCfC'}); 
	
	$$('.tab').invoke('hide');	
	$(obj).show();*/	//appear()
} ////////////////// end open tabs
 
function _getElementById(id){
  var item = null;

  if (document.getElementById){
    item = document.getElementById(id);
  } else if (document.all){
    item = document.all[id];
  } else if (document.layers){
    item = document.layers[id];
  } return item;
}

function _getElementByTag(tag){
  var item = null;
  item = document.getElementsByTagName(tag);
  return item;
}

function Help(section) {
  q=window.open('index.php?mod=help&section='+section, 'Help', 'scrollbars=1,resizable=1,width=450,height=400');
}

function ShowOrHide(d1, d2) {
  if (d1 != '')
  	 DoDiv(d1);

  if (d2 != '')
  	 DoDiv(d2);
}

function DoDiv(id) {
    if (!$(id)){
    } else if ($(id).style){
      
		if ($(id).style.display == 'none'){
			$(id).style.display = '';
		} else {
			$(id).style.display = 'none';
		}
  } else {
  	$(id).visibility = 'show';
  }
}


function confirmDelete(url){
    //var agree = confirm('Вы действительно хотите удалить это?');
    //confirm('Вы действительно хотите удалить это?') ?  
	
	if (confirm('Вы действительно хотите удалить это?')){
        document.location = url;
    }
}

function preview(mod){
    dd = window.open('', 'prv')
    document.addnews.mod.value = 'preview';
    document.addnews.target    = 'prv'
    document.addnews.submit();
    dd.focus()
    setTimeout("document.addnews.mod.value='"+mod+"';document.addnews.target='_self'", 500)
}

/*function focus(){
	document.forms[0].title.focus();
}*/

function showpreview(image,name){
	if (image != ""){
	    document.images[name].src = image;
	} else {
	    document.images[name].src = "skins/images/blank.gif";
	}
}

function ckeck_uncheck_all(area) {

    if (area == "editnews"){frm = document.editnews;}
    else if (area == "links"){frm = document.links;}
    else if (area == "guest"){frm = document.guest;}
    else if (area == "rating"){frm = document.rating;}
    else if (area == "comments"){frm = document.comments;}
    else if (area == "editusers"){frm = document.editusers;}
	else if (area == "mailbox"){frm = document.mailbox;}

    for (var i=0;i<frm.elements.length;i++) {
        var elmnt = frm.elements[i];
        if (elmnt.type=="checkbox") {
            if(frm.master_box.checked == true){ elmnt.checked = true; }
            else{ elmnt.checked=false; }
        }
    }
    if(frm.master_box.checked == true){ frm.master_box.checked = true; }
    else{ frm.master_box.checked = false; }
}


function process_form(form){
	var element_names = new Object()
	element_names["username"] 	 = "Логин"
	element_names["password"] 	 = "Пароль"
	element_names["title"]       = "Заголовок"
	element_names["poster"]      = "Автор"
	element_names["comment"]     = "Комментарий"
	element_names["regusername"] = "Логин"
	element_names["regpassword"] = "Пароль"

	if (document.all || document.getElementById){
	   for (i = 0; i < form.length; ++i){
		 var elem = form.elements[i]
         if (
			(
				elem.name == "poster"
			 || elem.name == "comment"
			 || elem.name == "username"
			 || elem.name == "password"
			 || elem.name == "regusername"
			 || elem.name == "regpassword")
			 && elem.value == ''
		 )
		 {
                alert("\"" + element_names[elem.name] + "\" это поле обязательно для заполнения в этой форме.")
                elem.focus()
                return false
            }
		}
	}           return true
}

function open_img() { //открытие окна
   w=window.open('','img_win','fullscreen=no,status=no,resizable=yes,top=23,left=27,width=700,height=600,scrollbars=no,menubar=no');
   w.focus();
}
    
function popupMedia(url,width,height) {
	winheight = parseFloat(height) + 10;
	winwidth =  parseFloat(width) + 10;

	newWin=window.open('', 'popupwin','resizable=0,HEIGHT='+winheight+',WIDTH='+winwidth+', scrollbars=0, toolbars=0', true);
	newWin.document.write('<html>\n');
	newWin.document.write('<head>\n');
	newWin.document.write('<title>Image</title>');
	newWin.document.write('<script language="javascript" type="text/javascript">\n<!--\n');
	newWin.document.write('var arrTemp=self.location.href.split("?");\n');
	newWin.document.write('var picUrl = (arrTemp.length>1)?arrTemp[1]:"";\n');
	newWin.document.write('var NS = (navigator.appName=="Netscape")?true:false;\n');
	newWin.document.write('function fitMedia() {\n');
	newWin.document.write('	iWidth = (NS)?window.innerWidth:document.body.clientWidth;\n');
	newWin.document.write('	iHeight = (NS)?window.innerHeight:document.body.clientHeight;\n');
	newWin.document.write('	iWidth = '+winwidth+' - iWidth;\n');
	newWin.document.write('	iHeight = '+winheight+' - iHeight;\n');
	newWin.document.write('	window.resizeBy(iWidth, iHeight);\n');
	newWin.document.write('	var posLeft = (window.screen.width - '+winwidth+')/2;\n');
	newWin.document.write('	window.moveTo(posLeft,80);\n');
	newWin.document.write('};\n');
	newWin.document.write('-->\n</script>\n');

	newWin.document.write('<style>html,body{height:100%;padding:0px;}</style>\n');
	newWin.document.write('</head>\n');
	newWin.document.write('<body style="background-color:white; margin:0px; padding:0px;">\n');
	newWin.document.write('<table border=0 style="height:100%; width:100%; text-align:center;"><tr><td>\n');
	newWin.document.write('<img src="'+url+'" width="'+width+'" height="'+height+'" alt="" />');
	newWin.document.write('</td>\n</tr>\n</table>\n</body>\n<script language="javascript" type="text/javascript">\n<!--\n');
	newWin.document.write('fitMedia();\n');
	newWin.document.write('-->\n</script>\n');	
	newWin.document.write('</html>');
	newWin.document.close()
	newWin.focus();
}
    
 function OpenWin(OUrl,WinH,WinW) { 
     window.open(OUrl, "DisplayWindows", "width="+WinW+", height="+WinH+", scrollbars=no, status=no, resizable=0, titlebar=no, menubar=no");  
 } 

 function insertext(open, close, area){

	var msgfield = document.getElementById(area);

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
 
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
} 


 function getXmlHttp(){
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
