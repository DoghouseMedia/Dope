dojo.provide('dope.layout.Operations');
dojo.require('dijit._Widget');
dojo.require('dijit._Templated');

dojo.declare('dope.layout.Operations', [dijit._Widget, dijit._Templated], {
	operations: [],
	baseClass: 'dopeOperations',
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
	},
	updateCounter: function() {
		this.counter.innerHTML = this.getOperations().length;
	},
	
	onOperationCreate: function(operation) {
		this.addOperation(operation);
	},
	onOperationComplete: function(operation) {
		this.removeOperation(operation);
	},
	addOperation: function(operation) {
		this.operations.push(operation);
		dojo.connect(operation, 'buildRendering', 
			dojo.hitch(this, 'onOperationRender', operation)
		);
	},
	removeOperation: function(operation) {
		this.operations.splice(this.operations.indexOf(operation), 1);
		operation.destroyRendering();
	},
	getOperations: function() {
		return this.operations;
	},
	onOperationRender: function(operation) {
		operation.placeAt(this.containerNode, 'last');
	}
});