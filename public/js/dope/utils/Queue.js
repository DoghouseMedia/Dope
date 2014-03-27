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

    findItemByItem: function(item) {
        var keys = Object.keys(item);
        var itemInQueue = null;

		dojo.some(this.items, function(_item) {
			if (_item.isRunning) {
				return false;
			}

			var isMatching = dojo.every(keys, function(key) {
				return (item[key] === _item[key]);
			});

            if (isMatching) {
                itemInQueue = _item;
                return true;
            }
            else {
                return false;
            }
		});

        return itemInQueue;
	},

	add: function(item, callback) {
        var itemInQueue = this.findItemByItem(item);

        if (itemInQueue && !itemInQueue.isRunning) {
            /**
             * The item exists in the queue AND isn't running. We can safely add our callback to this item.
             */
            this.addCallback(itemInQueue, callback);
        }
        else {
            /**
             * Either:
             *
             * 1. There is no similar item in queue
             * 2. The similar item in queue is running and we can't expect it will satisfy our dependencies
             */
            this.items.push(item)
            this.addCallback(item, callback);
        }
	},

    addCallback: function(item, callback) {
        if (dojo.isFunction(callback)) {
            if (! item.callbacks) {
                item.callbacks = [];
            }
            item.callbacks.push(callback);
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

        var item = this.items[0];
        item.isRunning = true;
        dojo.forEach(item.callbacks, dojo.hitch(this, function(callback) {
            this.callbacks.push(callback)
        }));

        this.isRunning = true;
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