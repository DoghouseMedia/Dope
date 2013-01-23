dojo.provide('dope.dialog.EntitySelector');
dojo.require('dope.dialog.Dialog');

dojo.declare('dope.dialog.EntitySelector', dope.dialog.Dialog, {
	onSelect: function() { /* Event */ },
	
	showOnCreate: false,
	title: 'Select...',
	confirmText: 'Done',
	href: null,
	
	getUrl: function() { 
		return new dope.utils.Url(this.href);
	},
	
	onGridRowClick: function(params) {
		new dope.operation.xhrGet({
			title: 'Load entity',
			url: params.href,
			load: this._onSelect.bind(this)
		});
		
	},
	
	_onSelect: function(entity) {
		this.onSelect(entity);
	}
});