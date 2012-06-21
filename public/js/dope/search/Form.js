dojo.provide('dope.search.Form');
dojo.require('dope.form.Form');
dojo.require('dope.data.JsonRestStore');
dojo.require('dope.search.form.Filters');
dojo.require('dope.utils.Url');

dojo.declare('dope.search.Form', dope.form.Form, {
	store: null,
	baseClass: 'dopeSearchForm',
	
	startup: function() {
		this.inherited(arguments);
		/*
		 * @todo This will react to all form changes,
		 * but we only want to react to changes in this tab!
		 */
		dojo.subscribe('/dope/search/form/domChange', this, 'onDomChange');
	},
	onDomChange: function() {
		this.getParent().resize();
	},
	onSubmit: function(e) {
		/* Prevent the form from really submitting */
		if (e) e.preventDefault();
		
		/* Stop execution if the parent returns false */
		if (! this.inherited(arguments)) {
			return false;
		}
		
		/* Store */
		this.store = new dope.data.JsonRestStore({
			target: String(new dope.utils.Url(this.domNode.action, 
				dojo.formToObject(this.domNode)
			))
		});
		
		dojo.publish('/dope/search/form/store/ready', [this]);
	},
	getStore: function() {
		return this.store;
	}
});