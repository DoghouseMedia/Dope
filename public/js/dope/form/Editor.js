dojo.provide('dope.form.Editor');
dojo.require('dijit.Editor');

dojo.declare('dope.form.Editor', dijit.Editor, {
	postCreate: function() {
		this.inherited(arguments);
		this.hiddenNode = dojo.create('input', {
			type: 'hidden',
			name: this.name
		}) ;
		dojo.place(this.hiddenNode, this.domNode);
	},
	onChange: function(e) {
		this.hiddenNode.value = this.get('value');
		this.inherited(arguments);
	},
});