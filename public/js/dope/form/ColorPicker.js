dojo.provide('dope.form.ColorPicker');
dojo.require('dope.widget.ColorPicker');
dojo.require('dijit._Widget');
dojo.require('dijit._Templated');
dojo.require('dope._Contained');

dojo.declare('dope.form.ColorPicker', [dijit._Widget, dijit._Templated, dope._Contained], {
	baseClass: 'dopeFormColorPicker',
	
	inputNode: null,
	colorPicker: null,
	
	widgetsInTemplate: true,
	templateString: '<div>'
		+ '<input type="hidden" name="color" data-dojo-attach-point="inputNode">'
		+ '<div data-dojo-type="dope.widget.ColorPicker" data-dojo-attach-point="colorPicker"></div>'
		+ '</div>',
	
	postCreate: function() {
		this.inherited(arguments);
		
		/* Set default color */
		if (this.srcNodeRef.value) {
			this.colorPicker.setColor(this.srcNodeRef.value);
		}
		
		/* Set form element name */
		if (this.srcNodeRef.name) {
			this.inputNode.name = this.srcNodeRef.name;
		}
		
		/* Update form value on color change */
		dojo.connect(this.colorPicker, 'onChange', dojo.hitch(this, 'setFormColorValue'));
	},
	
	setFormColorValue: function(color) {
		this.inputNode.value = color;
	}
});