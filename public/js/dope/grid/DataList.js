dojo.provide('dope.grid.DataList');
dojo.require('dope.grid.DataGrid');
dojo.require('dope.data.JsonRestStore');
dojo.require('dope._Contained');
dojo.require('dope.Exception');
dojo.require('dijit.layout._LayoutWidget');

dojo.declare('dope.grid.DataList', [dijit.layout._LayoutWidget, dope._Contained], {
	baseClass: 'dopeGridDataList',
	hasRemovableTeasers: false,

	postCreate: function() {
		this.store = new dope.data.JsonRestStore({
			target: this.storeUrl
		});
		this.refresh();
	},
	
	refresh: function() {
		this.store.fetch({
//			start: start,
//			count: this.rowsPerPage,
//			query: this.query,
//			sort: this.getSortProps(),
//			queryOptions: this.queryOptions,
//			isRender: isRender,
			onBegin: dojo.hitch(this, "_onFetchBegin"),
			onComplete: dojo.hitch(this, "_onFetchComplete"),
			onError: dojo.hitch(this, "_onFetchError")
		});
	},
	
	clear: function() {
		dojo.forEach(this.getChildren(), function(child) {
			child.destroy();
		});
	},
	
	getTeaser: function() {
		throw new dope.Exception( 
		    "Method not implemented."
		); 
	},
	
	_onFetchBegin: function(size, req) {
		
	},
	
	_onFetchComplete: function(items, req) {
		this.clear();
		var dataList = this;
		dojo.forEach(items, function(item, i) {
			dataList.addChild(
				dataList.getTeaser(item)
			);
		});
	},
	
	_onFetchError: function(err, req) {
		
	}
	
});
