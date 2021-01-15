
/**
 * File:         $HeadURL$
 * Description:  extensions for Prototype library
 * @autor        Mamontov Andrey <andrvm@andrvm.ru>
 * @copyright    Mamontov Andrey (andrvm) 2009 - 2010
 * @license      MIT
 * @date		 $Date$
 * @revision	 $Revision$
 * @changeby	 $Author$
 * 
 */

 Object.extend(Array.prototype, { // Arrays
 	
 	/**
 	 * Alignment blocks of height
 	 * Prototype version: >= 1.5.1
 	 * Example:
 	 * $('block1', 'block2').autoHeight();
 	 * $$('div').autoHeight();
 	 * Don't work with "!important" in style ({height:100px !important;})
 	 */
  	autoHeight: function(){
		var tmpH = 0;
		this.each(function(item){
			$(item).setStyle({height:tmpH + 'px'});}.bind(
				this.each(function(item){
					var h_= parseInt($(item).getStyle('height'),  10);	
					tmpH  = h_ > tmpH ?  h_ : tmpH;
		})))
  	},
 
  	/**
  	 * Open external link in new a window
  	 * Prototype version: >= 1.6
  	 * Example:
  	 * // external link
  	 * <a href="http://example.com" rel="external">External link</a>
  	 * // processing
  	 * $$('a[rel="external"]').external();
  	 */   
   	external: function(){
        this.each(function(item){
			$(item).writeAttribute('target', '_blank');
		});	
  	},
  	
  	/**
  	 * Tooltip
  	 * Prototype version: >= 1.5
  	 * 
  	 * Example:
  	 * 
  	 * // tip
  	 * <span class="tip" title="Tip"></span>
  	 * 
  	 * // processing
  	 * $$('.tip').tooltip();
  	 * 
  	 * PS. Styles for the .tip should be defined
  	 * 
  	 */   
    tooltip: function(){
    	
    	var html 	= $$('html')[0];                            // html
    	var body 	= document.body;                            // body
    	var tbox    = null;                                     // box
    	var cash    = {tbox:[]};                                // cash
    	var sw      = html.clientWidth;                         // screen width
    	var sh      = html.clientHeight;                        // screen height
    	var offsetX = Math.round((sw - body.clientWidth) / 2);  // offsetX (only body position with margin:0 auto)
    	var offsetY = Math.round((sh - body.clientHeight) / 2); // offsetY (only body position with margin:0 auto)
    	var e = null;                                           // element where the event occurred
       
    	
    	function createbox(evt){  // create tooltip box   	    		
    		e  = evt.target ? evt.target : evt.srcElement; // element where the event occurred
     	    var id = e.id.indexOf('_') >=0 ? e.id.substr(e.id.indexOf('_') + 1, e.id.length) : cash.tbox.length + 1; // id for tooltip box in the cache
     	 
     	    if(typeof(cash.tbox[id]) != 'undefined') {     	    	
     	    	tbox = cash.tbox[id];     	    	   	    	
     	    }
     	    else {	// create tooltip box
     	    	tbox = document.createElement('div');             // set class
     	    	$(tbox).addClassName('tipbox');
     	    	//$(tbox).addClassName(!param ? 'standart' : param); 
     	    	tbox.setAttribute('id', 'tipbox_' + id);          // set attributes
     	    	if(!e.id) e.setAttribute('id', 'ptipbox_'+ id);   // set id (if non exist)
     	    	$(tbox).update('<p>' + e.title + '</p>');         // fill tooltip
     	    	e.setAttribute('title', '');                      // hide title
     	    	cash.tbox[id] = tbox;                             // add tooltop to chache
     	    	$(tbox).observe('mousemove', posbox.bind(this));  // add mousemove event handler   			
     	    }     	    
     	     
     	    body.appendChild(tbox); //add tooltip to body
     	    posbox(evt);	    	
    	}
    	
    	function posbox(evt){ // position tooltip
    		
    		if(tbox == null) return;
    	   
    		e  = evt.target ? evt.target : evt.srcElement; // element where the event occurred
    
     	   	var x  = y = ''; // tooltip position
       		// mouse coordinates	
     	   	var gY = (evt.pageY) ? evt.pageY : evt.clientY + (document.documentElement.scrollTop  || body.scrollTop)  - document.documentElement.clientTop;
     	    var gX = (evt.pageX) ? evt.pageX : evt.clientX + (document.documentElement.scrollLeft || body.scrollLeft) - document.documentElement.clientLeft;    
     	    var realX = gX - offsetX + 1; // kill flicker in firefox (+1)
     	    var realY = gY - offsetY + 1;
     	    
     	    // left || right
     	    if( realX + parseInt($(tbox).getWidth()) > body.clientWidth )
     	    	$(tbox).setStyle({right:(body.clientWidth - realX) + 'px'});
     	    else
     	    	$(tbox).setStyle({left:realX + 'px'});      	         	    
    		    
			$(tbox).setStyle({top:  gY + 'px'});	    		
    	}	
    	
    	function delbox(evt){ // deleting toolbox
    	    		    		    		
    		e = evt.target ? evt.target : evt.srcElement; // element where the event occurred
    		var id = e.id.indexOf('_') >=0  ? e.id.substr(e.id.indexOf('_') + 1, e.id.length) : ''; // get id from element where event occurred
    		
    		if (!id || $('tipbox_' + id) == null) return;
    		body.removeChild($('tipbox_' + id));   // remove tooltim from DOM	
    	}
    	
       this.each(function(item){   		
    	    item.observe('mouseover', createbox.bind(this));
    		item.observe('mousemove', posbox.bind(this));
    		item.observe('mouseout',  delbox.bind(this));		
       });
    }	
});
 
