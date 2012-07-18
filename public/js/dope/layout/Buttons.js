dojo.provide('dope.layout.Buttons');
dojo.require('dope.layout.ContentPane');

dojo.declare('dope.layout.Buttons', dope.layout.ContentPane, {
	baseClass: 'dopeLayoutButtons',
	region: 'top',
	
	startup: function() {
		this.inherited(arguments);
		dojo.addClass(this.domNode, dope.layout.ContentPane.prototype.baseClass);
		if (this.getPane() && this.getPane().isTopPane) {
			dojo.addClass(this.domNode, 'dopeLayoutButtonsTop');
		}
	}
});