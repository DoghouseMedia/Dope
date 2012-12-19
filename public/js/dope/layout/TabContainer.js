dojo.provide("dope.layout.TabContainer");
dojo.require("dijit.layout.TabContainer");
dojo.require('dope.layout._ResizeParent');

dojo.declare('dope.layout.TabContainer', [
	dijit.layout.TabContainer,                          
	dope.layout._ResizeParent
], {
	onShow: function() {
		this.resize();
		this.inherited(arguments);
	},
	open: function(options) {
		var contentPane = new dope.layout.ContentPane(options);
		
		this.addChild(contentPane);
		
		if (options.focus) {
			/*
			 * Using a minimal delay fixes the bug in Chrome where 
			 * the browser scrolls back to the top of the grid!! 
			 */
			setTimeout(dojo.hitch(this, "selectChild", contentPane), 100);
		}
		
		return contentPane;
	},
	selectChild: function(tab) {
		var ret = this.inherited(arguments);
		this.resize();
		return ret;
	},
	closeAll: function() {
		/*
		 * Close each tab and add a delay between each call to closeChild()
		 * to keep the server happy.
		 * 
		 * We can't use $i for the delay since some of the children
		 * aren't tabs which can cause the first tab to have too much
		 * delay when closing, i.e. `i > 0`.
		 */
		var container = this;
		var numTabs = 0;
		dojo.forEach(this.getChildren(), function(tab, i) {
			if (tab.closable) {
				setTimeout(
					function() {
						container.closeChild(tab);
					},
					numTabs*100
				);
				numTabs++;
			}
		});
	}
});