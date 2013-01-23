dojo.provide('dope.notifications.Summary');
dojo.require('dijit.PopupMenuBarItem');

dojo.declare('dope.notifications.Summary', [dijit.PopupMenuBarItem], {
	baseClass: 'dopeNotificationsSummary',
	_orient: {'BR':'TR'},
	
	postCreate: function() {
		this.inherited(arguments);
		
		dojo.subscribe(
			'/dope/notifications/visualNotification/new', 
			dojo.hitch(this, 'onVisualNotificationNew')
		);
		
		dojo.subscribe(
			'/dope/notifications/visualNotification/destroy', 
			dojo.hitch(this, 'onVisualNotificationDestroy')
		);
	},
	
	startup: function() {
		this.inherited(arguments);
		this.popup._orient = {'BR':'TR'};
	},
	
	onVisualNotificationNew: function(visualNotification) {
		this.popup.addChild(visualNotification);
		this.updateLabel();
	},
	
	onVisualNotificationDestroy: function() {
		this.updateLabel();
	},
	
	updateLabel: function() {
		var count = this.popup.getChildren().length;
		this.set('label', count);
		if (count) {
			dojo.addClass(this.domNode, 'active');
		} else {
			dojo.removeClass(this.domNode, 'active');
		}
	}
	
});