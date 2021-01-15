// йа маладэц
Event.observe(window, 'load', function(){
	
    var titles = $A(document.all || document.getElementsByTagName('*'));

    titles.find(function(node) {
    	if (node.tagName == 'IMG' && node.alt && !node.title)
    	    node.title = node.alt;

    	if (node.tagName == 'A' && node.target == '_blank')
    	    node.title += (node.title ? ' ' : '') + '(откроется в новом окне)';

        if (node.title){
        	var div = document.createElement('DIV');
        	var title = node.title;
            var object = Try.these(function(){
            	return eval(node.title);
            })

            Element.extend(div);
            document.body.appendChild(div);

            div.setAttribute('id', 'tooltip')
                .setStyle({'position': 'absolute', 'z-index': 9999})
                .hide();

            node.removeAttribute('title');

            Event.observe(node, 'mousemove', function(event){
                if (object){
                    div.appendChild(object);
                    object.show();
                } else {
                    div.update(title.gsub(/\\n/, '<br />'));
                }

                div.setStyle({'left': Event.pointerX(event), 'top': Event.pointerY(event)}).show();
            })

            Event.observe(node, 'mouseout', function(event){
                div.hide();
            })
        }
    })
})

////////////////////////////////////////////////////////////////////////////////

function print(output){
	document.write(output);
}

////////////////////////////////////////////////////////////////////////////////

