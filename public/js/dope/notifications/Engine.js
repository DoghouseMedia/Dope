dojo.provide('dope.notifications.Engine');
dojo.require('dojo.io.script');

dojo.declare('dope.notifications.Engine', null, {
	retryAfter: null, //minutes
	
	constructor: function() {
		this.reset();
		this.connect();
	},
	reset: function() {
		this.retryAfter = 1;
	},
	connect: function() {
		this.url = new dope.utils.Url('/dope');
		this.url.set('port', 8181);
		
		dojo.io.script.get({
			url : String(this.url) + '/client.js',
			timeout: 2000
		}).then(
			dojo.hitch(this, 'onEngineReady'),
			dojo.hitch(this, 'onEngineError')
		);
	},
	onEngineReady: function() {
		this.client = new Faye.Client(String(this.url));
		this.reset();
	},
	onEngineError: function() {
		new dope.notifications.Notification({
			message: 'Unable to reach the notification server.'
		});
		
		setTimeout(dojo.hitch(this, 'connect'), this.retryAfter * 60000); //minutes
		
		// check after 1min, 5mins, 25mins, and every 60mins
		this.retryAfter = Math.min(this.retryAfter*5, 60);
	}
});
