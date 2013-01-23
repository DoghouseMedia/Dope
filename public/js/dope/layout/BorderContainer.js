dojo.provide('dope.layout.BorderContainer');
dojo.require('dijit.layout.BorderContainer');

dojo.declare('dope.layout.BorderContainer', [
	dijit.layout.BorderContainer
], {
	startup: function() {
		this.inherited(arguments);
		
		if (! this.getParent()._childOfLayoutWidget) {
			var width = 0;
			var height = 0;
			
			dojo.forEach(this.getChildren(), function(child) {
				var _width = dojo.position(child.domNode).w;
				var _height = dojo.position(child.domNode).h;
				
				switch (child.get('region')) {
					case 'top':
					case 'bottom':
						height += _height;
						break;
					case 'left':
					case 'right':
						width += _width;
						break;
					default:
						height += _height;
						width += _width;
						break;
				}
			});
			
			dojo.marginBox(this.domNode, {w: width, h: height});
		}
	}
});