dojo.provide('dope.date.locale');
dojo.require('dojo.date.locale');

dojo.declare('dope.date.locale', null);

dope.date.locale = dojo.date.locale;

if (window.casper) {
	var _bundle = dojo.date.locale._getGregorianBundle();
	_bundle['dateFormat-iso'] = 'yyyy-MM-dd';
	_bundle['timeFormat-iso'] = 'hh:mm:ss';
	_bundle['dateTimeFormat-iso'] = '{1}T{0}';
	
	dojo.date.locale._getGregorianBundle = function() {	
		return _bundle;
	};
}
else {
	var _parentGetGregorianBundle = dojo.date.locale._getGregorianBundle.bind({});
	
	dojo.date.locale._getGregorianBundle = function() {
		var bundle = _parentGetGregorianBundle.call(arguments);
		bundle['dateFormat-iso'] = 'yyyy-MM-dd';
		bundle['timeFormat-iso'] = 'hh:mm:ss'
		bundle['dateTimeFormat-iso'] = '{1}T{0}';
		return bundle;
	}
}