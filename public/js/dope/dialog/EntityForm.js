dojo.provide('dope.dialog.EntityForm');
dojo.require('dope.dialog.Dialog');

dojo.declare('dope.dialog.EntityForm', dope.dialog.Dialog, {
	startup: function() {
		this.inherited(arguments);
		
		/*
		 * This widget uses the form's own submit button
		 * so we don't need the confirmButtonNode.
		 */
		this.confirmButtonNode.destroy();
	},
	
	onLoad: function() {
		this.inherited(arguments);
		
		this._hideButtonBar();
		this._connectForm();
	},
	
	onFormComplete: function(data) {
		/* Event */
	},
	
	_onFormComplete: function(data, response) {
		this.inherited(arguments);
		
		if (data.status) {
			this.hide();
			this.onFormComplete(data);
		}
	},
	
	hide: function() {
		var deferred = this.inherited(arguments);
		
		/*
		 * For some odd reason destroyRecursive()
		 * won't work if called by dojo.hitch().
		 * Instead, we assign $this to a variable
		 * and call using an anonymous closure.
		 */
		var dialog = this;
		deferred.then(function() {
			dialog.destroyRecursive();
		});
		
		return deferred;
	},
	
	_hideButtonBar: function() {
		var buttonBar = dijit.byNode(
			dojo.query('.dopeLayoutButtons', this.domNode)[0]
		);
		buttonBar.set('region', 'bottom');
		buttonBar.getParent().resize();
	},
	
	_connectForm: function() {
		var form = dijit.byNode(
			dojo.query('form', this.domNode)[0]
		);
		
		dojo.connect(
			form,
			'onComplete',
			dojo.hitch(this, '_onFormComplete')
		);
	}
});