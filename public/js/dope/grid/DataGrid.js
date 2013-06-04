dojo.provide('dope.grid.DataGrid');
dojo.require('dojox.grid.DataGrid');
dojo.require('dope._Contained');
dojo.require('dope.data.JsonRestStore');
dojo.require('dope.utils.Url');
dojo.require('dope.dialog.Confirm');

dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dope.grid.enhanced.plugins.OptionsBar");

dojo.declare('dope.grid.DataGrid', [dojox.grid.EnhancedGrid, dope._Contained], {
	baseClass: 'dopeGridDataGrid',
	region: 'center',
	query: '',
	rowsPerPage: 40,
	clientSort: false,
	delayScroll: true,
	selectionMode: 'single',
	loadingMessage: 'Loading...',
	noDataMessage: 'No results',
	autoload: true,
	storeController: null,
	
	plugins: {
		optionsbar: {}
	},

	postCreate: function() {
		this.inherited(arguments);
		dojo.connect(this, 'onRowClick', dojo.hitch(this, '_onRowClick'));
	},
	
	startup: function() {
		/* @see http://mail.dojotoolkit.org/pipermail/dojo-interest/2010-March/044230.html */
		this.formatterScope = this;
		
		this.inherited(arguments);
		
		if (this.getPane() && this.getPane().getUrl) {
			if (!this.storeUrl && (this.autoload || this.getPane().getUrl().get('sender'))) {
				this.storeUrl = this.getPane().getUrl();
				this.storeUrl.remove('sender');
				if (this.storeController) {
					this.storeUrl.setController(this.storeController);
				}
			}
			
			if (this.storeUrl) {
				this.setStoreByUrl(this.storeUrl);
			}
		}
		
		this.subscribe('/dope/entity/form/add', dojo.hitch(this, 'onEntityAdd'));
	},
	setStoreByUrl: function(url) {
		this.setStore(new dope.data.JsonRestStore({
			target: String(this.storeUrl)
		}));
	},
	setStore: function(store) {
		dojo.connect(store, 'onDelete', dojo.hitch(this, 'onStoreChange'));
		
		if (this.getPane() && this.getPane().count) {
			this.getPane().count
				.addUrl(store.target)
				.refresh();
		}
		
		return this.inherited(arguments);
	},
	onStoreChange: function() {
		if (this.getPane() && this.getPane().count) {
			this.getPane().count.refresh();
		}
	},
	onEntityAdd: function(form) {
		if (form.getPane() == this.getPane()) {
			/*
			 * Refresh the grid
			 * 
			 * We're using an internal dojo method (see the underscore?) which might break
			 * or not be backward-compatible with a future release. For now, it works fine.
			 */
			this._refresh();
			this.onStoreChange();
		}
	},
	_onRowClick: function(e) {
		var item = this.getItem(e.rowIndex);
		var url = new dope.utils.Url(this.store.target);
		url.setAction(item.id);
		url.removeSearch();
		
		this.getPane().onGridRowClick({
			id: item.id,
			href: String(url),
			title: this._getTitleByItem(item),
			e: e
		});
	},
	_getTitleByItem: function(item) {
		if (this.stringify) {
			var toStringParts = [];
			dojo.forEach(this.stringify, function(fieldname) {
				toStringParts.push(item[fieldname]);
			});
			return toStringParts.join(' ');
		}
		else {
			var url = new dope.utils.Url(this.store.target);
			return url.getController() + ' #' + item.id;
		}
		
	},
	
	/* ---------- Formatters ---------- */
	
	/*
	 * DateFormatter
	 * 
	 * Dates in the Dojo + MySQL world are annoying.
	 * Currently MySQL stores dates in a format annoyingly close to the ISO8601 used by Dojo.
	 * This means we need to convert.
	 * 
	 * This converter here is for Dojo Grids. You can specify a formatter for a column,
	 * all it needs to do is take the variable in and output it again.
	 *   
	 * For every day use, it might be easier if we use MySQL's date formatting functions
	 * to handle the conversions. We should standardize somewhere. On something...
	 *  
	 * @todo: Standardize date formatting code across the application. 
	 */
	dateFormatter: function(date) {
		switch (typeof date) {
			case 'object':
				if (date) {
					date = date.date;
				}
			case 'string':
				if (date && date.match(/^\d\d\d\d-\d\d-\d\d/)) {
					// 20 May 2001
					return dojo.date.locale.format(
						dojo.date.stamp.fromISOString(date.replace(" ","T")), {
							datePattern: "d MMM y",
							selector: "date"
						}
					);
					break;
				}
			default:
				return '-';
				break;
		}
	},
	datetimeFormatter: function(date) {
		switch (typeof date) {
			case 'object':
				if (date) {
					date = date.date;
				}
			case 'string':
				if (date && date.match(/^\d\d\d\d-\d\d-\d\d/)) {
					// 20 May 2001 @ 1:07pm
					return dojo.date.locale.format(
						dojo.date.stamp.fromISOString(date.replace(" ","T")), {
							datePattern: "d MMM y @ K:ma",
							selector: "date"
						}
					);
					break;
				}
			default:
				return '-';
				break;
		}
	},
	commentFormatter: function(comment) {
		return comment.toUpperCase();
	},
	boolFormatter: function(val) {
		return (val > 0) ? 'Y' : 'N';
	},
	btnRecordFormatter: function(simpleRecord) {
		return new dijit.form.Button({
			label: simpleRecord.string,
			onClick: function(e) {
				e.preventDefault();
				e.stopPropagation();
				
				dojo.publish('/dope/layout/TabContainerMain/open', [{
					href: simpleRecord.url,
					title: simpleRecord.string
				}]);
				
				return false;
			}
		});
	},
	/**
	 * Delete button formatter
	 * 
	 * @param item
	 * @param i
	 * @param row
	 * @returns {dijit.form.Button}
	 */
	btnDeleteFormatter: function(item, i, row) {
		if (item.editable===false) {
			return '-';
		}
		
		return new dijit.form.Button({
			label: "Delete",
			onClick: function(e) {
				dojo.stopEvent(e);
				
				new dope.dialog.Confirm({
					onExecute: function() {
						/* 
						 * Bloody Dojo &^%$#(*)&^ !!! 
						 * or my misunderstanding of it...
						 * 
						 * If you don't call delete() through hitch(), it conflicts with 
						 * the core/native javascript delete() method in Webkit and IE browsers...
						 */
						dojo.hitch(row.grid.store.service, 'delete', item)();
						
						/* Remove the item from the grid */
						row.grid.store.deleteItem(item);
					}
				});
				
				return false;
			}
		});
	},
	btnRemoveFormatter: function(item, i, row) {
		return new dijit.form.Button({
			label: "Remove",
			onClick: function(e) {
				dojo.stopEvent(e);
				
				new dope.dialog.Confirm({
					onExecute: function() {
						var restUrl = new dope.utils.Url(row.grid.store.target, {
							'id': item.id
						});
						restUrl.setAction('unlink');
						restUrl.set(row.grid.query.sender, row.grid.query[row.grid.query.sender]);

						new dope.operation.xhrPost({
							title: 'Delete item from Grid',
							url: restUrl,
							load: function(){
								/* Remove the item from the grid */
								row.grid.store.deleteItem(item);
							}
						});
					}
				});
				
				return;
			}
		});
	}
});
