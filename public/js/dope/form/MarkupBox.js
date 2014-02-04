dojo.provide('dope.form.MarkupBox');
dojo.require('dijit.form.NumberTextBox');
dojo.require('dope._Contained');

dojo.declare('dope.form.MarkupBox', [dijit.form.NumberTextBox, dope._Contained], {
	fieldSalary: null,
    fieldRecoup: null,
    fieldFee: null,
	
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
        if (! this.fieldSalary) {
            return;
        }

        if (this.fieldRecoup) {
            var includesSalary = true;
            var fieldCut = this.fieldRecoup;
        }
        else if (this.fieldFee) {
            var includesSalary = false;
            var fieldCut = this.fieldFee;
        }

        var ratio = Math.round(100 / (
            Number(this.fieldSalary.get('value')) / Number(fieldCut)
        ));
        var value = 0.01 * (ratio - (includesSalary ? 100 : 0));
		
		this.set('value', value);
	}
});