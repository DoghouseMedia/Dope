dojo.provide("dope.entity._Base");

dojo.declare('dope.entity._Base', null, {
	_dndSubscribeHandle: null,
	pane: null,
	
	constructor: function(pane) {
		this.pane = pane;
		this.subscribe('delete', dojo.hitch(this, '_onDelete'));
	},
	subscribe: function(action, callback) {
		var subscribeChannel = '/dope/model/' + action + '/'
			+ this.pane.getUrl().get('controller')
			+ '-' + this.pane.getUrl().get('id');
		
		dojo.subscribe(subscribeChannel, callback);
	},
	_onDelete: function(args) {
		alert("I've been deleted! Bye!");
		this.pane.close();
	},
	getAlias: function() {
		return this.__proto__.declaredClass;
	},
	getPane: function() {
		return this.pane;
	},
	titleFormatter: function(row) {
		/*
		var msg = '[TRSW] Missing record formatter (' + this.getAlias() + ')';
		snowwhite.sendMail('jonathan@dhmedia.com.au', msg, msg);
		*/
		return snowwhite.ucfirst(this.getAlias()) + ' #' + row.id;
	},
	defaultAction: function() {
		dojo.query('.tool-action').style('display', 'none');
	},
	indexAction: function() {
		
	},
	browseAction: function() {
		
	},
	addAction: function() {
		
	},
	editAction: function() {
		
	},
	readAction: function() {
		
	},
	dndAction: function() {
		
	}
});