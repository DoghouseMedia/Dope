dojo.provide('dope.data.JsonRestStore');
dojo.require('dojox.data.JsonRestStore');
dojo.require('dope.rpc.Rest');
dojo.require('dope.rpc.JsonRest');


dojo.declare('dope.data.JsonRestStore', dojox.data.JsonRestStore, {
    //allowNoTrailingSlash: true,
	constructor: function(options) {
        dojo.connect(dope.rpc.Rest._index,"onUpdate",this,function(obj,attrName,oldValue,newValue){
            var prefix = this.service.servicePath;
            if(!obj.__id){
                console.log("no id on updated object ", obj);
            }else if(obj.__id.substring(0,prefix.length) == prefix){
                this.onSet(obj,attrName,oldValue,newValue);
            }
        });
        this.idAttribute = this.idAttribute || 'id';// no options about it, we have to have identity

        if(typeof options.target == 'string'){
            options.target = options.target.match(/\/$/) || this.allowNoTrailingSlash ? options.target : (options.target + '/');
            if(!this.service){
                this.service = dojox.rpc.JsonRest.services[options.target] ||
                    dope.rpc.Rest(options.target, true);
                // create a default Rest service
            }
        }

        dojox.rpc.JsonRest.registerService(this.service, options.target, this.schema);
        this.schema = this.service._schema = this.schema || this.service._schema || {};
        // wrap the service with so it goes through JsonRest manager
        this.service._store = this;
        this.service.idAsRef = this.idAsRef;
        this.schema._idAttr = this.idAttribute;
        var constructor = dope.rpc.JsonRest.getConstructor(this.service);
        var self = this;
        this._constructor = function(data){
            constructor.call(this, data);
            self.onNew(this);
        }
        this._constructor.prototype = constructor.prototype;
        this._index = dope.rpc.Rest._index;
    },

    // summary:
    //		Will load any schemas referenced content-type header or in Link headers
    loadReferencedSchema: true,
    // summary:
    //		Treat objects in queries as partially loaded objects
    idAsRef: false,
    referenceIntegrity: true,
    target:"",
    // summary:
    // 		Allow no trailing slash on target paths. This is generally discouraged since
    // 		it creates prevents simple scalar values from being used a relative URLs.
    // 		Disabled by default.
    allowNoTrailingSlash: false,
    //Write API Support
    newItem: function(data, parentInfo){
        // summary:
        //		adds a new item to the store at the specified point.
        //		Takes two parameters, data, and options.
        //
        //	data: /* object */
        //		The data to be added in as an item.
        data = new this._constructor(data);
        if(parentInfo){
            // get the previous value or any empty array
            var values = this.getValue(parentInfo.parent,parentInfo.attribute,[]);
            // set the new value
            values = values.concat([data]);
            data.__parent = values;
            this.setValue(parentInfo.parent, parentInfo.attribute, values);
        }
        return data;
    },
    deleteItem: function(item){
        // summary:
        //		deletes item and any references to that item from the store.
        //
        //	item:
        //		item to delete
        //

        //	If the desire is to delete only one reference, unsetAttribute or
        //	setValue is the way to go.
        var checked = [];
        var store = dojox.data._getStoreForItem(item) || this;
        if(this.referenceIntegrity){
            // cleanup all references
            dope.rpc.JsonRest._saveNotNeeded = true;
            var index = dope.rpc.Rest._index;
            var fixReferences = function(parent){
                var toSplice;
                // keep track of the checked ones
                checked.push(parent);
                // mark it checked so we don't run into circular loops when encountering cycles
                parent.__checked = 1;
                for(var i in parent){
                    if(i.substring(0,2) != "__"){
                        var value = parent[i];
                        if(value == item){
                            if(parent != index){ // make sure we are just operating on real objects
                                if(parent instanceof Array){
                                    // mark it as needing to be spliced, don't do it now or it will mess up the index into the array
                                    (toSplice = toSplice || []).push(i);
                                }else{
                                    // property, just delete it.
                                    (dojox.data._getStoreForItem(parent) || store).unsetAttribute(parent, i);
                                }
                            }
                        }else{
                            if((typeof value == 'object') && value){
                                if(!value.__checked){
                                    // recursively search
                                    fixReferences(value);
                                }
                                if(typeof value.__checked == 'object' && parent != index){
                                    // if it is a modified array, we will replace it
                                    (dojox.data._getStoreForItem(parent) || store).setValue(parent, i, value.__checked);
                                }
                            }
                        }
                    }
                }
                if(toSplice){
                    // we need to splice the deleted item out of these arrays
                    i = toSplice.length;
                    parent = parent.__checked = parent.concat(); // indicates that the array is modified
                    while(i--){
                        parent.splice(toSplice[i], 1);
                    }
                    return parent;
                }
                return null;
            };
            // start with the index
            fixReferences(index);
            dope.rpc.JsonRest._saveNotNeeded = false;
            var i = 0;
            while(checked[i]){
                // remove the checked marker
                delete checked[i++].__checked;
            }
        }
        dope.rpc.JsonRest.deleteObject(item);

        store.onDelete(item);
    },
    changing: function(item,_deleting){
        // summary:
        //		adds an item to the list of dirty items.	This item
        //		contains a reference to the item itself as well as a
        //		cloned and trimmed version of old item for use with
        //		revert.
        dope.rpc.JsonRest.changing(item,_deleting);
    },

    setValue: function(item, attribute, value){
        // summary:
        //		sets 'attribute' on 'item' to 'value'

        var old = item[attribute];
        var store = item.__id ? dojox.data._getStoreForItem(item) : this;
        if(dojox.json.schema && store.schema && store.schema.properties){
            // if we have a schema and schema validator available we will validate the property change
            dojox.json.schema.mustBeValid(dojox.json.schema.checkPropertyChange(value,store.schema.properties[attribute]));
        }
        if(attribute == store.idAttribute){
            throw new Error("Can not change the identity attribute for an item");
        }
        store.changing(item);
        item[attribute]=value;
        if(value && !value.__parent){
            value.__parent = item;
        }
        store.onSet(item,attribute,old,value);
    },
    setValues: function(item, attribute, values){
        // summary:
        //	sets 'attribute' on 'item' to 'value' value
        //	must be an array.


        if(!dojo.isArray(values)){
            throw new Error("setValues expects to be passed an Array object as its value");
        }
        this.setValue(item,attribute,values);
    },

    unsetAttribute: function(item, attribute){
        // summary:
        //		unsets 'attribute' on 'item'

        this.changing(item);
        var old = item[attribute];
        delete item[attribute];
        this.onSet(item,attribute,old,undefined);
    },
    save: function(kwArgs){
        // summary:
        //		Saves the dirty data using REST Ajax methods. See dojo.data.api.Write for API.
        //
        //	kwArgs.global:
        //		This will cause the save to commit the dirty data for all
        // 		JsonRestStores as a single transaction.
        //
        //	kwArgs.revertOnError
        //		This will cause the changes to be reverted if there is an
        //		error on the save. By default a revert is executed unless
        //		a value of false is provide for this parameter.
        //
        //	kwArgs.incrementalUpdates
        //		For items that have been updated, if this is enabled, the server will be sent a POST request
        // 		with a JSON object containing the changed properties. By default this is
        // 		not enabled, and a PUT is used to deliver an update, and will include a full
        // 		serialization of all the properties of the item/object.
        //		If this is true, the POST request body will consist of a JSON object with
        // 		only the changed properties. The incrementalUpdates parameter may also
        //		be a function, in which case it will be called with the updated and previous objects
        //		and an object update representation can be returned.
        //
        //	kwArgs.alwaysPostNewItems
        //		If this is true, new items will always be sent with a POST request. By default
        //		this is not enabled, and the JsonRestStore will send a POST request if
        //		the item does not include its identifier (expecting server assigned location/
        //		identifier), and will send a PUT request if the item does include its identifier
        //		(the PUT will be sent to the URI corresponding to the provided identifier).

        if(!(kwArgs && kwArgs.global)){
            (kwArgs = kwArgs || {}).service = this.service;
        }
        if("syncMode" in kwArgs ? kwArgs.syncMode : this.syncMode){
            dojox.rpc._sync = true;
        }

        var actions = dope.rpc.JsonRest.commit(kwArgs);
        this.serverVersion = this._updates && this._updates.length;
        return actions;
    },

    revert: function(kwArgs){
        // summary
        //		returns any modified data to its original state prior to a save();
        //
        //	kwArgs.global:
        //		This will cause the revert to undo all the changes for all
        // 		JsonRestStores in a single operation.
        dope.rpc.JsonRest.revert(kwArgs && kwArgs.global && this.service);
    },

    isDirty: function(item){
        // summary
        //		returns true if the item is marked as dirty.
        return dope.rpc.JsonRest.isDirty(item);
    },
    isItem: function(item, anyStore){
        //	summary:
        //		Checks to see if a passed 'item'
        //		really belongs to this JsonRestStore.
        //
        //	item: /* object */
        //		The value to test for being an item
        //	anyStore: /* boolean*/
        //		If true, this will return true if the value is an item for any JsonRestStore,
        //		not just this instance
        return item && item.__id && (anyStore || this.service == dope.rpc.JsonRest.getServiceAndId(item.__id).service);
    },
    _doQuery: function(args){
        var query= typeof args.queryStr == 'string' ? args.queryStr : args.query;
        var deferred = dope.rpc.JsonRest.query(this.service,query, args);
        var self = this;
        if(this.loadReferencedSchema){
            deferred.addCallback(function(result){
                var contentType = deferred.ioArgs && deferred.ioArgs.xhr && deferred.ioArgs.xhr.getResponseHeader("Content-Type");
                var schemaRef = contentType && contentType.match(/definedby\s*=\s*([^;]*)/);
                if(contentType && !schemaRef){
                    schemaRef = deferred.ioArgs.xhr.getResponseHeader("Link");
                    schemaRef = schemaRef && schemaRef.match(/<([^>]*)>;\s*rel="?definedby"?/);
                }
                schemaRef = schemaRef && schemaRef[1];
                if(schemaRef){
                    var serviceAndId = dope.rpc.JsonRest.getServiceAndId((self.target + schemaRef).replace(/^(.*\/)?(\w+:\/\/)|[^\/\.]+\/\.\.\/|^.*\/(\/)/,"$2$3"));
                    var schemaDeferred = dope.rpc.JsonRest.byId(serviceAndId.service, serviceAndId.id);
                    schemaDeferred.addCallbacks(function(newSchema){
                        dojo.mixin(self.schema, newSchema);
                        return result;
                    }, function(error){
                        console.error(error); // log it, but don't let it cause the main request to fail
                        return result;
                    });
                    return schemaDeferred;
                }
                return undefined;//don't change anything, and deal with the stupid post-commit lint complaints
            });
        }
        return deferred;
    },
    _processResults: function(results, deferred){
        // index the results
        var count = results.length;
        // if we don't know the length, and it is partial result, we will guess that it is twice as big, that will work for most widgets
        return {totalCount:deferred.fullLength || (deferred.request.count == count ? (deferred.request.start || 0) + count * 2 : count), items: results};
    },

    getConstructor: function(){
        // summary:
        // 		Gets the constructor for objects from this store
        return this._constructor;
    },
    getIdentity: function(item){
        var id = item.__clientId || item.__id;
        if(!id){
            return id;
        }
        var prefix = this.service.servicePath.replace(/[^\/]*$/,'');
        // support for relative or absolute referencing with ids
        return id.substring(0,prefix.length) != prefix ?	id : id.substring(prefix.length); // String
    },
    fetchItemByIdentity: function(args){
        var id = args.identity;
        var store = this;
        // if it is an absolute id, we want to find the right store to query
        if(id.toString().match(/^(\w*:)?\//)){
            var serviceAndId = dope.rpc.JsonRest.getServiceAndId(id);
            store = serviceAndId.service._store;
            args.identity = serviceAndId.id;
        }
        args._prefix = store.service.servicePath.replace(/[^\/]*$/,'');
        return store.inherited(arguments);
    },
    //Notifcation Support

    onSet: function(){},
    onNew: function(){},
    onDelete: 	function(){},

    getFeatures: function(){
        // summary:
        // 		return the store feature set
        var features = this.inherited(arguments);
        features["dojo.data.api.Write"] = true;
        features["dojo.data.api.Notification"] = true;
        return features;
    },

    getParent: function(item){
        //	summary:
        //		Returns the parent item (or query) for the given item
        //	item:
        //		The item to find the parent of

        return item && item.__parent;
    }


});
dope.data.JsonRestStore.getStore = function(options, Class){
    //	summary:
    //		Will retrieve or create a store using the given options (the same options
    //		that are passed to JsonRestStore constructor. Returns a JsonRestStore instance
    //	options:
    //		See the JsonRestStore constructor
    //	Class:
    //		Constructor to use (for creating stores from JsonRestStore subclasses).
    // 		This is optional and defaults to JsonRestStore.
    if(typeof options.target == 'string'){
        options.target = options.target.match(/\/$/) || options.allowNoTrailingSlash ?
            options.target : (options.target + '/');
        var store = (dope.rpc.JsonRest.services[options.target] || {})._store;
        if(store){
            return store;
        }
    }
    return new (Class || dojox.data.JsonRestStore)(options);
};
dojox.data._getStoreForItem = function(item){
    if(item.__id){
        var serviceAndId = dope.rpc.JsonRest.getServiceAndId(item.__id);
        if(serviceAndId && serviceAndId.service._store){
            return serviceAndId.service._store;
        }else{
            var servicePath = item.__id.toString().match(/.*\//)[0];
            return new dojox.data.JsonRestStore({target:servicePath});
        }
    }
    return null;
};
dojox.json.ref._useRefs = true; // Use referencing when identifiable objects are referenced