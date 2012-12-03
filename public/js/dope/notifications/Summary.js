dojo.provide('dope.notifications.Summary');
dojo.require('dijit.PopupMenuBarItem');
dojo.require('dope.notifications.Notification');

dojo.declare('dope.notifications.Summary', [dijit.PopupMenuBarItem], {
	baseClass: 'dopeNotificationsSummary',
	_orient: {'BR':'TR'},
	
	postCreate: function() {
		this.inherited(arguments);
		
		dojo.subscribe(
			'/dope/notifications/notification/new', 
			dojo.hitch(this, 'onNotificationNew')
		);
	},
	
	startup: function() {
		this.inherited(arguments);
		this.popup._orient = {'BR':'TR'};
	},
	
	onNotificationNew: function(notification) {
		this.popup.addChild(notification);
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