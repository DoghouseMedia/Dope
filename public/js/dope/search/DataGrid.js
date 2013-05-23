dojo.provide('dope.search.DataGrid');
dojo.require('dope.grid.DataGrid');

dojo.declare('dope.search.DataGrid', dope.grid.DataGrid, {
	autoload: false,
	postCreate: function() {
		this.subscribe('/dope/search/form/store/ready', dojo.hitch(this, 'onFormStoreReady'));
		return this.inherited(arguments);
	},
	onFormStoreReady: function(form) {
		if (this.getPane() === form.getPane()) {
			// Turn on saved searches
			form.getStore().service.useSavedSearch = true;
			// Set store
			this.setStore(form.getStore());
		}
	},
	_onFetchComplete: function(items, req){
		this.inherited(arguments);
		
		if (this.getPane() && this.getPane().setData) {
			var dopeSearchId = req.ioArgs.xhr.getResponseHeader('Dope-Search-Id') || null;
			
			if (dopeSearchId) {
				this.getPane().setData('dope-search-id', dopeSearchId);
			}
		}
	}
});
