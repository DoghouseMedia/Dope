dojo.provide('dope.search.form.Filters');
dojo.require('dope.search.form.Filter');
dojo.require('dope.search.form.FilterAdd');
dojo.require('dope.search.form.FilterValues');
dojo.require('dope._Contained');
dojo.require('dijit.layout._LayoutWidget');

dojo.declare('dope.search.form.Filters', [dope._Contained, dijit.layout._LayoutWidget], {
	form: null,
	filters: [],
	_onInitCallbacks: [],
	
	constructor: function() {
		dojo.subscribe('/dope/search/form/filterAddRequest', dojo.hitch(this, 'onFilterAddRequest'));
		dojo.subscribe('/dope/search/form/store/beforeFetch', dojo.hitch(this, 'onBeforeStoreFetch'));
	},
	onFilterAddRequest: function(data, initCallback) {
		this.add(data, initCallback);
		return this;
	},
	onBeforeStoreFetch: function(form, storeUrl) {
		if (form.getPane() !== this.getPane()) {
			return;
		}
		
		this.getPane().setData('formfilters', this.getSerialized());
		//
		dojo.forEach(this.getAsParams(), function(param) {
			storeUrl.set(param.key, param.value);
		});
	},
	add: function(options, initCallback) {
		if (initCallback) {
			this.addOnInit(initCallback);
		}
		
		var filter = new dope.search.form.Filter(dojo.mixin(options, {
			filters: this
		}));

		this.addChild(filter);
		this.filters.push(filter);
		
		dojo.publish('/dope/search/form/domChange');
		
		return filter;
	},
	removeFilter: function(filter) {
		this.filters.splice(this.filters.indexOf(filter), 1);
		dojo.publish('/dope/search/form/domChange');
		return this;
	},
	getAsParams: function() {
		var params = [];
		
		dojo.forEach(this.filters, function(filter, i) {
			var filterParams = filter.getAsParams();
			params.push(filterParams);
		});
		
		return params;
	},
	getSerialized: function() {
		var data = [];
		
		dojo.forEach(this.filters, function(filter, i) {
			data.push(filter.getSerialized());
		});
		
		return data;
	},
	onFilterLoad: function() {
		if (! this.testAllFiltersLoaded()) {
			return;
		}
		
		dojo.forEach(this._onInitCallbacks, function(callback) {
			callback();
		});
		
		this._onInitCallbacks = [];
	},
	addOnInit: function(callback) {
		this._onInitCallbacks.push(callback);
		return this;
	},
	testAllFiltersLoaded: function() {
		return dojo.every(this.filters, function(filter) {
			return filter.isLoaded();
		});
	}
});