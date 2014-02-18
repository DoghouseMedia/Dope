dojo.provide('dope.xhr._Base');

dojo.declare('dope.xhr._Base', null, {
	options: {},
	onComplete: function() { /* event */ },
	onExecute: function() { /* event */ },
	onLoad: function() { /* event */ },
	onError: function() { /* event */ },
	
	constructor: function(options) {
		this.options = dojo.mixin({
			handleAs: 'json',
			headers: {
				'Accept': 'application/json'
			},
			timeout: '5000',
			preventCache: true,
			handle: dojo.hitch(this, '_onComplete')
		}, options);
		if (dojo.exists('TRED.user.token')) {
			this.options.headers['Dope-Rest-Token'] = TRED.user.token;
		}
	},
	_onComplete: function(data, xhr) {
		this.onComplete(data, xhr);
	},
	_onExecute: function() {
		this.onExecute(arguments);
	},
	execute: function() {
		this._onExecute();
	}
});