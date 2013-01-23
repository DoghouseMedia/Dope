dojo.provide('dope.Exception');

dojo.declare('dope.Exception', null, {
	name: 'DopeException',
	constructor: function(message) {
		this.message = message;
	}
});