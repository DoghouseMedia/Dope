dojo.provide('dope.layout.pane.Count');
dojo.require('dijit._Widget');
dojo.require('dijit._Templated');
dojo.require('dope._Contained');

dojo.declare('dope.layout.pane.Count', [dijit._Widget, dijit._Templated, dope._Contained], {
	urls: [],
	operations: [],
	total: 0,
	active: true,
	templateString: '<span dojoattachpoint="containerNode"></span>',
	
	postCreate: function() {
		this.inherited(arguments);
		this.urls = [];
		this.operations = [];
	},
    destroy: function() {
        this.cancelOperations();
        this.inherited(arguments);
    },
	addUrl: function(url) {
		var url = String(
			new dope.utils.Url(String(url), {
				action: 'count.json',
				sender: null
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
    cancelOperations: function() {
        dojo.forEach(this.operations, function(operation) {
            operation.cancel();
        });
    },
	refresh: function() {
		if (this.active) {
			/* Reset counter */
			this.total = 0;

			/* Fetch counts */
			dojo.forEach(this.urls, dojo.hitch(this, '_fetch'));
		}
		
		return this;
	},
	setActive: function(active) {
		this.active = active;
		return this;
	},
	_fetch: function(url) {
        var urlIsRunning = dojo.some(this.operations, function(operation) {
            if (url == operation.url) {
                return true
            }
            return false;
        });

        if (urlIsRunning) {
            return;
        }

        var url = new dope.utils.Url(url);
        url.useHost(true); // sharding

		var op = new dope.operation.xhrGet({
            qos: 20,
			title: 'Count',
			url: String(url),
			handleAs: "text",
			load: dojo.hitch(this, '_onCount', op)
		});
		this.operations.push(op);
	},
	_onCount: function(operation, data, args) {
        this.operations.splice(this.operations.indexOf(operation), 1);
		var i = parseInt(data);
		if (isNaN(i)) return;
		this.total += i; 
		this.containerNode.innerHTML = ' (' + this.total + ')'
	}
});
