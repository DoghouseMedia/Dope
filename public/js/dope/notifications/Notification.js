dojo.provide('dope.notifications.Notification');
dojo.require('dope.menu.Item');

dojo.declare('dope.notifications.Notification', [dope.menu.Item], {
	message: 'Notification message',
	
	postCreate: function() {
		this.inherited(arguments);
		this.containerNode.innerHTML = this.message;
		dojo.publish('/dope/notifications/notification/new', [this]);
	}
});