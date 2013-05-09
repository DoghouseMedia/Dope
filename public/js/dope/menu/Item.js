dojo.provide("dope.menu.Item");
dojo.require("dijit.MenuItem");

dojo.declare('dope.menu.Item', dijit.MenuItem, {
	onClick: function(e) {
		this.inherited(arguments);
		dojo.hitch(this, this.action).call();
	},
	open: function() {
		dojo.publish('/dope/layout/TabContainerMain/open', [{
			href: this.url,
			title: dojo.trim(this.label),
			focus: true
		}]);
	},
	entityFormDialog: function(options) {
		new dope.dialog.EntityForm({
        	href: String(new dope.utils.Url(this.url, options))
        });
	},
	closeAll: function() {
		dojo.publish('/dope/layout/TabContainerMain/closeAll');
	},
	location: function() {
		window.location = this.url;
	}
});