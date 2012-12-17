/* Author: Cameron Little

*/
var rapids;
var json_rapids_array = [];
$.getJSON('app/json/rapids.json.php', function(rapids) {
	$.each(rapids, function(key, rapid) {
		var new_marker = new google.maps.Marker({
			id: rapid.id,
			type: rapid.type,
			position: new google.maps.LatLng(rapid.lat, rapid.lng),
			map: map,
			title: rapid.name,
			json_id: key
		});
		
		google.maps.event.addListener(new_marker, 'click', function() {
			$('.content').html(Mustache.to_html(river_template, rapids[this.json_id]));
			setHeight($('.content').height());
		});
			
		json_rapids_array.push(new_marker);
	})
});