dojo.provide('dope.layout.Notifications');
dojo.require('dijit._Widget');
dojo.require('dijit._Templated');

dojo.declare('dope.layout.Notifications', [dijit._Widget, dijit._Templated], {
	operations: [],
	baseClass: 'dopeNotifications',
	templateString: "<div>" + 
		"<div class='summary'>" +
		"<span data-dojo-attach-point='counter'>0</span> operation(s)" +
		"</div>" +
		"<div class='details' data-dojo-attach-point='containerNode'></div>" +
		"</div>",
	
	startup: function() {
		this.inherited(arguments);
		
		dojo.subscribe('/dope/operation/onCreate', dojo.hitch(this, 'onOperationCreate'));
		dojo.subscribe('/dope/operation/onComplete', dojo.hitch(this, 'onOperationComplete'));
		
		dojo.connect(this, 'addOperation', dojo.hitch(this, 'updateCounter'));
		dojo.connect(this, 'removeOperation', dojo.hitch(this, 'updateCounter'));
	},
	postCreate: function() {
		this.inherited(arguments);
	}
});