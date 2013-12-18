dojo.provide('dope.operation._Xhr');
dojo.require('dope.operation._Base');

dojo.declare('dope.operation._Xhr', dope.operation._Base, {
	xhr: null,
	runningXhr: null,
	constructor: function(options) {		
		var xhr = this.getXhr(options);
		
		dojo.connect(xhr, 'onExecute', dojo.hitch(this, '_onExecute'));
		dojo.connect(xhr, 'onComplete', dojo.hitch(this, '_onComplete'));
	},
	buildRendering: function() {
		this.inherited(arguments);
		this.containerNode.innerHTML = this.title || this.url || 'Operation';
	},
	postCreate: function() {
		this.inherited(arguments);
		this.runningXhr = this.getXhr().execute();				
	},
	getXhr: function(options) {
		if (! this.xhr) {
			this.xhr = this._getXhr(options);
		}
		
		return this.xhr;
	},
	cancel: function() {
		if (this.runningXhr) {
			/*
			 * We want to abort the operation without throwing an Error.
			 * @see dojo._base.xhr::_deferredCancel
			 */
			this.runningXhr.canceled = true;
			var runningXhrNative = this.runningXhr.ioArgs.xhr;
			switch(typeof runningXhrNative.abort) {
				case "function": 
				case "object":
				case "unknown":
					runningXhrNative.abort();
					this._onComplete();
					break;
			}
		}
		return this;
	},
	_onExecute: function() {
		this.inherited(arguments);
	},
	_onComplete: function(data, request) {
		this.inherited(arguments);
		
		if (dojo.exists('request.xhr.status')) {
			switch (request.xhr.status) {
				/* Unauthorized - Please log in */
				case 401: 
					snowwhite.gotoLogin();
					break;
					
				/* Forbidden - ACL restriction */
				case 403: 
					alert("403 is not implemented!"); 
					break;
					
				/* Error */
				case 500:
					alert(
						"ERROR!\n"
						+ "This function may not have completed successfully.\n"
						+ "Reload the page before trying anything else!\n"
						+ "\n"
					);
					break;
			}
		}
	}
});