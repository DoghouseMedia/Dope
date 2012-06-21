dojo.provide('dope.entity.form.Quick');
dojo.require('dope.entity.Form');
dojo.require('dope.operation.xhrPost');

dojo.declare('dope.entity.form.Quick', dope.entity.Form, {
	baseClass: 'dopeFormQuick',
	onChildChange: function(child, value) {
		this.inherited(arguments);
		
		var xhrUrl = new dope.utils.Url(this.action);
		xhrUrl.set(child.name, value ? String(value) : '0'); // default to 0 if no value
		
		new dope.operation.xhrPost({
			title: 'Update candidate',
			url: xhrUrl
		});
	}
});