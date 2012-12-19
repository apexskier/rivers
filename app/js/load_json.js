/* Author: Cameron Little

*/
var standard_icon_size = new google.maps.Size(28, 28);
var standard_icon_anchor = new google.maps.Point(14, 14);
var icon_img = '/app/img/icons-sprite.png';
var river_access_icon = new google.maps.MarkerImage(
	icon_img,
	new google.maps.Size(29, 27),
	new google.maps.Point(56, 29),
	new google.maps.Point(14, 13)
);
var fork_icon = new google.maps.MarkerImage(
	icon_img,
	new google.maps.Size(26, 28),
	new google.maps.Point(56, 78),
	new google.maps.Point(13, 14)
);
var gauge_icon = new google.maps.MarkerImage(
	icon_img,
	new google.maps.Size(29, 29),
	new google.maps.Point(56, 0),
	new google.maps.Point(15, 15)
);
var playspot_icon = new google.maps.MarkerImage(
	icon_img,
	new google.maps.Size(32, 22),
	new google.maps.Point(56, 56),
	new google.maps.Point(16, 11)
);
var hazard_icon = new google.maps.MarkerImage(
	icon_img,
	new google.maps.Size(27, 25),
	new google.maps.Point(56, 106),
	new google.maps.Point(13, 12)
);
var rapid_1_icon = new google.maps.MarkerImage(
	icon_img,
	standard_icon_size,
	new google.maps.Point(0, 0),
	standard_icon_anchor
);
var rapid_2_icon = new google.maps.MarkerImage(
	icon_img,
	standard_icon_size,
	new google.maps.Point(0, 28),
	standard_icon_anchor
);
var rapid_3_icon = new google.maps.MarkerImage(
	icon_img,
	standard_icon_size,
	new google.maps.Point(0, 56),
	standard_icon_anchor
);
var rapid_4_icon = new google.maps.MarkerImage(
	icon_img,
	standard_icon_size,
	new google.maps.Point(0, 84),
	standard_icon_anchor
);
var rapid_5_icon = new google.maps.MarkerImage(
	icon_img,
	standard_icon_size,
	new google.maps.Point(0, 112),
	standard_icon_anchor
);
var rapid_1plus_icon = new google.maps.MarkerImage(
	icon_img,
	standard_icon_size,
	new google.maps.Point(28, 0),
	standard_icon_anchor
);
var rapid_2plus_icon = new google.maps.MarkerImage(
	icon_img,
	standard_icon_size,
	new google.maps.Point(28, 28),
	standard_icon_anchor
);
var rapid_3plus_icon = new google.maps.MarkerImage(
	icon_img,
	standard_icon_size,
	new google.maps.Point(28, 56),
	standard_icon_anchor
);
var rapid_4plus_icon = new google.maps.MarkerImage(
	icon_img,
	standard_icon_size,
	new google.maps.Point(28, 84),
	standard_icon_anchor
);
var rapid_5plus_icon = new google.maps.MarkerImage(
	icon_img,
	standard_icon_size,
	new google.maps.Point(28, 112),
	standard_icon_anchor
);
var rapid_unrunnable = new google.maps.MarkerImage(
	icon_img,
	standard_icon_size,
	new google.maps.Point(0, 140),
	standard_icon_anchor
);
function return_icon(type) {
	switch (type) {
		case "River Access":
			return river_access_icon;
			break;
		case "Fork":
			return fork_icon;
			break;
		case "gauge":
			return gauge_icon;
			break;
		case "playspot":
			return playspot_icon;
			break;
		case "playspot_in":
			return playspot_icon;
			break;
		case "I":
			return rapid_1_icon;
			break;
		case "I+":
			return rapid_1plus_icon;
			break;
		case "II":
			return rapid_2_icon;
			break;
		case "II+":
			return rapid_2plus_icon;
			break;
		case "III":
			return rapid_3_icon;
			break;
		case "III+":
			return rapid_3plus_icon;
			break;
		case "IV":
			return rapid_4_icon;
			break;
		case "IV+":
			return rapid_4plus_icon;
			break;
		case "V":
			return rapid_5_icon;
			break;
		case "V+":
			return rapid_5plus_icon;
			break;
		default:
			return null;
			break;
	}
}

