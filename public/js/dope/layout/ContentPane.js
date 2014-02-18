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
	activateOnLoad: true,

    ioArgs: {
        headers: {}
    },

	
	constructor: function(options) {
		if (!options._data) {
			this._data = {};
		}

        if (dojo.exists('TRED.user.token')) {
            this.ioArgs.headers['Dope-Rest-Token'] = TRED.user.token;
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

		this.trackUrl(this.getUrl());
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
	},
	trackUrl: function(url) {
		if (dojo.exists('_gaq')) {
			/* "_trackEvent" is the pageview event */ 
			_gaq.push(['_trackPageview', String(url)]);
		}
	},
	onDownloadError: function(err) {
		this.inherited(arguments);

		if (err.status == 404) {
			if (this.isTopPane) {
				this.getParent().closeChild(this);
			}
		}
	},
	onLoad: function(data, xhr) {
		this.inherited(arguments);
		
		dojo.publish('/dope/layout/ContentPane/load', [this]);
		
		/* Hack */
		if (this.getChildren()[0].title) {
			this.set('title', this.getChildren()[0].title);
			dojo.publish('/dope/layout/ContentPane/change', [this]);
		}
		
		if (this.activateOnLoad) {
			this.activate();
		}

		this.resize();
		
		if (this.getPane() instanceof dope.layout.ContentPane) {
			this.getPane().resize();
		}
	},
    onUnload: function() {
        this.inherited(arguments);
        this.destroyAllChildWidgets();
    },
	onClose: function() {
		dojo.publish('/dope/layout/ContentPane/close', [this]);
		
		if (this._loadingDisabler) {
			this._loadingDisabler.destroy();
		}
		
		if (this.isTopPane) {
			this.destroyAllChildWidgets();
		}
		
		return this.inherited(arguments);
	},
    destroyAllChildWidgets: function() {
        dojo.forEach(this.getDescendants(), function(widget) {
            widget.destroy();
        });
    },
	getData: function(key) {
		return key ? this._data[key] : this._data;
	},
	setData: function(key, val) {
		this.prepareData(key, val);
		this.publishChange();
		return this;
	},
	prepareData: function(key, val) {
    this._data[key] = val;
    return this;
  },
  publishChange: function() {
    dojo.publish('/dope/layout/ContentPane/change', [this]);
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

        this.publishChange();
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
            // ctrlKey for Win & Nix, metaKey for Mac
			focus: !params.e.ctrlKey && !params.e.meteKey,
			_data: this.getData()
		}]);
	}
});