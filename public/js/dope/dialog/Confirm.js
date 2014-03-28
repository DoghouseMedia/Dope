dojo.provide('dope.dialog.Confirm');
dojo.require('dope.dialog.Dialog');
dojo.require('dijit._Templated');
dojo.require('dijit.form.Button');

dojo.declare('dope.dialog.Confirm', [dope.dialog.Dialog, dijit._Templated], {
	onCancel: function() { /* Event */ },
	
	widgetsInTemplate: true,
	title: 'Are you sure?',
	templateString: '<div class="dijitDialog" role="dialog" aria-labelledby="${id}_title">'
		+ '<div dojoAttachPoint="titleBar" class="dijitDialogTitleBar">'
		+ '<span dojoAttachPoint="titleNode" class="dijitDialogTitle" id="${id}_title"></span>'
		+ '<span dojoAttachPoint="closeButtonNode" class="dijitDialogCloseIcon" dojoAttachEvent="ondijitclick: onCancel" title="${buttonCancel}" role="button" tabIndex="-1">'
//			+ '<span dojoAttachPoint="closeText" class="closeText" title="${buttonCancel}">x</span>'
		+ '</span>'
		+ '</div>'
		+ '<div class="dijitDialogPaneContent">'
			+ '<div dojoAttachPoint="containerNode"></div>'
			+ '<div data-dojo-type="dijit.form.Button" data-dojo-attach-point="confirmButtonNode" tabIndex="-5">OK</div>'
			+ '<div data-dojo-type="dijit.form.Button" data-dojo-attach-point="cancelButtonNode" tabIndex="-4">Cancel</div>'
		+ '</div>'
		+ '</div>',
	
	startup: function() {
		this.inherited(arguments);
        if (this.cancelButtonNode) {
            dojo.connect(
                dijit.byId(this.cancelButtonNode.id),
                'onClick',
                dojo.hitch(this, '_onCancel')
            );
        }
	},
	_onCancel: function(e) {
		this.onCancel(e);
	}
});