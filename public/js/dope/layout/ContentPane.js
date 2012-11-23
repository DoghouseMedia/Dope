dojo.provide('dope.layout.ContentPane');
dojo.require('dijit.layout.ContentPane');
dojo.require('dope._Contained');
dojo.require('dope.xhr.Post');
dojo.require('dope.layout.LoadingDisabler');
dojo.require('dope.layout.pane.Count');

dojo.declare('dope.layout.ContentPane', [dijit.layout.ContentPane, dope._Contained], {
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
		if (this.controlButton && !this.isTopPane) {
			dojo.place(this.count.domNode, this.controlButton.containerNode);
			this.count.addUrl(this.getUrl()).refresh();
		}
	},
	onShow: function() {
		this.inherited(arguments);
		if (this.isLoaded || this.hasModel()) {
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
		this.setModelAlias();
		dojo.publish('/dope/layout/ContentPane/load', [this]);
	},
	onReady: function() {		
		this.activate();
		this.resize();
		
		if (this.getPane() instanceof dope.layout.ContentPane) {
			this.getPane().resize();
		}
	},
	onClose: function() {
		dojo.publish('/dope/layout/ContentPane/close', [this]);
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
			this._url = new dope.utils.Url(this.href);
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
	hasModel: function() {
		return this._model ? true : false;
	},
	getModel: function() {
		return this._model;
	},
	setModel: function(model) {
		this._model = model;
		
		this.onReady();
		
		/* Call generic method */
		dojo.hitch(this.getModel(), "defaultAction").call();
		
		/* Call action specific method */
		dojo.hitch(this.getModel(), this.getAction().replace('-', '') + "Action").call();
	},
	setModelAlias: function() {
		/*
		 * @todo Why is our Dope library aware of snowwhite?!?! Fix.
		 */
		var modelClass = 'snowwhite.entity.' + this.getUrl().get('controller');
		dojo['require'](modelClass);
		dojo.addOnLoad(dojo.hitch(this, 'onModelLoad', modelClass));
	},
	onModelLoad: function(modelClass) {
		eval('this.setModel(new ' + modelClass + '(this));');
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