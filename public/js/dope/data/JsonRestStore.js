define("dope/data/JsonRestStore", ["dojo", "dojox", "dojox/data/JsonRestStore", "dope/rpc/Rest"], function(dojo, dojox) {

dojo.declare('dope.data.JsonRestStore', dojox.data.JsonRestStore, {
	constructor: function(options) {
		this.service = dope.rpc.Rest(options.target, true);
	}
});

});