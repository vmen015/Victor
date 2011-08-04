(function(){

this.mm = {
	version:'1.0'
};

var global = this;
mm.log = function(){
	if (global.console && console.log){
		try {
			console.log.apply(console, arguments);
		} catch(e) {
			console.log(Array.slice(arguments));
		}
	}
};

mm.info = mm.log;

})();