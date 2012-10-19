dojo.provide('dope._Contained');
dojo.require('dijit._Contained');

dojo.declare('dope._Contained', dijit._Contained, {
	getPane: function() {
		var current = this;
		var parent;
		
		while (current.domNode && current.getParent && (parent = current.getParent())) {
			if (parent.href) {
				return parent;
			}
			current = parent;
		}
		
		return false;
	}
});