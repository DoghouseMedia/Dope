dojo.provide('dope.grid.DataGrid');
dojo.require('dojox.grid.DataGrid');
dojo.require('dope._Contained');
dojo.require('dope.data.JsonRestStore');
dojo.require('dope.utils.Url');
dojo.require('dope.dialog.Confirm');

dojo.declare('dope.grid.DataGrid', [dojox.grid.DataGrid, dope._Contained], {
	baseClass: 'dopeGridDataGrid',
	region: 'center',
	query: '',
	rowsPerPage: 25,
	clientSort: false,
	delayScroll: true,
	selectionMode: 'single',
	loadingMessage: 'Loading...',
	noDataMessage: 'No results',
	autoload: true,
	storeController: null,
	
	startup: function() {
		/* @see http://mail.dojotoolkit.org/pipermail/dojo-interest/2010-March/044230.html */
		this.formatterScope = this;
		
		this.inherited(arguments);
		
		if (this.autoload || this.getPane().getUrl().get('sender')) {
			var storeUrl = this.getPane().getUrl();
			storeUrl.remove('sender');
			if (this.storeController) {
				storeUrl.setController(this.storeController);
			}
			this.setStore(new dope.data.JsonRestStore({
				target: String(storeUrl)
			}));
		}
		
		this.subscribe('/dope/entity/form/add', dojo.hitch(this, 'onEntityAdd'));
	},
	setStore: function(store) {
		dojo.connect(store, 'onDelete', dojo.hitch(this, 'onStoreChange'));
		
		this.getPane().count
			.addUrl(store.target)
			.refresh();
		
		return this.inherited(arguments);
	},
	onStoreChange: function() {
		this.getPane().count.refresh();
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
	onRowClick: function(e) {
		var id = this.getItem(e.rowIndex).id;
		var url = new dope.utils.Url(this.store.target);
		url.setAction(id);
		url.removeSearch();
		
		dojo.publish('/dope/layout/TabContainerMain/open', [{
			href: String(url),
			title: url.getController() + ' #' + id,
			focus: !e.ctrlKey,
			_data: this.getPane().getData()
		}]);
		
		return this.inherited(arguments);
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
	},
	btnDoneFormatter: function(item, i, row) {
		//Create a button programmatically:
		var btn = new dijit.form.ToggleButton({
			label: "Done",
			onClick: function(e) {
				e.preventDefault();
				e.stopPropagation();
				
				var restUrl = new dope.utils.Url(row.grid.store.target);
				
				var toggleState = ! btn.value;
				
				snowwhite.toggleButton(btn, toggleState, restUrl.get('controller'), item.id, 'is_done');
				
				return false;
			}
		});
		
		btn.attr('iconClass', "dijitCheckBoxIcon");
		btn.setValue(item.is_done);
		btn.setChecked(item.is_done);
		
		return btn;
	},
	btnEmailFileFormatter: function(item, i, row) {
		return;
		
		var url = new dope.utils.Url('/mailer', dojo.mixin({
			action: row.grid.query.send_to,
			files: item.id,
			format: 'html'
		}, row.grid.query));
		
		//Create a button programmatically:
		return new dijit.form.Button({
			label: "Email to " + row.grid.query.send_to,
			onClick: function(e) {
				e.preventDefault();
				e.stopPropagation();
				
				dojo.publish('/dope/layout/TabContainerMain/open', [{
					href: url,
					title: "Email " + item.filename
				}]);
				
				return false;
			}
		});
	},
	btnDownloadFormatter: function(item, i, row) {
		var btn = new dope.form.Button({
			label: "Download",
			onClick: function(e) {
				dojo.stopEvent(e);
				new dope.operation.xhrGet({
					title: 'Get file',
					url: '/file/' + item.id,
					load: function(data) {
						if (data.url) {
							var dlBtn = new dope.form.button.Download({
								label: 'Open file',
								href: data.url
							});
							dojo.place(dlBtn.domNode, btn.domNode, 'replace');
						}
						else {
							new dope.dialog.Dialog({
								title: 'Oops',
								content: snowwhite.nl2br(
									"Try again in a few seconds.\n\n"
									+ "If this persists, contact IT."
								)
							});
						}
					}
				});
				return false;
			}
		});
		return btn;
	}
});
