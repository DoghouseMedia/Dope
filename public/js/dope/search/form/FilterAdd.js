dojo.provide("dope.search.form.FilterAdd");
dojo.require("dijit.form.Select");

dojo.declare('dope.search.form.FilterAdd', dijit.form.Select, {
  form: null,
  
	onChange: function(newValue) {
		this.inherited(arguments);
		
		if (! newValue) {
			return;
		}
		
		dojo.publish('/dope/search/form/filterAddRequest', [
			this.form,
			this.getOptions(String(newValue))
		]);
		
		this.reset();
		this.domNode.blur();
	},
	/*
	 * We had to override _fillContent() in order to include
	 * the other "data-" attributes we wanted to pass from the HTML.
	 * 
	 * @todo Monitor bug report and rewrite accordingly 
	 * @see http://bugs.dojotoolkit.org/ticket/15045
	 */
	_fillContent: function(){
		// summary:
		//		Loads our options and sets up our dropdown correctly.  We
		//		don't want any content, so we don't call any inherit chain
		//		function.
		var opts = this.options;
		if(!opts){
			opts = this.options = this.srcNodeRef ? dojo.query(">",
						this.srcNodeRef).map(function(node){
							if(node.getAttribute("type") === "separator"){
								return { value: "", label: "", selected: false, disabled: false };
							}
							return {
								/*
								 * Dope FilterAdd Attributes
								 * 
								 * @todo Ideally, we probably should write this in a generic way
								 * that "maps" all "data-" attribtutes, and put it in dope.form.Select
								 */
								key: node.getAttribute("data-key"),
								type: node.getAttribute("data-type"),
								title: node.getAttribute("data-title"),
								modelAlias: node.getAttribute("data-modelAlias"),
								
								value: (node.getAttribute("data-" + dojo._scopeName + "-value") || node.getAttribute("value")),
										label: String(node.innerHTML),
								// FIXME: disabled and selected are not valid on complex markup children (which is why we're
								// looking for data-dojo-value above.  perhaps we should data-dojo-props="" this whole thing?)
								// decide before 1.6
										selected: node.getAttribute("selected") || false,
								disabled: node.getAttribute("disabled") || false
							};
						}, this) : [];
		}
		if(!this.value){
			this._set("value", this._getValueFromOpts());
		}else if(this.multiple && typeof this.value == "string"){
			this._set("value", this.value.split(","));
		}
		
		this.inherited(arguments);
	}
});