dojo.provide('dope.layout.ContentPane');
dojo.require('dijit.layout.ContentPane');
dojo.require('dope._Contained');
dojo.require('dope.layout._ResizeParent');
dojo.require('dope.xhr.Post');
dojo.require('dope.layout.LoadingDisabler');
dojo.require('dope.layout.pane.Count');

dojo.declare('dope.layout.ContentPane', [
	dijit.layout.ContentPane,
	dope._Contained,
	dope.layout._ResizeParent
], {
	isLayoutContainer: true,
	isContainer: true,
	
	_url: null,
	_model: null,
	_loadingDisabler: null,
	count: null,
	_data: null,
	
	title: '',
	href: '',
	closable: false,
	loadingMessage: '',
	useIframe: false,
	isTopPane: false,
	
	constructor: function(options) {
		if (!options._data) {
			this._data = {};
		}
	},
	startup: function() {
		this.inherited(arguments);
		
		dojo.publish('/dope/layout/ContentPane/open', [this]);
				
		this.count = new dope.layout.pane.Count();
		if (this.controlButton && !this.isTopPane && this.getUrl()) {
			dojo.place(this.count.domNode, this.controlButton.containerNode);
			this.count.addUrl(this.getUrl()).refresh();
		}
		else {
			this.count.setActive(false);
		}
	},
	onShow: function() {
		this.inherited(arguments);
		if (this.isLoaded) {
			this.activate();
		} else {
			this.deactivate();
		}
	},
	onHide: function() {
		this.inherited(arguments);
		if (this.isTopPane) {
			if (! this.isLoaded) {
				this._loadingDisabler.hide();
			}
		}
	},
	deactivate: function() {
		if (this.isTopPane) {
			if (! (this._loadingDisabler instanceof dope.layout.LoadingDisabler)) {
				this._loadingDisabler = new dope.layout.LoadingDisabler({pane: this});
				this._loadingDisabler.placeAt(this.domNode, 'before');
			}	
			
			this._loadingDisabler.show();
		}
	},
	activate: function() {
		if (this.isTopPane) {
			if (this._loadingDisabler instanceof dope.layout.LoadingDisabler) {
				this._loadingDisabler.hide();
			}
		}
		
		this.trackUrl(this.getUrl());
	},
	trackUrl: function(url) {
		if (dojo.exists('_gaq')) {
			/* "_trackEvent" is the pageview event */ 
			_gaq.push(['_trackPageview', String(url)]);
		}
	},
	onLoad: function(data, xhr) {
		this.inherited(arguments);
		
		dojo.publish('/dope/layout/ContentPane/load', [this]);
			
		this.activate();
		this.resize();
		
		if (this.getPane() instanceof dope.layout.ContentPane) {
			this.getPane().resize();
		}
	},
	onClose: function() {
		dojo.publish('/dope/layout/ContentPane/close', [this]);
		
		if (this._loadingDisabler) {
			this._loadingDisabler.destroy();
		}
		
		dojo.forEach(this.getDescendants(), function(widget) {
			widget.destroyRecursive();
		});
		
		return this.inherited(arguments);
	},
	getData: function(key) {
		return key ? this._data[key] : this._data;
	},
	setData: function(key, val) {
		this._data[key] = val;
		dojo.publish('/dope/layout/ContentPane/change', [this]);
		return this;
	},
	getUrl: function(forceUpdate) {
		if (! this._url || forceUpdate) {
			this._url = this.href
				? new dope.utils.Url(this.href)
				: false;
		}
		
		return this._url;
	},
	setUrl: function(url, noReload) {
		if (! noReload) {
			this.disconnect();
		}
		
		this._url = url;
		
		if (noReload) {
			this.href = String(url);
		} else {
			this.set('href', String(url));
		}
		
		dojo.publish('/dope/layout/ContentPane/change', [this]);
	},
	getController: function() {
		return this.getUrl().get('controller');
	},
	getAction: function() {
		var action = this.getUrl().get('action');
		if (! action) {
			return 'browse';
		} else if (parseInt(action) == Number(action)) {
			return 'read';
		} else {
			return action;
		}
	},
	onGridRowClick: function(params) {
		dojo.publish('/dope/layout/TabContainerMain/open', [{
			href: params.href,
			title: params.title,
			focus: !params.e.ctrlKey,
			_data: this.getData()
		}]);
	}
});