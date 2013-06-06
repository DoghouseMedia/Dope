dojo.provide('dope.form.StoreBox');
dojo.require('dijit.form.FilteringSelect');
dojo.require('dope.data.ItemFileReadStore');

dojo.declare('dope.form.StoreBox', dijit.form.FilteringSelect, {
	searchAttr: "__toString",
	labelAttr: "__toString",
	silence: false,
	_value: null,
	onCompleteCallback: null,
	
	postCreate: function() {
		this.onCompleteCallback = null;
		this._value = null;
		this.inherited(arguments);
		this.fetchStore();
	},
	onFormFieldChange: function(field, value) {
	  if (this.get('value')) {
	      this.set('_value', this.get('value'));
	  }
		this.setStoreParam(field.name, value);
	},
	setStoreParam: function(key, val) {
		var url = new dope.utils.Url(this.getStoreUrl());
		url.set(key, val);
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
			onComplete: this.onStoreComplete.bind(this),
			onError: this.onStoreError.bind(this)
		});
	},
	onStoreComplete: function() {
		this.set('disabled', false);
		this.set('value', this._value);
		
		if (this.onCompleteCallback) {
			this.onCompleteCallback();
		}
	},
	onStoreError: function(v1,v2) {
		if (console) console.log('onStoreError', v1, v2);
	}
});