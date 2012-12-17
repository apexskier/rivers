/* Author: Cameron Little

*/
var classes = ["I", "I+", "II", "II+", "III", "III+", "IV", "IV+", "V", "V+", "Unrunnable"];
var centerMarker = new google.maps.Marker({
	map: null,
	title: "Spot picked"
});
var allMarkers = new Array();
var showGaugesBool = false,
    showPlayspotsBool = false,
    showMarkersBool = false,
    showRapidsBool = false,
    lightboxActive = false;

window.onresize = function(e) {
	setHeight($('#main').height(), false);
}

function initialize() { // on body load
	function minimize() {
		setHeight(0);
		setClickHandlers();
		$('.content').html("");
	}
	$('.minimize').click(function() {
		minimize();
	});
	$(document).keydown(function(e) {
	    // ESCAPE key pressed
	    if (e.keyCode == 27) {
	    	if (!lightboxActive) {
		    	minimize();
	    	} else {
		    	$('#lightbox-bg').fadeOut(300, function() {
			    	lightboxActive = false;
		    	});
	    	}
	    }
	});
	
	var mapOptions = {
		zoom: 10,
		mapTypeId: google.maps.MapTypeId.TERRAIN
	};
	map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
	console.log('map loaded');
	
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
	
	for (var i = 0; i < runs_array.length; i++) {
		runs_array[i].setMap(map);
	}
	for (var i = 0; i < playspots_in_array.length; i++) {
		playspots_in_array[i].setMap(map);
	}
	
	setHeight($('#main').height(), false);
	setClickHandlers();
	
	if ($('.success').length > 0) {
		$('.success').delay(2000).fadeOut(300);
	}
	if ($('.error').length > 0) {
		$('.error').delay(2000).fadeOut(300);
	}
	if ($('.intro').length > 0) {
		$('.intro .close').click(function() {
			$('.intro').fadeOut(300);
		});
	}
	
	$('#main .photo').click(function() {
		$('#lightbox').empty();
		var webimage = new Image();
		webimage.src = "/img/user/uploaded/web/" + this.getAttribute('data-photo-id');
		
		$('#lightbox').append("<span class='close'>X</span>");
		if (this.getAttribute('data-title') != "") {
			$('#lightbox').append("<h3>" + this.getAttribute('data-title') + "</h3>");
		}
		$('#lightbox').append(webimage);
		$('#lightbox').append("<span class='meta'>" + this.getAttribute('data-description') + " <a href='/img/user/uploaded/original/" + this.getAttribute('data-photo-id') + "' target='_blank'>View original.</a><p></span>");
		
		$('#lightbox .close').click(function() {
			$('#lightbox-bg').fadeOut(300);
		});
		
		$('#lightbox-bg').fadeIn(300);
		$('#lightbox').fadeIn(300);
		lightboxActive = true;
	});
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


/* Map for picking a latitude and longitude for php input */
function loadPickerMap() {
	geocoder = new google.maps.Geocoder();
	var latVal;
	var lngVal;
	
	centerMarker.setPosition(map.getCenter());
	centerMarker.setMap(map);
	
	google.maps.event.addListener(map, 'click', function(event) {
		centerMarker.setPosition(event.latLng);
		map.panTo(event.latLng);
		latVal = event.latLng.lat();
		lngVal = event.latLng.lng()
		document.getElementById("lat").value = Math.round(latVal * Math.pow(10, 10)) / Math.pow(10, 10);
		document.getElementById("lng").value = Math.round(lngVal * Math.pow(10, 10)) / Math.pow(10, 10);
	});
	

	$('.lat').bind("propertychange keyup input paste", function(event){
		if (latVal != $(this).val()) {
			latVal = $(this).val();
			var newPos = new google.maps.LatLng(latVal, lngVal);
			centerMarker.setPosition(newPos);
			if (lngVal != "" && lngVal != null) {
				map.panTo(newPos);
			}
		}
	});
	
	$('.lng').bind("propertychange keyup input paste", function(event){
		if (lngVal != $(this).val()) {
			lngVal = $(this).val();
			var newPos = new google.maps.LatLng(latVal, lngVal);
			centerMarker.setPosition(newPos);
			if (latVal != "" && latVal != null) {
				map.panTo(newPos);
			}
		}
	});

}

$('#pickermap_button').click(function() {
	showAddress(document.getElementById("address").value);
});

function showAddress(address) {
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
}

var standard_icon_size = new google.maps.Size(28, 28);
var standard_icon_anchor = new google.maps.Point(14, 14);
var river_access_icon = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	new google.maps.Size(29, 27),
	new google.maps.Point(56, 29),
	new google.maps.Point(14, 13)
);
var fork_icon = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	new google.maps.Size(26, 28),
	new google.maps.Point(56, 78),
	new google.maps.Point(13, 14)
);
var gauge_icon = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	new google.maps.Size(29, 29),
	new google.maps.Point(56, 0),
	new google.maps.Point(15, 15)
);
var playspot_icon = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	new google.maps.Size(32, 22),
	new google.maps.Point(56, 56),
	new google.maps.Point(16, 11)
);
var hazard_icon = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	new google.maps.Size(27, 25),
	new google.maps.Point(56, 106),
	new google.maps.Point(13, 12)
);
var rapid_1_icon = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	standard_icon_size,
	new google.maps.Point(0, 0),
	standard_icon_anchor
);
var rapid_2_icon = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	standard_icon_size,
	new google.maps.Point(0, 28),
	standard_icon_anchor
);
var rapid_3_icon = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	standard_icon_size,
	new google.maps.Point(0, 56),
	standard_icon_anchor
);
var rapid_4_icon = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	standard_icon_size,
	new google.maps.Point(0, 84),
	standard_icon_anchor
);
var rapid_5_icon = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	standard_icon_size,
	new google.maps.Point(0, 112),
	standard_icon_anchor
);
var rapid_1plus_icon = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	standard_icon_size,
	new google.maps.Point(28, 0),
	standard_icon_anchor
);
var rapid_2plus_icon = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	standard_icon_size,
	new google.maps.Point(28, 28),
	standard_icon_anchor
);
var rapid_3plus_icon = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	standard_icon_size,
	new google.maps.Point(28, 56),
	standard_icon_anchor
);
var rapid_4plus_icon = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	standard_icon_size,
	new google.maps.Point(28, 84),
	standard_icon_anchor
);
var rapid_5plus_icon = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	standard_icon_size,
	new google.maps.Point(28, 112),
	standard_icon_anchor
);
var rapid_unrunnable = new google.maps.MarkerImage(
	'img/icons-sprite.png',
	standard_icon_size,
	new google.maps.Point(0, 140),
	standard_icon_anchor
);

