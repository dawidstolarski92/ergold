$(document).ready(function () {

	if ($('.pk_instafeed')[0]) {

		var debug = false,
			$el = $('.pk_instafeed'),
			config = {
				apicode: $el.data('apicode'),
				apisecret: $el.data('apisecret'),
				at: $el.data('at'),
				apicallback: $el.data('apicallback'),
				carousel: $el.data('carousel'),
				contenttype: $el.data('contenttype'),
				username: $el.data('username'),
				userid: $el.data('userid'),
				sortby: $el.data('sortby'),
				number: $el.data('number'),
				numbervis: $el.data('numbervis'),
				hashtag: $el.data('hashtag'),
				links: $el.data('links'),
				back: $el.data('back'),
				auto: $el.data('auto'),
				template: $el.data('template'),
				suffix: $el.data('suffix')
			};

		var feed = new Instafeed({
			clientId: config.apicode,
		    accessToken: config.at,
		    target: 'instafeed_'+config.suffix,    
		    get: config.contenttype,	
		    tagName: config.hashtag,
		    userId: parseInt(config.userid),
		    sortBy: config.sortby,
		    limit: parseInt(config.number),
		    links: Boolean(config.links),
		    template: config.template,
		    resolution: "standard_resolution"
		});

		if (debug) {
			console.log("API: "+config.apicode+"\naccessToken: "+config.at+"\ntarget: instafeed_"+config.suffix+"\nget: "+config.contenttype+"\ntagName: "+config.hashtag+"\nuserId: "+config.userid+"\nsortBy: "+config.sortby+"\nlimit: "+parseInt(config.number)+"\nlinks: "+Boolean(config.links)+"\ntemplate: "+config.template+"\nresponse:");
			console.log(feed);
		}

		if (config.apicode !== '' && config.apisecret !== '' && config.at !== '' && config.apicallback !== '') {
			feed.run();
		}
	  	
	  	if (config.carousel == true)
			$("#instafeed_"+config.suffix).flexisel({
			    pref: "insta"+config.suffix,
			    visibleItems: parseInt(config.numbervis),
			    animationSpeed: 500,
			    autoPlay: config.auto,
			    autoPlaySpeed: 3000,            
			    pauseOnHover: true,
			    enableResponsiveBreakpoints: true,
			    clone : true,
			    responsiveBreakpoints: { 
			        portrait: { 
			            changePoint:400,
			            visibleItems: 1
			        }, 
			        landscape: { 
			            changePoint:768,
			            visibleItems: 2
			        },
			        tablet: { 
			            changePoint:991,
			            visibleItems: 3
			        },
			        tablet_land: { 
			            changePoint:1199,
			            visibleItems: parseInt(config.numbervis)
			        }
			    }
			});

		if (config.back == true) 
	    	parallax($(".instagram-feed"));

		$(".instagram-feed").find(".flexisel-nav").appendTo(".instagram-feed .carousel-title");

	}

});