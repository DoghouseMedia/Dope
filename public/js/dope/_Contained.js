dojo.provide('dope._Contained');
dojo.require('dijit._Contained');

dojo.declare('dope._Contained', dijit._Contained, {
	getPane: function() {
		var current = this;
		var parent;
		
		while (current.domNode && (parent = current.getParent())) {
			if (parent.href) {
				return parent;
			}
			current = parent;
		}
	}
});