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
	closeAll: function() {
		dojo.publish('/dope/layout/TabContainerMain/closeAll');
	},
	location: function() {
		window.location = this.url;
	},
	openExternal: function() {
		window.open(this.url);
	}
});