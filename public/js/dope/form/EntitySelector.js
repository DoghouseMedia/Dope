dojo.provide('dope.form.EntitySelector');
dojo.require('dijit.MenuItem');
dojo.require('dope.dialog.EntitySelector');

dojo.declare('dope.form.EntitySelector', dijit.MenuItem, {
	onSelect: function() { /* Event */ },
	
	dialog: null,
	
	postCreate: function() {
		this.inherited(arguments);
		this.dialog = new dope.dialog.EntitySelector({
			href: '/' + this.entityType,
			onSelect: dojo.hitch(this, '_onSelect')
		});
	},
	
	_onSelect: function(params) {
		this.onSelect(params);
	},
	
	_onClick: function(e) {
		this.inherited(arguments);
		this.dialog.show();
	}
});