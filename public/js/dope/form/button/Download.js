dojo.provide('dope.form.button.Download');
dojo.require('dope.form.Button');

dojo.declare('dope.form.button.Download', dope.form.Button, {
	href: '',
	
	onClick: function(e) {
		if (e) dojo.stopEvent(e);
		this.inherited(arguments);
		window.open(this.href);
		return false;
	}
});