function createMarker(id_, type_, p, t, d, marker_type) {
	switch (marker_type) {
		case "River Access":
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d,
				icon: river_access_icon
			});
			break;
		case "Fork":
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d,
				icon: fork_icon
			});
			break;
		case "gauge":
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d,
				icon: gauge_icon
			});
			break;
		case "playspot":
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d,
				icon: playspot_icon
			});
			break;
		case "playspot_in":
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d,
				icon: playspot_icon
			});
			break;
		case "I":
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d,
				icon: rapid_1_icon,
			});
			break;
		case "I+":
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d,
				icon: rapid_1plus_icon,
			});
			break;
		case "II":
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d,
				icon: rapid_2_icon,
			});
			break;
		case "II+":
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d,
				icon: rapid_2plus_icon,
			});
			break;
		case "III":
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d,
				icon: rapid_3_icon,
			});
			break;
		case "III+":
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d,
				icon: rapid_3plus_icon,
			});
			break;
		case "IV":
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d,
				icon: rapid_4_icon,
			});
			break;
		case "IV+":
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d,
				icon: rapid_4plus_icon,
			});
			break;
		case "V":
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d,
				icon: rapid_5_icon,
			});
			break;
		case "V+":
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d,
				icon: rapid_5plus_icon,
			});
			break;
		default:
			var new_marker = new google.maps.Marker({
				id: id_,
				type: type_,
				position: p,
				map: null,
				title: t,
				data: d
			});
			break;
	}
	allMarkers.push(new_marker);
	return new_marker;
}

function createRun(id_, type_, t, d) {
	var new_polyline = new google.maps.Polyline({
		id: id_,
		type: type_,
		map: null,
		strokeColor: 'rgb(255, 255, 255)',
		strokeOpacity: 0.75,
		strokeWeight: 8,
		title: t,
		data: d
	});
	google.maps.event.addListener(new_polyline, 'mouseover', function() {
		this.setOptions({ strokeOpacity: 1.0 });
	});
	google.maps.event.addListener(new_polyline, 'mouseout', function() {
		this.setOptions({ strokeOpacity: 0.75 });
	});
	return new_polyline;
}

