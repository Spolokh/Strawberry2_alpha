/*  Starry Set Form Widget (c) 2008 Chris Iufer <chris@duarte.com> Starry is freely distributable under the terms of an MIT-style license. See the Duarte Design web site: http://www.duarte.com/*/
var debug = false;
var StarryDefaults = {
width: 14, 
height: 14,
margin: '0 1px', 
startAt: 0, 
maxLength: 5, 
multiplier: 1, 
align: 'left', 
showNull: false, 
feedback: true, 
sprite: SERVER+'/themes/'+THEMES+'/images/stars.png'
};

var Starry = Class.create(); // The Starry Class new Starry('id_of_element'[, {options}]); This is the main starry widget. Create new widgets after window load
Starry.prototype = {
	initialize: function(element) { //console.log($(element));
		document.write('<div id="'+element+'"></div>');
		while($(element) == null){} //timeout
		this.element = $(element).addClassName('starry');				
		this.options = {}; // get our defaults
		Object.extend(this.options, StarryDefaults);
		Object.extend(this.options, arguments[1] || {});		
		this.name = this.options.name || 'starry' + id.next();
		this.element.style.height = this.options.height + 'px';	
        //this.element.style.margin = this.options.margin;	
		if(debug) console.log(this.options.showNull);
		this.children = new Array(this.options.maxLength + 1); // lets build our array with an extra one for null
		if(debug) console.log('children length '+ this.children.length);		
		this.hidden   = new Element('input', {type:'hidden', name: this.name});
		this.element.appendChild(this.hidden);				
		for(i = 0; i < this.children.length; i++){ // build out each child
			  this.children[i] = new SingleStar(this, i);
			  this.element.appendChild(this.children[i].element);
		}
		if(this.options.feedback) {
		      this.feedback = new Element('div', {className:'feedback'}).setStyle({float: this.options.align});
			  this.element.appendChild(this.feedback);
		}	  // startup
		this.selected = this.options.startAt;
		this.reset(this.selected);	
	},
	
	set: function(index) {
		if(debug) console.log('set index '+ index);
		for(var i = 1; i < this.children.length; i++) // set the child
			this.children[i].element.style.backgroundPosition = (i <= index) ? '0 -' + this.options.height * 2 + 'px' : '0 0';
		if(this.options.feedback) 
			this.feedback.update(this.children[index].value);
		this.selected = index;	// set the form value
		this.hidden.value = this.children[index].value;
		if(debug) console.log('set value ' + this.hidden.value);
	},
	show: function(index){
		if(debug) console.log('show index '+ index); // show the child
		for(var i = 1; i < this.children.length; i++)
			this.children[i].element.style.backgroundPosition = (i <= index) ? '0 -' + this.options.height + 'px' : '0 0';
		if(this.options.feedback)
			this.feedback.update(this.children[index].value);	
	},
	reset: function(){this.set(this.selected);},
	clear: function(){
		for(var i=1; i < this.children.length; i++) this.children[i].element.style.backgroundPosition = '0 0';
	}
};

// Class: SingleStar(parent_object, index of that parent's children) Not to be called directly, inherits its options from a Starry object
var SingleStar = Class.create();
SingleStar.prototype = {
	initialize: function(parent, index) {
		this.parent = parent;
		this.index  = index;
		if(debug) console.log('Creating star at index '+ this.index);
		this.value = this.index * this.parent.options.multiplier;
		if(debug) console.log('value '+ this.value);
		this.element = new Element('div').addClassName('standard_star');
		this.element.style.cssFloat = this.parent.options.align;
		this.element.style.margin = this.parent.options.margin;
		this.element.style.width    = this.parent.options.width + 'px';
		this.element.style.height   = this.parent.options.height + 'px';
		this.element.style.backgroundImage = 'url(' + this.parent.options.sprite + ')';
		this.element.style.backgroundPosition = (this.index == 0) ? '0 -' + this.parent.options.height * 3  +'px' : '0 0';
		if(!this.parent.options.showNull && this.index == 0) this.element.hide();
		
		//this.element.observe('click', this.parent.set(this.index).bind(this));

		this.element.onclick = function(){ 
		         this.parent.set(this.index);
		}.bind(this);

		
		this.element.onmouseover = function() { this.parent.show(this.index); }.bind(this);
		this.element.onmouseout = this.parent.reset.bind(this.parent);
		if(debug) console.log('set onclick handler');
	}
};

var id = {start: 0, prev: 0, next: function() {return this.start + this.prev++;}}; // this function manages an auto_increment for id's