dojo.provide("dope.search.form.FilterValues");
dojo.require("dijit._Widget");

dojo.declare('dope.search.form.FilterValues', dijit._Widget, {
	baseClass: 'dopeSearchFormFilterValues',
	
	startup: function() {
		this.values = dojo.fromJson(this.domNode.innerHTML);
		//console.log(this.values, 'FILTER VALUES');
//		dojo.forEach(this.getOptions(), function(option) {
//			if (option.value) {
//				var optionParts = option.value.split('-');
//				if (optionParts[0] && dojo.exists(optionParts[0], filterAddValues)) {
//					var filter = filters.add({
//						key: optionParts[0],
//						type: optionParts[1],
//						modelAlias: optionParts[2],
//						title: option.label,
//						doNotAddValue: true
//					});
//					
//					var valueStrings = dojo.getObject(optionParts[0], false, filterAddValues);
//					if (! dojo.isArray(valueStrings)) {
//						values = [valueStrings];
//					}
//					dojo.forEach(valueStrings, function(valueString) {
//						var valueStringParts = valueString.split(':');
//						var values = valueStringParts[2].split(',');
//						
//						filter.setOperator(valueStringParts[0] + ':' + valueStringParts[1]);
//						
//						dojo.forEach(values, function(value) {
//							filter.addValue(value);
//						});
//					});
//				}
//			}
//		});
	}
});