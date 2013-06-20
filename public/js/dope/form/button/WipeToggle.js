dojo.provide('dope.form.button.WipeToggle');
dojo.require('dope.form.Button');

dojo.declare('dope.form.button.WipeToggle', dope.form.Button, {
	href: '',
	selector: '',
	autoHide: false,
	
	postCreate: function() {
		this.inherited(arguments);
		
		this.node = dojo.query(this.selector, this.getPane().domNode)[0];
		
		if (this.autoHide) {
			dojo.style(this.node, "display", "none");
			dojo.removeClass(this.node, 'visible');
		}
	},
	
	_hide: function() {
		dojo.fx.wipeOut({'node': this.node}).play();
		dojo.removeClass(this.node, 'visible');
	},
	
	_show: function() {
		dojo.fx.wipeIn({'node': this.node}).play();
		dojo.addClass(this.node, 'visible');
	},
	
	_isVisible: function() {
		return dojo.hasClass(this.node, 'visible');
	},
	
	onClick: function(e) {
		this.inherited(arguments);
		
		if (e) dojo.stopEvent(e);
		
		if (this._isVisible()) {
			this._hide();
		}
		else {
			this._show();
		}
	}
});