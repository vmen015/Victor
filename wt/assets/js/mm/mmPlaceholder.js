/*
	mmPlaceholder extends OverText to use placeholder attribute in all browsers and to correctly use validation and styling of placeholders(overtexts) etc
*/

var mmPlaceholder = new Class({
	Extends: OverText
	, options: {
	/*
		textOverride: null,
		onFocus: function(){},
		onTextHide: function(textEl, inputEl){},
		onTextShow: function(textEl, inputEl){}, */
		element: 'label',
		positionOptions: {
			position: 'upperLeft',
			edge: 'upperLeft',
			offset: {
				x: 4,
				y: 2
			}
		},
		poll: false,
		pollInterval: 250,
		wrap: false		
	}
	, initialize: function(element, options) {
		// initialising options from element attributes
		var t = element.get('placeholder');
		if (t) {
			element.erase('placeholder'); // to avoid doubles when placeholder attribute is supported
			options.textOverride = t;
		} else {
			t = element.get('data-placeholder');
			if (t) {
				options.textOverride = t;
			}
		}
		var x = element.get('data-placeholder_x');
		if (x) {
			options.positionOptions.offset.x = x.toInt();
		} else {
			options.positionOptions.offset.x = element.getStyle('padding-left').toInt();
		}
		var y = element.get('data-placeholder_y');
		if (y) {
			options.positionOptions.offset.y = y.toInt();
		} else {
			options.positionOptions.offset.y = element.getStyle('padding-top').toInt();
		}		
		var position = element.get('data-placeholder_position');
		if (position) {
			options.positionOptions.position = position;
		}		
		var edge = element.get('data-placeholder_edge');
		if (edge) {
			options.positionOptions.edge = edge;
		}		
		var wrap = element.get('data-placeholder_wrap');
		if (wrap) { 
			options.wrap = wrap.toInt();
			// alert(options.wrap);
		}
		var placeholder_class = element.get('data-placeholder_class');
		if (placeholder_class) {
			options.className = placeholder_class;
		}
		options = Object.merge(this.options, options);
	
		this.parent.apply(this, [element, options]);
	}
	, enable: function() {
		this.parent.apply(this);
		this.text.setStyle('font-size', this.element.getStyle('font-size'));
		this.text.setStyle('font-family', this.element.getStyle('font-family'));
		if (this.options.className) {
			this.text.addClasses(this.options.className.split(' '));
		}
		return this;
	}
});

