dojo.provide('dope.form.EmailBox');
dojo.require('dijit.form.ValidationTextBox');

dojo.declare('dope.form.EmailBox', dijit.form.ValidationTextBox, {
	contacts: [],
	
	startup: function() {
		this.inherited(arguments);
		dojo.addClass(this.domNode, 'dopeEmailBox');
		dojo.connect(this, 'onKeyUp', this._onKeyUp.bind(this));

		this.valueNode = dojo.create('input', {name: this.name, type: 'hidden'});
		this.textbox.name = null;
		dojo.place(this.valueNode, this.domNode);
		
		this.reset();
	},
	updateFormValue: function() {
		this.valueNode.value = this.getContactsValue();
	},
	getContactsValue: function() {
		var contactValues = [];
		dojo.forEach(this.contacts, function(contact) {
			contactValues.push(contact.get('value'));
		});
		return contactValues.join();
	},
	_onKeyUp: function(e) {
		switch (e.keyCode) {
			case 32: // enter
			case 13: // space
				this.parseValueForContacts();
				break;
			case 8: // backspace
				var contact = this.contacts[this.contacts.length-1];
				this.removeContact(contact);
				break;
		}
	},
	onChange: function(newValue) {
		this.parseValueForContacts();
	},
	parseValueForContacts: function() {
		this.addContact(this.get('value'));
		this.set('value', '');
	},
	addContact: function(value) {
		if (! value) {
			return;
		}
		
		this.contacts.push(new dope.form.EmailBox.Contact({
			'value': value,
			'emailBox': this
		}));
		
		this.updateFormValue();
	},
	removeContact: function(contact) {
		var _contacts = [];
		dojo.forEach(this.contacts, function(_contact) {
			if (_contact == contact) {
				contact.destroy();
			} else {
				_contacts.push(_contact);
			}
		});
		this.contacts = _contacts;
		
		this.updateFormValue();
	},
	clearContacts: function() {
		dojo.forEach(this.contacts, dojo.destroy);
		dojo.empty(this.contactContainerNode);
		this.contacts = [];
		
		this.updateFormValue();
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
		dojo.place(this.domNode, this.emailBox.contactContainerNode, 'last');
	},
	remove: function() {
		this.emailBox.removeContact(this);
	}
});