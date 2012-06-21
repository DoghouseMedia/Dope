dojo.provide('dope.xhr.Delete');
dojo.require('dope.xhr._Base');

dojo.declare('dope.xhr.Delete', dope.xhr._Base, {
	execute: function() {
		this.inherited(arguments);		
		return dojo.xhrDelete(this.options);
	}
});