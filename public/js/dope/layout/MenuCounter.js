dojo.provide('dope.layout.MenuCounter');
dojo.require('dijit.PopupMenuBarItem');

dojo.declare('dope.layout.MenuCounter', dijit.PopupMenuBarItem, {
	baseClass: 'dopeLayoutMenuCounter',
	_orient: {'BR':'TR'},
	
	buildRendering: function() {
		this.inherited(arguments);
		if (this.iconClass) {
			dojo.create('i', {className: this.iconClass}, this.domNode, 'prepend');
		}
	},
	
	startup: function() {
		this.inherited(arguments);
		this.popup._orient = {'BR':'TR'};
	},
	
	onItemNew: function(item) {
		if (this.popup) {
			this.popup.addChild(item);
		}
		this.updateLabel();
	},
	
	onItemDestroy: function() {
		this.updateLabel();
	},
	
	updateLabel: function() {
		if (this.popup) {
			var count = this.popup.getChildren().length;
			this.set('label', count);
			if (count) {
				dojo.addClass(this.domNode, 'active');
			} else {
				dojo.removeClass(this.domNode, 'active');
			}
		}
	}
});