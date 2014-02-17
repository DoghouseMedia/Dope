dojo.provide('dope.operation._Xhr');
dojo.require('dope.operation._Base');

dojo.declare('dope.operation._Xhr', dope.operation._Base, {
	xhr: null,
	constructor: function(options) {
        this.xhr = this._getXhr(options);
		
		dojo.connect(this.xhr, 'onExecute', dojo.hitch(this, '_onExecute'));
		dojo.connect(this.xhr, 'onComplete', dojo.hitch(this, '_onComplete'));
	},
	buildRendering: function() {
		this.inherited(arguments);
		this.containerNode.innerHTML = this.title || this.url || 'Operation';
	},
	postCreate: function() {
		this.inherited(arguments);
        dojo.publish('/dope/operation/requestExecute', [this]);
	},
    execute: function() {
        this.xhr.execute();
    },
	cancel: function() {
		if (this.xhr) {
			/*
			 * We want to abort the operation without throwing an Error.
			 * @see dojo._base.xhr::_deferredCancel
			 */
			this.xhr.canceled = true;
			var xhrNative = this.xhr.ioArgs.xhr;
			switch(typeof xhrNative.abort) {
				case "function": 
				case "object":
				case "unknown":
                    xhrNative.abort();
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