/*
	initial javascript start of application
*/
window.addEvent('domready', function() {
	if (mm.runtime != undefined && mm.runtime.developer != undefined && mm.runtime.developer) {
// mm.log('mm.runtime : ');	
// mm.log(mm.runtime);
	}
	mm.main.init();
	mm.app = new App();
});