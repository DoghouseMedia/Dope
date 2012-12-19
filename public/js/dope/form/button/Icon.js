dojo.provide('dope.form.button.Icon');
dojo.require('dope.form.Button');

dojo.declare('dope.form.button.Icon', dope.form.Button, {
	href: '',
	
	postCreate: function() {
		this.inherited(arguments);
		dojo.addClass(this.domNode, 'iconButton');
	},
	
	onClick: function(e) {
		if (e) dojo.stopEvent(e);
		this.inherited(arguments);
		
		
		
		return false;
	}
});