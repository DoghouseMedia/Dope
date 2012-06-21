dojo.provide('dope.themes');

dojo.declare('dope.themes', null, {
	reload: function(theme) {
		dojo.place(
			dojo.create('link', {
				href: ["/js", theme, "themes", theme, theme+".css"].join('/'), 
				media: "all", 
				rel: "stylesheet", 
				type: "text/css"
			}),
			dojo.body()
		);
	} 	
});

var dopeThemes = new dope.themes();