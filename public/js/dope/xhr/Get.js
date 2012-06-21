dojo.provide('dope.xhr.Get');
dojo.require('dope.xhr._Base');

dojo.declare('dope.xhr.Get', dope.xhr._Base, {
	execute: function() {
		this.inherited(arguments);
		return dojo.xhrGet(this.options);
	}
});