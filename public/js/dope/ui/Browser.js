dojo.provide('dope.ui.Browser');
dojo.require('dope._Contained');
dojo.require('dijit._Widget');
dojo.require('dijit._Templated');

dojo.declare('dope.ui.Browser', [dijit._Widget, dope._Contained, dijit._Templated], {
	baseClass: 'dopeUiBrowser',
	templatePath: dojo.moduleUrl("dope.ui","templates/Browser.html"),
	widgetsInTemplate: true,
	
	postCreate: function() {
		this.inherited(arguments);
		
		this._calculateIframeHeight();
		
		dojo.connect(this.inputUrl, 'keypress', dojo.hitch(this, 'onKeyPress'));
	},
	
	_calculateIframeHeight: function() {
		var newHeight = dojo.position(this.getParent().domNode).h - dojo.position(this.formContainer.domNode).h;		
		this.iframe.height = newHeight + 'px';
	},
	
	onKeyPress: function(e) {
		if (e.keyIdentifier == 'Enter') {
			this.onSubmit();
		}
	},
	
	onSubmit: function() {
		console.log(this.inputUrl, this.inputUrl.value);
		this.iframe.src = this.inputUrl.value;
	}
	
	
});