dojo.provide('dope.form.MarkupAwareCurrencyTextBox');
dojo.require('dijit.form.CurrencyTextBox');

dojo.declare('dope.form.MarkupAwareCurrencyTextBox', [dijit.form.CurrencyTextBox, dope._Contained], {
	markup_field: '',

    startup: function() {
        this.inherited(arguments);
        dojo.connect(this, 'onKeyUp', dojo.hitch(this, 'publishValue'));
        this.publishValue();
    },
    publishValue: function() {
        dojo.publish('/dope/form/markupAwareCurrencyTextBox/change', [this]);
    }
});