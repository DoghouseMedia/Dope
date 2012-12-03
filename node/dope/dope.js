var http = require('http'),
    faye = require('faye');

var bayeux = new faye.NodeAdapter({mount: '/dope', timeout: 45});

var tokens = new Array();

/* Auth */
bayeux.addExtension({
    incoming: function(message, callback) {
    	
    	// Avoid undeclared vars
    	if (! message.ext) message.ext={};
    	if (! message.ext.token) message.ext.token='';
    	
    	/*
    	 * If it's the PHP app, allow everything
    	 * @todo This token should be stored in a config file
    	 */
    	if (message.ext.token == '70628fd29fb2d6583b83bb1bad94d140') {
    		/*
    		 * Let the PHP authorise tokens
    		 */
    		if (message.channel == '/auth/authorise') {
    		    tokens.push(message.data.token);
    		    console.log('Authorise token: ' + message.data.token);
    		}
    	}
    	/*
    	 * Else, it's a browser-client
    	 */
    	else {
    		/*
    		 * Don't bust anyones balls unless they're trying to subscribe
    		 */
    		if (message.channel !== '/meta/subscribe') {
        	    return callback(message);
    		}
    		
    		/*
    		 * Check for a valid token
    		 */
    		if (tokens.indexOf(message.ext.token) < 0) {
    			message.error = 'Invalid auth token: ' + message.ext.token;
    			console.log(message.error);
    		}
    	}
    	
        // Call the server back now we're done
        callback(message);
    }
});
		
bayeux.listen(8181);