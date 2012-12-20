/* Author: Cameron Little

*/
var classes = ["I", "I+", "II", "II+", "III", "III+", "IV", "IV+", "V", "V+", "Unrunnable"];
var centerMarker = new google.maps.Marker({
	map: null,
	title: "Spot picked"
});
var myPos = new google.maps.Marker({
	map: null,
	title: "You are here"
});
var showGaugesBool = false,
    showPlayspotsBool = false,
    showMarkersBool = false,
    showRapidsBool = false,
    lightboxActive = false;

window.onresize = function(e) {
	setMapHeight();
}

var all_markers_array = {};
var all_rapids_array = {};
var locations_loaded = {};

function initialize(lat, lng, zoom, type, id) { // on body load
	var mapOptions = {
		zoom: 11,
		mapTypeId: google.maps.MapTypeId.TERRAIN,
		center: new google.maps.LatLng(48.118146, -123.430741)
	};
	map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
	console.log('Map loaded');
	
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			myPos.setMap(map);
			myPos.setPosition(pos);
		});
	}
	
	if (lat == null || lng == null) {
		goToMe();
	} else {
		map.setCenter(new google.maps.LatLng(lat, lng));
		if (zoom != null) {
			map.setZoom(zoom);
		}
	}
	
	callOnInitialize();
}

function goToMe() {
	// Try HTML5 geolocation
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			map.panTo(pos);
			myPos.setMap(map);
			myPos.setPosition(pos);
			setMapLink();
		});
	} else {
		// Browser doesn't support Geolocation
		alert("We can't seem to find you!");
	}
}

