dojo.provide('dope.form.StoreBox');
dojo.require('dijit.form.FilteringSelect');
dojo.require('dope.data.ItemFileReadStore');

dojo.declare('dope.form.StoreBox', dijit.form.FilteringSelect, {
	onStoreComplete: function() { /* Event */ },

	searchAttr: "__toString",
	labelAttr: "__toString",
	pageSize: 20,
	noisy: true,
	deaf: false,
	
	postCreate: function() {
		this.inherited(arguments);
		this.fetchStore();
	},
	onFormFieldChange: function(field, value) {
		if (! this.deaf) {
			this.setStoreParam(field.name, value);
		}
	},
	setStoreParam: function(key, val) {
		var url = new dope.utils.Url(this.getStoreUrl());
		if (key) {
			url.set(key, val);
		}
		return this.setStoreUrl(String(url));
	},
	getStoreUrl: function() {
		return this.store.url;
	},
	setStoreUrl: function(storeUrl) {
		return this.setStore(new dope.data.ItemFileReadStore({
			url: storeUrl
		}));
	},
	setStore: function(store) {
		this.store = store;
		return this.fetchStore();
	},
	fetchStore: function() {
		this.set('disabled', true);
		return this.store.fetch({
			onComplete: this._onStoreComplete.bind(this),
			onError: this._onStoreError.bind(this)
		});
	},
	_onStoreComplete: function() {
		this.set('disabled', false);
		this.set('value', this.value);
		
		this.onStoreComplete();
	},
	_onStoreError: function(v1,v2) {
		if (console) console.log('onStoreError', v1, v2);
	}
});