var json_rivers_array = [];

var rapids_markers_array = [];
var json_rapids_array = [];

var markers_markers_array = [];
var json_markers_array = [];

var gauges_markers_array = [];
var json_gauges_array = [];

var runs_polyline_array = [];
var json_runs_array = [];

var playspots_markers_array = [];
var json_playspots_array = [];

var all_map_objects = [rapids_markers_array, markers_markers_array, gauges_markers_array, runs_polyline_array, playspots_markers_array];

function setMarkerClick(marker) {
	function markerCase() {
		google.maps.event.addListener(marker, 'click', function() {
			$('.content').html(Mustache.to_html(marker_template, json_markers_array[this.id])).removeClass('overflowing');
			pullContent();
		});
	}
	if (marker.type == "I"
	 || marker.type == "I+"
	 || marker.type == "II"
	 || marker.type == "II+"
	 || marker.type == "III"
	 || marker.type == "III+"
	 || marker.type == "IV"
	 || marker.type == "IV+"
	 || marker.type == "V"
	 || marker.type == "V+") {
		var marker_type = "rapid";
	} else {
		var marker_type = marker.type;
	}
	switch (marker_type) {
		case "marker":
			markerCase();
			break;
		case "River Access":
			markerCase();
		case "Fork":
			break;
		case "rapid":
			google.maps.event.addListener(marker, 'click', function() {
				$('.content').html(Mustache.to_html(rapid_template, json_rapids_array[this.id])).removeClass('overflowing');
				pullContent();
			});
			break;
		case "gauge":
			google.maps.event.addListener(marker, 'click', function() {
				$('.content').html(Mustache.to_html(gauge_template, json_gauges_array[this.id])).removeClass('overflowing');
				pullContent();
			});
			break;
		case "run":
			google.maps.event.addListener(marker, 'click', function() {
				var content = Mustache.to_html(run_template1, json_runs_array[this.id]);
				if (this.gauge_id != null) {
					content = content + Mustache.to_html(items_gauge_template, json_gauges_array[this.gauge_id]);
				}
				content = content + Mustache.to_html(run_template2, json_runs_array[this.id]);
				$('.content').html(content).removeClass('overflowing');
				pullContent();
			});
			break;
		case "playspot":
			google.maps.event.addListener(marker, 'click', function() {
				var playspot_gauge = $.grep(json_gauges_array, function(gauge) {
					return gauge.database_id == this.gauge_id;
				});
				var content = Mustache.to_html(playspot_template1, json_playspots_array[this.id]);
				if (playspot_gauge.length > 0) {
					content = content + Mustache.to_html(items_gauge_template, json_gauges_array[playspot_gauge[0].id]);
				}
				content = content + Mustache.to_html(playspot_template2, json_playspots_array[this.id]);
				$('.content').html(content).removeClass('overflowing');
				pullContent();
			});
			break;
	}
}

function createMarker(data, icon) {
	var this_icon = return_icon(icon);
	
	var gauge_id;
	if (data.hasOwnProperty('gauge')) {
		gauge_id = data.gauge.id;
	}
	
	var new_marker = new google.maps.Marker({
		id: data.id,
		database_id: data.database_id,
		type: data.type,
		position: new google.maps.LatLng(data.lat, data.lng),
		map: null,
		title: data.name,
		gauge_id: gauge_id,
		icon: this_icon,
	});
	
	return new_marker;
}

