dojo.provide('dope.operation.xhrGet');
dojo.require('dope.operation._Xhr');
dojo.require('dope.xhr.Get');

dojo.declare('dope.operation.xhrGet', dope.operation._Xhr, {
	_getXhr: function(options) {
		return new dope.xhr.Get(options);
	}
});