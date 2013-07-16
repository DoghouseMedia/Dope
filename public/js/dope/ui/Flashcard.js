dojo.provide('dope.ui.Flashcard');
dojo.require('dijit._Widget');
dojo.require('dijit._Templated');

dojo.declare('dope.ui.Flashcard', [dijit._Widget], {
	baseClass: 'dopeUiFlashcard',
	enabled: true,
	
	startup: function() {
		this.inherited(arguments);
		
		if (! this.enabled) {
			dojo.addClass(this.domNode, 'disabled');
		}
		
		this._adjustHeight();
	},
	_adjustHeight: function() {
		var thisNode = this.domNode;
		var maxHeight = parseInt(dojo.style(thisNode, 'height'));
		dojo.forEach(dojo.query('.' + this.baseClass, thisNode.parentNode), function(flashcardNode) {
			var _height = parseInt(dojo.style(flashcardNode, 'height'));
			if (_height > maxHeight) {
				maxHeight = _height;
			}
		});
		dojo.style(thisNode, 'height', maxHeight + 'px');
	}
});