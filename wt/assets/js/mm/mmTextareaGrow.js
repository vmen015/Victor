/*
UvumiTools TextArea v1.1.0 http://tools.uvumi.com/textarea.html

Copyright (c) 2008 Uvumi LLC

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
*/

var mmTextareaGrow = new Class({

	Implements:Options,

	options:{
		resizeDuration:20,		//animation duration of progress bar and resizing, in milliseconds		
		minSize: false,			//minimum height in pixels you can reduce the textarea to. If set to false, the default value, the original textarea's height will be used as a minimum				
		catchTab: false		//if the textarea should override the tab default event and insert a tab in the text. Default is true, but if you're not going to support it on the back-end, you should disable it		
		, extraHeight: 0	// extraHeight to auto include, use padding-top + padding-bottom
	},

	initialize: function(el, options){
		this.setOptions(options);
		//each textarea will have its own elements, all storred in arrays
		this.dummy = false;
		this.textarea = document.id(el); //the textarea element.		
		//window.addEvent('domready',this.domReady.bind(this));
		this.setupElements();
	},
	setupElements:function() {
		this.tEffect = new Fx.Tween(this.textarea,{
			duration:this.options.resizeDuration, transition:'expo:in:out', 
			link:'cancel'
		});		
		this.textarea.setStyle('overflow','hidden');
		//if minimum size option is false, we use the original size as minimum.
		if(!this.options.minSize){
			this.options.minSize = this.textarea.getSize().y;
		}		
		
		this.dummy = this.textarea.clone().setStyles({
				'width':this.textarea.getStyle('width').toInt(),
				'position':'absolute',
				'top':0,
				'height':this.options.minSize,
				'left':-3000
				, 'visibility':'hidden'
		}).store('height',0); //.inject($(document.body));
		this.dummy.erase('name').inject(this.textarea, 'after');
		this.textarea.addEvents({
			'keydown':this.onKeyPress.bindWithEvent(this,[this.options.catchTab]), // here and like on all the other events, we must use bindWithEvent because we pass an additionnal parameter beside the event object
			'keyup':this.onKeyPress.bindWithEvent(this),
			'focus':this.startObserver.bind(this),
			'blur':this.stopObserver.bind(this)
		});		
		var value = this.textarea.get('value');
		this.previousLength = value.length;		
		this.dummy.set('value',value);
		this.textarea.setStyle('height',0);				
		var height = (this.dummy.getScrollSize().y>this.options.minSize?this.dummy.getScrollSize().y:this.options.minSize);
// dbug.log('TEXTAREA HEIGHT: '+height);		
		height += this.options.extraHeight;
// dbug.log('TEXTAREA HEIGHT: '+height);		
		if(this.dummy.retrieve('height')!=height){
			this.dummy.store('height',height);
			this.textarea.setStyle('height',height);
// dbug.log('heights: '+height);			
		}		
		this.update();
	},
		
	onKeyPress: function(event,tab) {
		if(tab && event.key == "tab"){
			event.preventDefault();
			this.insertTab();
		}
		if(!event.shift && !event.control && !event.alt && !event.meta){
			this.update();
		}
		this.startObserver();
	},
	
	startObserver:function(){
		$clear(this.observer);
		this.observer = this.observe.periodical(500,this);
	},
	
	stopObserver:function(){
		$clear(this.observer);
	},
	
	observe:function(){
		if(this.textarea.get('value').length != this.previousLength){
			this.previousLength = this.textarea.get('value').length;
			this.update();
		}
	},

	
	update:function(){
		var value = this.textarea.get('value');
		this.previousLength = value.length;
		this.updateHeight(value);
	},
	
	updateHeight: function(value){
		this.dummy.set('value',value);
		var height = (this.dummy.getScrollSize().y > this.options.minSize) ? this.dummy.getScrollSize().y : this.options.minSize ;
		height += this.options.extraHeight;
		if(this.dummy.retrieve('height')!=height){
			this.dummy.store('height',height);
			this.tEffect.start('height',height);
		}
	},
	
	insertTab: function(i){
		if(Browser.Engine.trident) {
			var range = document.selection.createRange();
			range.text = "\t";
		}else{
			var start = this.textarea.selectionStart;
			var end = this.textarea.selectionEnd;
			var value = this.textarea.get('value');
			this.textarea.set('value', value.substring(0, start) + "\t" + value.substring(end, value.length));
			start++;
			this.textarea.setSelectionRange(start, start);
		}
	}
});