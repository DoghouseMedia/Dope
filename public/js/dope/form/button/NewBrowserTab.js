dojo.provide('dope.form.button.NewBrowserTab');
dojo.require('dojo.form.Button');

dojo.declare('dope.form.button.NewBrowserTab', dojo.form.Button, {
	postCreate: function() {
		this.inherited(arguments);
		this.domNode.target = '_blank';
	}
});