dojo.provide('dope.operation.xhrPost');
dojo.require('dope.operation._Xhr');
dojo.require('dope.xhr.Post');

dojo.declare('dope.operation.xhrPost', dope.operation._Xhr, {
	_getXhr: function(options) {
		return new dope.xhr.Post(options);
	}
});