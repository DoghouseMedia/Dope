dojo.provide('dope.link.NewWindow');
dojo.require('dope.link._Base');

dojo.declare('dope.link.NewWindow', dope.link._Base, {
	onClick: function(e) {
		this.inherited(arguments);
	
		window.open(this.href);
		
		return false;
	}
});