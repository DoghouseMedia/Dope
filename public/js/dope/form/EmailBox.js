dojo.provide('dope.form.EmailBox');
dojo.require('dijit.form.ValidationTextBox');

dojo.declare('dope.form.EmailBox', dijit.form.ValidationTextBox, {
	contacts: [],
	
	startup: function() {
		this.inherited(arguments);
		dojo.addClass(this.domNode, 'dopeEmailBox');
		this.reset();
		this.addContact('jonathan@dhmedia.com.au');
		dojo.connect(this, 'onKeyUp', this._onKeyUp.bind(this));
		//this.parseValueForContacts.bind(this));
	},
	_onKeyUp: function(e) {
		/* Enter or space */
		if (e.keyCode == 32 || e.keyCode == 13) {
			this.parseValueForContacts();
		}
	},
	onChange: function(newValue) {
		console.log(this.get('value'), newValue);
		this.parseValueForContacts();
	},
	parseValueForContacts: function() {
		var values = this.get('value').split(/[,\s]/);
		//this.clearContacts();
		dojo.forEach(values, this.addContact.bind(this));
	},
	addContact: function(value) {
		this.contacts.push(new dope.form.EmailBox.Contact({
			'value': value,
			'emailBox': this
		}));
	},
	removeContact: function(contact) {
		alert('Remove');
	},
	clearContacts: function() {
		dojo.forEach(this.contacts, dojo.destroy);
		dojo.empty(this.contactContainerNode);
		this.contacts = [];
	},
	reset: function() {
		if (! this.contactContainerNode) {
			this.contactContainerNode = dojo.create('div', {
				className: 'dopeEmailBoxContactContainer'
			});
			dojo.place(this.contactContainerNode, this.domNode, 'first');
		}
		
		this.clearContacts();
	}
});

dojo.declare('dope.form.EmailBox.Contact', [dijit._Widget, dijit._Templated], {
	value: '',
	emailBox: null,
	baseClass: 'dopeEmailBoxContact',
	
	templateString: '<div>'
		+ '<span data-dojo-attach-point="emailNode"></span>'
		+ '<span data-dojo-attach-point="removeNode" class="dopeEmailBoxContactRemove">x</span>'
		+ '</div>',

	postCreate: function() {
		this.inherited(arguments);
		this.emailNode.innerHTML = this.get('value');
		dojo.connect(this.removeNode, 'onclick', this.remove.bind(this));
		
		console.log(this, this.domNode, this.emailBox, this.emailBox.contactContainerNode);
		dojo.place(this.domNode, this.emailBox.contactContainerNode, 'last');
	},
	remove: function() {
		this.emailBox.removeContact(this);
	}
});