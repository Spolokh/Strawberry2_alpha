
var Protip = Class.create({
	
	initialize: function(element, options){

		this.element =$(element);
		this.options = {
			maxWidth	: '',
			offsetX		: 15,
			offsetY		: 15,
			opacity		: .90,
			hideDuration: 0.2,
			appearDuration: 0.2
		};
		
		Object.extend(this.options, options || {});

		this.addObservers();
		this.setupProtip();
	},
	
	setupProtip: function() {
		
		this.content 		= this.element.readAttribute('title');
		this.element.title 	= '';
		this.element.descendants().each(function(el){
			if (el.readAttribute('alt')) {
				el.alt = '';
			}
		});
		
		if(!this.content){
			return;
		}
		
		this._protip = new Element('div', {'class':'protip'}).update(this.content);
		document.body.insert(this._protip.hide());
		
		this.protipWidth = (this.options.maxWidth != '' && this._protip.getWidth() > this.options.maxWidth) ? this.options.maxWidth : this._protip.getWidth();
		this._protip.setStyle({
			width: this.protipWidth + 'px'
		});
	},
	
	addObservers: function() {
		Event.observe(this.element, "mouseover", this.showProtip.bind(this));
   		Event.observe(this.element, "mouseout", this.hideProtip.bind(this));
    	Event.observe(this.element, "mousemove", this.moveProtip.bindAsEventListener(this));	
	},
	
	showProtip: function() {
		this._protip.currentEffect && this._protip.currentEffect.cancel();
		this._protip.currentEffect = new Effect.Appear(this._protip, {
			duration: this.options.appearDuration, 
			to: this.options.opacity
		});
	},
	
	hideProtip: function() {
		this._protip.currentEffect && this._protip.currentEffect.cancel();
      	this._protip.currentEffect = new Effect.Fade(this._protip, {duration: this.options.hideDuration});
	},
	
	moveProtip: function(event){
		this.mouseX = Event.pointerX(event);
		this.mouseY = Event.pointerY(event);	
		if((this.protipWidth+this.mouseX) >= (Element.getWidth(document.body) - this.options.offsetX)) {
			this.mouseX = (this.mouseX - this.protipWidth) - 2 * this.options.offsetX;
		}
		
		this._protip.setStyle({ 
			top:this.mouseY  + this.options.offsetY + "px", 
			left:this.mouseX + this.options.offsetX + "px" 
		});
	}
});
