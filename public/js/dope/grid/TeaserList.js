dojo.provide('dope.grid.TeaserList');
dojo.require('dope.grid.DataList');
dojo.require('dope.entity.Teaser');

dojo.declare('dope.grid.TeaserList', dope.grid.DataList, {
	baseClass: 'dopeGridTeaserList',
	teaserWidgetClass: 'dope.entity.Teaser',
	teaserEntityLabel: null,
	
	onTeaserLoad: function() { /* Event */ },
	onTeaserRemove: function() { /* Event */ },
	
	postCreate: function() {
		this.inherited(arguments);
		
		dojo.create('h3', {
			innerHTML: this.teaserEntityLabel
		}, this.domNode, 'prepend');
	},
	
	getTeaser: function(item) {
		return new dojo.getObject('dope.entity.Teaser')({
			entity: item,
			entityLabel: this.teaserEntityLabel,
			isRemovable: this.hasRemovableTeasers,
			onLoad: dojo.hitch(this, 'onTeaserLoad'),
			onRemove: dojo.hitch(this, 'onTeaserRemove')
		});
	}
});
