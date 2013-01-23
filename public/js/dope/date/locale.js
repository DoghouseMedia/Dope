dojo.provide('dope.date.locale');
dojo.require('dojo.date.locale');

dojo.declare('dope.date.locale', null);

dope.date.locale = dojo.date.locale;

var _parentGetGregorianBundle = dojo.date.locale._getGregorianBundle.bind({});

dojo.date.locale._getGregorianBundle = function() {
	var bundle = _parentGetGregorianBundle.call(arguments);
	bundle['dateFormat-iso'] = 'yyyy-MM-dd';
	bundle['timeFormat-iso'] = 'hh:mm:ss'
	bundle['dateTimeFormat-iso'] = '{1}T{0}';
	return bundle;
};