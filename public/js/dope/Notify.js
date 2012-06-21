dojo.provide('dope.Notify');
dojo.require('dojo.io.script');

dojo.declare('dope.Notify', null, {
	fayeUrl: null,
	constructor: function() {
		var notify = this;
		dojo.io.script.get({url : notify.fayeUrl + '/client.js'}).then(
			dojo.hitch(this, 'onNotifyReady')
		);
	},
	onNotifyReady: function() {
		
	}
});
