dojo.provide('dope.form.Uploader');
dojo.require('dojox.form.Uploader');
dojo.require('dojox.form.uploader.FileList');
dojo.require("dojox.form.uploader.plugins.HTML5");

dojo.declare('dope.form.Uploader', dojox.form.Uploader, {
	baseClass: 'dopeFileInput',
	uploadOnSelect: false,
	label: 'Select file',
	filenameNode: null,
	multiple: false,
	
	postCreate: function() {
        /*
         * Make the form aware of the uploader
         */
		dijit.byNode(this.getForm()).uploader = this;
		this.inherited(arguments);
	},

	onChange: function(fileArray) {
		this.inherited(arguments);
		this.setLabel(fileArray[0].name);
	},

    reset: function() {
        this.setLabel(null);
        return this.inherited(arguments);
    },

	setLabel: function(label) {
        /*
         * Create and inject the filename SPAN if it doesn't exist
         */
        if (!this.filenameNode) {
            this.filenameNode = dojo.create('span', {
                innerHTML: 'No file selected',
                className: 'dopeFileName'
            });
            dojo.place(this.filenameNode, this.domNode, 'last');
        }

		this.label = label;
		this.filenameNode.innerText = label;
		return this;
	},
	
	_getFileFieldName: function() {
		if (this.multiple) {
			if (this.name.substr(-1) == ']') {
				return this.name + '[]';
			} else {
				return this.name + 's[]';
			}
		} else {
			return this.name;
		}
	},

	uploadWithFormData: function(/* Object */data){
		// summary
		// 		Used with WebKit and Firefox 4+
		// 		Upload files using the much friendlier FormData browser object.
		// tags:
		// 		private
		//
		if(!this.getUrl()){
			console.error("No upload url found.", this); return;
		}

		var fd = new FormData();
		dojo.forEach(this.inputNode.files, function(f, i){
			fd.append(this._getFileFieldName(), f); // Dope
		}, this);

		if(data){
			for(var nm in data){
				fd.append(nm, data[nm]);
			}
		}

		var xhr = this.createXhr();
		xhr.send(fd);
	},

	createXhr: function(){
		var xhr = new XMLHttpRequest();
		var timer;
        xhr.upload.addEventListener("progress", dojo.hitch(this, "_xhrProgress"), false);
        xhr.addEventListener("load", dojo.hitch(this, "_xhrProgress"), false);
        xhr.addEventListener("error", dojo.hitch(this, function(evt){
			this.onError(evt);
			clearInterval(timer);
		}), false);
        xhr.addEventListener("abort", dojo.hitch(this, function(evt){
			this.onAbort(evt);
			clearInterval(timer);
		}), false);
        xhr.onreadystatechange = dojo.hitch(this, function() {
			if (xhr.readyState === 4) {
				console.info("COMPLETE")
				clearInterval(timer);
				console.log(xhr, 'xhr');
				this.onComplete(xhr.responseText);
				// Dope
				dijit.byNode(this.getForm()).onComplete(
					dojo.fromJson(xhr.responseText),
					xhr
				);
			}
		});
        xhr.open("POST", this.getUrl());

		timer = setInterval(dojo.hitch(this, function(){
			try{
				if(typeof(xhr.statusText)){} // accessing this error throws an error. Awesomeness.
			}catch(e){
				//this.onError("Error uploading file."); // not always an error.
				clearInterval(timer);
			}
		}),250);

		// Dope
		xhr.setRequestHeader('Accept', 'application/json');

		return xhr;
	},

	_buildRequestBody : function(data, boundary) {
		var EOL  = "\r\n";
		var part = "";
		boundary = "--" + boundary;

		var filesInError = [];
		dojo.forEach(this.inputNode.files, function(f, i){
			var fieldName = this._getFileFieldName(); // Dope
			var fileName  = this.inputNode.files[i].fileName;
			var binary;

			try{
				binary = this.inputNode.files[i].getAsBinary() + EOL;
				part += boundary + EOL;
				part += 'Content-Disposition: form-data; ';
				part += 'name="' + fieldName + '"; ';
				part += 'filename="'+ fileName + '"' + EOL;
				part += "Content-Type: " + this.getMimeType() + EOL + EOL;
				part += binary;
			}catch(e){
				filesInError.push({index:i, name:fileName});
			}
		}, this);

		if(filesInError.length){
			if(filesInError.length >= this.inputNode.files.length){
				// all files were bad. Nothing to upload.
				this.onError({
					message:this.errMsg,
					filesInError:filesInError
				});
				part = false;
			}
		}

		if(!part) return false;

		if(data){
			for(var nm in data){
				part += boundary + EOL;
				part += 'Content-Disposition: form-data; ';
				part += 'name="' + nm + '"' + EOL + EOL;
				part += data[nm] + EOL;
			}
		}


		part += boundary + "--" + EOL;
		return part;
	}
});