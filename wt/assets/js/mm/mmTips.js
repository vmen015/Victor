(function(){

var read = function(option, element){
	return (option) ? ($type(option) == 'function' ? option(element) : element.get(option)) : '';
};

this.mmTips = new Class({

	Implements: [Events, Options],

	options: {
		/*
		onAttach: $empty(element),
		onDetach: $empty(element),
		*/
		onShow: function(){
			this.tip.setStyle('display', 'block');
		},
		onHide: function(){
			this.tip.setStyle('display', 'none');
		},
		title: 'title',
		text: function(element){
			return element.get('rel') || element.get('href');
		},
		showDelay: 100,
		hideDelay: 100,
		showEvent: 'enter',
		hideEvent: 'leave',
		tweenProps: {duration:100,transition:'sine:in:out'},
		className: 'mmTipContainer',
		offset: {x: 16, y: 16},		
		fixed: false,
		fixedDirection: 'top', 	// 'top' / 'bottom' 
		fixedPointer: 'bottom'	// 'bottom'
	},

	initialize: function(){
		var params = Array.link(arguments, {options: Object.type, elements: $defined});
		this.setOptions(params.options);
		document.id(this);
		if (params.elements) this.attach(params.elements);
	},

	toElement: function(){
		if (this.tip) return this.tip;
//dbug.log('tween:'+this.options.tween);		
		this.container = new Element('div', {'class': 'tip'});
		this.tip = new Element('div', {
			'class': this.options.className,
			styles: {
				position: 'absolute',
				top: 0,
				left: 0
			}
			, tween: this.options.tweenProps
		}).adopt(
			new Element('div', {'class': 'tip-top'}),
			this.container,
			new Element('div', {'class': 'tip-bottom'})
		).inject(document.body);
		if (this.options.fixed) {
			if (this.options.fixedPointer == 'bottom') {
				this.bottomPointerLeft = new Element('div', {'class':'tip-bottom-pointer-left'}).inject(this.tip,'bottom');
				this.bottomPointerRight = new Element('div', {'class':'tip-bottom-pointer-right'}).inject(this.tip,'bottom');				
			} else {
				new Element('div', {'class':'tip-top-pointer-left'}).inject(this.tip,'bottom');
				new Element('div', {'class':'tip-top-pointer-right'}).inject(this.tip,'bottom');				
			}
		}		
		return this.tip;
	},

	attach: function(elements){
		$$(elements).each(function(element){
			var title = read(this.options.title, element),
				text = read(this.options.text, element);
			
			// element.erase('title');
			element.erase('data-tip');
			
// dbug.log('tip:title: '+element.retrieve('tip:title'));			
			element.store('tip:native', title).retrieve('tip:title', title);
			element.retrieve('tip:text', text);
// dbug.log('native tip:'+element.retrieve('tip:native'));			
			this.fireEvent('attach', [element]);
			
			var events = [this.options.showEvent, this.options.hideEvent];
			if (!this.options.fixed) events.push('move');
			
			events.each(function(value){
				var event = element.retrieve('tip:' + value);
				if (!event) event = this['element' + value.capitalize()].bindWithEvent(this, element);
				
				element.store('tip:' + value, event);
				var mouseevents = ['enter', 'leave', 'move', 'over', 'out']; // separate mouseevents from blur / focus type events
				element.addEvent(mouseevents.contains(value) ? 'mouse' + value : value, event);
			}, this);
		}, this);
		
		return this;
	},

	detach: function(elements){
		$$(elements).each(function(element){
			['enter', 'leave', 'move', 'focus', 'blur'].each(function(value){
				element.removeEvent('mouse' + value, element.retrieve('tip:' + value)).eliminate('tip:' + value);
			});
			
			this.fireEvent('detach', [element]);
			
			if (this.options.title == 'title'){ // This is necessary to check if we can revert the title
				var original = element.retrieve('tip:native');
				// if (original) element.set('title', original);
				if (original) element.set('data-tip', original);
			}
		}, this);
		
		return this;
	},

	elementEnter: function(event, element){
		this.container.empty();
		this.tip.setStyle('visibility','hidden');
		['title', 'text'].each(function(value){
			var content = element.retrieve('tip:' + value);
			if (content) this.fill(new Element('div', {'class': 'tip-' + value}).inject(this.container), content);
		}, this);
		
		$clear(this.timer);
		this.timer = this.show.delay(this.options.showDelay, this, [element,event]);
		// this.position((this.options.fixed) ? {page: element.getPosition()} : event);
	},

	elementLeave: function(event, element){
// dbug.log('mmTips elementLeave');		
		$clear(this.timer);
		this.timer = this.hide.delay(this.options.hideDelay, this, element);		
//		this.hide(element);
//		this.fireForParent(event, element);
	},
	elementFocus: function(event, element) {
// dbug.log('elementFocus');
		this.container.empty();
		this.tip.setStyle('visibility','hidden');
		['title', 'text'].each(function(value){
			var content = element.retrieve('tip:' + value);
			if (content) this.fill(new Element('div', {'class': 'tip-' + value}).inject(this.container), content);
		}, this);
		
		$clear(this.timer);
		this.timer = this.show.delay(this.options.showDelay, this, [element,event]);	
		// var e = (this.options.fixed) ? {page: element.getPosition()} : event ;
		// this.position(e, element);		
	},
	elementBlur: function(event, element) {
		$clear(this.timer);
		this.timer = this.hide.delay(this.options.hideDelay, this, element);		
	},	

	fireForParent: function(event, element){
		element = element.getParent();
		if (!element || element == document.body) return;
		if (element.retrieve('tip:enter')) element.fireEvent('mouseenter', event);
		else this.fireForParent(event, element);
	},

	elementMove: function(event, element){
		this.position(event);
	},

	position: function(event, element){
// dbug.log('position');		
		this.tip.setStyle('visibility','visible').setStyle('display','block'); // this makes sure the offsetHeight does work on first time
		var size = window.getSize(), scroll = window.getScroll(),
			tip = {x: this.tip.offsetWidth, y: this.tip.offsetHeight},
			props = {x: 'left', y: 'top'},
			obj = {};
		var extraoffset = 0;
// dbug.log('offsetH = '+this.tip.offsetHeight);			
		if (this.options.fixed) { 
			if (this.options.fixedDirection == 'top') {				
				extraoffset = -tip.y;
// dbug.log('fixed + top ='+extraoffset);				
			} else {
				extraoffset = tip.y;
			}
		} 
		var extra = 0;
		for (var z in props){			
			extra = (z=='x') ? 0 : extraoffset ;
			obj[props[z]] = event.page[z] + this.options.offset[z] + extra;
			if ((obj[props[z]] + tip[z] - scroll[z]) > size[z]) { 
				// obj[props[z]] = event.page[z] + this.options.offset[z] + extra + element.getSize().x - tip[z];
				// this.bottomPointerLeft.setStyles({'left':null,'right':20});
			}
		}
// dbug.log(obj);		
		this.tip.setStyles(obj);
	},

	fill: function(element, contents){
		if(typeof contents == 'string') element.set('html', contents);
		else element.adopt(contents);
	},

	show: function(element, event){
		this.position((this.options.fixed) ? {page: element.getPosition()} : event);		
		this.tip.setStyle('visibility','visible');	
		this.fireEvent('show', [this.tip, element]);
	},

	hide: function(element){		
		this.fireEvent('hide', [this.tip, element]);
	}

});

})();


