dojo.provide('dope.entity.Paginator');
dojo.require('dijit._Widget');
dojo.require('dijit._Templated');
dojo.require('dope._Contained');

dojo.declare('dope.entity.Paginator', [dijit._Widget, dijit._Templated, dope._Contained], {
	baseClass: 'dopeEntityPaginator',
	ids: {},
	widgetsInTemplate: true,
	/* @todo Move this (and other Dojo templates to their own HTML file) */
	templateString: '<div>'
		+ '<div' 
		+ ' data-dojo-type="dope.form.Button"'
		+ ' data-dojo-attach-point="btnFirst"'
		+ ' data-dojo-attach-event="onClick:onFirst"'
		+ '>&laquo;'
		+ '</div>'
		+ '<div' 
		+ ' data-dojo-type="dope.form.Button"'
		+ ' data-dojo-attach-point="btnPrev"'
		+ ' data-dojo-attach-event="onClick:onPrev"'
		+ '>&lt;'
		+ '</div>'
		+ '<span class="text">'
		+ '<span data-dojo-attach-point="indexNode">x</span>'
		+ '/'
		+ '<span data-dojo-attach-point="lengthNode">x</span>'
		+ '</span>'
		+ '<div' 
		+ ' data-dojo-type="dope.form.Button"'
		+ ' data-dojo-attach-point="btnNext"'
		+ ' data-dojo-attach-event="onClick:onNext"'
		+ '>&gt;'
		+ '</div>'
		+ '<div' 
		+ ' data-dojo-type="dope.form.Button"'
		+ ' data-dojo-attach-point="btnLast"'
		+ ' data-dojo-attach-event="onClick:onLast"'
		+ '>&raquo;'
		+ '</div>'
		+ '</div>',
	
	postCreate: function() {
		this.inherited(arguments);
		
		this.ids = {};
		this.id = null;
		this.indexOf = null;
		this.length = null;
		this.page = {
			first: null,
			prev: null,
			next: null,
			last: null
		};
		
		this.hide();
	},
	hide: function() {
		dojo.style(this.domNode, "display", "none");
	},
	show: function() {
		var nextNode = dojo.query("+ *", this.domNode);
		if (nextNode && nextNode[0] && dijit.byNode(nextNode[0])) {
			dojo.place(dojo.create('div', {className: 'separator'}), this.domNode, 'after');
		}
		
		dojo.fx.wipeIn({
			node: this.domNode,
			onEnd: this.getPane().resize.bind(this.getPane())
		}).play();
	},
	startup: function() {
		this.inherited(arguments);
		
		this.getPane().watch('_data', dojo.hitch(this, 'reloadData'));
		this.reloadData();
	},
	reloadData: function() {
		if (this.getPane() && this.getPane().getData() && this.getPane().getData('dope-entity-ids')) {
			this.ids = this.getPane().getData('dope-entity-ids');
			this.id = Number(this.getPane().getUrl().getAction());
			this.indexOf = this.ids.indexOf(this.id) >= 0
				? this.ids.indexOf(this.id)
				: this.ids.indexOf(String(this.id));
			this.length = this.ids.length;
			this.page = {
				first: this.ids[0],
				prev: this.ids[this.indexOf - 1],
				next: this.ids[this.indexOf + 1],
				last: this.ids[this.length - 1]
			};
			
			this.indexNode.innerHTML = this.indexOf + 1;
			this.lengthNode.innerHTML = this.length;
			
			if (this.id == this.page.first) {
				this.btnFirst.set('disabled', 'disabled');
				this.btnPrev.set('disabled', 'disabled');
			}
			if (this.id == this.page.last) {
				this.btnNext.set('disabled', 'disabled');
				this.btnLast.set('disabled', 'disabled');
			}			
			
			this.show();
		}
	},
	gotoId: function(id) {
		return this.getPane().setUrl(this.getPane().getUrl().setAction(
			id
		));
	},
	onFirst: function() {
		this.gotoId(this.page.first);
	},
	onPrev: function() {
		this.gotoId(this.page.prev);
	},
	onNext: function() {
		this.gotoId(this.page.next);
	},
	onLast: function() {
		this.gotoId(this.page.last);
	}
});