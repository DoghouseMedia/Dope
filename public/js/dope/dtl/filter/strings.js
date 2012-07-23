dojo.provide('dope.dtl.filter.strings');

/*
 * This is a bit of a mess.
 * 
 * First, see if bug mentioned below is fixed,
 * then re-avaluate writing this as a mixin that "fixes"
 * dojox.dtl.filter.strings._urlQuote()
 */

dojo.declare('dope.dtl.filter.strings', null);
dojo.mixin(dope.dtl.filter.strings, dojox.dtl.filter.strings);
dojo.mixin(dope.dtl.filter.strings, {
	/**
	 * Temporary urlencode bug fix
	 * 
	 * @todo Remove this when dojo is fixed and upgraded
	 * 
	 * @see http://bugs.dojotoolkit.org/ticket/15737
	 * @see http://bugs.dojotoolkit.org/ticket/13669
	 */
	_urlquote: function(/*String*/ url, /*String?*/ safe){
		if(!safe){
			safe = "/";
		}
		return dojox.string.tokenize(url, /([^\w-_.])/g, function(token){
			if(safe.indexOf(token) == -1){
				if(token == " "){
					return "+";
				}else{
					return encodeURIComponent(token);
				}
			}
			return token;
		}).join("");
	},
	urlencode: function(value){
		return dope.dtl.filter.strings._urlquote(value);
	}
});