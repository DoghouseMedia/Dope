dojo.provide('dope.form.Textarea');
dojo.require("dijit.form.SimpleTextarea");
dojo.require("dijit.form.ValidationTextBox");

dojo.declare('dope.form.Textarea', [dijit.form.SimpleTextarea, dijit.form.ValidationTextBox], {
  regExp: "^(.*\n?)*$",
  
  templateString: "<textarea ${!nameAttrSetting} data-dojo-attach-point='focusNode,containerNode,textbox' autocomplete='off'></textarea>",
  
  constructor: function() {
    this.constraints = {};
    this.baseClass += ' dopeFormTextarea';
  }
});