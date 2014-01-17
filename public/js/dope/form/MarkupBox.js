dojo.provide('dope.form.MarkupBox');
dojo.require('dijit.form.NumberTextBox');
dojo.require('dope._Contained');

dojo.declare('dope.form.MarkupBox', [dijit.form.NumberTextBox, dope._Contained], {
	fieldPay: null,
	fieldCut: null,
	
	postCreate: function() {
		this.inherited(arguments);
		
		dojo.subscribe(
			'/dope/form/markupAwareCurrencyTextBox/change', 
			dojo.hitch(this, 'onDependencyChange')
		);
	},

    onFocus: function() {
        this.inherited(arguments);
        alert("Do NOT edit this field directly");
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
		
		var ratio = Math.round(100 / ( 
			Number(this.fieldPay.get('value'))
			/
			Number(this.fieldCut.get('value'))
		));
		
		var value = 0.01 * (ratio - 100);
		
		this.set('value', value);
	}
});