////////////////////////////////////////////////////////////////////////////////////////////////////////////
/* Simple tabs using Prototype http://tetlaw.id.au/view/blog/fabtabulous-simple-tabs-using-prototype/ Andrew Tetlaw version 1.1 2006-05-06 */

var Fabtabs = Class.create(); 
Fabtabs.prototype = {
	initialize : function(element){
		this.element = $(element);
		var options  = Object.extend({}, arguments[1] || {});
		this.menu    = $A(this.element.getElementsBySelector('a'));
		this.show(this.getInitialTab());
		this.menu.each(this.setupTab.bind(this));
	},
	setupTab : function(elm) { 
		elm.observe('click',  function(e) {
			var elm = Event.findElement(e, 'a');
			e.stop();	this.show(elm);
			this.menu.without(elm).each(this.hide.bind(this));
		}.bindAsEventListener(this), false);
	},

	hide : function(elm){
		$(elm).removeClassName('active-tab');
		$(this.tabID(elm)).removeClassName('active-tab-body');
	},
	show : function(elm){
		$(elm).addClassName('active-tab');
		$(this.tabID(elm)).addClassName('active-tab-body');
	},
	tabID : function(elm){
		return elm.href.match(/#(\w.+)/)[1];
	},
	getInitialTab : function(){
		var loc = RegExp.$1;
		var elm = this.menu.find(function(value) { 
			return value.href.match(/#(\w.+)/)[1] == loc; 
		});
		return document.location.href.match(/#(\w.+)/) ? elm || this.menu.first() : this.menu.first();
	}
}


/* =========================================================
* bootstrap-modal.js v2.3.2
* http://twitter.github.com/bootstrap/javascript.html#modals
* =========================================================
* Copyright 2012 Twitter, Inc.
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
* http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
* ========================================================= */
/*
Modified for use with PrototypeJS
http://github.com/jwestbrook/bootstrap-prototype
*/
"use strict";

/* MODAL CLASS DEFINITION
* ====================== */
if(BootStrap === undefined){
	var BootStrap = {};
}

BootStrap.Modal = Class.create({
	initialize : function (element, options) {
		element.store('bootstrap:modal',this)
		this.$element = $(element);
		this.options = options !== undefined ? options : {}
		this.options.backdrop = this.options.backdrop !== undefined ? options.backdrop : true
		this.options.keyboard = this.options.keyboard !== undefined ? options.keyboard : true
		this.options.show = this.options.show !== undefined ? options.show : true

		if( this.options.show )
			this.show();
		$$("[data-dismiss='modal']").invoke("observe","click",function(){
			this.hide()
		}.bind(this))

		if ( this.options.remote && this.$element.select('.modal-body') ) {
			var t = new Ajax.Updater(this.$element.select('.modal-body')[0], 
				this.options.remote,  {
					evalScripts: true
				}
			);
		}
	}
	, toggle: function () {
		return this[!this.isShown ? 'show' : 'hide']()
	}
	, show: function (e) {
		var that = this

		this.$element.setStyle({display:'block'})

		var showEvent = this.$element.fire('bootstrap:show')

		if (this.isShown || showEvent.defaultPrevented) return

		this.isShown = true

		this.escape()

		this.backdrop(function () {
			var transition = (BootStrap.handleeffects == 'css' || (BootStrap.handleeffects == 'effect' && typeof Effect !== 'undefined' && typeof Effect.Fade !== 'undefined')) && that.$element.hasClassName('fade')

			if (that.$element.up('body') === undefined) {
				$$("body")[0].insert(that.$element);
			}
			that.$element.setStyle({display: 'block'})

			if(transition && BootStrap.handleeffects == 'css') {
				that.$element.observe(BootStrap.transitionendevent,function(){
					that.$element.fire("bootstrap:shown");
				});
				setTimeout(function(){
					that.$element.addClassName('in').writeAttribute('aria-hidden', 'false');
				},1);
			} else if( transition && BootStrap.handleeffects == 'effect' ) {
				new Effect.Parallel([
					new Effect.Morph(that.$element,{sync:true, style:'top:10%'}),
					new Effect.Opacity(that.$element,{sync:true,from:0,to:1})
				],{duration:0.3, afterFinish: function(){
					that.$element.addClassName('in').writeAttribute('aria-hidden', 'false')
					that.$element.fire("bootstrap:shown");
				}})
			} else {
				that.$element.addClassName('in').writeAttribute('aria-hidden', 'false').fire("bootstrap:shown");
			}

			that.enforceFocus()
		})
	}
	, hide: function (e) {

		var that = this

		var hideEvent = this.$element.fire('bootstrap:hide')

		if (!this.isShown || hideEvent.defaultPrevented) return

		this.isShown = false

		this.escape()

		if(BootStrap.handleeffects == 'css' && this.$element.hasClassName('fade')) {
			this.hideWithTransition()
		} else if(BootStrap.handleeffects == 'effect' && typeof Effect !== 'undefined' && typeof Effect.Fade !== 'undefined' && this.$element.hasClassName('fade')) {
			this.hideWithTransition()
		} else {
			this.hideModal()
			this.$element.setStyle({display: ''});
		}
	}
	, enforceFocus: function () {
		var that = this
		$(document).on('focus', function (e) {
			if (that.$element[0] !== e.target && !that.$element.has(e.target).length) {
				that.$element.focus()
			}
		})
	}

	, escape: function () {
		var that = this
		if (this.isShown && this.options.keyboard) {
			$(document).on('keyup', function (e) {
				e.which == Event.KEY_ESC && that.hide()
			})
		} else if (!this.isShown) {
			$(document).stopObserving('keyup')
		}
	}

	, hideWithTransition: function () {
		var that = this

		if(BootStrap.handleeffects == 'css') {
			this.$element.observe(BootStrap.transitionendevent,function() {
				this.setStyle({display:''});
				//this.removeClassName('in');
				this.hideModal();
				this.stopObserving(BootStrap.transitionendevent);
			});
		 
		} else {
			new Effect.Morph(this.$element,{duration: 0.30, style: 'top:-25%;', afterFinish: function(effect){
				effect.element.removeClassName('in').writeAttribute('aria-hidden', 'true');
				effect.element.setStyle({top: ''});
				effect.hideModal();
			}})
		}
	}

	, hideModal: function () {
		this.$element.hide()
		this.$element.removeClassName('in').writeAttribute('aria-hidden', 'true')
		this.backdrop(function(){
			this.removeBackdrop()
			this.$element.fire('bootstrap:hidden')
		}.bind(this))

	}
	, removeBackdrop: function () {
		this.$backdrop && this.$backdrop.remove()
		this.$backdrop = null
	}

	, backdrop: function (callback) {

		var that = this
		, animate = this.$element.hasClassName('fade') ? 'fade' : ''

		if (this.isShown && this.options.backdrop) {
			var doAnimate = (BootStrap.handleeffects == 'css' || (BootStrap.handleeffects == 'effect' && typeof Effect !== 'undefined' && typeof Effect.Fade !== 'undefined')) && animate

			this.$backdrop = new Element("div",{"class":"modal-backdrop "+animate})
			if(doAnimate && BootStrap.handleeffects == 'css') {
				this.$backdrop.observe(BootStrap.transitionendevent,function(){
					callback()
					this.stopObserving(BootStrap.transitionendevent)
				})
			} else if(doAnimate && BootStrap.handleeffects == 'effect') {
				this.$backdrop.setOpacity(0)
			}

			this.$backdrop.observe("click",function(){
				that.options.backdrop == 'static' ? '' : that.hide()
			})

			$$("body")[0].insert(this.$backdrop)

			if(doAnimate && BootStrap.handleeffects == 'effect') {
				new Effect.Appear(this.$backdrop,{from:0,to:0.80,duration:0.3,afterFinish:callback})
			} else {
				callback();
			}
			setTimeout(function(){
				$$('modal-backdrop').invoke('addClassName','in')
			},1);


		} else if (!this.isShown && this.$backdrop) {
			if(animate && BootStrap.handleeffects == 'css'){
				that.$backdrop.observe(BootStrap.transitionendevent,function(){
					callback()
				});
				setTimeout(function(){
					that.$backdrop.removeClassName('in')
				},1);
			} else if(animate && BootStrap.handleeffects == 'effect' && typeof Effect !== 'undefined' && typeof Effect.Fade !== 'undefined') {
				new Effect.Fade(that.$backdrop,{duration:0.3,from:that.$backdrop.getOpacity()*1,afterFinish:function(){
					that.$backdrop.removeClassName('in')
					callback()
				}})
			} else {
				that.$backdrop.removeClassName('in')
				callback()
			}

		} else if (callback) {
			callback()
		}
	}
});

/*domload*/
document.observe("dom:loaded", function(){
	
	$$('[data-toggle="modal"]').invoke('observe', 'click', function(e)
	{
		var element = this.dataset.target || (this.href && this.href.replace(/.*(?=#[^\s]+$)/,'').replace(/#/,''));
		var options = {};

		if ($(element) !== undefined)
		{
			element = $(element);
			if( !/#/.test(this.href) ) {
				options.remote = this.href;
			}

			$$('.modal-header h3').invoke('update', e.target.title);

			new BootStrap.Modal(element, options);
		}	e.stop();
	});	
});
/*
$(document).observe('dom:loaded', function(){

	$('ajax-upload').on('submit', function(e){

	  e.stop()
	  var form = e.target
	  var data = new FormData(form)
	  new Ajax.Request(form.action, {
		//Needed to work with this test server
		requestHeaders: {"X-Prototype-Version": null},
		contentType: null,
		method: form.method,
		postBody: data,
		onSuccess: function(response){
		  $('result').update(response.responseText)
		}
	  })
	})
  })*/
