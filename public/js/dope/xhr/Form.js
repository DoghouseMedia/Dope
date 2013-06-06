dojo.provide('dope.xhr.Form');
dojo.require('dope.form.Form');
dojo.require('dope.operation.xhrGet');
dojo.require('dope.operation.xhrPut');
dojo.require('dope.operation.xhrPost');
dojo.require('dope.operation.xhrDelete');

dojo.declare('dope.xhr.Form', dope.form.Form, {
	uploader: null,
	
	onSubmit: function(e) {
		if (! this.inherited(arguments)) {
			return false;
		}
		
		/* Prevent the form from really submitting */
		if (e) e.preventDefault();
		
		/* Uploader handles things on its own */
		if (this.uploader) {
			return true;
		}		
		
		var params = {
			title: 'Submit form',
			form: this.domNode,
			load: dojo.hitch(this, 'onComplete')
		};
		
		switch (this.method.toLowerCase()) {
			case 'get':
				new dope.operation.xhrGet(params);
				break;
			case 'put':
				new dope.operation.xhrPut(params);
				break;
			case 'delete':
				new dope.operation.xhrDelete(params);
				break;
			default:
				new dope.operation.xhrPost(params);
				break;
		}
		
		return false;
	},
	onComplete: function(data, response) {
		this.inherited(arguments);
		//console.log('FORM COMPLETE', arguments);
	}
});