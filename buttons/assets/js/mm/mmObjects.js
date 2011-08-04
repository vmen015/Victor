/* mmObjects */
var mmObjects = new Class({
	Implements : [Options, Events]
	, options : {	
		
	}
	, initialize: function(options) {
		this.setOptions(options);
		this.scan(); // initial scan for document.body
	}
	, domReady:function() {

	}
	, scan:function(container) {
		if (container == undefined) {
			this.container = document.id(document.body);			
		} else {
			this.container = document.id(container);
		}

		this.setupElements();
		
	}
	, setupElements:function() {
		// disabled links
		var disabledLinks = this.container.getElements('a.disabled');
		if (disabledLinks.length > 0) {
			disabledLinks.each(function(link) {
				link.addEvent('click', function(e) {
					e.stop();
				});
			},this);
		}
		
		// disabled btn links		
		var mmBtns = this.container.getElements('a.mmBtn, a.mmBtnPill');
		if (mmBtns.length > 0) {
			mmBtns.each(function(btn) {
				btn.addEvent('click', function(e) {
					if (btn.hasClass('btn-disabled')) {
						e.stop();
					} else {
						btn.blur();
					}
				});
			});		
		}
		
		// styled select elements
		var mmSelects = this.container.getElements('select.mmSelect');
		if (mmSelects.length > 0) {
			mmSelects.each(function(el) {
				new mmSelect(el,{

				});
			});
		}		
		
		// input placeholders
		var mmPlaceholders = this.container.getElements('input.mmPlaceholder, textarea.mmPlaceholder');
		if (mmPlaceholders.length > 0) {
			// new mmPlaceholder({elements:mmPlaceholders});
			mmPlaceholders.each(function(input){
				new mmPlaceholder(input, {positionOptions:{offset:{x:5, y:5}}});
			});			
		}
		
		// automatic form validators
		var mmFormValidates = this.container.getElements('form.mmFormValidate');
		if (mmFormValidates.length > 0) {
			mmFormValidates.each(function(f) {
				fV = new Form.Validator(f, {
					evaluateFieldsOnBlur:false, evaluateFieldsOnChange:false, serial:false
				});
				f.store('formValidator', fV);
				f.store('isValidated', false);				
				f.addEvent('submit', function(e) {
					e.stop();					
				});
				var bS = f.getElement('.mmFormSubmit');
				if (bS) {
					bS.addEvent('click', function(e) {
						e.preventDefault();						

						fV.options.evaluateFieldsOnBlur = true;
						fV.options.evaluateFieldsOnChange = true;					
						fV.watchFields(fV.getFields());		
						if (fV.validate()) {
							if (!f.hasClass('mmFormAjax') && !f.hasClass('mmFormRequest')) {
								f.submit();							
							} else {
								f.store('isValidated', true);
							}
						} else {
							f.store('isValidated', false);
						}
					});
				}
			});
			// this.request2FormValidator = new Form.Validator(this.request2Form, {
			// 	evaluateFieldsOnBlur:false, evaluateFieldsOnChange:false, serial:false
			// });			
		}
		
		// positioned tips on elements
		var mmTips = this.container.getElements('.mmTip');
		if (mmTips.length > 0) {
	        mmTips.each(function(el) {
	            var tag = el.get('tag');
	// console.log(tag);            
	            var pos = el.get('data-tip-position');
	            var edge = el.get('data-tip-edge');            
	            var autohide = el.get('data-tip-autohide');
	            var delay = el.get('data-tip-delay');
				var arrow = el.get('data-tip-arrow');
				var offset = el.get('data-tip-offset');

	            if (!pos) { pos = 'top'; }
	            if (!edge) { edge = 'bottom'; }
	            if (!autohide) { autohide = true; } else { autohide = autohide.toInt(); }
	            if (!delay) { delay = 400; } else { delay = delay.toInt(); }
				if (!arrow) { arrow = true; } else { arrow = arrow.toInt(); }
				if (!offset) {offset = 12; } else { offset = offset.toInt(); }

	            var tipEvent = 'mouseenter';
	            var tip = ToolTip.instance(el, {
	                   position: {
	                       position: pos, edge: edge, offset: {x:0, y: 0}                           
	                   }
	           		, autohide: autohide
	           		, offset: offset
	           		, hideDelay: delay                       
	               }, el.get('data-tip'));
	            if (tag != 'input' && tag != 'textarea') {
	               el.addEvent(tipEvent, function(e) {
	                   tip.show();
	               }); 
	           } else {
	               el.addEvents({
	                   'focus' : function(e) {
	                       tip.show();
	                   }
	                   , blur : function(e) {
	                       tip.hide();
	                   }
	               });
	           }
				if (!arrow) {
					tip.addEvent('show', function() {
						tip.arrow.hide();
					});
				}
	        });			
		}
		
		var mmTextareas = this.container.getElements('.mmTextarea');
		if (mmTextareas.length > 0) {
			mmTextareas.each(function(el) {
				var extrah = el.getStyle('padding-top').toInt() + el.getStyle('padding-bottom').toInt();
				var minsize = el.get('data-textarea-minsize');
				if (!minsize) { minsize = 40; } else { minsize = minsize.toInt(); }
				if (el.hasClass('bsB')) {
					extrah += 5;			
				}	
				
				new mmTextareaGrow(el, { /* resizeDuration:50, */  minSize:minsize, extraHeight:extrah});				
			});
		}
		
		if (Browser.ie) {
			this.setupIEElments();
		}
		
		
	}
	, setupIEElments: function() {
		var ie_notice = this.container.getElement('#ie_notice');
		if (ie_notice) {
			ie_notice.show();
		}		
	}	
	
	
});