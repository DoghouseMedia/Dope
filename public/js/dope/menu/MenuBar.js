dojo.provide("dope.menu.MenuBar");
dojo.require("dijit.MenuBar");

dojo.declare('dope.menu.MenuBar', dijit.MenuBar, {
	_originalOrient: null,
	
	postCreate: function() {
		this.inherited(arguments);
		this._originalOrient = this._orient;
	},
	
	_openPopup: function(){
		this._orient = this.focusedChild._orient ?
			this.focusedChild._orient :
			this._originalOrient;
		
		return this.inherited(arguments);
	}
});