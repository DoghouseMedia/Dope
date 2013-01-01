dojo.provide('dope.layout.LoadingDisabler');
dojo.require('dijit._Widget');
dojo.require('dijit._Templated');

dojo.declare('dope.layout.LoadingDisabler', [dijit._Widget, dijit._Templated], {
	baseClass: 'dopeLoadingDisabler',
	templateString: '<div><div><p class="animated flash">Loading...</p></div></div>',
	
	postCreate: function() {
		this.deactivate();
		this.inherited(arguments);
	},
	show: function() {
		this.activate();
		this.inherited(arguments);
	},
	hide: function() {
		this.deactivate();
		this.inherited(arguments);
	},
	activate: function() {
		dojo.style(this.domNode, 'display', 'block');
		if (! dojo.hasClass(this.domNode, "active")) {
			dojo.addClass(this.domNode, "active");
		}
	},
	deactivate: function() {
		dojo.style(this.domNode, 'display', 'none');
		if (dojo.hasClass(this.domNode, "active")) {
			dojo.removeClass(this.domNode, "active");
		}
	}
});