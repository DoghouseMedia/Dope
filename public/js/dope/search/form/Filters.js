dojo.provide('dope.search.form.Filters');
dojo.require('dope.search.form.Filter');
dojo.require('dope.search.form.FilterAdd');
dojo.require('dope.search.form.FilterValues');

dojo.declare('dope.search.form.Filters', [dijit._Contained, dijit.layout._LayoutWidget], {
	form: null,
	filters: [],
	_onInitCallbacks: [],
	
	startup: function() {
		this.inherited(arguments);
		dojo.subscribe('/dope/search/form/filterAddRequest', this, 'onFilterAddRequest');
		dojo.subscribe('/dope/search/form/store/beforeFetch', this, 'onBeforeStoreFetch');
	},
	onFilterAddRequest: function(data) {
		this.add(data);
		return this;
	},
	onBeforeStoreFetch: function(storeUrl) {
		dojo.forEach(this.getAsParams(), function(param) {
			storeUrl.set(param.key, param.value);
		});
		console.log(storeUrl, String(storeUrl));
	},
	add: function(options) {
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