function setHeight(contentHeight, animateBool) {
	animateBool = typeof animateBool !== 'undefined' ? animateBool : true;
	var minMapHeight = 200,
	    minFooterHeight = 0,
	    animationSpeed = 150;
	var preCenter = map.getCenter();
	var availableHeight = $(window).height() - 82;
	
	if (animateBool) {
		function displayMinimize() {
			if ($('#main').height() > minFooterHeight) {
				$('.minimize').show();
			} else {
				$('.minimize').hide();
				if (centerMarker) {
					centerMarker.setMap(null);
				};
			}
			google.maps.event.trigger(map, 'resize');
			map.panTo(preCenter);
		}
		if (contentHeight > availableHeight - minMapHeight) {
			if (availableHeight > minMapHeight + minFooterHeight) {
				$('#main').animate({
					height: availableHeight - minMapHeight
				}, animationSpeed, function() {
					displayMinimize();
				});
			} else {
				$('#main').animate({
					height: minFooterHeight
				}, animationSpeed, function() {
					displayMinimize();
				});
			}
			$('#map_canvas').animate({
				height: minMapHeight
			}, animationSpeed, function() {
    			displayMinimize();
			});
		} else {
			$('#main').animate({
				height: contentHeight
			}, animationSpeed, function() {
				displayMinimize();
			});
			$('#map_canvas').animate({
				height: availableHeight - contentHeight
			}, animationSpeed, function() {
    			displayMinimize();
    		});
		}
	} else {
		if (contentHeight > availableHeight - minMapHeight) {
			if (availableHeight > minMapHeight + minFooterHeight) {
				$('#main').height(availableHeight - minMapHeight);
			} else {
				$('#main').height(minFooterHeight);
			}
			$('#map_canvas').height(minMapHeight);
		} else {
			$('#main').height(contentHeight);
			$('#map_canvas').height(availableHeight - contentHeight);
		}
		if ($('#main').height() > minFooterHeight) {
			$('.minimize').show();
		} else {
			$('.minimize').hide();
			if (centerMarker) {
				centerMarker.setMap(null);
			};
		}
	}
}

function loadForm(form_url, pickermap_bool) {
	$('.content').load(form_url, function() {
		if (pickermap_bool) {
			loadPickerMap();
		}
		loadSlider();
		switch (form_url) {
			case 'add-playspot.php':
				setClickHandlers('gauge');
				break;
			case 'add-run.php':
				setClickHandlers('gauge');
				break;
			case 'add-video.php':
				setClickHandlers('media');
				break;
			case 'add-photo.php':
				setClickHandlers('media');
				break;
			default:
				setClickHandlers();
				break;
		}
	});
	$('#user-controls ul').slideToggle(150);
	setHeight(1000);
}

function loadSlider() {
	if ($(".class-slider").length > 0) {
		$(".class-slider").slider({
			max: 10,
			min: 0,
			step: 1,
			slide: function( event, ui ) {
				$(".class-slider-value").html(classes[ui.value]);
				document.getElementById("class-value").value = classes[ui.value];
			}
		});
	}
	if ($(".run-class-slider").length > 0) {
		$(".run-class-slider").slider({
			max: 10,
			min: 0,
			step: 1,
			range: true,
			values: [4, 6],
			slide: function( event, ui ) {
				if (ui.values[0] == ui.values[1]) {
					$(".run-class-slider-value-low").html(classes[ui.values[0]]);
					$(".run-class-slider-value-high").html("");
					document.getElementById("class-value").value = classes[ui.values[0]];
				} else {
					$(".run-class-slider-value-low").html(classes[ui.values[0]] + "/");
					$(".run-class-slider-value-high").html(classes[ui.values[1]]);
					document.getElementById("class-value").value = classes[ui.values[0]] + "/" + classes[ui.values[1]];
				}
			}
		});
	}
}

