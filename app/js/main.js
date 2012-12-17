/* Author: Cameron Little

*/
var classes = ["I", "I+", "II", "II+", "III", "III+", "IV", "IV+", "V", "V+", "Unrunnable"];
var centerMarker = new google.maps.Marker({
	map: null,
	title: "Spot picked"
});
var showGaugesBool = false,
    showPlayspotsBool = false,
    showMarkersBool = false,
    showRapidsBool = false,
    lightboxActive = false;

window.onresize = function(e) {
	setMapHeight();
}

function initialize() { // on body load
	var mapOptions = {
		zoom: 10,
		mapTypeId: google.maps.MapTypeId.TERRAIN
	};
	map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
	console.log('Map loaded');
	google.maps.event.addListener(map, 'zoom_changed', function() {
		showByZoom();
	});
	
	// Try HTML5 geolocation
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			map.setCenter(pos);
		}, function() {
			handleNoGeolocation(true);
		});
	} else {
		// Browser doesn't support Geolocation
		handleNoGeolocation(false);
	}
	
	callOnInitialize();
}

function handleNoGeolocation(errorFlag) {
	if (errorFlag) {
		alert('Error: The Geolocation service failed.');
	} else {
		alert('Error: Your browser doesn\'t support geolocation.');
	}

	var options = {
		map: map,
		position: new google.maps.LatLng(47.7501, -123.7510),
		content: content
	};
}


/*function showAddress(address) {
	geocoder.geocode( { 'address': address }, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			pickermap.setCenter(results[0].geometry.location);
			centerMarker.setMap(null);
			centerMarker = new google.maps.Marker({
				map: pickermap,
				position: results[0].geometry.location,
				title: "Results"
			});
			document.getElementById("lat").value = results[0].geometry.location.lat();
			document.getElementById("lng").value = results[0].geometry.location.lng();
		} else {
			alert('Geocode was not successful for the following reason: ' + status);
		}
	});
}*/