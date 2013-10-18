dojo.provide('dope.layout.Quicklinks');
dojo.require('dope.layout.ContentPane');

dojo.declare('dope.layout.Quicklinks', dope.layout.ContentPane, {
	baseClass: 'dopeLayoutQuicklinks',
	
	titleNode: null,
	menuNode: null,
	formPaneNode: null,
	yOffset: 0,
	legends: [],
	
	postCreate: function() {
		this.inherited(arguments);
		
		/*
		 * Assume the form contentPane is the "center" one.
		 * @todo refactor forms a little to make this less messy
		 */
		this.formPaneNode = dojo.query('.dijitContentPane[region="center"]', this.getPane().domNode)[0];
		this.titleNode = dojo.create('h3', {innerHTML: 'Quick links'}, this.domNode);
		this.menuNode = dojo.create('ul', {}, this.domNode);
		
		if (! this.formPaneNode) {
			return;
		}
		
		this.yOffset = (dojo.position(this.formPaneNode).h - dojo.contentBox(this.formPaneNode).h) / 2;
		
		/* Setup legends */
		this.legends = [];
		dojo.query('legend', this.formPaneNode).forEach(dojo.hitch(this, '_initLegend'));
		
		/* Determine active */
		dojo.connect(this.formPaneNode, 'onscroll', dojo.hitch(this, 'determineActive'));
		this.determineActive();
	},
	
	_initLegend: function(legendNode, index) {
		var li = dojo.create('li', {}, this.menuNode);
		var a = dojo.create('a', {
			innerHTML: legendNode.innerHTML,
			href: '#'
		}, li);
		
		dojo.connect(a, 'onclick', dojo.hitch(this, function() {
			this.setActive(index);
			this.moveTo(index);
		}));
		
		this.legends.push({
			legendNode: legendNode,
			a: a
		});
	},
	
	determineActive: function() {
		var cutoff = dojo.position(this.formPaneNode).y + this.yOffset;
		
		var currentLegendIndex = 0;
		
		this.legends.every(function(legend, index) {
			if (dojo.position(legend.legendNode).y > cutoff) {
				return false;
			}
			
			currentLegendIndex = index;
			return true;
		});
		
		this.setActive(currentLegendIndex);
	},
	
	setActive: function(legendIndex) {
		this.legends.forEach(function(legend, index) {
			if (index == legendIndex) {
				dojo.addClass(legend.a, 'active');
			}
			else {
				dojo.removeClass(legend.a, 'active');
			}
		});
	},
	
	moveTo: function(legendIndex) {
		this.formPaneNode.scrollTop = this.legends[legendIndex].legendNode.offsetTop - this.yOffset;
	}
});