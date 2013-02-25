dojo.provide('dope.Entity');
dojo.require('dope.store.JsonRest');

dojo.declare('dope.Entity', [], {
	_entityKey: null,
	
	constructor: function(entityKey, options) {
		this.merge(options);
		this._entityKey = entityKey;
	},
	
	getStore: function() {
		if (this._entityKey in dope.entity.stores) {
		    //
		} else {
			dope.entity.stores[this._entityKey] = new dope.store.JsonRest({
				target: "/" + this._entityKey + "/"
			});
		}
		
		return dope.entity.stores[this._entityKey];
	},
	
	load: function(id) {
		if (id) {
			this.id = id;
		}
		return this.getStore().get(this.id).then(dojo.hitch(this, 'merge'));
	},
	
	save: function() {
		return this.getStore().put(this).then(dojo.hitch(this, 'merge'));
	},
	
	merge: function(entity) {
		dojo.mixin(this, entity);
	},
	
	delete: function() {
		return this.getStore().remove(this.id);
	}
});
dope.entity.stores = [];