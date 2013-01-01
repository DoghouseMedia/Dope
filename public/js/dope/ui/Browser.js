dojo.provide('dope.ui.Browser');
dojo.require('dope.layout.LoadingDisabler');
dojo.require('dope.layout.Buttons');
dojo.require('dope._Contained');
dojo.require('dijit._Widget');
dojo.require('dijit._Templated');

dojo.declare('dope.ui.Browser', [dijit._Widget, dope._Contained, dijit._Templated], {
	baseClass: 'dopeUiBrowser',
	templatePath: dojo.moduleUrl("dope.ui","templates/Browser.html"),
	widgetsInTemplate: true,
	
	_timeout: null,
	
	constructor: function() {
		this._timeout = null;
	},
	
	postCreate: function() {
		this.inherited(arguments);
		
		this._calculateIframeHeight();
		
		dojo.connect(this.inputUrl, 'keypress', dojo.hitch(this, 'onKeyPress'));
		dojo.connect(this.iframe, 'load', dojo.hitch(this, 'onIframeLoad'));
		
		if (this.url) {
			this.setUrl(this.url);
		}
	},
	
	_calculateIframeHeight: function() {
		var parentHeight = dojo.position(this.getParent().domNode).h;
		var containerHeight = dojo.position(this.formContainer.domNode).h;
		var newHeight = parentHeight - containerHeight;
		
		this.iframe.height = newHeight + 'px';
	},
	
	onKeyPress: function(e) {
		if (e.keyIdentifier == 'Enter') {
			this.setUrl(this.getValue());
		}
	},
	
	getValue: function() {
		return this.inputUrl.value;
	},
	
	setValue: function(value) {
		this.inputUrl.value = value;
	},
	
	setUrl: function(url) {
		this.setTimeout();
		this.getParent().deactivate();
		
		this.iframe.src = url;
		this.setValue(url);
		this.getParent().setUrl('/browser/show?url=' + url, true);
	},
	
	onIframeLoad: function() {
		this.clearTimeout();
		this.getParent().activate();
	},
	
	onIframeError: function() {
		alert("ERROR.\n\nThe site has taken too long to load or can't be embedded.");
		this.clearTimeout();
		this.getParent().activate();
	},
	
	setTimeout: function() {
		// call the wrror handler after 10 seconds
		this._timeout = setTimeout(dojo.hitch(this, 'onIframeError'), 10000);
	},
	
	clearTimeout: function() {
		clearTimeout(this._timeout);
	}
	
	
});