function setClickHandlers(action) {
	switch (action) {
		case "gauge":
			for (var i = 0; i < gauges_array.length; i++) {
				google.maps.event.clearListeners(gauges_array[i], 'click');
				google.maps.event.addListener(gauges_array[i], 'click', function() {
					$('input[name=gauge_id]').val(this.id);
					$(".guage_title").html(this.title);
				});
			}
			break;
		case "media":
			function mediaAction(e) {
				$('input[name=associated_id]').val(e.id);
				$('input[name=associated_type]').val(e.type);
				if (e.title == "") {
					$(".associated_title").html("Untitled " + e.type);
				} else {
					$(".associated_title").html(e.title + " " + e.type);
				}
			}
			for (var i = 0; i < allMarkers.length; i++) {
				google.maps.event.clearListeners(allMarkers[i], 'click');
				google.maps.event.addListener(allMarkers[i], 'click', function() {
					mediaAction(this);
				});
			}
			for (var i = 0; i < runs_array.length; i++) {
				google.maps.event.clearListeners(runs_array[i], 'click');
				google.maps.event.addListener(runs_array[i], 'click', function() {
					mediaAction(this);
				});
			}
			for (var i = 0; i < gauges_array.length; i++) {
				google.maps.event.clearListeners(gauges_array[i], 'click');
				google.maps.event.addListener(gauges_array[i], 'click', function() {
					mediaAction(this);
				});
			}
			break;
		default:
			function defaultAction(e) {
				var contentHTML = "";
				if (e.title != "") {
					contentHTML += "<h3>" + e.title + "</h3>";
				}
				if (e.data != "") {
					contentHTML += e.data;
				}
				$('.content').html(contentHTML);
				setHeight($('.content').height() + 50);
				/* !lightbox */
				$('#main .photo').click(function() {
					$('#lightbox').empty();
					var webimage = new Image();
					webimage.src = "/img/user/uploaded/web/" + this.getAttribute('data-photo-id');
					
					$('#lightbox').append("<span class='close'>X</span>");
					if (this.getAttribute('data-title') != "") {
						$('#lightbox').append("<h3>" + this.getAttribute('data-title') + "</h3>");
					}
					$('#lightbox').append(webimage);
					$('#lightbox').append("<span class='meta'>" + this.getAttribute('data-description') + " <a href='/img/user/uploaded/original/" + this.getAttribute('data-photo-id') + "' target='_blank'>View original.</a><p></span>");
					
					$('#lightbox .close').click(function() {
						$('#lightbox-bg').fadeOut(300);
					});
					
					$('#lightbox-bg').fadeIn(300);
					$('#lightbox').fadeIn(300);
					lightboxActive = true;
				});
			}
			for (var i = 0; i < allMarkers.length; i++) {
				google.maps.event.clearListeners(allMarkers[i], 'click');
				google.maps.event.addListener(allMarkers[i], 'click', function() {
					defaultAction(this);
				});
			}
			for (var i = 0; i < runs_array.length; i++) {
				google.maps.event.clearListeners(runs_array[i], 'click');
				google.maps.event.addListener(runs_array[i], 'click', function() {
					defaultAction(this);
				});
			}
			for (var i = 0; i < gauges_array.length; i++) {
				google.maps.event.clearListeners(gauges_array[i], 'click');
				google.maps.event.addListener(gauges_array[i], 'click', function() {
					defaultAction(this);
				});
			}
			break;
	}
}

function showGauges() {
	if (showGaugesBool) {
		if (map.getZoom() < 11) {
			for (var i = 0; i < gauges_array.length; i++) {
				gauges_array[i].setMap(null);
			}
		}
		showGaugesBool = false;
	} else {
		if (map.getZoom() < 11) {
			for (var i = 0; i < gauges_array.length; i++) {
				gauges_array[i].setMap(map);
			}
		}
		showGaugesBool = true;
	}
}
function showPlayspots() {
	if (showPlayspotsBool) {
		if (map.getZoom() < 12) {
			for (var i = 0; i < playspots_array.length; i++) {
				playspots_array[i].setMap(null);
			}
		}
		if (map.getZoom() < 10) {
			for (var i = 0; i < playspots_in_array.length; i++) {
				playspots_in_array[i].setMap(null);
			}
		}
		showPlayspotsBool = false;
	} else {
		if (map.getZoom() < 12) {
			for (var i = 0; i < playspots_array.length; i++) {
				playspots_array[i].setMap(map);
			}
		}
		if (map.getZoom() < 10) {
			for (var i = 0; i < playspots_in_array.length; i++) {
				playspots_in_array[i].setMap(map);
			}
		}
		showPlayspotsBool = true;
	}
}
function showMarkers() {
	if (showMarkersBool) {
		if (map.getZoom() < 12) {
			for (var i = 0; i < markers_array.length; i++) {
				markers_array[i].setMap(null);
			}
		}
		showMarkersBool = false;
	} else {
		if (map.getZoom() < 12) {
			for (var i = 0; i < markers_array.length; i++) {
				markers_array[i].setMap(map);
			}
		}
		showMarkersBool = true;
	}
}
function showRapids() {
	if (showRapidsBool) {
		if (map.getZoom() < 13) {
			for (var i = 0; i < rapids_array.length; i++) {
				rapids_array[i].setMap(null);
			}
		}
		showRapidsBool = false;
	} else {
		if (map.getZoom() < 13) {
			for (var i = 0; i < rapids_array.length; i++) {
				rapids_array[i].setMap(map);
			}
		}
		showRapidsBool = true;
	}
}

function gotoMarker(id) {
	map.panTo(id.getPosition());
	if (map.getZoom() < 14) {
		map.setZoom(14);
	}
}