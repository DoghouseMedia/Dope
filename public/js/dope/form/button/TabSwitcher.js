dojo.provide('dope.form.button.TabSwitcher');
dojo.require('dope.form.Button');

dojo.declare('dope.form.button.TabSwitcher', dope.form.Button, {
	tabId: null,
	tabDijit: null,
	tabContainerDijit: null,
	
	startup: function() {
		this.inherited(arguments);
		this.tabDijit = dijit.byId(this.tabId);
		this.tabContainerDijit = dijit.byId(this.tabContainerId);
	},
	
	onClick: function() {
		this.inherited(arguments);
		this.tabContainerDijit.selectChild(this.tabDijit);
	}
});