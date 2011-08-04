/*
	mm.main
	main singleton object
*/

mm.main = {
	myvar: 'a string property of the main object' 
	, options: {
		tooltipDelays: {show:1,hide:1} 
		, tooltipTweenProps: {duration:750, transition:'expo:in:out'}
		, tooltipInfoTweenProps: {duration:250, transition:'expo:in:out'}
	}
	, init: function() {
		this.bound = this.bound||{};
		this.unloading = false;
		this.setupDefaultObjects();
		this.setupDefaultEvents();
		this.route();
	}
	, route:function() {
		// mmMain specific routing stuff
	}
	, setupDefaultObjects: function() {
		this.setupPublicObjects();		
		// this.setupCoookie();	
	}
	, setupDefaultEvents: function() {
		
	}
	, setupPublicObjects:function() {
		// this.setupTips();
		// this.setupNotices();		// later

		// finally mmObjects and default objects
		mm.objects = new mmObjects({});	// runs inital scan for object on document.body
		
	}
	, setupTips: function() {

		var tipsRegular = $$('.mmTip');
		if (tipsRegular.length > 0) {
			tipsRegular.each(function(el) {
//dbug.log(el.get('title'));				
				var t = el.get('data-tip');
				if (t) {
					var cnt = t.split('::');
					el.store('tip:title',cnt[0]).store('tip:text',cnt[1]||'');
				} else {
					el.store('tip:title','').store('tip:text','');					
				}
			});
		}
		var tipsInfo = $$('.mmTipInfo');
		if (tipsInfo.length > 0) {
			tipsInfo.each(function(el) {
//dbug.log(el.get('title'));				
				var t = el.get('data-tip');
				if (t) {
					var cnt = t.split('::');
					el.store('tip:title',cnt[0]).store('tip:text',cnt[1]||'');
				} else {
					el.store('tip:title','').store('tip:text','');					
				}
			});
		}		
		var tipsError = $$('.mmTipError');
		if (tipsError.length > 0) {
			tipsError.each(function(el) {
				var t = el.get('data-tip');
				if (t) {
					var cnt = t.split('::');
					el.store('tip:title',cnt[0]).store('tip:text',cnt[1]||'');
				} else {
					el.store('tip:title','').store('tip:text','');					
				}
			});
		}
		mm.tips = new mmTips('.mmTip', {
			className:'mmTipContainer'
//			, title: function(el) { var cnt = el.get('title').split('::'); return cnt[0] || false; }
//			, text: function(el){ var cnt = el.get('title').split('::'); return cnt[1] || false; }
			, showDelay: this.options.tooltipDelays.show
			, tweenProps: this.options.tooltipTweenProps 
			, hideDelay: this.options.tooltipDelays.hide
			, onShow: function(tip,el) {
//dbug.log('SHOWING TIP');								
				tip.setStyles({'opacity':0,'display':'block'}).fade(1);
			}
			, onHide: function(tip,el) {
//dbug.log('HIDING TIP');				
				//tip.setStyles({'opacity':0,'display':'none'});
				tip.fade(0);
			}
		});	
		mm.tipsInfo = new mmTips('.mmTipInfo', {
			className:'mmTipInfoContainer'
//			, title: function(el) { var cnt = el.get('title').split('::'); return cnt[0] || false; }
//			, text: function(el){ var cnt = el.get('title').split('::'); return cnt[1] || false; }
			// , showDelay:mm.options.tooltipDelays.show
			, hideDelay: this.options.tooltipDelays.hide
			, tweenProps: this.options.tooltipInfoTweenProps
			, fixed: true
			, fixedDirection: 'top'
			, fixedPointer: 'bottom'
			, showDelay: 100			
			, offset: {x:-9, y:-9}
			, showEvent: 'focus'
			, hideEvent: 'blur'				
			, onShow: function(tip,el) {
				tip.setStyles({'opacity':0,'display':'block'}).fade(1);
			}					
			, onHide: function(tip,el) {
				tip.fade(0);
			}			
		});				
		window.addEvent('scroll', function(e) {
// dbug.log('scroll');			
			mm.tipsInfo.tip.hide();
		});	
		mm.tipsError = new mmTips('.mmTipError', {
			className:'mmTipErrorContainer'
//			, title: function(el) { var cnt = el.get('title').split('::'); return cnt[0] || false; }
//			, text: function(el){ var cnt = el.get('title').split('::'); return cnt[1] || false; }
			, showDelay: this.options.tooltipDelays.show
			, hideDelay: this.options.tooltipDelays.hide
			, tweenProps: this.options.tooltipTweenProps	
			, onShow: function(tip,el) {
				tip.setStyles({'opacity':0,'display':'block'}).fade(1);
			}					
			, onHide: function(tip,el) {
				tip.fade(0);
			}			
		});

	}		
	, setupNotices: function() {
		mm.notimoo = new Notimoo({
//		   parent: document.id('maintop')
		   locationVType: 'top'
		   , locationHType: 'right'
			, visibleTime: 3000
		});
		mm.noticeInfo = new Notimoo({		
			locationVType: 'top'			
			, locationHType: 'right'
			, locationVBase: 20
			, locationHBase: 20			
			, className: 'noticeInfo'
			, visibleTime: 3000
		});
		mm.noticeError = new Notimoo({		
			locationVType: 'top'			
			, locationHType: 'right'
			, locationVBase: 20
			, locationHBase: 20			
			, className: 'noticeError'
			, visibleTime: 3000
		});		

//		mm.ajax = new mm.AjaxControllerClass({});
		if (mm.runtime.noticeInfo) {
			mm.noticeInfo.show({title:mm.runtime.noticeInfo});
		}
		if (mm.runtime.noticeError) {
			mm.noticeError.show({title:mm.runtime.noticeError});
		}		
		if (mm.runtime.highlightId) {
dbug.log('>>> highlightId = '+mm.runtime.highlightId);
		}		
	}	
	

};

// extending the main object to have events
Object.append(mm.main, new Events());