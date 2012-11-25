dojo.provide('dope.report.Form');
dojo.require('dope.search.Form');

dojo.declare('dope.report.Form', dope.search.Form, {
	onSubmit: function(e) {
		return true;
	}
});