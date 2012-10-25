dojo.provide('dope.form.EmailBox');
dojo.require('dijit.form.ValidationTextBox');

dojo.declare('dope.form.EmailBox', dijit.form.ValidationTextBox, {
	contacts: [],
	_currentValueLength: 0,
	_backspaceTimer: null,
	_parsingBackspace: false,
	_pausedBackspaces: 0,
	_numSequentialBackspaces: 0,
	
	startup: function() {
		this.inherited(arguments);
		dojo.addClass(this.domNode, 'dopeEmailBox');
		dojo.connect(this, 'onKeyDown', dojo.hitch(this, '_onKeyDown'));
		dojo.connect(this, 'onKeyUp', dojo.hitch(this, '_onKeyUp'));

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
			case 8: // backspace
				this._numSequentialBackspaces = 0;
				break;
		}
	},
	_onKeyDown: function(e) {
		switch (e.keyCode) {
			case 9: // tab
			case 32: // enter
			case 13: // space
			case 186: // semicolon
			case 188: // comma
				e.preventDefault();
				this.parseValueForContacts();
				return false;
				break;
			case 8: // backspace
				if (this._currentValueLength == 0 && this.contacts.length) {
					
					if (this._numSequentialBackspaces > 0 && this._pausedBackspaces < 10) {
						this._pausedBackspaces++;
						break;
					}
					this._pausedBackspaces = 0;
					
					var contact = this.contacts[this.contacts.length-1];
					this._parsingBackspace = true;
					this.removeContact(contact);
					this.set('value', contact.get('value'));
				}
				this._calculateValueLength();
				this._numSequentialBackspaces++;
				break;
			default:
				this._calculateValueLength();
				break;
		}
	},
	
	_calculateValueLength: function(add) {
		this._currentValueLength = this.get('value').length > 0 ? this.get('value').length -1 : 0;
	},
	
	onChange: function(newValue) {
		if (this._parsingBackspace) {
			this._parsingBackspace = false;
		} else {
			this.parseValueForContacts();
		}
	},
	parseValueForContacts: function() {
		dojo.forEach(
			this.get('value').split(/,| /),
			this.addContact.bind(this)
		);
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