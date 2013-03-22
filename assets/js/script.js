$(document).ready(function(){

	var radius = 20000,			// max radius of the search
		locale = 'fr',			// language of the search
		latitude,				// current latitude
		longitude,				// current longitude
		pois = [];				// (Points Of Interest) wikimedia results

	// Get the current location
	(function getLocation(){
		if(navigator.geolocation){
			navigator.geolocation.getCurrentPosition(setLocation);
		}else{
			lat.html("Geolocation is not supported by this browser.");
		}
	})();

	// Set the current location
	function setLocation(position){
		var latitude = position.coords.latitude,
			longitude = position.coords.longitude;
		$('#lat').html('Latitude: ' + latitude + '<br>Longitude: ' + longitude);
		getWikiLocation(latitude, longitude);
	}

	// Load the closest Landmarks in range
	function getWikiLocation(latitude, longitude){
		$.getJSON('http://api.wikilocation.org/articles?lat='+latitude+'&lng='+longitude+'&radius='+radius+'limit=50&locale='+locale+'&format=json&jsonp=?',
			function(data){
				initPOIs(data);
			}
		);
	}

	// Start all functions related to Points of Interest
	function initPOIs(data){
		getPOIs(data);
		listPOIs();
		loadArticle(pois[0].id);
		downloadClosest();
	}

	// Build an array with the data of close Landmarks
	function getPOIs(data){
		for(var i=0 ; i<data.articles.length ; i++){
			var d = data.articles[i];
			if(data.articles[i].type !== ''){
				pois.push(d);
			}
		}
	}

	// Display points of interest as a list
	function listPOIs(){
		var toAppend = '<ol>';
		for (var i=0 ; i<pois.length ; i++){
			toAppend += '<li><a href="#" data-id="'+pois[i].id+'" class="list">'+pois[i].title+'</a>';
			toAppend += ' / Distance : '+pois[i].distance+' / Type : '+pois[i].type+'</li>';
		}
		toAppend += '</ol>';
		$('#articles').append(toAppend);
		loadNext();
	}

	// Load next article on
	function loadNext(){
		$('.list').click(function(e){
			e.preventDefault();
			var u = $(this).attr('data-id');
			loadArticle(u);

		});
	}

	// Download closest
	function downloadClosest() {
		var c = pois[0].url;

		$.post("download_page.php", { "url": c },
			function(data){
				// console.log(data);
				$('#right').append(data);
			});
	}

	// Load and display the Wikipedia data for the closest Landmark
	function loadArticle(id){
		var query = 'http://'+locale+'.wikipedia.org/w/api.php?action=query&format=json&prop=revisions&rvprop=content&pageids='+id+'&callback=?';
		$.getJSON(query, function(data){
			var result = data.query.pages[id].revisions[0]['*'],
				title = data.query.pages[id].title;
			$('#closest').html('<h4>'+title+'</h4><p>'+result+'</p>');
		});
	}
});
