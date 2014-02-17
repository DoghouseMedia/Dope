dojo.provide('dope.operation.Summary');
dojo.require('dope.layout.MenuCounter');

dojo.declare('dope.operation.Summary', dope.layout.MenuCounter, {
	'class': 'dopeOperationSummary',
	iconClass: 'icon-refresh',
    operations: [],
    numRunning: 0,
    maxRunning: 3,
	
	postCreate: function() {
		this.inherited(arguments);

        this.operations = [];
		
		dojo.subscribe('/dope/operation/onExecute', dojo.hitch(this, 'onOperationExecute'));
		dojo.subscribe('/dope/operation/onComplete', dojo.hitch(this, 'onOperationComplete'));
        dojo.subscribe('/dope/operation/requestExecute', dojo.hitch(this, 'onRequestExecute'));

        setInterval(dojo.hitch(this, 'tryExecute'), 100);
	},

    onOperationExecute: function(operation) {
        this.addItem(operation);
        operation.isRunning = true;
        this.numRunning++;
    },

    onOperationComplete: function(operation) {
        this.removeItem(operation);
        this.operations.splice(this.operations.indexOf(operation), 1);
        //delete operation;
        this.numRunning--;
    },

    onRequestExecute: function(operation) {
        this.operations.push(operation);

        this.operations.sort(function(a, b) {
            return (b.qos - a.qos);
        });
    },

    tryExecute: function() {
        if (this.numRunning >= this.maxRunning) {
            console.log("THROTTLE TOTAL");
            return;
        }

        dojo.some(this.operations, dojo.hitch(this, function(operation) {
            if (operation.isRunning || operation.canceled) {
                return false;
            }

            if (operation.qos <= 0 && this.numRunning > this.maxRunning/2) {
                return false;
            }

            operation.execute();
            return true;
        }));
    }
});