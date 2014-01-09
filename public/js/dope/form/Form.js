dojo.provide('dope.form.Form');
dojo.require('dijit.form.Form');
dojo.require('dijit._Container');
dojo.require('dope._Contained');

dojo.declare('dope.form.Form', [
	dijit.form.Form,
	dijit._Container,
	dope._Contained
], {
	baseClass: 'dopeForm',
	handles: [],
	childrenChanging: 0,
	submitAfterChangeComplete: false,

	startup: function() {
		this.inherited(arguments);
		this.childrenChanging = 0;
		this.submitAfterChangeComplete = false;
		this.handles = [];
		
		/* 
		 * Prevent Webkit from validating fields,
		 * since it errors on hidden fields, eg.
		 * fields in other tabs...
		 */
		dojo.attr(this.domNode, 'novalidate', 'novalidate');
		
		/* Attach Children onChange */
		dojo.forEach(this.getDescendants(), dojo.hitch(this, '_setupChild'));
	},
	_setupChild: function(child) {
		this.handles.push(
			dojo.connect(child, 'onChange', dojo.hitch(this, 'onChildChange', child))
		);
	},
	onChildChange: function(changedChild, value) {
		if (! changedChild.noisy) {
			return;
		}
		
		dojo.forEach(this.getDescendants(), dojo.hitch(this, function(child) {
			if (child === changedChild) {
				return;
			}
			
			if (! child.onFormFieldChange) {
				return;
			}
			
			if (child.deaf) {
			  return;
			}
	        
			dojo.connect(child, 'onStoreComplete', dojo.hitch(this, 'onChildChangeComplete'));
			
		  this.childrenChanging++;
			child.onFormFieldChange(changedChild, value);
		}));
	},
	onChildChangeComplete: function() {
		if (this.childrenChanging > 0) {
			this.childrenChanging--;
		}
	  
//		if (this.submitAfterChangeComplete && !this.hasChildrenChanging()) {
//			this.submit();
//		}
    
		return this;
	},
	hasChildrenChanging: function() {
	  return (this.childrenChanging > 0);
	},
	onSubmit: function(e) {
		if (this.hasChildrenChanging()) {
			if (e) e.preventDefault();

            alert("Parts of the form are still loading. Try again in a few seconds.");
//			this.submitAfterChangeComplete = true;
	    
			return false;
		}
	  
		this.submitAfterChangeComplete = false;
	  
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