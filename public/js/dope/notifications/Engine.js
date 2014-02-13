dojo.provide('dope.notifications.Engine');
dojo.require('dope.operation.xhrGet');
dojo.require('dojo.io.script');
dojo.require('dijit._Widget');
dojo.require('dope.notifications.VisualNotification');

dojo.declare('dope.notifications.Engine', dijit._Widget, {
	retryAfter: null, //minutes
	authToken: null,
	
	postCreate: function() {
		this.reset();
		this.prepareConnect();
	},
	reset: function() {
		this.retryAfter = 1;
		//this.authToken = null;
	},
	prepareConnect: function() {
		new dope.operation.xhrGet({
			url: '/app/auth/token',
			load: dojo.hitch(this, 'onTokenReady')
		});
	},
	onTokenReady: function(data) {
		this.authToken = data.token;
		this.connect();
	},
	connect: function() {
		this.url = new dope.utils.Url('/dope');
		this.url.set('port', 8181);
        this.url.noHost(true);
		
		dojo.io.script.get({
			url : String(this.url) + '/client.js',
			timeout: 2000
		}).then(
			dojo.hitch(this, 'onEngineReady'),
			dojo.hitch(this, 'onEngineError')
		);
	},
	onEngineReady: function() {
		var engine = this;
		
		this.client = new Faye.Client(String(this.url));
		this.client.addExtension({
			outgoing: function(message, callback) {
				message.ext = {
					token: engine.authToken
				};
				callback(message);
			}
		});
		
		this.reset();
	},
	onEngineError: function() {
		new dope.notifications.VisualNotification({
			message: 'Unable to reach the notification server.'
		});
		
		setTimeout(dojo.hitch(this, 'connect'), this.retryAfter * 60000); //minutes
		
		// check after 1min, 5mins, 25mins, and every 60mins
		this.retryAfter = Math.min(this.retryAfter*5, 60);
	}
});
