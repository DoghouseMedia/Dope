dojo.provide('dope.form.button.LoadSelf');
dojo.require('dope.form.Button');

dojo.declare('dope.form.button.LoadSelf', dope.form.Button, {
	href: '',
	
	onClick: function(e) {
		this.inherited(arguments);
		
		this.getPane().setUrl(
			new dope.utils.Url(this.href)
		);
		
		if (e) dojo.stopEvent(e);
	}
});