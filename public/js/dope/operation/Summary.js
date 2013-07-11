dojo.provide('dope.operation.Summary');
dojo.require('dope.layout.MenuCounter');

dojo.declare('dope.operation.Summary', dope.layout.MenuCounter, {
	'class': 'dopeOperationSummary',
	iconClass: 'icon-refresh',
	
	postCreate: function() {
		this.inherited(arguments);
		
		dojo.subscribe('/dope/operation/onExecute', dojo.hitch(this, 'onItemNew'));
		dojo.subscribe('/dope/operation/onComplete', dojo.hitch(this, 'onItemDestroy'));
	}
});