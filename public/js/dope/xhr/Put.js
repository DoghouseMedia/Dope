dojo.provide('dope.xhr.Put');
dojo.require('dope.xhr._Base');

dojo.declare('dope.xhr.Put', dope.xhr._Base, {
	execute: function() {
		this.inherited(arguments);		
		return dojo.xhrPut(this.options);
	}
});