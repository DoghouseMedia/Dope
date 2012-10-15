dojo.provide('dope.TooltipDialog');
dojo.require('dijit.TooltipDialog');

dojo.declare('dope.TooltipDialog', dijit.TooltipDialog, {
	startup: function() {
		this.resize();
		this.inherited(arguments);
	}
});
