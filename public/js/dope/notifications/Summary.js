dojo.provide('dope.notifications.Summary');
dojo.require('dope.layout.MenuCounter');

dojo.declare('dope.notifications.Summary', dope.layout.MenuCounter, {
	'class': 'dopeNotificationsSummary',
	iconClass: 'icon-bullhorn',
	
	postCreate: function() {
		this.inherited(arguments);
		
		dojo.subscribe(
			'/dope/notifications/visualNotification/new', 
			dojo.hitch(this, 'addItem')
		);
		
		dojo.subscribe(
			'/dope/notifications/visualNotification/destroy', 
			dojo.hitch(this, 'removeItem')
		);
	}
});