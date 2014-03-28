dojo.provide('dope.form.Button');
dojo.require('dijit.form.Button');
dojo.require('dope._Contained');

dojo.declare('dope.form.Button', [dijit.form.Button, dope._Contained], {
    /**
     * Fix Dojo's method by checking innerText isn't empty before overriding the label setting.
     */
    _fillContent: function(/*DomNode*/ source){
        // Overrides _Templated._fillContent().
        // If button label is specified as srcNodeRef.innerHTML rather than
        // this.params.label, handle it here.
        // TODO: remove the method in 2.0, parser will do it all for me
        if(source && source.innerText && (!this.params || !("label" in this.params))){
            this.set('label', source.innerHTML);
        }
    }
});