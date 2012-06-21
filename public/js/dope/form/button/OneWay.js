dojo.provide('dope.form.button.OneWay');
dojo.require('dope.form.Button');

dojo.declare('dope.form.button.OneWay', dope.form.Button, {
	href: '',
	
	onClick: function(e) {
		this.inherited(arguments);
		
		new dope.operation.xhrPost({
			title: 'Run command',
			url: new dope.utils.Url(this.href)
		});
		
//		if (closeCurrent) {
//			pane.getDijit().close(pane);
//		}
		
		if (e) dojo.stopEvent(e);
	}
});