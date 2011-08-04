/* mmTextareaTags */
var mmTextareaTags = new Class({
	// Implements: [Class.ToElement, Class.Occlude, Options, Events]
	Implements: [Class.Occlude, Options, Events]
	, options : {
		tagType : 'code'	// 'html', 'code'
		, autogrow: false
		, autogrowMinSize: 32
		, previewEl : false
		, previewHidden : true	// if true, preview is only shown and updated on preview mode active (button)
		, previewBtn : false
		, previewHover: false
		, previewSource: false
		, maxChars: false
		, showCharCount: false
		, charCountEl: false
		, charCountPrefix: ''
		, charCountSuffix: ''
	}
	, property: 'mmTextareaTags'
	, initialize: function(input, buttons, commands, options){
mm.log('initializing');		
		this.setOptions(options);
		this.tagType = this.options.tagType;
		this.element = document.id(input);
		if (this.occlude(this.element)) { return this.occluded; }
		if (this.options.autogrow) {
mm.log('should autogrow');			
			if (mmTextareaGrow) { 
				var extrah = this.element.getStyle('padding-top').toInt() + this.element.getStyle('padding-bottom').toInt();
				if (this.element.hasClass('bsB')) {
					extrah += 5;			
				}	
				
				new mmTextareaGrow(this.element, { /* resizeDuration:50, */  minSize:this.options.autogrowMinSize, extraHeight:extrah});	
			}
		}
		this.maxChars = this.options.maxChars;
		this.showCharCount = this.options.showCharCount;
		this.charCountEl = false;
		if (this.options.charCountEl) { this.charCountEl = document.id(this.options.charCountEl); }		
		this.commands = new Hash($extend(mmTextareaTags.commands, commands||{}));
		this.buttons = $$(buttons);
		this.buttons.each(function(button){
			button.addEvent('click', function(e) {
				e.stop();
				this.exec(button.get('rel'));
			}.bind(this));
		}.bind(this));
		document.id(this.element).addEvent('keydown', function(e){
			if (e.control||e.meta) {
				var key = this.shortCutToKey(e.key, e.shift);
				if (key) {
					e.stop();
					this.exec(key);
				}
			}
		}.bind(this));
		this.element.addEvents({
			'focus':this.focusElement.bindWithEvent(this)
			, 'blur':this.blurElement.bindWithEvent(this)
		});
		this.mode = 'edit'; // edit / preview
		this.lastValue = false;
		this.curValue = this.element.get('value');
		
		if (this.options.previewEl) {
			this.previewEl = document.id(this.options.previewEl);
			if (this.previewEl) { 
				this.previewEl.setStyles({'padding':this.element.getStyle('padding')});
			}
			this.previewBtn = document.id(this.options.previewBtn);
			this.previewHidden = this.options.previewHidden;
			this.previewHover = this.options.previewHover;
			this.previewSource = this.options.previewSource;
			if (!this.previewHidden) {
				this.changeEvent();
			}
			if (this.previewBtn) {
				this.previewBtn.setStyle('cursor','pointer');
				if (this.previewHover) {
					this.previewBtn.addEvents({
						'mouseenter': function(e) {			
							e.stop();
							this.showPreview();
						}.bind(this)
						, 'mouseleave': function(e) {			
							e.stop();
							this.hidePreview();
						}.bind(this)
					});					
				} else {
					this.previewBtn.addEvent('click', function(e) {			
						e.stop();
						if (this.mode == 'edit') {
							this.previewBtn.set('text', 'edit');
							this.showPreview();
						} else {
							this.previewBtn.set('text', 'show preview');							
							this.hidePreview();
						}
					}.bind(this));
				}
			}
			if (this.previewEl && this.previewHidden && this.previewBtn) {
				this.previewEl.setStyle('display','none');
			}
		}
		this.watchChange = false;	
		this.element.addEvents({
			'keydown':this.keydown.bindWithEvent(this)
			, 'keyup':this.keyup.bindWithEvent(this)
		});
	}
	, keydown: function(e) {
		if (this.maxChars) {		
			if (this.element.get('value').length > this.maxChars) {
				e.stop();
				this.checkMaxLength();
			}
		}		
	}
	, keyup: function(e) {
		if (this.maxChars) {		
			if (this.element.get('value').length > this.maxChars) {
				e.stop();
				this.checkMaxLength();
			}			
		}		
	}
	, checkMaxLength: function() {
		this.curValue = this.element.get('value').substr(0,this.maxChars);
		this.lastValue = this.curValue;
		this.element.set('value', this.curValue);		
	}
	, focusElement: function(e) {
mm.log('focus');		
		this.changeEvent.periodical(750, this);
	}
	, blurElement: function(e) {
mm.log('blur');		
		$clear(this.watchChange);
	}	
	, changeEvent: function() {
		this.curValue = this.element.get('value');
		if (this.curValue != this.lastValue) {
//mm.log('changeEvent changed');
			this.lastValue = this.curValue;
//mm.log('lengh: '+this.curValue.length);
			if (this.showCharCount) {
				var count = this.curValue.length;
				var countStr = this.options.charCountPrefix;
				countStr += ' '+count;
				if (this.maxChars) {
					countStr += ' / '+this.maxChars;
				}
				countStr += ' '+this.options.charCountSuffix;
mm.log('chars: '+countStr);		
				if (this.charCountEl) {
					this.charCountEl.set('html', countStr);
				}
			}
			if (this.previewEl && !this.previewHidden) {
				this.updatePreview(this.curValue);
			}
		}
	}
	, showPreview: function() {
		if (!this.previewEl) { return; }
		if (this.previewHidden) { 
			this.mode = 'preview';
			this.updatePreview(this.curValue);
			this.buttons.setStyle('display','none');
			this.element.setStyle('display','none');
			this.previewEl.setStyle('display', 'block'); 
			var overtextLabel = this.element.getParent().getElement('label[class~=overTxtLabel][for='+this.element.get('id')+']');
			if (overtextLabel) {
				overtextLabel.setStyle('display','none');
			}
		}		
	}
	, hidePreview: function() {
		if (!this.previewEl) { return; }
		if (this.previewHidden) { 
			this.mode = 'edit';			
			this.buttons.setStyle('display','inline-block');			
			this.element.setStyle('display','block');
			this.previewEl.setStyle('display', 'none'); 
			var overtextLabel = this.element.getParent().getElement('label[class~=overTxtLabel][for='+this.element.get('id')+']');
			if (overtextLabel) {
				//overtextLabel.setStyle('display','none');
				OverText.update();
			}			
		}		
	}
	, updatePreview: function(curValue) {
		if (!this.previewEl) { return; }		
		if (this.previewHidden) { this.previewEl.setStyle('display', 'block'); }
		if (this.tagType == 'code') {
/*
			var blockBreaks = ['h1','h2','h3','ul','ol','li'];
			blockBreaks.each(function(tag) {
//				var reg = "/\[\/"+tag+"\]\n|\[\/"+tag+"\]\r/gm";
				var reg = '/[/'+tag+']\n|[/'+tag+']\r/gm'.escapeRegExp();
mm.log('reg = '+reg);				
				curValue = curValue.replace(reg, '[/'+tag+']');				
			});
*/
			
			curValue = this.stripInproperNewlines(curValue);
			curValue = this.parseCodeToHtml(curValue);		
			//curValue = this.commands.get('bold').parseBack.run(curValue,this);
		}
		
		if (this.previewSource) {
			curValue += '<br /><br /><code style="font-size:.75em; font-style:normal;">'+mmTextareaTags.htmlspecialchars(curValue)+'</code>';
		}
		
		this.previewEl.set('html', curValue);
	}
	, stripInproperNewlines:function(curValue) {
		// newlines before and after block els:
		// curValue = curValue.replace(/\[\/h1\]\n|\[\/h1\]\r/gm, '[/h1]');
		curValue = curValue.replace(/\[\/h1\]\r\n|\[\/h1\]\r|\[\/h1\]\n/gm, '[/h1]');
		curValue = curValue.replace(/\[\/h2\]\r\n|\[\/h2\]\r|\[\/h2\]\n/gm, '[/h2]');			
		curValue = curValue.replace(/\[\/h3\]\r\n|\[\/h3\]\r|\[\/h3\]\n/gm, '[/h3]');						
		curValue = curValue.replace(/\[\/p\]\r\n|\[\/p\]\r|\[\/p\]\n/gm, '[/p]');		
		curValue = curValue.replace(/\[\/ul\]\r\n|\[\/ul\]\r|\[\/ul\]\n/gm, '[/ul]');
		curValue = curValue.replace(/\[\/ol\]\r\n|\[\/ol\]\r|\[\/ol\]\n/gm, '[/ol]');
		curValue = curValue.replace(/\[\/li\]\r\n|\[\/li\]\r|\[\/li\]\n/gm, '[/li]');
		curValue = curValue.replace(/\r\n\[li\]|\r\[li\]|\n\[\li\]/gm, '[li]');	
		curValue = curValue.replace(/\[\/quote\]\r\n|\[\/quote\]\r|\[\/quote\]\n/gm, '[/quote]');
		curValue = curValue.replace(/\[-\]\r\n|\[-\]\r|\[-\]\n/gm, '[-]');
		return curValue;		
	}
	, parseCodeToHtml:function(curValue) {
		// regular tabs & newlines:		
		curValue = curValue.replace(/\t/gm, '');
		curValue = curValue.replace(/\r\n|\r|\n/gm,'<br />');			
		this.commands.each(function(value, key) {
			if (value.parseBack) {
				curValue = this.commands.get(key).parseBack.run(curValue,this);
			}
		},this);		
		// regular string parsing for stuff:
		// urls without tags:
		curValue = curValue.replace(/[^=](http|https|ftp):\/\/{1}([a-zA-Z0-9\/%@?:#&+._=-]*)/gim, ' <a href=$1://$2>$1://$2</a>');
//		$patterns[] = '/(?<!href)([\w\.+#-]+)@([\w\.-]+\.\w{2,4})/im';
//		$replacements[] = '<a href=mailto:$1@$2>$1@$2</a>';		
		// email adresses without tags:
		curValue = curValue.replace(/[^=]([\w\.+#-]+)@([\w\.-]+\.\w{2,4})/gim, ' <a href=mailto:$1@$2>$1@$2</a>');
		return curValue;
	}
	, shortCutToKey: function(shortcut, shift){
		var returnKey = false;
		this.commands.each(function(value, key){
			var ch = (value.shortcut ? value.shortcut.toLowerCase() : value.shortcut);
			if (value.shortcut == shortcut || (shift && ch == shortcut)) returnKey = key;
		});
		return returnKey;
	}
	, addCommand: function(key, command, shortcut){
		this.commands.set(key, {
			command: command,
			shortcut: shortcut
		});
	}
	, addCommands: function(commands){
		this.commands.extend(commands);
	}
	, exec: function(key){
mm.log('exec key:'+key);		
		var currentScrollPos; 
		if (document.id(this.element).scrollTop || document.id(this.element).scrollLeft) {
			currentScrollPos = {
				scrollTop: document.id(this.element).getScroll().y,
				scrollLeft: document.id(this.element).getScroll().x
			};
		}
		if (this.commands.has(key)) this.commands.get(key).command.run(document.id(this.element), this);
		if (currentScrollPos) {
			document.id(this.element).set('scrollTop', currentScrollPos.getScroll().y);
			document.id(this.element).set('scrollLeft', currentScrollPos.getScroll().x);
		}
	}	
});

$extend(mmTextareaTags, {
	commands: {},
	addCommand: function(key, command, shortcut){
		mmTextareaTags.commands[key] = {
			command: command,
			shortcut: shortcut
		}
	},
	addCommands: function(commands){
		$extend(mmTextareaTags.commands, commands);
	}
});
mmTextareaTags.addCommands({
	header1: {
		shortcut: 'h',
		command: function(input){			
			switch(this.tagType) {
				case 'html' :
					input.insertAroundCursor({before:'<h1>',after:'</h1>'});
				break;
				case 'code' :
					input.insertAroundCursor({before:'\n[h1]',after:'[/h1]\n'});				
				break;
			}			
			// input.insertAroundCursor({before:'<strong>',after:'</strong>'});
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					// return input.replace(/\[h1\]/g, '<h1>').replace(/\[\/h1\]/g, '</h1>');
					return input.replace(/\[h1\](.*?)\[\/h1\]/gm, '<h1>$1</h1>');										
				break;
				default : return input;
			} 
		}
	},	
	header2: {
		shortcut: false,
		command: function(input){			
			switch(this.tagType) {
				case 'html' :
					input.insertAroundCursor({before:'<h2>',after:'</h2>'});
				break;
				case 'code' :
					input.insertAroundCursor({before:'\n[h2]',after:'[/h2]\n'});				
				break;
			}			
			// input.insertAroundCursor({before:'<strong>',after:'</strong>'});
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					// return input.replace(/\[h2\]/g, '<h2>').replace(/\[\/h2\]/g, '</h2>');
					return input.replace(/\[h2\](.*?)\[\/h2\]/gm, '<h2>$1</h2>');										
				break;
				default : return input;
			} 
		}		
	},	
	header3: {
		shortcut: false,
		command: function(input){			
			switch(this.tagType) {
				case 'html' :
					input.insertAroundCursor({before:'<h3>',after:'</h3>'});
				break;
				case 'code' :
					input.insertAroundCursor({before:'\n[h3]',after:'[/h3]\n'});				
				break;
			}			
			// input.insertAroundCursor({before:'<strong>',after:'</strong>'});
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					// return input.replace(/\[h3\]/g, '<h3>').replace(/\[\/h3\]/g, '</h3>');
					return input.replace(/\[h3\](.*?)\[\/h3\]/gm, '<h3>$1</h3>');					
				break;
				default : return input;
			} 
		}		
	},	
	bold: {
		shortcut: 'b',
		command: function(input){			
			switch(this.tagType) {
				case 'html' :
					input.insertAroundCursor({before:'<strong>',after:'</strong>'});
				break;
				case 'code' :
					input.insertAroundCursor({before:'[b]',after:'[/b]'});				
				break;
			}			
			// input.insertAroundCursor({before:'<strong>',after:'</strong>'});
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					//return input.replace(/\[b\]/g, '<strong>').replace(/\[\/b\]/g, '</strong>');
					return input.replace(/\[b\](.*?)\[\/b\]/gm, '<strong>$1</strong>');
				break;
				default : return input;
			} 
		}		
	},
	underline: {
		shortcut: 'u',
		command: function(input){
			switch(this.tagType) {
				case 'html' :
					input.insertAroundCursor({before:'<span style="text-decoration:underline">',after:'</span>'});
				break;
				case 'code' :
					input.insertAroundCursor({before:'[u]',after:'[/u]'});				
				break;
			}			
			//input.insertAroundCursor({before:'<span style="text-decoration:underline">',after:'</span>'});
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					// return input.replace(/\[u\]/g, '<span style="text-decoration:underline;">').replace(/\[\/u\]/g, '</span>');
					return input.replace(/\[u\](.*?)\[\/u\]/gm, '<span style="text-decoration:underline;">$1</span>');					
				break;
				default : return input;
			} 
		}		
	},
	strike: {
		shortcut: 'k',
		command: function(input){
			switch(this.tagType) {
				case 'html' :
					input.insertAroundCursor({before:'<strike>',after:'</strike>'});
				break;
				case 'code' :
					input.insertAroundCursor({before:'[s]',after:'[/s]'});					
				break;
			}			
		//	input.insertAroundCursor({before:'<strike>',after:'</strike>'});
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					// return input.replace(/\[s\]/g, '<strike>').replace(/\[\/s\]/g, '</strike>');
					return input.replace(/\[s\](.*?)\[\/s\]/gm, '<strike>$1</strike>');					
				break;
				default : return input;
			} 
		}		
	},
	italic: {
		shortcut: 'i',
		command: function(input){
			switch(this.tagType) {
				case 'html' :
					input.insertAroundCursor({before:'<em>',after:'</em>'});
				break;
				case 'code' :
					input.insertAroundCursor({before:'[i]',after:'[/i]'});
				break;
			}			
			//input.insertAroundCursor({before:'<em>',after:'</em>'});
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					// return input.replace(/\[i\]/g, '<em>').replace(/\[\/i\]/g, '</em>');
					return input.replace(/\[i\](.*?)\[\/i\]/gm, '<em>$1</em>');										
				break;
				default : return input;
			} 
		}		
	},	
	anchor: {
		shortcut: 'l',
		command: function(input){
			switch(this.tagType) {
				case 'html' :
					if (window.TagMaker){
						if (!this.linkBuilder) this.linkBuilder = new TagMaker.anchor();
						this.linkBuilder.prompt(input);
					} else {
						var href = window.prompt(mmTextareaTags.getMsg('linkURL'));
						var opts = {before: '<a href="'+href+'">', after:'</a>'};
						if (!input.getSelectedText()) opts.defaultMiddle = window.prompt(mmTextareaTags.getMsg('linkText'));
						input.insertAroundCursor(opts);
					}

				break;
				case 'code' :
					//input.insertAroundCursor({before:'[i]',after:'[/i]'});
					var href = window.prompt('Specify the hyperlink url:','http://');
					if ($defined(href) && href != '') {
						var opts = {before: '[url='+href+']', after:'[/url]'};
						if (!input.getSelectedText()) { opts.defaultMiddle = window.prompt('Specify the hyperlink text:'); }
						input.insertAroundCursor(opts);					
					}
				break;
			}			
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					//return input.replace(/\[a href=\]/g, '<a href=').replace(/\[\/a\]/g, '</a>');
					// return input.replace(/\[a\shref=(.*)\[\/a\]/gm, '<a href=$1</a>');		
					return input.replace(/\[url=(http|https|ftp):\/\/{1}([a-zA-Z0-9\/%@?:#&+._=-]*)\](.*?)\[\/url\]/gim, '<a href=$1://$2>$3</a>');													
				break;
				default : return input;
			} 
		}		
	},
	email: {
		shortcut: false
		, command: function(input) {
			switch(this.tagType) {
				case 'html' :
					var href = window.prompt(mmTextareaTags.getMsg('linkURL'));
					var opts = {before: '<a href="mailto:'+href+'">', after:'</a>'};
					if (!input.getSelectedText()) { opts.defaultMiddle = window.prompt(mmTextareaTags.getMsg('linkText')); }
					input.insertAroundCursor(opts);				
				break;
				case 'code' :
					var href = window.prompt('Specify the e-mail address:',input.getSelectedText());
					if ($defined(href) && href != '') {
						var opts = {before: '[email='+href+']', after:'[/email]'};
						if (!input.getSelectedText()) { opts.defaultMiddle = window.prompt('Specify the e-mail text:'); }
						input.insertAroundCursor(opts);					
					}				
				break;
			}
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					//return input.replace(/\[a href=\]/g, '<a href=').replace(/\[\/a\]/g, '</a>');
					// return input.replace(/\[a\shref=(.*)\[\/a\]/gm, '<a href=$1</a>');	
					//return input.replace(/(?:\b|\+)(?:mailto:)?([\w\.+#-]+)@([\w\.-]+\.\w{2,4})\b/gim, '<a href=mailto:$1>$1</a>');	
					return input.replace(/\[email=([\w\.+#-]+)@([\w\.-]+\.\w{2,4})\](.*?)\[\/email\]/gim, '<a href=mailto:$1@$2>$3</a>');
					//return input.replace(/\[email=mailto:{1}([a-zA-Z0-9\/%@?:#&+._=-]*)\](.*?)\[\/email\]/gim, '<a href=mailto:$1>$2</a>');													
				break;
				default : return input;
			}			
		}
	},
	hr: {
		shortcut: '-',
		command: function(input){
			//input.insertAtCursor('\n<hr/>\n');
			switch(this.tagType) {
				case 'html' :
					input.insertAtCursor('\n<hr/>\n');
				break;
				case 'code' :
					input.insertAtCursor('\n[-]\n');
				break;
			}			
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					return input.replace(/\[-\]/g, '<hr />');
				break;
				default : return input;
			} 
		}		
	},
	img: {
		shortcut: 'g',
		command: function(input){
			
			switch(this.tagType) {
				case 'html' :
					if (window.TagMaker) {
						if (!this.anchorBuilder) this.anchorBuilder = new TagMaker.image();
						this.anchorBuilder.prompt(input);
					} else {
						var href = window.prompt(mmTextareaTags.getMsg('imgURL'));
						var alt = window.prompt(mmTextareaTags.getMsg('imgAlt'));
						input.insertAtCursor('<img src="'+href+'" alt="'+alt.replace(/"/g,'')+'" />');
					}
				break;
				case 'code' :
					var href = window.prompt('Specify an image url:', 'http://');
					if ($defined(href) && href != '') {
//						var alt = window.prompt('Specify the image alt attribute:');
						input.insertAtCursor('[img='+href+' ]');
					}
				break;
			}			
			

		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
//					return input.replace(/\[img=\]/g, '<img src=').replace(/\[\/a\]/g, ' />');
					return input.replace(/\[img=(http|https|ftp):\/\/{1}([a-zA-Z0-9\/%@?:#&+._=-]*)\.(jpg|jpeg|png|gif|bmp|tif|tiff|ico)\s*?\]/gim, '<img src=$1://$2.$3 />');						
				break;
				default : return input;
			} 
		}		
	},
	stripTags: {
		shortcut: '\\',
		command: function(input){
			input.insertAtCursor(input.getSelectedText().stripTags());
		}
	},
	sup: {
		shortcut: false,
		command: function(input){			
			switch(this.tagType) {
				case 'html' :
					input.insertAroundCursor({before:'<sup>', after: '</sup>'});
				break;
				case 'code' :
					input.insertAroundCursor({before:'[sup]',after:'[/sup]'});				
				break;
			}			
			// input.insertAroundCursor({before:'<strong>',after:'</strong>'});
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					//return input.replace(/\[b\]/g, '<strong>').replace(/\[\/b\]/g, '</strong>');
					return input.replace(/\[sup\](.*?)\[\/sup\]/gm, '<sup>$1</sup>');
				break;
				default : return input;
			} 
		}		
	},
	sub: {
		shortcut: false,
		command: function(input){			
			switch(this.tagType) {
				case 'html' :
					input.insertAroundCursor({before:'<sub>', after: '</sub>'});
				break;
				case 'code' :
					input.insertAroundCursor({before:'[sub]',after:'[/sub]'});				
				break;
			}			
			// input.insertAroundCursor({before:'<strong>',after:'</strong>'});
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					//return input.replace(/\[b\]/g, '<strong>').replace(/\[\/b\]/g, '</strong>');
					return input.replace(/\[sub\](.*?)\[\/sub\]/gm, '<sub>$1</sub>');
				break;
				default : return input;
			} 
		}		
	},
	blockquote: {
		shortcut: false,
		command: function(input){			
			switch(this.tagType) {
				case 'html' :
					input.insertAroundCursor({before:'<blockquote>', after: '</blockquote>'});
				break;
				case 'code' :
					input.insertAroundCursor({before:'\n[quote]',after:'[/quote]\n'});				
				break;
			}			
			// input.insertAroundCursor({before:'<strong>',after:'</strong>'});
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					//return input.replace(/\[b\]/g, '<strong>').replace(/\[\/b\]/g, '</strong>');
					return input.replace(/\[quote\](.*?)\[\/quote\]/gm, '<blockquote>$1</blockquote>');
				break;
				default : return input;
			} 
		}		
	},
	paragraph: {
		shortcut: 'enter',
		command: function(input){			
			switch(this.tagType) {
				case 'html' :
					input.insertAroundCursor({before:'\n<p>', after: '</p>\n'});
				break;
				case 'code' :
					input.insertAroundCursor({before:'\n[p]',after:'[/p]\n'});				
				break;
			}			
			// input.insertAroundCursor({before:'<strong>',after:'</strong>'});
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					//return input.replace(/\[b\]/g, '<strong>').replace(/\[\/b\]/g, '</strong>');
					return input.replace(/\[p\](.*?)\[\/p\]/gm, '<p>$1</p>');
				break;
				default : return input;
			} 
		}		
	},
	unorderedlist: {
		shortcut: false,
		command: function(input){			
			switch(this.tagType) {
				case 'html' :
					input.insertAroundCursor({before:'<ul>\n	<li>',after:'</li>\n</ul>'});
				break;
				case 'code' :
					input.insertAroundCursor({before:'\n[ul]\n[li]',after:'[/li]\n[/ul]\n'});				
				break;
			}			
			// input.insertAroundCursor({before:'<strong>',after:'</strong>'});
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					//return input.replace(/\[b\]/g, '<strong>').replace(/\[\/b\]/g, '</strong>');
					input = input.replace(/\[li\](.*?)\[\/li\]/gm, '<li>$1</li>');	
					return input.replace(/\[ul\](.*?)\[\/ul\]/gm, '<ul>$1</ul>');
				break;
				default : return input;
			} 
		}		
	},
	orderedlist: {
		shortcut: false,
		command: function(input){			
			switch(this.tagType) {
				case 'html' :
					input.insertAroundCursor({before:'<ol>\n	<li>',after:'</li>\n</ol>'});
				break;
				case 'code' :
					input.insertAroundCursor({before:'\n[ol]\n[li]',after:'[/li]\n[/ol]\n'});				
				break;
			}			
			// input.insertAroundCursor({before:'<strong>',after:'</strong>'});
		}
		, parseBack: function(input) {
			switch(this.tagType) {
				case 'html' :
					return input;
				break;
				case 'code' :			
					//return input.replace(/\[b\]/g, '<strong>').replace(/\[\/b\]/g, '</strong>');
					input = input.replace(/\[li\](.*?)\[\/li\]/gm, '<li>$1</li>');	
					return input.replace(/\[ol\](.*?)\[\/ol\]/gm, '<ol>$1</ol>');
				break;
				default : return input;
			} 
		}		
	},
	clean: {
		shortcut: false,
		command: function(input){
			input.tidy();
		}
	},
	preview: {
		shortcut: false,
		command: function(input){
			
			switch(this.tagType) {
				case 'html' :
					try {
						if (!this.container){
							this.container = new Element('div', {
								styles: {
									border: '1px solid black',
									padding: 8,
									height: 300,
									overflow: 'auto'
								}
							});
							this.preview = new StickyWin.Modal({
								content: StickyWin.ui("preview", this.container, {
									width: 600,
									buttons: [{
										text: 'close',
										onClick: function(){
											this.container.empty();
										}.bind(this)
									}]
								}),
								showNow: false
							});
						}
						this.container.set('html', input.get('value'));
						this.preview.show();
					} catch(e){mm.log('you need StickyWin.Modal and StickyWin.ui')}			
				break;
				case 'code' :
					if (!this.previewContainer) {
						this.previewContainer = new Element('div', {'class':'mmTextareaTagPreviewContainer'}).inject(document.getElement('body'));
					}
					var value = input.get('value');
					value = value.replace(/\[b\]/g, '<b>').replace(/\[\/b\]/g, '</b>');
					this.previewContainer.set('html', value);
				break;
			}			
			

		}
	}
});
mmTextareaTags.getMsg = function(key, language){
	return MooTools.lang.get('mmTextareaTags', key, args);
};
mmTextareaTags.htmlspecialchars = function(string, quote_style, charset, double_encode) {
    // http://kevin.vanzonneveld.net
    // +   original by: Mirek Slugen
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Nathan
    // +   bugfixed by: Arno
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Ratheous
    // +      input by: Mailfaker (http://www.weedem.fr/)
    // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +      input by: felix
    // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
    // %        note 1: charset argument not supported
    // *     example 1: htmlspecialchars("<a href='test'>Test</a>", 'ENT_QUOTES');
    // *     returns 1: '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'
    // *     example 2: htmlspecialchars("ab\"c'd", ['ENT_NOQUOTES', 'ENT_QUOTES']);
    // *     returns 2: 'ab"c&#039;d'
    // *     example 3: htmlspecialchars("my "&entity;" is still here", null, null, false);
    // *     returns 3: 'my &quot;&entity;&quot; is still here'

    var optTemp = 0, i = 0, noquotes= false;
    if (typeof quote_style === 'undefined' || quote_style === null) {
        quote_style = 2;
    }
    string = string.toString();
    if (double_encode !== false) { // Put this first to avoid double-encoding
        string = string.replace(/&/g, '&amp;');
    }
    string = string.replace(/</g, '&lt;').replace(/>/g, '&gt;');

    var OPTS = {
        'ENT_NOQUOTES': 0,
        'ENT_HTML_QUOTE_SINGLE' : 1,
        'ENT_HTML_QUOTE_DOUBLE' : 2,
        'ENT_COMPAT': 2,
        'ENT_QUOTES': 3,
        'ENT_IGNORE' : 4
    };
    if (quote_style === 0) {
        noquotes = true;
    }
    if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
        quote_style = [].concat(quote_style);
        for (i=0; i < quote_style.length; i++) {
            // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
            if (OPTS[quote_style[i]] === 0) {
                noquotes = true;
            }
            else if (OPTS[quote_style[i]]) {
                optTemp = optTemp | OPTS[quote_style[i]];
            }
        }
        quote_style = optTemp;
    }
    if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
        string = string.replace(/'/g, '&#039;');
    }
    if (!noquotes) {
        string = string.replace(/"/g, '&quot;');
    }

    return string;
}; 
