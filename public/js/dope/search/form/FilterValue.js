dojo.provide("dope.search.form.FilterValue");
dojo.require("dope.form.StoreBox");
dojo.require("dope.data.ItemFileReadStore");

dojo.declare('dope.search.form.FilterValue', [
	dijit.layout._LayoutWidget,
	dijit._Templated
], {
	baseClass: 'dopeSearchFormFilterValue',
	_isLoaded: false,
	_value: null,
	filter: null,
	widgetsInTemplate: true,
	templateString: '<div>'
		+ '<div dojoType="dope.form.StoreBox" dojoAttachPoint="valueSelect"></div>'
		+ '</div>',
	
	isFirst: function() {
		return (this.params.i == 0);
	},
	postCreate: function() {
		this.inherited(arguments);
		
		switch(this.params.type) {
			case 'enum':
				var storeUrl = new dope.utils.Url('/datastore/field', {
					modelAlias: this.params.modelAlias,
					fieldName: this.params.key
				});
				break;
			case 'yesno':
				var storeUrl = new dope.utils.Url('/datastore/yesno');
				break;
			default:
				var storeUrl = new dope.utils.Url('/' + this.params.key + '/autocomplete');
				break;
		}
		
		var dataStore = new dope.data.ItemFileReadStore({
			url: storeUrl
		});
		
		this.valueSelect
			.set('pageSize', 20)
			.set('_value', this._value)
			.set('onCompleteCallback', dojo.hitch(this, 'onDataStoreLoad'))
			.setStore(dataStore);
		
		//dataStore.fetch({onComplete: dojo.hitch(this, 'onDataStoreLoad')});
		
		if (! this.isFirst()) {
			dojo.place(dojo.create('div', {
				className: 'separator'
			}), this.domNode, 'first');
			
			var removeBtn = new dope.form.Button({
				onClick: dojo.hitch(this, 'remove'),
				label: 'x',
				className: 'remove'
			});
			dojo.place(removeBtn.domNode, this.domNode);
		}
	},
	onDataStoreLoad: function() {
		this._isLoaded = true;
		//this.assignValue();
		this.params.filter.onValueLoad();
	},
	isLoaded: function() {
		return this._isLoaded;
	},
	getValue: function() {
		return this.valueSelect.get('value');
	},
	setValue: function(value) {
		this._value = value;
		this.assignValue();
		return this;
	},
	assignValue: function() {
		this.valueSelect.set('value', this._value);
		return this;
	},
	remove: function(e) {
		//dojo.destroy(this.dom);
		this.destroyRendering();
		
		this.params.filter.removeValue(this);
		dojo.publish('/dope/search/form/domChange');
		
		dojo.stopEvent(e);
	}
});