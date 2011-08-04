var MooDropbox = new Class({

	Implements: [Options, Events]
	, options: {
		consumerKey: ''
		, consumerSecret: ''
		, accessToken: ''
		, accessSecret: ''
		, userEmail: ''
		, userPassword: ''
		, apiVersion: '0'
		, apiSubdomain: 'api' // some methods need api-content.dropbox.com
		, isSandbox: false
	}
	, initialize: function(options) {
mm.log('MooDropbox initialize');		
		this.setOptions(options);
		this.is_connected = false;
		this.accessToken = false;
		this.accessTokenSecret = false;	
		this.accountInfo = false;	
		if (this.options.accessToken != '' && this.options.accessTokenSecret != '') {
			this.accessToken = this.options.accessToken;
			this.accessTokenSecret = this.options.accessTokenSecret;
			this.is_connected = true;				
		}
		this.setupElements();
		this.setupEvents();
	} 
	, setupElements: function() {
		this.consumerKey = this.options.consumerKey;
		this.consumerSecret = this.options.consumerSecret;
		this.userEmail = this.options.userEmail;
		this.userPassword = this.options.userPassword;		
		this.apiRequest = new Request.JSONP({
			url: ''
			, method: 'get'
			, data: {}
			, onSuccess: function() { }
			, onFailure: function() {}
			// , onSuccess:function() { alert('bla');}
// 			, onSuccess:function(rJson, rText) {
// mm.log(rJson);				
// 			}.bind(this)
// 			, onRequest:function(){ 
// mm.log('onRequest..');
// 			}.bind(this)
// 			, onFailure:function(xhr) {
// mm.log('onFailure xhr:');
// mm.log(xhr);
// 			}

		});
	}
	, setupEvents: function() {
		
	}
	, setUser: function(email, password) {
		if (email != undefined && email != null && email != '') {
			this.userEmail = email;
		} 
		if (password != undefined && password != null && password != '') {
			this.userPassword = password;
		}	
		return this;	
	}
	, authenticate:function(callback) {
		if (this.consumerKey != '' && this.consumerSecret != '' && this.userEmail != '' && this.userPassword != '') {
			
			// var url = "https://" + params.apiSubdomain + ".dropbox.com/" + params.apiVersion + path;
			
			this.request('/token'
				, {
					sendAuth:false
					, onSuccess:function(rJson) {
mm.log('authenticate success');
mm.log(rJson);
						if (rJson) {
					        this.accessToken = rJson.token;
					        this.accessTokenSecret = rJson.secret;							
							this.is_connected = true;
							
							this.getInfo(true);
							
							if (callback != undefined) {
								callback();
							}							
						} else {
mm.log('authentication request success, but auth failed');
							this.is_connected = false;
							this.accessToken = false;
							this.accessTokenSecret = false;
						}
					}.bind(this)
					, onFailure:function(xhr) {
mm.log('authenticate failure:');
mm.log(xhr);						
					}.bind(this)
				}
				, {
					email: this.userEmail
					, password: this.userPassword
				}
			);
									
		} else {
			return false;
		}
	}
	, request: function(path, params, data) {
		
		params = Object.merge({
			apiSubdomain: this.options.apiSubdomain
			, apiVersion: this.options.apiVersion
			, sendAuth: true
			, method: 'get'			
			// , onSuccess: function(rJson) { mm.log('default success handler'); }
			// , onRequest: function() { mm.log('default requesting handler'); }
			// , onFailure: function(xhr) { mm.log('default failure handler'); }
		}, params || {});
		
		
	    if (params.sendAuth && !this.accessToken) {
	      throw "Authenticated method called before authenticating";
	    }		
	
		var apiUrl = "https://" + params.apiSubdomain + ".dropbox.com/" + params.apiVersion + path;	
mm.log('request params:');
mm.log(params);

		var count = Request.JSONP.counter;
		var jsonpcallback = 'Request.JSONP.request_map.request_'+count;

	    var message = {
			action: apiUrl,
			method: params.method,
			parameters: {
				oauth_consumer_key: this.consumerKey,
				oauth_signature_method: "HMAC-SHA1",
				callback: jsonpcallback
			}
	    };

	    message.parameters = Object.merge(message.parameters, data);
// mm.log('message:');
// mm.log(message);
	    if (params.sendAuth) {		
			message.parameters.oauth_token = this.accessToken;
mm.log('if sendauth message.parameters.oauth_token = '+message.parameters.oauth_token);			
	    }

	    var oauthBits = {
	      consumerSecret: this.consumerSecret	
	    };
mm.log('consumerSecret = '+this.consumerSecret);	

	    if (params.sendAuth) {
	      oauthBits.tokenSecret = this.accessTokenSecret;
mm.log('oauthBits.tokenSecret = '+oauthBits.tokenSecret);	
	    }

	    OAuth.setTimestampAndNonce(message);
	    OAuth.SignatureMethod.sign(message, oauthBits);

		var apiData = OAuth.getParameterMap(message.parameters);
mm.log('apiData:');		
mm.log(apiData);		
	
		this.apiRequest = new Request.JSONP({
			onSuccess: params.onSuccess
			, onFailure: params.onFailure
		}).send({
			url: apiUrl
			, data: apiData
	      	// , onSuccess: params.onSuccess
	      	// , onFailure: params.onFailure			
		});	

		// this.apiRequest.options.onSuccess = params.onSuccess;
		// this.apiRequest.options.onFailure = params.onFailure;				
		// this.apiRequest.send({
		// 	url: apiUrl
		// 	, data: OAuth.getParameterMap(message.parameters)
		// 	      	// , onSuccess: params.onSuccess
		// 	      	// , onFailure: params.onFailure			
		// });
	}
	, getInfo:function(internal) {
mm.log('getInfo is_connected = '+this.is_connected+' access: '+this.accessToken+' accessSecret: '+this.accessTokenSecret);		
		if (this.is_connected && this.accessToken && this.accessTokenSecret) {
			this.request('/account/info', {
				onSuccess:function(rJson) {
mm.log('getInfo success:');
mm.log(rJson);
					if (rJson) {
						if (internal != undefined && internal) {
							this.accountInfo = rJson;
mm.log('this.accountInfo:');
mm.log(this.accountInfo);					
							this.fireEvent('info', this.accountInfo);
						}
					}
				}.bind(this)
				, onFailure:function(xhr) {
mm.log('getInfo failure');					
				}.bind(this)
			}, {});
		}
	}
	, metadata:function(path, callback) {
		if (this.is_connected && this.accessToken && this.accessTokenSecret) {
			if (path == undefined) {path = '';}
mm.log('metadata path = /metadata/dropbox'+escape(path));
			this.request('/metadata/dropbox'+escape(path), {
				onSuccess:function(rJson) {
mm.log('metadata success:');
mm.log(rJson);

					if (callback) {
						callback(rJson);
					}
				}.bind(this)
				, onFailure:function(xhr) {
mm.log('getInfo failure');					
				}.bind(this)
			}, {});
		}		
	}
	/*
		get_file only works without script tag jsonp
	*/
	, get_file: function(path, callback) {
		if (this.is_connected && this.accessToken && this.accessTokenSecret) {
			if (path == undefined) { path = ''; }			
			this.request('/files/dropbox'+escape(path), {
				apiSubdomain: 'api-content'
				, onSuccess:function(data) {
mm.log('getFile success:');
mm.log(data);

					if (callback) {
						callback(data);
					}
				}.bind(this)
				, onFailure:function(xhr) {
mm.log('getFile failure');					
				}.bind(this)
			}, {});			
		}
	}
	/*
		put_file only works without script tag jsonp
	*/	
	, put_file: function(path, fileData, callback) {
		if (this.is_connected && this.accessToken && this.accessTokenSecret) {
			if (path == undefined) { path = ''; }			
			this.request('/files/dropbox'+escape(path), {
				apiSubdomain: 'api-content'
				, method: 'post'
				, onSuccess:function(rJson) {
mm.log('getFile success:');
mm.log(rJson);

					if (callback) {
						callback(rJson);
					}
				}.bind(this)
				, onFailure:function(xhr) {
mm.log('getFile failure');					
				}.bind(this)
			}, {file: fileData});			
		}
	}	
	/*
		thumbnails is to receive the actual thumbnail data, which must then be saved to a local file somewhere to do something with it
	*/
	, thumbnails: function(path, opts, callback) {
		if (this.is_connected && this.accessToken && this.accessTokenSecret) {
			if (path == undefined) { path = ''; }
			if (opts == undefined) { opts = {size: 'small', format:'JPEG'}; } else { if (opts.size == undefined) {opts.size = 'small';} if (opts.format == undefined) {opts.format = 'JPEG';} }

			this.request('/thumbnails/dropbox'+escape(path), {
				apiSubdomain: 'api-content'
				, onSuccess:function(rJson) {
mm.log('createFolder success:');
mm.log(rJson);

					if (callback) {
						callback(rJson);
					}
				}.bind(this)
				, onFailure:function(xhr) {
mm.log('getInfo failure');					
				}.bind(this)
			}, {size:opts.size, format:opts.format});
		}		
	}
	, create_folder:function(path, callback) {
		if (this.is_connected && this.accessToken && this.accessTokenSecret) {
			if (path == undefined) {
				path = '';
			}
			this.request('/fileops/create_folder', {
				onSuccess:function(rJson) {
mm.log('createFolder success:');
mm.log(rJson);

					if (callback) {
						callback(rJson);
					}
				}.bind(this)
				, onFailure:function(xhr) {
mm.log('getInfo failure');					
				}.bind(this)
			}, {root:this.options.isSandbox ? 'sandbox' : 'dropbox', path: path});
		}		
	}
	, deleteItem:function(path, callback) {
		if (this.is_connected && this.accessToken && this.accessTokenSecret) {
			if (path == undefined) {
				path = '';
			}
			this.request('/fileops/delete', {
				onSuccess:function(rJson) {
mm.log('delete success:');
mm.log(rJson);

					if (callback) {
						callback(rJson);
					}
				}.bind(this)
				, onFailure:function(xhr) {
mm.log('delete failure');					
				}.bind(this)
			}, {root:this.options.isSandbox ? 'sandbox' : 'dropbox' , path:path});
		}		
	}	
	, move:function(from_path, to_path, callback) {
		if (this.is_connected && this.accessToken && this.accessTokenSecret) {
			if (path == undefined) {
				path = '';
			}
			this.request('/fileops/move', {
				onSuccess:function(rJson) {
mm.log('move success:');
mm.log(rJson);

					if (callback) {
						callback(rJson);
					}
				}.bind(this)
				, onFailure:function(xhr) {
mm.log('move failure');					
				}.bind(this)
			}, {root:this.options.isSandbox ? 'sandbox' : 'dropbox' , from_path:from_path, to_path:to_path});
		}		
	}	
	, copy:function(from_path, to_path, callback) {
		if (this.is_connected && this.accessToken && this.accessTokenSecret) {
			if (path == undefined) {
				path = '';
			}
			this.request('/fileops/copy', {
				onSuccess:function(rJson) {
mm.log('copy success:');
mm.log(rJson);

					if (callback) {
						callback(rJson);
					}
				}.bind(this)
				, onFailure:function(xhr) {
mm.log('copy failure');					
				}.bind(this)
			}, {root:this.options.isSandbox ? 'sandbox' : 'dropbox' , from_path:from_path, to_path:to_path});
		}		
	}	
});