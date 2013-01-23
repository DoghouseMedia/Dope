dojo.provide('dope.grid.DataList');
dojo.require('dope.grid.DataGrid');
dojo.require('dope.data.JsonRestStore');
dojo.require('dope._Contained');
dojo.require('dope.Exception');
dojo.require('dijit.layout._LayoutWidget');

dojo.declare('dope.grid.DataList', [dijit.layout._LayoutWidget, dope._Contained], {
	baseClass: 'dopeGridDataList',

	postCreate: function() {
		this.store = new dope.data.JsonRestStore({
			target: this.storeUrl
		});
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
	
	getTeaser: function() {
		throw new dope.Exception( 
		    "Method not implemented."
		); 
	},
	
	_onFetchBegin: function(size, req) {
		
	},
	
	_onFetchComplete: function(items, req) {
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