function setMapLink() {
	var center = map.getCenter();
	var currentURL = "/" + (Math.round(center.lat() * 1000000000)/1000000000) + "/" + (Math.round(center.lng() * 1000000000)/1000000000) + "/" + map.getZoom();
	$('.map-link').attr("href", "http://rivers.camlittle.com" + currentURL);
	window.history.replaceState("", "", currentURL);
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

	
function callOnInitialize() {
	setMapHeight();
	
	google.maps.event.addListener(map, 'zoom_changed', function() {
		showByZoom();
		setMapLink();
		var southwest_lat = parseInt(map.getBounds().getSouthWest().lat());
		var southwest_lng = parseInt(map.getBounds().getSouthWest().lng());
		var northeast_lat = parseInt(map.getBounds().getNorthEast().lat());
		var northeast_lng = parseInt(map.getBounds().getNorthEast().lng());
		for (var lat = southwest_lat; lat <= northeast_lat; lat++) {
			for (var lng = southwest_lng; lng <= northeast_lng; lng++) {
				getMarkersAroundCenter(lat, lng);
			}
		}
	});
	$('#map_canvas').mouseup(function() {
		setMapLink();
		var southwest_lat = parseInt(map.getBounds().getSouthWest().lat());
		var southwest_lng = parseInt(map.getBounds().getSouthWest().lng());
		var northeast_lat = parseInt(map.getBounds().getNorthEast().lat());
		var northeast_lng = parseInt(map.getBounds().getNorthEast().lng());
		for (var lat = southwest_lat; lat <= northeast_lat; lat++) {
			for (var lng = southwest_lng; lng <= northeast_lng; lng++) {
				getMarkersAroundCenter(lat, lng);
			}
		}
	});
	
	$('.status').ajaxStart(function() {
		$(this).html('Loading<span class="dots"></span>');
		animateLoad();
	});
	function animateLoad() {
		var text = '....';
		jQuery({count:0}).animate({count:text.length}, {
			duration: 2000,
			step: function() {
				$(".dots").text( text.substring(0, Math.round(this.count)) );
			},
			complete: function() {
				animateLoad();
			}
		});
	}
	
	$('.status').ajaxStop(function() {
		$(this).html('Resources loaded.');
	});
	
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
	
	if ($('.alert-success').length > 0) {
		$('.alert-success').delay(3000).fadeOut(300);
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

var showGaugesBool = false,
	showPlayspotsBool = false,
	showMarkersBool = false,
	showRapidsBool = false;

var gaugeZoomThreshold = 9,
	playspotZoomThreshold = 12,
	markerZoomThreshold = 12,
	rapidZoomThreshold = 11;

function showByZoom() {
	var current_zoom = map.getZoom();
	
	setByZoom(gaugeZoomThreshold, gauges_markers_array);
	setByZoom(playspotZoomThreshold, playspots_markers_array);
	setByZoom(markerZoomThreshold, river_markers_array);
	setByZoom(rapidZoomThreshold, rapids_markers_array);
	
	function setByZoom(threshold, array) {
		if (current_zoom >= threshold) {
			$.each(array, function(key, e) {
				e.setMap(map);
			});
		} else {
			$.each(array, function(key, e) {
				e.setMap(null);
			});
		}
	}
}

function showByType(element, type_array, zoom_level) {
	var vis_bool = element.checked;
	if (vis_bool) {
		for (var i = 0; i < type_array.length; i++) {
			type_array[i].setMap(map);
		}
	} else {
		if (map.getZoom() <= zoom_level) {
			for (var i = 0; i < type_array.length; i++) {
				type_array[i].setMap(map);
			}
		} else {
			for (var i = 0; i < type_array.length; i++) {
				type_array[i].setMap(null);
			}
		}
	}
}

function setMapHeight() {
	$('#map_canvas').height($(window).height() - $('header').height() - $('footer').height() - $('.main-container').height());
}
var animationSpeed = 150;
var minMapHeight = 200;
function pullContent() {
	var preCenter = map.getCenter();
	var availableHeight = $(window).height() - $('header').height() - $('footer').height();
	var contentHeight = $('#main').height() + 10;
	$('.content').addClass('open');
	if (contentHeight > availableHeight - minMapHeight) {
		$('.main-container').css("height", availableHeight - minMapHeight);
		$('#map_canvas').animate({
			height: minMapHeight
		}, animationSpeed, function() {
			google.maps.event.trigger(map, 'resize');
			map.panTo(preCenter);
		});
		$('.content').addClass('overflowing');
	} else {
		$('.main-container').css("height", $('#main').height() + 10);
		$('#map_canvas').animate({
			height: availableHeight - $('.main-container').height()
		}, animationSpeed, function() {
			google.maps.event.trigger(map, 'resize');
			map.panTo(preCenter);
		});
	}
}
function minimize() {
	var preCenter = map.getCenter();
	$('.main-container').animate({
		height: 0
	}, animationSpeed);
	$('#map_canvas').animate({
		height: $(window).height() - $('header').height() - $('footer').height()
	}, animationSpeed, function() {
		google.maps.event.trigger(map, 'resize');
		map.panTo(preCenter);
	});
	$('.content').html("").removeClass('open').removeClass('overflowing');
	if (centerMarker) {
		centerMarker.setMap(null);
	};
	setClickHandlers();
}

function loadForm(form_url, pickermap_bool, click_handlers) {
	$('.content').load(form_url, function() {
		if (centerMarker) {
			centerMarker.setMap(null);
		};
		setClickHandlers(click_handlers);
		if (pickermap_bool) {
			loadPickerMap();
		}
		loadSlider();
		pullContent();
	});
}

function loadRegistration() {
	$.getScript("http://www.google.com/recaptcha/api/js/recaptcha_ajax.js", function() {
		$('.content').load('/app/php/forms/register.php', function() {
			Recaptcha.create("6Lfe0tUSAAAAACJW64aO2_fowRUsjV-zsahPZePZ", "recaptcha", {
				theme: "red",
				callback: function() {
					if (centerMarker) {
						centerMarker.setMap(null);
					};
					setClickHandlers();
					pullContent();
				}
			});
		});
	});
}

/* Map for picking a latitude and longitude for php input */
function loadPickerMap() {
	geocoder = new google.maps.Geocoder();
	var latVal;
	var lngVal;
	
	centerMarker.setPosition(map.getCenter());
	centerMarker.setMap(map);
	
	$.each(all_map_objects, function(key, ar) {
		$.each(ar, function(key, e) {
			google.maps.event.clearListeners(e, 'click');
		});
	});
	
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

// for reference: var all_map_objects = [rapids_markers_array, markers_markers_array, gauges_markers_array, runs_polyline_array, playspots_markers_array];
function setClickHandlers(action) {
	google.maps.event.clearListeners(map, 'click');
	switch (action) {
		case "gauge":
				$.each(all_map_objects, function(key, ar) {
					$.each(ar, function(key, e) {
						google.maps.event.clearListeners(e, 'click');
					});
				});
				$.each(gauges_markers_array, function(key, e) {
					google.maps.event.addListener(e, 'click', function() {
						$('input[name=gauge_id]').val(this.database_id);
						$(".gauge_title").html(this.title);
					});
				});
			break;
		case "media":
			$.each(all_map_objects, function(key, ar) {
				$.each(ar, function(key, e) {
					google.maps.event.clearListeners(e, 'click');
					google.maps.event.addListener(e, 'click', function() {
						$('input[name=associated_id]').val(e.id);
						$('input[name=associated_type]').val(e.type);
						var type = e.type.charAt(0).toUpperCase() + e.type.slice(1);
						if (e.title == "" || e.title == null) {
							$(".associated_title").html("Untitled " + type);
						} else {
							$(".associated_title").html(e.title + " " + type);
						}
					});
				});
			});
			break;
		default:
			$.each(all_map_objects, function(key, ar) {
				$.each(ar, function(key, e) {
					var gauge = $.grep(json_gauges_array, function(gauge) {
						return gauge.database_id == e.gauge_id;
					});
					google.maps.event.clearListeners(e, 'click');
					setMarkerClick(e, gauge);
				});
			});
			break;
	}
}

function gotoMarker(key, arr) {
	map.panTo(arr[key].getPosition());
	if (map.getZoom() < 14) {
		map.setZoom(14);
	}
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

function edit(type, id) {
	var array;
	switch (type) {
		case 'River Access':
			array = json_markers_array[id];
			type = 'marker';
			break;
		case 'Fork':
			array = json_markers_array[id];
			type = 'marker';
			break;
		case 'rapid':
			array = json_rapids_array[id];
			break;
		case 'run':
			array = json_runs_array[id];
			break;
		case 'playspot':
			array = json_playspots_array[id];
			break;
		default:
			break;
	}
	$.ajax({
	    url: '/app/php/forms/edit/' + type + '.php',
	    data: array,
	    success: function(html){
			$(".content").html(html);
			loadSlider();
			pullContent();
		}
	});
}

function loadRiver(id) {
	$.ajax({
	    url: '/app/php/templates/river-page.php',
	    data: {river_id: id},
	    success: function(html){
			$(".content").html(html);
			loadSlider();
			pullContent();
		}
	});
}

function loadUser(username) {
	$.ajax({
	    url: '/app/php/templates/user-page.php',
	    data: {username: username},
	    success: function(html){
			$(".content").html(html);
			loadSlider();
			pullContent();
		}
	});
}