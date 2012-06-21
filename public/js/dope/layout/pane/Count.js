dojo.provide('dope.layout.pane.Count');
dojo.require('dijit._Widget');
dojo.require('dijit._Templated');

dojo.declare('dope.layout.pane.Count', [dijit._Widget, dijit._Templated], {
	urls: [],
	operations: [],
	total: 0,
	templateString: '<span dojoattachpoint="containerNode"></span>',
	
	postCreate: function() {
		this.inherited(arguments);
		this.urls = [];
		this.operations = [];
	},
	addUrl: function(url) {
		var url = String(
			new dope.utils.Url(String(url), {
				action: 'count'
			})
		);
		
		if (url && (this.urls.indexOf(url) == -1)) {
			this.urls.push(url);
		}
		return this;
	},
	resetUrls: function() {
		this.urls = [];
		return this;
	},
	resetOperations: function() {
		dojo.forEach(this.operations, function(operation) {
			operation.cancel();
		});
		this.operations = [];
		return this;
	},
	refresh: function() {
		/* Cancel running operations */
		this.resetOperations();
		
		/* Reset counter */
		this.total = 0;
		
		/* Fetch counts */
		dojo.forEach(this.urls, dojo.hitch(this, '_fetch'));
		
		return this;
	},
	_fetch: function(url) {
		var op = new dope.operation.xhrGet({
			title: 'Count',
			url: String(url),
			handleAs: "text",
			load: dojo.hitch(this, '_onCount')
		});
		this.operations.push(op);
	},
	_onCount: function(data, args) {
		var int = parseInt(data);
		if (isNaN(int)) return;
		this.total += int; 
		this.containerNode.innerHTML = ' (' + this.total + ')'
	}
});