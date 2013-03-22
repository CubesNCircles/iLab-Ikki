(function() {

	var ikki = {
		config: {
			radius: 2000,
			locale: 'fr',
			latitude: '',
			longitude: '',
			articleWrapper: $('#right'),
			listWrapepr: $('#articles')
		},
		pois: [],

		/**
		 * Load options & Init the ikki object
		 * @param  array config config array
		 */
		init: function (config) {
			$.extend(this.config, config);
			this.getLocation.call();
		},

		/**
		 * Get the current location w/ geolocation
		 */
		getLocation: function() {
			if(navigator.geolocation) {
				navigator.geolocation.getCurrentPosition( ikki.setLocation );
			} else {
				lat.html("Geolocation is not supported by this browser.");
			}
		},

		/**
		 * Set the location variables
		 */
		setLocation: function(position) {
			var config = ikki.config;
			config.latitude = position.coords.latitude;
			config.longitude = position.coords.longitude;
			ikki.getWikiLocation(config.latitude, config.longitude);
		},

		/**
		 * Query the Wiki API & get close locations
		 * @param  {int} latitude
		 * @param  {int} longitude
		 */
		getWikiLocation: function(latitude, longitude) {
			var config = ikki.config;

			$.getJSON('http://api.wikilocation.org/articles?lat=' + config.latitude + '&lng=' + config.longitude + '&radius=' + config.radius + 'limit=50&locale=' + config.locale + '&format=json&jsonp=?',
				function(data){
					ikki.getPOIs(data);
				}
			);
		},

		/**
		 * Get Points of Interest
		 * @param  {obj} data
		 */
		getPOIs: function(data) {
			for(var i=0 ; i < data.articles.length ; i++){
				var d = data.articles[i];
				if(data.articles[i].type !== ''){
					this.pois.push(d);
				}
			}
			ikki.listPOIs();
			ikki.downloadClosest();
		},

		/**
		 * Make a list with the point of interests
		 */
		listPOIs: function() {
			var toAppend = '<li class="dropdown">',
				pois = this.pois,
				listWrapper = this.config.listWrapepr,
				articleWrapper = this.config.articleWrapper;
			toAppend += '<a href="#" class="dropdown-toggle" data-toggle="dropdown">Choose destination <b class="caret"></b></a>';
			toAppend += '<ul class="dropdown-menu">';
			for (var i=0 ; i < pois.length ; i++){
				toAppend += '<li><a href="#" data-id="'+pois[i].id+'" data-url="'+pois[i].url+'" class="list">'+pois[i].title+'</li>';
				// toAppend += ' <span class="badge badge-info">'+pois[i].distance+'</span>  <span class="badge badge-warning">'+pois[i].type+'</span></a></li>';
				// toAppend += '<li class="divider"></li>';
			}
			toAppend += '</ul></li>';
			listWrapper.append(toAppend);
			listWrapper.find('a').on('click', function(e) {
				e.preventDefault();
				// get the url
				var url = $(this).data('url');
				ikki.downloadPage(url);
			});
		},

		downloadClosest: function() {
			var url = this.pois[0].url;
			ikki.downloadPage(url);
		},
		/**
		 * Download the given page
		 * @param  {str} url
		 */
		downloadPage: function(url) {
			var articleWrapper = this.config.articleWrapper;
			// ajax post to download page & append to #right
			$.post("download_page.php", { "url": url }, function(data){
				articleWrapper.html('');
				articleWrapper.append(data);
			});
		}
	};
	// Start things off, options possible
	ikki.init();

})();
