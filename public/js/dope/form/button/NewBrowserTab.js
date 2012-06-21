dojo.provide('dojo.form.button.NewBrowserTab');
dojo.require('dojo.form.Button');

dojo.declare('dojo.form.button.NewBrowserTab', dojo.form.Button, {
	postCreate: function() {
		this.inherited(arguments);
		this.domNode.target = '_blank';
	}
});