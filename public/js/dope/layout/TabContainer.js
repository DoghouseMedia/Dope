dojo.provide("dope.layout.TabContainer");
dojo.require("dijit.layout.TabContainer");

dojo.declare('dope.layout.TabContainer', dijit.layout.TabContainer, {
	onShow: function() {
		this.resize();
		this.inherited(arguments);
	},
	open: function(options) {
		var contentPane = new dope.layout.ContentPane(options);
		
		this.addChild(contentPane);
		
		if (options.focus) {
			this.selectChild(contentPane);
		}
		
		return contentPane;
	},
	selectChild: function(tab) {
		/*
		 * Using a minimal delay fixes the bug in Chrome where 
		 * the browser scrolls back to the top of the grid!! 
		 * 
		 * @todo Unfortunately, this breaks the selectChild() logic
		 * which should return a promise for tab with an href.
		 * Figure out what's making Chrome behave like that in dojo core,
		 * and fix that, instead of hacking this. Also, Dojo might have fixed it by now.
		 * At time of writing, there's a bug that hides the grid when changing tabs,
		 * so it's impossible to test.
		 */
		//setTimeout(dojo.hitch(this, "inherited", arguments), 100);
		
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