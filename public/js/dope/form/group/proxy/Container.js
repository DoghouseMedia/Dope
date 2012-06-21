dojo.provide('dope.form.group.proxy.Container');
dojo.require('dope.layout.ContentPane');
dojo.require('dope.form.group.proxy.fields.Local');
dojo.require('dope.form.group.proxy.fields.Foreign');
dojo.require('dijit._Templated');

dojo.declare('dope.form.group.proxy.Container', [dope.layout.ContentPane, dijit._Templated], {
	baseClass: 'dopeFormGroupProxyContainer',
	widgetsInTemplate: true,
	proxyEntity: null,
	localNode: null,
	foreignNode: null,
	originalContainerNode: null,
	templateString: '<div>' +
			'<div data-dojo-type="dope.layout.Buttons" data-dojo-attach-point="buttonsNode">' +
				'Do NOT forget to update the candidate info too! ' +
			'</div>' +
			'<div>' +
				'<div data-dojo-type="dope.form.group.proxy.fields.Local" data-dojo-attach-point="localNode">' +
					'<div data-dojo-attach-point="containerNode"></div>' +
				'</div>' +
				'<div data-dojo-type="dope.form.group.proxy.fields.Foreign" data-dojo-attach-point="foreignNode">' +
				'</div>' +
			'</div>' +
		'</div>',
	
//	postscript: function() {
//		this.inherited(arguments);
//		this.originalContainerNode = dojo.clone(this.containerNode);
//		console.log(this.originalContainerNode.innerHTML);
//	},
//	startup: function() {
//		this.inherited(arguments);
//		dojo.forEach(dojo.query('> *', this.originalContainerNode),
//			dojo.hitch(this.foreignNode, 'addFieldByNode')
//		);
//	}
});