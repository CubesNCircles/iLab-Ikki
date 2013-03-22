(function() {

	var ikki = {
		config: {
			radius: 2000,
			locale: 'fr',
			latitude: '',
			longitude: ''
		},
		pois: [],

		init: function (config) {
			$.extend(this.config, config);
			this.getLocation.call();
		},

		getLocation: function() {
			if(navigator.geolocation) {
				navigator.geolocation.getCurrentPosition( ikki.setLocation );
			} else {
				lat.html("Geolocation is not supported by this browser.");
			}
		},

		setLocation: function(position) {
			var config = ikki.config;
			config.latitude = position.coords.latitude;
			config.longitude = position.coords.longitude;
			ikki.getWikiLocation(config.latitude, config.longitude);
		},

		getWikiLocation: function(latitude, longitude) {
			var config = ikki.config;

			$.getJSON('http://api.wikilocation.org/articles?lat=' + config.latitude + '&lng=' + config.longitude + '&radius=' + config.radius + 'limit=50&locale=' + config.locale + '&format=json&jsonp=?',
				function(data){
					ikki.getPOIs(data);
				}
			);
		},

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

		listPOIs: function() {
			var toAppend = '<ol>',
				pois = this.pois;
			for (var i=0 ; i < pois.length ; i++){
				toAppend += '<li><a href="#" data-id="'+pois[i].id+'" data-url="'+pois[i].url+'" class="list">'+pois[i].title+'</a>';
				toAppend += ' / Distance : '+pois[i].distance+' / Type : '+pois[i].type+'</li>';
			}
			toAppend += '</ol>';
			$('#articles').append(toAppend);
			$('.list').click(function(e){
				e.preventDefault();
				var u = $(this).attr('data-id');
				loadArticle(u);

				// get the url
				var url = $(this).attr('data-url');

				// ajax post to download page & append to #right
				$.post("download_page.php", { "url": url }, function(data){
					// console.log(data);
					$('#right').html('');
					$('#right').append(data);
				});

			});
		},

		downloadClosest: function() {
			var c = this.pois[0].url;

			$.post("download_page.php", { "url": c },
			function(data){
				// console.log(data);
				$('#right').append(data);
			});
		}
	};

	ikki.init({
		radius: 10000
	});

})();
