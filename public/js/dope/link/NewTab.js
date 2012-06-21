dojo.provide('dope.link.NewTab');
dojo.require('dope.link._Base');
dojo.require('dope.utils.Url');

dojo.declare('dope.link.NewTab', dope.link._Base, {
	href: '',
	title: '',
	
	onClick: function(e) {
		this.inherited(arguments);
	
		dojo.publish('/dope/layout/TabContainerMain/open', [{
			href: this.href,
			title: this.title || this.href,
			focus: true
		}]);
		
		return false;
	}
});