function totranslit(text, that){ // http://textpattern.com/
    var map = {'À': 'A', 'Á': 'A', 'Â': 'A', 'Ã': 'A', 'Ä': 'A', 'Å': 'A', 'Æ': 'AE', 'Ā': 'A', 'Ą': 'A', 'Ă': 'A', 'Ç': 'C', 'Ć': 'C', 'Č': 'C', 'Ĉ': 'C', 'Ċ': 'C', 'Ď': 'D', 'Đ': 'D', 'È': 'E', 'É': 'E', 'Ê': 'E', 'Ë': 'E', 'Ē': 'E', 'Ę': 'E', 'Ě': 'E', 'Ĕ': 'E', 'Ė': 'E', 'Ĝ': 'G', 'Ğ': 'G', 'Ġ': 'G', 'Ģ': 'G', 'Ĥ': 'H', 'Ħ': 'H', 'Ì': 'I', 'Í': 'I', 'Î': 'I', 'Ï': 'I', 'Ī': 'I', 'Ĩ': 'I', 'Ĭ': 'I', 'Į': 'I', 'İ': 'I', 'Ĳ': 'IJ', 'Ĵ': 'J', 'Ķ': 'K', 'Ľ': 'K', 'Ĺ': 'K', 'Ļ': 'K', 'Ŀ': 'K', 'Ł': 'L', 'Ñ': 'N', 'Ń': 'N', 'Ň': 'N', 'Ņ': 'N', 'Ŋ': 'N', 'Ò': 'O', 'Ó': 'O', 'Ô': 'O', 'Õ': 'O', 'Ö': 'O', 'Ø': 'O', 'Ō': 'O', 'Ő': 'O', 'Ŏ': 'O', 'Œ': 'OE', 'Ŕ': 'R', 'Ř': 'R', 'Ŗ': 'R', 'Ś': 'S', 'Ş': 'S', 'Ŝ': 'S', 'Ș': 'S', 'Š': 'S', 'Ť': 'T', 'Ţ': 'T', 'Ŧ': 'T', 'Ț': 'T', 'Ù': 'U', 'Ú': 'U', 'Û': 'U', 'Ü': 'Ue', 'Ū': 'U', 'Ů': 'U', 'Ű': 'U', 'Ŭ': 'U', 'Ũ': 'U', 'Ų': 'U', 'Ŵ': 'W', 'Ŷ': 'Y', 'Ÿ': 'Y', 'Ý': 'Y', 'Ź': 'Z', 'Ż': 'Z', 'Ž': 'Z', 'à': 'a', 'á': 'a', 'â': 'a', 'ã': 'a', 'ä': 'a', 'ā': 'a', 'ą': 'a', 'ă': 'a', 'å': 'a', 'æ': 'ae', 'ç': 'c', 'ć': 'c', 'č': 'c', 'ĉ': 'c', 'ċ': 'c', 'ď': 'd', 'đ': 'd', 'è': 'e', 'é': 'e', 'ê': 'e', 'ë': 'e', 'ē': 'e', 'ę': 'e', 'ě': 'e', 'ĕ': 'e', 'ė': 'e', 'ƒ': 'f', 'ĝ': 'g', 'ğ': 'g', 'ġ': 'g', 'ģ': 'g', 'ĥ': 'h', 'ħ': 'h', 'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i', 'ī': 'i', 'ĩ': 'i', 'ĭ': 'i', 'į': 'i', 'ı': 'i', 'ĳ': 'ij', 'ĵ': 'j', 'ķ': 'k', 'ĸ': 'k', 'ł': 'l', 'ľ': 'l', 'ĺ': 'l', 'ļ': 'l', 'ŀ': 'l', 'ñ': 'n', 'ń': 'n', 'ň': 'n', 'ņ': 'n', 'ŉ': 'n', 'ŋ': 'n', 'ò': 'o', 'ó': 'o', 'ô': 'o', 'õ': 'o', 'ö': 'o', 'ø': 'o', 'ō': 'o', 'ő': 'o', 'ŏ': 'o', 'œ': 'oe', 'ŕ': 'r', 'ř': 'r', 'ŗ': 'r', 'ś': 's', 'š': 's', 'ť': 't', 'ù': 'u', 'ú': 'u', 'û': 'u', 'ü': 'u', 'ū': 'u', 'ů': 'u', 'ű': 'u', 'ŭ': 'u', 'ũ': 'u', 'ų': 'u', 'ŵ': 'w', 'ÿ': 'y', 'ý': 'y', 'ŷ': 'y', 'ż': 'z', 'ź': 'z', 'ž': 'z', 'ß': 'ss', 'ſ': 'ss', 'Α': 'A', 'Ά': 'A', 'Ἀ': 'A', 'Ἁ': 'A', 'Ἂ': 'A', 'Ἃ': 'A', 'Ἄ': 'A', 'Ἅ': 'A', 'Ἆ': 'A', 'Ἇ': 'A', 'ᾈ': 'A', 'ᾉ': 'A', 'ᾊ': 'A', 'ᾋ': 'A', 'ᾌ': 'A', 'ᾍ': 'A', 'ᾎ': 'A', 'ᾏ': 'A', 'Ᾰ': 'A', 'Ᾱ': 'A', 'Ὰ': 'A', 'Ά': 'A', 'ᾼ': 'A', 'Β': 'B', 'Γ': 'G', 'Δ': 'D', 'Ε': 'E', 'Έ': 'E', 'Ἐ': 'E', 'Ἑ': 'E', 'Ἒ': 'E', 'Ἓ': 'E', 'Ἔ': 'E', 'Ἕ': 'E', 'Έ': 'E', 'Ὲ': 'E', 'Ζ': 'Z', 'Η': 'I', 'Ή': 'I', 'Ἠ': 'I', 'Ἡ': 'I', 'Ἢ': 'I', 'Ἣ': 'I', 'Ἤ': 'I', 'Ἥ': 'I', 'Ἦ': 'I', 'Ἧ': 'I', 'ᾘ': 'I', 'ᾙ': 'I', 'ᾚ': 'I', 'ᾛ': 'I', 'ᾜ': 'I', 'ᾝ': 'I', 'ᾞ': 'I', 'ᾟ': 'I', 'Ὴ': 'I', 'Ή': 'I', 'ῌ': 'I', 'Θ': 'TH', 'Ι': 'I', 'Ί': 'I', 'Ϊ': 'I', 'Ἰ': 'I', 'Ἱ': 'I', 'Ἲ': 'I', 'Ἳ': 'I', 'Ἴ': 'I', 'Ἵ': 'I', 'Ἶ': 'I', 'Ἷ': 'I', 'Ῐ': 'I', 'Ῑ': 'I', 'Ὶ': 'I', 'Ί': 'I', 'Κ': 'K', 'Λ': 'L', 'Μ': 'M', 'Ν': 'N', 'Ξ': 'KS', 'Ο': 'O', 'Ό': 'O', 'Ὀ': 'O', 'Ὁ': 'O', 'Ὂ': 'O', 'Ὃ': 'O', 'Ὄ': 'O', 'Ὅ': 'O', 'Ὸ': 'O', 'Ό': 'O', 'Π': 'P', 'Ρ': 'R', 'Ῥ': 'R', 'Σ': 'S', 'Τ': 'T', 'Υ': 'Y', 'Ύ': 'Y', 'Ϋ': 'Y', 'Ὑ': 'Y', 'Ὓ': 'Y', 'Ὕ': 'Y', 'Ὗ': 'Y', 'Ῠ': 'Y', 'Ῡ': 'Y', 'Ὺ': 'Y', 'Ύ': 'Y', 'Φ': 'F', 'Χ': 'X', 'Ψ': 'PS', 'Ω': 'O', 'Ώ': 'O', 'Ὠ': 'O', 'Ὡ': 'O', 'Ὢ': 'O', 'Ὣ': 'O', 'Ὤ': 'O', 'Ὥ': 'O', 'Ὦ': 'O', 'Ὧ': 'O', 'ᾨ': 'O', 'ᾩ': 'O', 'ᾪ': 'O', 'ᾫ': 'O', 'ᾬ': 'O', 'ᾭ': 'O', 'ᾮ': 'O', 'ᾯ': 'O', 'Ὼ': 'O', 'Ώ': 'O', 'ῼ': 'O', 'α': 'a', 'ά': 'a', 'ἀ': 'a', 'ἁ': 'a', 'ἂ': 'a', 'ἃ': 'a', 'ἄ': 'a', 'ἅ': 'a', 'ἆ': 'a', 'ἇ': 'a', 'ᾀ': 'a', 'ᾁ': 'a', 'ᾂ': 'a', 'ᾃ': 'a', 'ᾄ': 'a', 'ᾅ': 'a', 'ᾆ': 'a', 'ᾇ': 'a', 'ὰ': 'a', 'ά': 'a', 'ᾰ': 'a', 'ᾱ': 'a', 'ᾲ': 'a', 'ᾳ': 'a', 'ᾴ': 'a', 'ᾶ': 'a', 'ᾷ': 'a', 'β': 'b', 'γ': 'g', 'δ': 'd', 'ε': 'e', 'έ': 'e', 'ἐ': 'e', 'ἑ': 'e', 'ἒ': 'e', 'ἓ': 'e', 'ἔ': 'e', 'ἕ': 'e', 'ὲ': 'e', 'έ': 'e', 'ζ': 'z', 'η': 'i', 'ή': 'i', 'ἠ': 'i', 'ἡ': 'i', 'ἢ': 'i', 'ἣ': 'i', 'ἤ': 'i', 'ἥ': 'i', 'ἦ': 'i', 'ἧ': 'i', 'ᾐ': 'i', 'ᾑ': 'i', 'ᾒ': 'i', 'ᾓ': 'i', 'ᾔ': 'i', 'ᾕ': 'i', 'ᾖ': 'i', 'ᾗ': 'i', 'ὴ': 'i', 'ή': 'i', 'ῂ': 'i', 'ῃ': 'i', 'ῄ': 'i', 'ῆ': 'i', 'ῇ': 'i', 'θ': 'th', 'ι': 'i', 'ί': 'i', 'ϊ': 'i', 'ΐ': 'i', 'ἰ': 'i', 'ἱ': 'i', 'ἲ': 'i', 'ἳ': 'i', 'ἴ': 'i', 'ἵ': 'i', 'ἶ': 'i', 'ἷ': 'i', 'ὶ': 'i', 'ί': 'i', 'ῐ': 'i', 'ῑ': 'i', 'ῒ': 'i', 'ΐ': 'i', 'ῖ': 'i', 'ῗ': 'i', 'κ': 'k', 'λ': 'l', 'μ': 'm', 'ν': 'n', 'ξ': 'ks', 'ο': 'o', 'ό': 'o', 'ὀ': 'o', 'ὁ': 'o', 'ὂ': 'o', 'ὃ': 'o', 'ὄ': 'o', 'ὅ': 'o', 'ὸ': 'o', 'ό': 'o', 'π': 'p', 'ρ': 'r', 'ῤ': 'r', 'ῥ': 'r', 'σ': 's', 'ς': 's', 'τ': 't', 'υ': 'y', 'ύ': 'y', 'ϋ': 'y', 'ΰ': 'y', 'ὐ': 'y', 'ὑ': 'y', 'ὒ': 'y', 'ὓ': 'y', 'ὔ': 'y', 'ὕ': 'y', 'ὖ': 'y', 'ὗ': 'y', 'ὺ': 'y', 'ύ': 'y', 'ῠ': 'y', 'ῡ': 'y', 'ῢ': 'y', 'ΰ': 'y', 'ῦ': 'y', 'ῧ': 'y', 'φ': 'f', 'χ': 'x', 'ψ': 'ps', 'ω': 'o', 'ώ': 'o', 'ὠ': 'o', 'ὡ': 'o', 'ὢ': 'o', 'ὣ': 'o', 'ὤ': 'o', 'ὥ': 'o', 'ὦ': 'o', 'ὧ': 'o', 'ᾠ': 'o', 'ᾡ': 'o', 'ᾢ': 'o', 'ᾣ': 'o', 'ᾤ': 'o', 'ᾥ': 'o', 'ᾦ': 'o', 'ᾧ': 'o', 'ὼ': 'o', 'ώ': 'o', 'ῲ': 'o', 'ῳ': 'o', 'ῴ': 'o', 'ῶ': 'o', 'ῷ': 'o', '¨': '', '΅': '', '᾿': '', '῾': '', '῍': '', '῝': '', '῎': '', '῞': '', '῏': '', '῟': '', '῀': '', '῁': '', '΄': '', '΅': '', '`': '', '῭': '', 'ͺ': '', '᾽': '', 'А': 'A', 'Б': 'B', 'В': 'V', 'Г': 'G', 'Д': 'D', 'Е': 'E', 'Ё': 'E', 'Ж': 'ZH', 'З': 'Z', 'И': 'I', 'Й': 'I', 'К': 'K', 'Л': 'L', 'М': 'M', 'Н': 'N', 'О': 'O', 'П': 'P', 'Р': 'R', 'С': 'S', 'Т': 'T', 'У': 'U', 'Ф': 'F', 'Х': 'KH', 'Ц': 'TS', 'Ч': 'CH', 'Ш': 'SH', 'Щ': 'SHCH', 'Ы': 'Y', 'Э': 'E', 'Ю': 'YU', 'Я': 'YA', 'Ъ': '', 'Ь': '', 'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'e', 'ж': 'zh', 'з': 'z', 'и': 'i', 'й': 'i', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'kh', 'ц': 'ts', 'ч': 'ch', 'ш': 'sh', 'щ': 'shch', 'ы': 'y', 'э': 'e', 'ю': 'yu', 'я': 'ya', 'ъ': '', 'ь': '', 'ð': 'd', 'Ð': 'D', 'þ': 'th', 'Þ': 'TH', 'ა': 'a', 'ბ': 'b', 'გ': 'g', 'დ': 'd', 'ე': 'e', 'ვ': 'v', 'ზ': 'z', 'თ': 't', 'ი': 'i', 'კ': 'k', 'ლ': 'l', 'მ': 'm', 'ნ': 'n', 'ო': 'o', 'პ': 'p', 'ჟ': 'zh', 'რ': 'r', 'ს': 's', 'ტ': 't', 'უ': 'u', 'ფ': 'p', 'ქ': 'k', 'ღ': 'gh', 'ყ': 'q', 'შ': 'sh', 'ჩ': 'ch', 'ც': 'ts', 'ძ': 'dz', 'წ': 'ts', 'ჭ': 'ch', 'ხ': 'kh', 'ჯ': 'j', 'ჰ': 'h', '&': 'and'};

	text = text.stripTags().strip();
    text = text.gsub(/\&\w+\;/, '');

	for (i in map)
		text = text.gsub(i, map[i]);

	text = text.gsub(/\W|\_|\-/, '|');
	text = text.gsub(/^\|+/, '').gsub(/\|+$/, '');
	text = text.gsub(/\|+/, (that || '-'));
	return text;
}

////////////////////////////////////////////////////////////////////////////////

function ckeck_uncheck(id){
    var s = Form.getElements(id);

    for (var i = 0; i < s.length; i++){
    	var e = s[i];

    	if (e.type == 'checkbox'){
            e.checked =  (e.checked == true) ? false : true;
            
            if (e.checked == true)
    	        e.checked = false;
    	    else
    	        e.checked = true;
    	}
    }
}

////////////////////////////////////////////////////////////////////////////////

// не помню откуда спёр
// http://punbb.org ?
function insert(open, close, area){
	var msgfield = $(area);

    // IE support
    if (document.selection && document.selection.createRange){
        msgfield.focus();
        sel = document.selection.createRange();
        sel.text = open + sel.text + close;
        msgfield.focus();
    }

    // Moz support
    else if (msgfield.selectionStart || msgfield.selectionStart == "0"){
        var startPos = msgfield.selectionStart;
        var endPos = msgfield.selectionEnd;

        msgfield.value = msgfield.value.substring(0, startPos) + open + msgfield.value.substring(startPos, endPos) + close + msgfield.value.substring(endPos, msgfield.value.length);
        msgfield.selectionStart = msgfield.selectionEnd = endPos + open.length + close.length;
        msgfield.focus();
    }

    // Fallback support for other browsers
    else {
        msgfield.value += open + close;
        msgfield.focus();
    }
}