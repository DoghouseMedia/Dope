dojo.provide('dope.link._Base');
dojo.require('dijit._Widget');
dojo.require('dope._Contained');

dojo.declare('dope.link._Base', [dijit._Widget, dope._Contained], {
	href: '',
	title: '',
	
	postCreate: function() {
		this.inherited(arguments);

		dojo.connect(this.domNode, 'onclick', dojo.hitch(this, 'onClick'));
		
		/* Ensure dom's href is set so open in new tab works and cursor/pointer is applied */
		this.domNode.href = this.href;
	},
	onClick: function(e) {
		if (e) e.preventDefault();
		if (e) e.stopPropagation();
		if (e) dojo.stopEvent(e);
		return false;
	}
});