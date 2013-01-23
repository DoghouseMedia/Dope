dojo.provide('dope.notifications.VisualNotification');
dojo.require('dope.menu.Item');

dojo.declare('dope.notifications.VisualNotification', [dope.menu.Item], {
	message: 'Notification message',
	
	postCreate: function() {
		this.inherited(arguments);
		this.containerNode.innerHTML = this.message;
		dojo.publish('/dope/notifications/visualNotification/new', [this]);
	},
	
	destroy: function() {
		this.inherited(arguments);
		dojo.publish('/dope/notifications/visualNotification/destroy', [this]);
	}
});