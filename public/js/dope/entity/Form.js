dojo.provide('dope.entity.Form');
dojo.require('dope.xhr.Form');
dojo.require('dope.dialog.Dialog');

dojo.declare('dope.entity.Form', dope.xhr.Form, {
	submitModifiers: [],
	
	validate: function() {
		if (this.inherited(arguments)) {
			dojo.forEach(dojo.query('.dijitTab.dopeTabError', this.domNode), function(tab) {
				dojo.removeClass(tab, 'dopeTabError');
			});
		} else {
			var totalErrors = 0;
			var tabs = dojo.query('.dijitTab', this.domNode);
			
			if (tabs.length) {
    			dojo.forEach(
    				dojo.query('.dijitContentPane.dijitTabPane', this.domNode), 
    				function(contentPane, i) {
    					dojo.removeClass(tabs[i], 'dopeTabError');
    				
    					var numErrors = dojo.query('.dijitError', contentPane).length;
	    				if (numErrors) {
	    					totalErrors += numErrors;
	    					dojo.addClass(tabs[i], 'dopeTabError');
	    				}
    				}
    			);
			} else {
				totalErrors += dojo.query('.dijitError', this.domNode).length;
			}

			new dope.dialog.Dialog({
				title: "Sorry, there's an error!",
				content: snowwhite.nl2br(
					"Unfortunately there are still " 
					+ totalErrors + " error(s) in your form.\n"
					+ "Please correct them and try again!"
				)
			});

			return false;
		}
		
		return true;
	},
	onSubmit: function(e) {
		/* Prevent the form from really submitting */
		if (e) e.preventDefault();
		
		/* Stop execution if the parent returns false */
		if (! this.inherited(arguments)) {
			return false;
		}
		
		/* Disable elements */
		dojo.forEach(dojo.query('.dijitButton', this.domNode), function(btnNode) {
			dijit.byNode(btnNode).set('disabled', true);
		});
		
		dojo.forEach(this.submitModifiers, function(callback) {
			callback();
		});
	},
	onComplete: function(data, response) {
		/* Enable elements */
		dojo.forEach(dojo.query('.dijitButton', this.domNode), function(btnNode) {
			dijit.byNode(btnNode).set('disabled', false);
		});
		
		if (data.status) {
			if (dojo.indexOf(['add','edit'], this.getPane().getUrl().getAction()) >= 0) {
				this.getPane().setUrl(
					new dope.utils.Url(data.controller + '/' + data.id)
				);
			}
			dojo.publish('/dope/entity/form/add', [this]);
			
			/* Reset the form */
			this.reset();
		} else {
			console.log("Something went wrong!", data, response);
			alert("Something went wrong!");
		}
	}
});