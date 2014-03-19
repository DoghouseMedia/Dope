dojo.provide('dope.form.Editor');
dojo.require('dijit.Editor');
dojo.require('dijit._editor.RichText');
dojo.require('dojox.editor.plugins.PasteFromWord');

dojo.declare('dope.form.Editor', dijit.Editor, {
    extraPlugins: ['pastefromword'],

	postCreate: function() {
		this.inherited(arguments);
		this.addStyleSheet('/css/font.css');
		this.hiddenNode = dojo.create('input', {
			type: 'hidden',
			name: this.name,
            value: this.value
		});
		dojo.place(this.hiddenNode, this.domNode);
		this.watch('value', this.updateHiddenField.bind(this));
	},

	updateHiddenField: function() {
		this.hiddenNode.value = this.get('value');
	},

	destroy: function() {
		/*
		 * Sometimes the toolbar has already been destroyed by us when
		 * dojo tries to delete it, so I've hacked a void method here.
		 * Lame.
		 */
		if (!this.toolbar) {
			this.toolbar = {
				destroyRecursive: function() {}	
			};
		}
		this.inherited(arguments);
	},

    _endEditing: function(cmd) {
        this.inherited(arguments);
        this.cleanHtml();
    },

    cleanHtml: function() {
        var div = dojo.create('div', {
            innerHTML: this.editNode.innerHTML
        });
        this._cleanHtml(div);
		
        this.editNode.innerHTML = div.innerHTML;
        this.set('value', div.innerHTML);
    },

    _cleanHtml: function(dom) {
		for (var i=0; i < dom.attributes.length; i++) {
			var attr = dom.attributes[i];
			
			if (attr.localName != 'href') {
				dom.attributes.removeNamedItem(attr.localName);
				i--;
			}
		}
		
		for (var i=0; i < dom.childNodes.length; i++) {
			var tag = dom.childNodes[i];
			
            if (!tag.tagName) {
				tag.textContent = tag.textContent.replace(/[\n\r\t]/mg, ' ');
                tag.textContent = tag.textContent.replace('&nbsp;', ' ');
				
				if (tag.textContent.length) {
					tag.textContent = tag.textContent.replace(/[^ a-zA-Z0-9\!\@\#\$%\^\&\*\(\)\[\]\{\}\'\"\:\;\/\?\.\,\>\<\`\~\_\+\=\n]*/, '');	
					if (tag.textContent.slice(-1) != ' ') {
						tag.textContent += ' ';
					}
				}
				
                continue;
            }

            this._cleanHtml(tag);

            if (tag.tagName == 'P') {
                var html = String(tag.innerHTML);
                if (! html.match(/<br *\/*>$/)) {
                    html += '<br>';
                }
                tag.outerHTML = html.replace(/ */, '');
            }
            else if (null == tag.tagName.match(/^(A|UL|OL|LI|BR|B|STRONG|EM|I)$/)) {
                var html = String(tag.innerHTML);
                if (! html.match(/ +$/)) {
                    html += ' ';
                }
                tag.outerHTML = html.replace(/ */, '');
            }
        }
    },

    /*
     * Modify method to be Apple-friendly
     */
    onKeyDown: function(e) {
        // summary:
        //		Handler for onkeydown event.
        // tags:
        //		private

        var isCtrlKey = e.ctrlKey || e.metaKey;

        //We need to save selection if the user TAB away from this editor
        //no need to call _saveSelection for IE, as that will be taken care of in onBeforeDeactivate
        if(!dojo.isIE && !this.iframe && e.keyCode == dojo.keys.TAB && !this.tabIndent){
            this._saveSelection();
        }
        if(!this.customUndo){
            this.inherited(arguments);
            return;
        }
        var k = e.keyCode, ks = dojo.keys;
        if(isCtrlKey && !e.altKey){//undo and redo only if the special right Alt + z/y are not pressed #5892
            if(k == 90 || k == 122){ //z
                dojo.stopEvent(e);
                this.undo();
                return;
            }else if(k == 89 || k == 121){ //y
                dojo.stopEvent(e);
                this.redo();
                return;
            }
        }
        dijit._editor.RichText.prototype.onKeyDown.apply(this, arguments);

        switch(k){
            case ks.ENTER:
            case ks.BACKSPACE:
            case ks.DELETE:
                this.beginEditing();
                break;
            case 88: //x
            case 86: //v
                if(isCtrlKey && !e.altKey){
                    this.endEditing();//end current typing step if any
                    if(e.keyCode == 88){
                        this.beginEditing('cut');
                        //use timeout to trigger after the cut is complete
                        setTimeout(dojo.hitch(this, this.endEditing), 1);
                    }else{
                        this.beginEditing('paste');
                        //use timeout to trigger after the paste is complete
                        setTimeout(dojo.hitch(this, this.endEditing), 1);
                    }
                    break;
                }
            //pass through
            default:
                if(!e.ctrlKey && !e.altKey && !e.metaKey && (e.keyCode<dojo.keys.F1 || e.keyCode>dojo.keys.F15)){
                    this.beginEditing();
                    break;
                }
            //pass through
            case ks.ALT:
                this.endEditing();
                break;
            case ks.UP_ARROW:
            case ks.DOWN_ARROW:
            case ks.LEFT_ARROW:
            case ks.RIGHT_ARROW:
            case ks.HOME:
            case ks.END:
            case ks.PAGE_UP:
            case ks.PAGE_DOWN:
                this.endEditing(true);
                break;
            //maybe ctrl+backspace/delete, so don't endEditing when ctrl is pressed
            case ks.CTRL:
            case ks.SHIFT:
            case ks.TAB:
                break;
        }
    },
});
//@ sourceURL=/js/dojo/../dope/form/Editor.js
//@ sourceURL=/js/dojo/../dope/form/Editor.js
//@ sourceURL=/js/dojo/../dope/form/Editor.js
//@ sourceURL=/js/dojo/../dope/form/Editor.js
//@ sourceURL=/js/dojo/../dope/form/Editor.js
//@ sourceURL=/js/dojo/../dope/form/Editor.js