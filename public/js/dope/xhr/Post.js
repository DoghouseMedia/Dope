dojo.provide('dope.xhr.Post');
dojo.require('dope.xhr._Base');
dojo.require('dojo.io.iframe');

dojo.declare('dope.xhr.Post', dope.xhr._Base, {
	execute: function() {
		this.inherited(arguments);
		return dojo.xhrPost(this.options);
	}
});