/* mmTips */
/*
var mmTips = new Class ({
	
	Extends: Tips,
	
	attach: function(elements){
		$$(elements).each(function(element){
			var title = read(this.options.title, element),
				text = read(this.options.text, element);
			
			element.erase('title').store('tip:native', title).retrieve('tip:title', title);
			element.retrieve('tip:text', text);
			this.fireEvent('attach', [element]);
			
			var events = ['enter', 'leave'];
			if (!this.options.fixed) events.push('move');
			
			events.each(function(value){
				var event = element.retrieve('tip:' + value);
				if (!event) event = this['element' + value.capitalize()].bindWithEvent(this, element);
				
				element.store('tip:' + value, event).addEvent('mouse' + value, event);
			}, this);
			element.store('mmTip', this);			
		}, this);
		
		return this;
	},	

	detach: function(elements){
		$$(elements).each(function(element){
			['enter', 'leave', 'move'].each(function(value){
				element.removeEvent('mouse' + value, element.retrieve('tip:' + value)).eliminate('tip:' + value);
			});
			
			this.fireEvent('detach', [element]);
			
			if (this.options.title == 'title'){ // This is necessary to check if we can revert the title
				var original = element.retrieve('tip:native');
				if (original) element.set('title', original);
			}
			element.store('mmTip', false);				
		}, this);
		
		return this;
	}	
	
});
*/