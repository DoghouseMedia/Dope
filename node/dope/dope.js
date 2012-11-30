var http = require('http'),
    faye = require('faye');

var bayeux = new faye.NodeAdapter({mount: '/dope', timeout: 45});
bayeux.listen(8181);
