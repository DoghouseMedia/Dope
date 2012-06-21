dojo.provide('dope.form.Form');
dojo.require('dijit.form.Form');
dojo.require('dope._Contained');

dojo.declare('dope.form.Form', [
	dijit.form.Form,
	dope._Contained
], {
	baseClass: 'dopeForm',
	handles: [],
	
	startup: function() {
		this.inherited(arguments);
		
		this.handles = [];
		
		/* 
		 * Prevent Webkit from validation fields,
		 * since it errors on hidden fields, eg.
		 * fields in other tabs...
		 */
		dojo.attr(this.domNode, 'novalidate', 'novalidate');
		
		/* Attach Children onChange */
		dojo.forEach(this.getChildren(), dojo.hitch(this, '_setupChild'));
	},
	_setupChild: function(child) {
		this.handles.push(
			dojo.connect(child, 'onChange', dojo.hitch(this, 'onChildChange', child))
		);
	},
	onChildChange: function(changedChild, value) {
		dojo.forEach(this.getChildren(), function(child) {
			if (child === changedChild) return;
			
			if (child.onFormFieldChange) {
				child.onFormFieldChange(changedChild, value);
			}
		});
	},
	onSubmit: function(e) {
		this.inherited(arguments);
		
		/* Prevent the form from really submitting */
		if (e) e.preventDefault();
		
		return this.validate();
	},
	validate: function() {
		var isValid = true;
		
		dojo.forEach(this.getDescendants(), function(widget){
            /*
             * Need to set this so that "required" widgets get their state set.
             * @todo Check if our refactoring has removed the need for this.
             */
            widget._hasBeenBlurred = true;
            
            if (!widget.disabled && widget.validate && !widget.validate()) {
            	isValid = false;
            }
        });
		
		return isValid;
	},
	destroy: function() {
		dojo.forEach(this.handles, dojo.disconnect);
		return this.inherited(arguments);
	}
});