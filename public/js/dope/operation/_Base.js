dojo.provide('dope.operation._Base');
dojo.require('dijit._Widget');
dojo.require('dijit._Templated');

dojo.declare('dope.operation._Base', [dijit._Widget, dijit._Templated], {
	baseClass: 'dopeOperation',
	templateString: "<div>" +
		"<span data-dojo-attach-point='titleNode'></span>" +
		"</div>",
	
	onComplete: function() { /* event */ },
	onExecute: function() { /* event */ },
	
	constructor: function(options) {
		dojo.publish('/dope/operation/onCreate', [this]);
	},
	buildRendering: function() {
		this.inherited(arguments);
		this.titleNode.innerHTML = this.title || 'Operation';
	},
	_onExecute: function() {
		dojo.publish('/dope/operation/onExecute', [this]);
	},
	_onComplete: function() {
		dojo.publish('/dope/operation/onComplete', [this]);
	}
});