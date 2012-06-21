dojo.provide('dope.dnd.Source');
dojo.require('dojo.dnd.Source');

dojo.declare('dope.dnd.Source', dojo.dnd.Source, {
	action: null,
	
	markupFactory: function(params, node) {
        return new dope.dnd.Source(node, params);
    },
	onDropExternal: function(nodes, copy) {
		if (! this.action) {
			throw "action is not set";
		}
		
		var pane = dijit.byNode(nodes.anchor.children[0]).getPane();
		
		var xhrUrl = pane.getUrl()
			.setAction(this.action)
			.set('id', dojo.attr(copy[0], 'dndid'));
		
		new dope.operation.xhrPost({
			title: 'Save Drag & Drop',
			url: String(xhrUrl),
			load: function() {
				pane.count.refresh();
			}
		});
		
		return this.inherited(arguments);
	} 
});