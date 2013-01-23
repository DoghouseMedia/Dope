dojo.provide('dope.dialog.Dialog');
dojo.require('dijit.Dialog');
dojo.require('dijit.layout._LayoutWidget');

dojo.declare('dope.dialog.Dialog', [dijit.Dialog, dijit.layout._LayoutWidget], {
	onExecute: function() { /* Event */ },
	confirmText: 'OK',
	preload: true,
	showOnCreate: true,
	widgetsInTemplate: true,
	title: 'Are you sure?',
	templateString: '<div class="dijitDialog" role="dialog" aria-labelledby="${id}_title">'
		+ '<div dojoAttachPoint="titleBar" class="dijitDialogTitleBar">'
		+ '<span dojoAttachPoint="titleNode" class="dijitDialogTitle" id="${id}_title"></span>'
		+ '<span dojoAttachPoint="closeButtonNode" class="dijitDialogCloseIcon" dojoAttachEvent="ondijitclick: onCancel" title="${buttonCancel}" role="button" tabIndex="-1">'
		//	+ '<span dojoAttachPoint="closeText" class="closeText" title="${buttonCancel}">x</span>'
		+ '</span>'
		+ '</div>'
		+ '<div class="dijitDialogPaneContent">'
			+ '<div dojoAttachPoint="containerNode"></div>'
			+ '<div data-dojo-type="dijit.form.Button" data-dojo-attach-point="confirmButtonNode" tabIndex="-5">${confirmText}</div>'
		+ '</div>'
		+ '</div>',
	
	_onLoadHandler: function() {
		this.inherited(arguments);
		this._position(); // re-center after loading
	},
		
	postCreate: function() {
		this.inherited(arguments);
		
		if (this.showOnCreate) {
			this.show();
		}
	},
	
	startup: function() {
		this.inherited(arguments);
		dojo.connect(
			dijit.byId(this.confirmButtonNode.id), 
			'onClick', 
			dojo.hitch(this, '_onExecute')
		);
	},
	
	_onExecute: function(e) {
		this.onExecute(e);
	}
});