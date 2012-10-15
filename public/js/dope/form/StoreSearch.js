dojo.provide('dope.form.StoreSearch');
dojo.require('dope.form.StoreBox');

dojo.declare('dope.form.StoreSearch', dope.form.StoreBox, {
	storeUrl: null,
	postCreate: function() {
		this.inherited(arguments);
		if (this.storeUrl) {
			this.setStoreUrl(this.storeUrl);
		}
		dojo.connect(this, 'onKeyUp', this.refreshStore.bind(this));
	},	
	getStoreUrl: function() {
		return this.storeUrl;
	},
	setStoreUrl: function(storeUrl) {
		this.storeUrl = storeUrl;
		new dope.operation.xhrGet({
			title: 'Searching...',
			url: String(storeUrl),
			headers: {
				'Accept': 'application/x-dojo-json',
				'Range': 'items=0-9'
			},
			load: dojo.hitch(this, '_onStoreLoad')
		});
		return this;
	},
	_onStoreLoad: function(storeData, req) {
		this.setStore(new dope.data.ItemFileReadStore({
			data: storeData
		})); 
	},
	refreshStore: function(e) {
		this.setStoreParam('query', this._lastQuery);
	}
});