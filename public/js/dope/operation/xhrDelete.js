dojo.provide('dope.operation.xhrDelete');
dojo.require('dope.operation._Xhr');
dojo.require('dope.xhr.Delete');

dojo.declare('dope.operation.xhrDelete', dope.operation._Xhr, {
	_getXhr: function(options) {
		return new dope.xhr.Delete(options);
	}
});