dojo.provide("dope.utils.Queue");


dojo.declare('dope.utils.Queue', null, {
	options: {},
	items: [],
	callbacks: [],
	timer: null,
	isRunning: false,

	constructor: function(options) {
		this.options = options;
		this.items = [];
		this.callbacks = [];

		dojo.connect(
			options.endEvent.element, 
			options.endEvent.event, 
			this.end.bind(this)
		);
	},

	exists: function(item) {
		var keys = Object.keys(item);
		
		return dojo.some(this.items, function(_item) {
			if (_item.isRunning) {
				return false;
			}

			return dojo.every(keys, function(key) {
				return (item[key] === _item[key]);
			});
		});
	},

	add: function(item, callback) {
		this.items.push(item);

		if (dojo.isFunction(callback)) {
			this.callbacks.push(callback);
		}
	},

	run: function() {
		if (! this.timer) {
			this.timer = setInterval(this.run.bind(this), 1000);
		}

		if (this.isRunning) {
			return;
		}

		if (this.items.length == 0) {
			clearInterval(this.timer);
			this.timer = null;
			return;
		}

		this.isRunning = true;
		this.items[0].isRunning = true;
		this.options.onRun.call(null, this.items[0]);
		this.items.splice(0,1);
	},

	end: function() {
		dojo.forEach(this.callbacks, function(callback) {
			callback.call();
		});
		this.callbacks = [];

		this.isRunning = false;
		this.options.onEnd.call();
	}
});