$(document).ready(function() {
	$.getJSON('/app/json/gauges.json.php', function(gauges) {
		$.each(gauges, function(key, gauge) {
			var new_gauge = createMarker(gauge, "gauge");
			setMarkerClick(new_gauge);
			gauges_markers_array.push(new_gauge);
		});	
		json_gauges_array = gauges;
		console.log("Gauges loaded");
		showByZoom();
		
		$.getJSON('/app/json/playspots.json.php', function(playspots) {
			$.each(playspots, function(key, playspot) {
				var new_playspot = createMarker(playspot, "playspot");
				setMarkerClick(new_playspot);
				playspots_markers_array.push(new_playspot);
				showByZoom();
			});
			json_playspots_array = playspots;
			console.log("Playspots loaded");
		});
		
		$.getJSON('/app/json/runs.json.php', function(runs) {
			$.each(runs, function(key, run) {
				var run_color = "hsl(0, 0%, 0%)";
				
				var run_path = [];
				var run_points;
				var run_path_url = '/app/json/run_points/run_' + run.database_id + '.json';
				$.getJSON(run_path_url, function(points) {
					run_points = points;
					$.each(points, function(key, point) {
						run_path.push(new google.maps.LatLng(point.lat, point.lng));
					});
					
					var run_gauge = $.grep(json_gauges_array, function(gauge) {
						return gauge.database_id == run.gauge.id;
					});
					
					var gauge_id = null;
					
					if (run_gauge.length > 0) {
						gauge_id = run_gauge[0].id;
						var flow = +run_gauge[0].current_flow;
						var flow_min = +run.gauge.min;
						var flow_max = +run.gauge.max;
						if (flow > flow_min
						 && flow < flow_max) {
							hue = flow * 240 / flow_max;
							run_color = "hsl(" + hue + ", 80%, 50%)";
						} else if (flow <= flow_min) {
							run_color = "hsl(0, 80%, 50%)"; // red
						} else if (flow >= flow_max) {
							run_color = "hsl(240, 80%, 50%)"; // blue
						} else if (flow_min == 0
						 || flow_max == 0
						 || flow_min >= flow_max) {
							run_color = "hsl(0, 0%, 0%)"; // black
						} else {
							run_color = "hsl(0, 0%, 0%)"; // black
						}
					}
					
					var new_polyline = new google.maps.Polyline({
						id: run.id,
						map: map,
						strokeColor: run_color,
						strokeOpacity: 0.75,
						strokeWeight: 8,
						title: run.name,
						gauge_id: gauge_id,
						gauge_min: run.gauge.min,
						gauge_max: run.gauge.max,
						type: run.type,
						path: run_path
					});
					
					google.maps.event.addListener(new_polyline, 'mouseover', function() {
						this.setOptions({ strokeOpacity: 1.0 });
					});
					google.maps.event.addListener(new_polyline, 'mouseout', function() {
						this.setOptions({ strokeOpacity: 0.75 });
					});
					
					setMarkerClick(new_polyline);
					runs_polyline_array.push(new_polyline);
				});
			});
			json_runs_array = runs;
			console.log("Runs loaded");
		});
	});
	
	
	
	$.getJSON('/app/json/rivers.json.php', function(rivers) {
		json_rivers_array = rivers;
		console.log("Rivers loaded");
	});
	
	$.getJSON('/app/json/rapids.json.php', function(rapids) {
		$.each(rapids, function(key, rapid) {
			var new_marker = createMarker(rapid, rapid.rating);
			setMarkerClick(new_marker);
			rapids_markers_array.push(new_marker);
		});
		json_rapids_array = rapids;
		console.log("Rapids loaded");
		showByZoom();
	});
	
	$.getJSON('/app/json/markers.json.php', function(markers) {
		$.each(markers, function(key, marker) {
			var new_marker = createMarker(marker, marker.type);
			setMarkerClick(new_marker);
			markers_markers_array.push(new_marker);
		});
		json_markers_array = markers;
		console.log("Markers loaded");
		showByZoom();
	});
});