dojo.provide('dope.entity.Teaser');
dojo.require('dope._Contained');
dojo.require('dijit._Widget');
dojo.require('dijit._Templated');
dojo.require('dijit._Container');

dojo.declare('dope.entity.Teaser', [dijit._Widget, dijit._Templated, dope._Contained, dijit._Container], {
	entity: null,
	entityLabel: '?',
	baseClass: 'dopeEntityTeaser',
	widgetsInTemplate: true,
	isRemovable: false,
	
	onLoad: function() { /* Event */ },
	onRemove: function() { /* Event */ },
	
	templateString: '<div class="${baseClass}">'
			+ '<span class="title">${!entity.__toString}</span>'
			+ '<span class="remove"><div data-dojo-type="dijit.form.Button" data-dojo-attach-point="removeBtn">x</div></span>'
			+ '<div data-dojo-attach-point="containerNode"></div>'
		+ '</div>',
		
	postCreate: function() {
		this.inherited(arguments);
		
		if (this.isRemovable) {
			dojo.connect(this.removeBtn, 'onClick', dojo.hitch(this, 'onRemoveClick'));
		} else {
			this.removeBtn.destroy();
		}
		
		this.onLoad(this);
	},
	
	onRemoveClick: function() {
		new dope.dialog.Confirm({
			title: 'Remove ' + this.entityLabel + '?',
			content: "It's up to you to remove any email addresses and files associated to this " + this.entityLabel + ".",
			onExecute: dojo.hitch(this, function() {
				this.destroy();
				this.onRemove(this);
			})
		});
	}
});
