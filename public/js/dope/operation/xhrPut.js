dojo.provide('dope.operation.xhrPut');
dojo.require('dope.operation._Xhr');
dojo.require('dope.xhr.Put');

dojo.declare('dope.operation.xhrPut', dope.operation._Xhr, {
	_getXhr: function(options) {
		return new dope.xhr.Put(options);
	}
});