dojo.provide("dope.grid.enhanced.plugins.OptionsBar");

dojo.require('dope.form.button.Download');

dojo.require("dijit.form.NumberTextBox");
dojo.require("dijit.form.Button");
dojo.require("dojox.grid.enhanced._Plugin");

dojo.requireLocalization("dojox.grid.enhanced", "Pagination");

dojo.declare("dope.grid.enhanced.plugins.OptionsBar", dojox.grid.enhanced._Plugin, {
	
	name: "optionsbar",
	
	init: function(){
		this.gh = null;
		this.nls = dojo.i18n.getLocalization("dojox.grid.enhanced", "Pagination");
		this.bar = new dope.grid.enhanced.plugins._OptionsBar(
			dojo.mixin(this.option, {plugin: this})
		);
	},
	
	destroy: function(){
		this.inherited(arguments);
		var g = this.grid;
		try{
			this.bar.destroy();
			this.bar = null;
			this.nls = null;
		}catch(e){
			console.warn("Bar.destroy() error: ", e);
		}
	}
});


dojo.declare("dope.grid.enhanced.plugins._OptionsBar", [dijit._Widget,dijit._Templated], {
	templatePath: dojo.moduleUrl("dope.grid","enhanced/templates/OptionsBar.html"),
		
	// pagination bar position - "bottom"|"top"
	position: "bottom",
	
	widgetsInTemplate: true,
	
	constructor: function(params){
		dojo.mixin(this, params);
		this.grid = this.plugin.grid;
	},
	
	postCreate: function(){
		this.inherited(arguments);
		var self = this;
		var g = this.grid;
		this.plugin.connect(g, "_resize", dojo.hitch(this, "_resetGridHeight"));
		this._originalResize = dojo.hitch(g, "resize");
		g.resize = function(changeSize, resultSize){
			self._changeSize = g._pendingChangeSize = changeSize;
			self._resultSize = g._pendingResultSize = resultSize;
			g.sizeChange();
		};
		this._placeSelf();
		
		/* Dope magic */
		this.exportBtn.set('disabled', 'disabled');
		dojo.connect(this.grid, '_setStore', dojo.hitch(this, 'onSetStore'));
		
		this.searchBox.destroy();
//		if (this.grid instanceof dope.search.DataGrid) {
//			this.searchBox.destroy();
//		} else {
//			dojo.connect(this.searchBox, 'onKeyUp', dojo.hitch(this, 'onRefine'));
//		}
	},
	
	onRefine: function(e) {
//		var storeUrl = new dope.utils.Url(this.grid.store.target);
//		storeUrl.set('query', '*' + this.searchBox.get('value') + '*');
//		
//		var store = new dope.data.JsonRestStore({
//			target: String(storeUrl)
//		});
//		
//		this.grid.setStore(store);
	},
	
	onSetStore: function() {
		var url = new dope.utils.Url(this.grid.store.target);
		url.setController(url.getController() + '.csv');
		
		this.exportBtn.set('disabled', '');
		this.exportBtn.set('href', String(url));
	},
	
	destroy: function(){
		this.inherited(arguments);
		this.grid.resize = this._originalResize;
	},
	
	_placeSelf: function(){
		// summary:
		//		Place options bar to a position.
		//		There are two options, top of the grid, bottom of the grid.
		var g = this.grid;
		var	position = dojo.trim(this.position.toLowerCase());
		switch(position){
			case "top":
				this.placeAt(g.viewsHeaderNode, "before");
				break;
			case "bottom":
			default:
				this.placeAt(g.viewsNode, "after");
				break;
		}
	},
	
	_resetGridHeight: function(changeSize, resultSize){
		// summary:
		//		Function of resize grid height to place this options bar.
		//		Since the grid would be able to add other element in its domNode, we have
		//		change the grid view size to place the options bar.
		//		This function will resize the grid viewsNode height, scorllboxNode height
		var g = this.grid;
		changeSize = changeSize || this._changeSize;
		resultSize = resultSize || this._resultSize;
		delete this._changeSize;
		delete this._resultSize;
		if(g._autoHeight){
			return;
		}
		var padBorder = g._getPadBorder().h;
		if(!this.plugin.gh){
			this.plugin.gh = dojo.contentBox(g.domNode).h + 2 * padBorder;
		}
		if(resultSize){
			changeSize = resultSize;
		}
		if(changeSize){
			this.plugin.gh = dojo.contentBox(g.domNode).h + 2 * padBorder;
		}
		var gh = this.plugin.gh,
			hh = g._getHeaderHeight(),
			ph = dojo.marginBox(this.domNode).h;
		if(typeof g.autoHeight == "number"){
			var cgh = gh + ph - padBorder;
			dojo.style(g.domNode, "height", cgh + "px");
			dojo.style(g.viewsNode, "height", (cgh - ph - hh) + "px");
			
			this._styleMsgNode(hh, dojo.marginBox(g.viewsNode).w, cgh - ph - hh);
		}else{
			var h = gh - ph - hh - padBorder;
			dojo.style(g.viewsNode, "height", h + "px");
			var hasHScroller = dojo.some(g.views.views, function(v){
				return v.hasHScrollbar();
			});
			dojo.forEach(g.viewsNode.childNodes, function(c, idx){
				dojo.style(c, "height", h + "px");
			});
			dojo.forEach(g.views.views, function(v, idx){
				if(v.scrollboxNode){
					if(!v.hasHScrollbar() && hasHScroller){
						dojo.style(v.scrollboxNode, "height", (h - dojox.html.metrics.getScrollbar().h) + "px");
					}else{
						dojo.style(v.scrollboxNode, "height", h + "px");
					}
				}
			});
			this._styleMsgNode(hh, dojo.marginBox(g.viewsNode).w, h);
		}
	},
	
	_styleMsgNode: function(top, width, height){
		var messagesNode = this.grid.messagesNode;
		dojo.style(messagesNode, {"position": "absolute", "top": top + "px", "width": width + "px", "height": height + "px", "z-Index": "100"});
	}
});

dojox.grid.EnhancedGrid.registerPlugin(dope.grid.enhanced.plugins.OptionsBar/*name:'optionsbar'*/);
