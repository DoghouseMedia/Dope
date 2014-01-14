dojo.provide('dope.form.button.DeleteSelf');
dojo.require('dope.form.Button');
dojo.require('dope.operation.xhrDelete');
dojo.require('dope.utils.Url');

dojo.declare('dope.form.button.DeleteSelf', dope.form.Button, {
	href: '',
	title: '',
	
	onClick: function(e) {
		this.inherited(arguments);

		var deleteUrl = new dope.utils.Url(this.href);
		
		new dope.operation.xhrDelete({
			title: 'Delete',
			url: String(deleteUrl)
		});
		
		dojo.publish('/dope/model/delete', {
			alias: deleteUrl.get('controller'),
			id: deleteUrl.get('action')
		});
		
		if (e) dojo.stopEvent(e);
	}
});