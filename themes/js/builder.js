var Builder={NODEMAP:{AREA:'map',CAPTION:'table',COL:'table',COLGROUP:'table',LEGEND:'fieldset',OPTGROUP:'select',OPTION:'select',PARAM:'object',TBODY:'table',TD:'table',TFOOT:'table',TH:'table',THEAD:'table',TR:'table'},node:function(a){a=a.toUpperCase();var b=this.NODEMAP[a]||'div';var c=document.createElement(b);try{c.innerHTML="<"+a+"></"+a+">"}catch(e){}var d=c.firstChild||null;if(d&&(d.tagName.toUpperCase()!=a))d=d.getElementsByTagName(a)[0];if(!d)d=document.createElement(a);if(!d)return;if(arguments[1])if(this._isStringOrNumber(arguments[1])||(arguments[1]instanceof Array)||arguments[1].tagName){this._children(d,arguments[1])}else{var f=this._attributes(arguments[1]);if(f.length){try{c.innerHTML="<"+a+" "+f+"></"+a+">"}catch(e){}d=c.firstChild||null;if(!d){d=document.createElement(a);for(attr in arguments[1])d[attr=='class'?'className':attr]=arguments[1][attr]}if(d.tagName.toUpperCase()!=a)d=c.getElementsByTagName(a)[0]}}if(arguments[2])this._children(d,arguments[2]);return d},_text:function(a){return document.createTextNode(a)},ATTR_MAP:{'className':'class','htmlFor':'for'},_attributes:function(a){var b=[];for(attribute in a)b.push((attribute in this.ATTR_MAP?this.ATTR_MAP[attribute]:attribute)+'="'+a[attribute].toString().escapeHTML().gsub(/"/,'&quot;')+'"');return b.join(" ")},_children:function(a,b){if(b.tagName){a.appendChild(b);return}if(typeof b=='object'){b.flatten().each(function(e){if(typeof e=='object')a.appendChild(e);else if(Builder._isStringOrNumber(e))a.appendChild(Builder._text(e))})}else if(Builder._isStringOrNumber(b))a.appendChild(Builder._text(b))},_isStringOrNumber:function(a){return(typeof a=='string'||typeof a=='number')},build:function(a){var b=this.node('div');$(b).update(a.strip());return b.down()},dump:function(b){if(typeof b!='object'&&typeof b!='function')b=window;var c=("A ABBR ACRONYM ADDRESS APPLET AREA B BASE BASEFONT BDO BIG BLOCKQUOTE BODY "+"BR BUTTON CAPTION CENTER CITE CODE COL COLGROUP DD DEL DFN DIR DIV DL DT EM FIELDSET "+"FONT FORM FRAME FRAMESET H1 H2 H3 H4 H5 H6 HEAD HR HTML I IFRAME IMG INPUT INS ISINDEX "+"KBD LABEL LEGEND LI LINK MAP MENU META NOFRAMES NOSCRIPT OBJECT OL OPTGROUP OPTION P "+"PARAM PRE Q S SAMP SCRIPT SELECT SMALL SPAN STRIKE STRONG STYLE SUB SUP TABLE TBODY TD "+"TEXTAREA TFOOT TH THEAD TITLE TR TT U UL VAR").split(/\s+/);c.each(function(a){b[a]=function(){return Builder.node.apply(Builder,[a].concat($A(arguments)))}})}};