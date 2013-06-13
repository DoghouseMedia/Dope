dojo.provide('dope.dialog.EntitySelector');
dojo.require('dope.dialog.Dialog');

dojo.declare('dope.dialog.EntitySelector', dope.dialog.Dialog, {
	onSelect: function() { /* Event */ },
	
	showOnCreate: false,
	title: 'Select...',
	confirmText: 'Close',
	href: null,
	isContainer: true,
	isLayoutContainer: true,
	
	getUrl: function() { 
		return new dope.utils.Url(this.href);
	},

	onShow: function() {
		/*
		 * _checkIfSingleChild() will define _singleChild if it exists
		 */
		this._checkIfSingleChild();
		if (this._singleChild) {
			this._singleChild.resize({
				w: 1040,
				h: 340
			});
		}
		
		this.inherited(arguments);
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