dojo.provide('dope.layout._ResizeParent');

dojo.declare('dope.layout._ResizeParent', null, {
	resize: function(changeSize, resultSize, notifyParent) {
		var r = this.inherited(arguments);
		if (notifyParent) {
			this.getParent().resize(); // get the parent to adjust any other children
		}
		return r;
	}
});