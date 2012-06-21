dojo.provide("dope.layout.TabContainerMain");
dojo.require("dope.layout.TabContainer");

dojo.declare('dope.layout.TabContainerMain', dope.layout.TabContainer, {
	tabStrip: true,
	useMenu: true,
	useSlider: true,
	region: 'center',
	
	startup: function() {
		dojo.subscribe('/dope/layout/TabContainerMain/open', dojo.hitch(this, 'open'));
		dojo.subscribe('/dope/layout/TabContainerMain/closeAll', dojo.hitch(this, 'closeAll'));
		dojo.subscribe('/dope/layout/TabContainerMain/closeChild', dojo.hitch(this, 'closeChild'));
		return this.inherited(arguments);
	},
	
	open: function(options) {
		/*
		 * @todo Put this somewhere else. eg, the notifier listeners.
		 */
		if (options.tab_id) {
			var tabNotExists = dojo.every(this.getChildren(), function(tab) {
				return (tab.get('tab_id') != options.tab_id);
			});
			if (! tabNotExists) {
				return;
			}
		}
		
		options.isTopPane = true;
		options.closable = true;
		return this.inherited(arguments);
	}
});
