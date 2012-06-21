dojo.provide("dope.utils.Url");
dojo.require("dope.dtl.filter.strings");

/**
 * dope.utils.Url helper
 * 
 * Usage:
 * 
 * var url = new dope.utils.Url('/some/path', {
 *   key1: 'val1',
 *   key2: 'val2
 * });
 * 
 * url.remove('key1');
 * url.set('key3', 'val3')
 * 
 * alert(url.get('controller')); // output: "some"
 * alert(url.get('action')); // output: "path"
 * alert(String(url)); // output: "/some/path?key2=val2&key3=val3"
 */
dojo.declare('dope.utils.Url', null, {
	a: null,
	
	constructor: function(href, parts) {
		this.a = document.createElement('a');
		
		if (href) {
			this.a.href = href;
		}
		
		if (typeof(parts) == 'object') {
			this.set(parts);
		}
	},
	
	getA: function() {
		return this.a; 
	},
	
	get: function(key) {
		switch (key) {
			case 'controller':
				return this.getController();
				break;
			case 'action':
				return this.getAction();
				break;
			default:
				return this.getSearch(key);
				break;
		}
	},
	
	set: function(key, val) {
		if (typeof(key) == 'object') {
			for(var k in key) {
				this.set(k, key[k]);
			}
			
			return;
		}
		
		val = dope.dtl.filter.strings.urlencode(String(val));
		
		switch (key) {
			case 'controller':
				return this.setController(val);
				break;
			case 'action':
				return this.setAction(val);
				break;
			default:
				return this.setSearch(key, val);
				break;
		}
	},
	
	remove: function(key) {
		switch (key) {
			case 'controller':
				this.removeController();
				break;
			case 'action':
				this.removeAction();
				break;
			default:
				this.removeSearch(key);
				break;
		}
	},
	
	getController: function() {
		return this._getPathPart(1);
	},
	
	setController: function(controller) {
		return this._setPathPart(1, controller);
	},
	
	removeController: function() {
		return this.setController(null);
	},
	
	getAction: function() {
		return this._getPathPart(2);
	},
	
	setAction: function(action) {
		return this._setPathPart(2, action);
	},
	
	removeAction: function() {
		return this.setAction(null);
	},
	
	getSearch: function(key) {
		var params = this._getSearchParams();
		return params[key];
	},
	
	setSearch: function(key, val) {
		var params = this._getSearchParams();
		params[key] = val;
		return this._setSearchParams(params);
	},
	
	removeSearch: function(key) {
		if (key) {
			var params = this._getSearchParams();
			params.splice(key);
		} else {
			var params = [];
		}
		return this._setSearchParams(params);
	},
	
	_getSearchParams: function() {
		var params = [];
		
		dojo.forEach(this.a.search.substr(1).split('&'), function(param) {
			var paramParts = param.split('=');
			params[paramParts[0]] = paramParts[1];
		});
		
		return params;
	},
	
	_setSearchParams: function(params) {
		var flatParams = '';

		for (var key in params) {
			flatParams += key + '=' + params[key] + '&';
		}

		this.a.search = '?' + flatParams.replace(/&$/,"");;
		return this;
	},
	
	_getPathPart: function(index) {
		var pathParts = this._getPathParts();
		return pathParts[index];
	},
	
	_setPathPart: function(index, value) {
		var pathParts = this._getPathParts();
		pathParts[index] = value;
		return this._setPathParts(pathParts);
	},
	
	_getPathParts: function() {
		return this.a.pathname.split('/');
	},
	
	_setPathParts: function(pathParts) {
		this.a.pathname = pathParts.join('/');
		return this;
	},
	
	_removeHost: function(a) {
		/* If hosts match, make url relative */
		if (a.host == window.location.host) {
			return a.pathname + a.search + a.hash;
		}
		else {
			return a.href;
		}
	},
	
	_getRandomHost: function() {
		if (ajaxHosts.length) {
			var index = Math.floor(Math.random()*ajaxHosts.length);
			return window.location.protocol + '//' + ajaxHosts[index];
		}
		else {
			return false;
		}
	},
	
	toString: function() {
		var url = this._removeHost(this.a);
		//var randomHost = this._getRandomHost();
		
		//if (randomHost) {
		//	url = randomHost + url;
		//}
		
		return url;
	}
});