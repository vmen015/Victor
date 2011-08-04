Element.implement({
	addClasses: function(classes) {
// mm.log(classes);		
		classes.each(function(cl) {
			this.addClass(cl);
		},this);
		return this;		
	},
	removeClasses: function(classes) {
		classes.each(function(cl) {
			this.removeClass(cl);
		},this);		
		return this;		
	}
});

mm.random = function(min, max){
	if (min != undefined && max != undefined) {
		return Math.floor(Math.random() * (max - min + 1) + min);
	} else {
		if (min != undefined) {
			return Math.floor(Math.random() * (min - 0 + 1) + 0);
		} else {
			return Math.random();
		}
	}
};