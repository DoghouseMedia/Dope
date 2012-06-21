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
			this.setStore(form.getStore());
		}
	},
	_onFetchComplete: function(items, req){
		this.inherited(arguments);
		
		var ids = req.ioArgs.xhr.getResponseHeader('Dope-Entity-Ids');
		if (typeof(ids) == "string") {
			ids = dojo.fromJson(ids);
		}
		this.getPane().setData('dope-entity-ids', ids);
	}
});
