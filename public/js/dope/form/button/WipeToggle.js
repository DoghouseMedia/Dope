dojo.provide('dope.form.button.WipeToggle');
dojo.require('dope.form.Button');

dojo.declare('dope.form.button.WipeToggle', dope.form.Button, {
	href: '',
	selector: '',
	
	onClick: function(e) {
		this.inherited(arguments);
		
		var node = dojo.query(this.selector, this.getPane().domNode)[0];
			
		if (dojo.hasClass(node, 'visible')) {
			dojo.fx.wipeOut({'node': node}).play();
		} else {
			dojo.fx.wipeIn({'node': node}).play();
		}
			
		dojo.toggleClass(node, 'visible');
		
		if (e) dojo.stopEvent(e);
	}
});