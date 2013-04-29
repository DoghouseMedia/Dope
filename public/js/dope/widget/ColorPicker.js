dojo.provide('dope.widget.ColorPicker');
dojo.require('dojo.cache');
dojo.require('dojox.widget.ColorPicker');

dojo.declare('dope.widget.ColorPicker', dojox.widget.ColorPicker, {
	showHsv: false,
	showHex: false,
	showRgb: false,
	
	previewNode: null,
	
	// don't change to d.moduleUrl, build won't intern it.
	templateString: dojo.cache("dope.widget","ColorPicker/ColorPicker.html"),
	
	onChange: function(color) {
		this.inherited(arguments);
		
		dojo.style(this.previewNode, {
			backgroundColor: color
		});
	}
});