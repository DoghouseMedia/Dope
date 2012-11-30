dojo.provide('dope.Notify');
dojo.require('dojo.io.script');

dojo.declare('dope.Notify', null, {
	constructor: function() {
		this.url = new dope.utils.Url('/dope');
		this.url.set('port', 8181);
		
		dojo.io.script.get({url : String(this.url) + '/client.js'}).then(
			dojo.hitch(this, 'onNotifyReady')
		);
	},
	onNotifyReady: function() {
		this.client = new Faye.Client(String(this.url));
	}
});
