dojo.provide('dope.link.NewTab');
dojo.require('dope.link._Base');
dojo.require('dope.utils.Url');

dojo.declare('dope.link.NewTab', dope.link._Base, {
	onClick: function(e) {
		this.inherited(arguments);

        if (TRED.tabManager) {
            TRED.tabManager.createTab(this.href);
        }
        else if (window.opener) {
            window.opener.TRED.tabManager.createTab(this.href);
        }
        else {
            alert("You closed the main app window, sorry!");
        }
		
		return false;
	}
});