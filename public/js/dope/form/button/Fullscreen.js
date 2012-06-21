dojo.provide('dope.form.button.Fullscreen');
dojo.require('dope.form.Button');

dojo.declare('dope.form.button.Fullscreen', dope.form.Button, {
	href: '',
	
	onClick: function(e) {
		this.inherited(arguments);
		
		window.location.href = this.href;
		
		if (e) dojo.stopEvent(e);
	}
});