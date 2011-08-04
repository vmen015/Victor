var App = new Class({
	Implements: [Options, Events]
	, options: {
		
	}
	, initialize:function(options) {
		this.setOptions(options);
// mm.log('App init');		
// mm.log(document.location.protocol);
		this.controller = false;
		this.page = false;
		this.setupEvents();
	}
	, setupEvents:function() {
		window.addEvent('domready', function(e) {
mm.log(mm.runtime);			
			if (mm.runtime) {
// mm.log(mm.runtime);
				if (mm.runtime.controller != undefined) {
					this.controller = mm.runtime.controller;
					if (mm.runtime.page != undefined) {
						this.page = mm.runtime.page;
					}
					this.switchController();
				}
			}
		}.bind(this));
	}
	, switchController:function(){ 
		switch(this.controller) {
			case 'contact' : 
				this.initContact();
			break;
			case 'account' : 
				this.initAccount();
			break;
		}
	}
	, initAccount: function() {
mm.log('initAccount');		
		if (this.page != undefined && this.page == 'songs') {
	       var songsContainer = document.id('songsContainer');
	       if (songsContainer) {
	           var songs = songsContainer.getElements('.item');
	           if (songs.length > 1) {
	               var songsSortables = new Sortables('songsContainer', {
	                   revert: { duration: 350, transition: 'pow:out' }
	                   , clone: true
						, opacity: 0.2
	                   , onComplete:function() {
	                       var new_order = [];
	                        songsContainer.getElements('.item').each(function(song){ 
	// console.log(song.get('data-song_id'));    
	                            new_order.push(song.get('data-song_id').toInt());
	                        });
	console.log(new_order);
							var url = mm.runtime.site_url+'ajax/songs_order';
console.log(url);							
	                        new Request.JSON({
	                            url: url
								, method: 'post'
								, data: {'order': new_order}
								, onSuccess: function(rJson, rText) {
console.log(rJson);									
								}
	                        }).send();
	                   }
	               });               
	           }
	       }			
		}
	}
	, initContact: function() {
		var contact_form = document.id('contact_form');
		if (contact_form) {
			var contact_form_submit = document.id('contact_form_submit');
			contact_form.addEvent('submit', function(e) {
				e.stop();
			});
			if (contact_form_submit) {
				var update = contact_form.getParent();
				var contactFormRequest = new Form.Request(contact_form, update, {
					url: contact_form.get('action')
				});
				contact_form_submit.addEvent('click', function(e) {
					var isValidated = true;
					if (contact_form.hasClass('mmFormValidate')) {
						if (!contact_form.retrieve('isValidated',false)) {
							isValidated = false;
						}
					}
					if (isValidated) {
						contactFormRequest.send();
					}
				});
			}
		}		
	}
});

// window.addEvent('domready', function(){
// 
// });