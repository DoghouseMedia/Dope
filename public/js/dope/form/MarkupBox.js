dojo.provide('dope.form.MarkupBox');
dojo.require('dijit.form.NumberTextBox');
dojo.require('dope._Contained');

dojo.declare('dope.form.MarkupBox', [dijit.form.NumberTextBox, dope._Contained], {
	disabled: true,
	fieldPay: null,
	fieldCut: null,
	
	postCreate: function() {
		this.inherited(arguments);
		
		dojo.subscribe(
			'/dope/form/markupAwareCurrencyTextBox/change', 
			dojo.hitch(this, 'onDependencyChange')
		);
	},
	
	onDependencyChange: function(dependencyFormElement) {
		if (dependencyFormElement.getPane() == this.getPane()) {
			this[dependencyFormElement.get('markup_field')] = dependencyFormElement;
			this.calculateMarkup();
		}
	},
	
	calculateMarkup: function() {
		if (! this.fieldPay || ! this.fieldCut) {
			return;
		}
		
		var value = 0.01 * (100 / Math.round(
			Number(this.fieldPay.get('value'))
			/
			Number(this.fieldCut.get('value'))
		));
		
		this.set('value', value);
	}
});