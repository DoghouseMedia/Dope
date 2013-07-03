dojo.provide('dope.form.Editor');
dojo.require('dijit.Editor');

dojo.declare('dope.form.Editor', dijit.Editor, {
	postCreate: function() {
		this.inherited(arguments);
		this.addStyleSheet('/css/font.css');
		this.hiddenNode = dojo.create('input', {
			type: 'hidden',
			name: this.name
		});
		dojo.place(this.hiddenNode, this.domNode);
		this.watch('value', this.updateHiddenField.bind(this));
	},
	updateHiddenField: function() {
		this.hiddenNode.value = this.get('value');
	},
	
	destroy: function() {
		/*
		 * Sometimes the toolbar has already been destroyed by us when
		 * dojo tries to delete it, so I've hacked a void method here.
		 * Lame.
		 */
		if (!this.toolbar) {
			this.toolbar = {
				destroyRecursive: function() {}	
			};
		}
		this.inherited(arguments);
	}
});