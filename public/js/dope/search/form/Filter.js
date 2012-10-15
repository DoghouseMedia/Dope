dojo.provide("dope.search.form.Filter");
dojo.require("dope.search.form.FilterValue");
dojo.require('dijit.form.Select');
dojo.require('dijit.form.Button');
dojo.require('dijit.layout._LayoutWidget');
dojo.require('dijit._Templated');

dojo.declare('dope.search.form.Filter', [
	dijit.layout._LayoutWidget,
	dijit._Templated
],{
	baseClass: 'dopeSearchFormFilter',
	_isLoaded: false,
	values: [],
	widgetsInTemplate: true,
	templateString: '<div>'
		+ '<div dojoType="dijit.form.Button" title="Remove filter" dojoAttachPoint="removeBtn">x</div>'
		+ '<span class="text" dojoAttachPoint="titleNode"></span>'
		+ '<div dojoType="dijit.form.Select" dojoAttachPoint="operatorSelect"></div>'
		+ '<div class="container" dojoAttachPoint="valuesContainerNode"></div>'
		+ '<div dojoType="dijit.form.Button" class="addValue" title="Add condition" dojoAttachPoint="addValueBtn">+</div>'
		+ '</div>',
	
	postCreate: function() {
		this.inherited(arguments);
		
		this.values = [];
		
		this.titleNode.innerHTML =  this.title;
		
		this.operatorSelect.set('options', [
			{'value': 'has:and', 'label': 'AND', 'disabled': !this.allowHasAllOf()},
			{'value': 'has:or', 'label': 'OR', 'selected': true},
			{'value': 'hasnot:and', 'label': 'EXCLUDE'}
		]).reset();
		
		dojo.connect(this.addValueBtn, 'onClick', dojo.hitch(this, 'addValue'));
		dojo.connect(this.removeBtn, 'onClick', dojo.hitch(this, 'remove'));
	},
	startup: function() {
		this.inherited(arguments);
		/* Add first value selector */
		//if (! this.doNotAddValue) {
			this.addValue();
		//}
	},
	allowHasAllOf: function() {
		if (this.type == 'Doctrine_Relation_LocalKey') {
			return false;
		}
		
		if (this.type == 'enum') {
			return false;
		}
		
		return true;
	},
	setOperator: function(operatorValue) {
		this.operatorSelect.set('value', operatorValue);
	},
	getValues: function() {
		return this.values;
	},
	addValue: function(selectedValue) {
		var value = new dope.search.form.FilterValue({
			filter: this,
			key: this.key,
			modelAlias: this.modelAlias,
			type: this.type,
			i: this.values.length
		});
		dojo.place(value.domNode, this.valuesContainerNode);
		
		this.values.push(value);
		
		if (selectedValue) {
			value.setValue(selectedValue);
		}
		
		dojo.publish('/dope/search/form/domChange');
	},
	removeValue: function(value) {
		this.values.splice(this.values.indexOf(value), 1);
	},
	remove: function(doNotRemoveFromFilters) {
		dojo.destroy(this.domNode);
		this.filters.removeFilter(this);
		delete this;
	},
	getAsParams: function() {
		var urlValues = [];
		
		dojo.forEach(this.values, function(value) {
			urlValues.push(value.getValue());
		});

		return {
			key: this.key,
			value: this.operatorSelect.get('value') + ':' + urlValues.join(',')
		};
	},
	isLoaded: function() {
		return this._isLoaded;
	},
	onValueLoad: function() {
		if (! dojo.every(this.values, function(value) {
			return value.isLoaded();
		})) {
			return;
		}
		this._isLoaded = true;
		
		this.filters.onFilterLoad();
		return this;
	}
});