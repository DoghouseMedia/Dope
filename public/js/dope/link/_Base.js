dojo.provide('dope.link._Base');
dojo.require('dijit._Widget');
dojo.require('dope._Contained');

dojo.declare('dope.link._Base', [dijit._Widget, dope._Contained], {
	postCreate: function() {
		this.inherited(arguments);
		dojo.connect(this.domNode, 'onclick', dojo.hitch(this, 'onClick'));
	},
	onClick: function(e) {
		if (e) e.preventDefault();
		if (e) e.stopPropagation();
		if (e) dojo.stopEvent(e);
		return false;
	}
});