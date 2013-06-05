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
		
		/*
		 * React to _data change after tab load
		 */
		this.getPane().watch('_data', this.onDataChange.bind(this));
	},
	onDomChange: function() {
		this.getPane().resize();
	},
	onDataChange: function() {
		var formdata = this.getPane().getData('formdata');
		dojo.forEach(this.getChildren(), function(child) {
			if (child.name && formdata[child.name]) {
				child.set('value', formdata[child.name]);
			}
		});
		this.submit();
	},
	onSubmit: function(e) {
		/* Prevent the form from really submitting */
		if (e) e.preventDefault();
		
		/* Stop execution if the parent returns false */
		if (! this.inherited(arguments)) {
			return false;
		}
		
		var formdata = dojo.formToObject(this.domNode);
		var storeUrl = new dope.utils.Url(this.domNode.action, formdata);
		this.getPane().setData('formdata', formdata);
		
		dojo.publish('/dope/search/form/store/beforeFetch', [storeUrl]);
		
		/* Store */
		this.store = new dope.data.JsonRestStore({
			target: String(storeUrl)
		});
		
		dojo.publish('/dope/search/form/store/ready', [this]);
	},
	getStore: function() {
		return this.store;
	}
});