dojo.provide('dope.operation._Base');
dojo.require('dope.menu.Item');

dojo.declare('dope.operation._Base', dope.menu.Item, {
	onComplete: function() { /* event */ },
	onExecute: function() { /* event */ },
    qos: 0,
	
	constructor: function(options) {
		dojo.publish('/dope/operation/onCreate', [this]);
	},
	_onExecute: function() {
		dojo.publish('/dope/operation/onExecute', [this]);
	},
	_onComplete: function() {
		this.destroy();
		dojo.publish('/dope/operation/onComplete', [this]);
	}
});