dojo.provide('dope.entity.Form');
dojo.require('dope.xhr.Form');
dojo.require('dope.dialog.Dialog');

dojo.declare('dope.entity.Form', dope.xhr.Form, {
	submitModifiers: [],
	
	validate: function() {
		if (! this.inherited(arguments)) {
			var totalErrors = dojo.query('.dijitError', this.domNode).length;
			
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
	onComplete: function(data, response) {
		/* Enable elements */
		dojo.forEach(dojo.query('.dijitButton', this.domNode), function(btnNode) {
			dijit.byNode(btnNode).set('disabled', false);
		});
		
		if (data.status) {
			if (this._canRedirect(data)) {
				this.getPane().setUrl(
					new dope.utils.Url('/' + data.controller + '/' + data.id)
				);
			}
			dojo.publish('/dope/entity/form/add', [this]);
			
			if (!data.preventReset) {
				/* Reset the form */
				this.reset();
			}
		} else {
			console.log("Something went wrong!", data, response);
			alert("Something went wrong!");
		}
	},
	_canRedirect: function(data) {
		if (data.preventRedirect) {
			return false;
		}
		
		if (! this.getPane()) {
			return false;
		}
		
		if (! this.getPane().getUrl) {
			return false;
		}
		
		if (dojo.indexOf(['add','edit'], this.getPane().getUrl().getAction()) < 0) {
			return false;
		}
		
		return true;
	}
});