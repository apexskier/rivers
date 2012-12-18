var meta_start = '<h4>{{{name}}}</h4><p class="meta"><strong><a onclick="loadRiver(\'{{river_id}}\');">{{river_name}}</a></strong>';

var rapid_meta_template    = ' - Class {{rating}} rapid </p>';
var marker_meta_template   = ' - {{type}}</p>';
var gauge_meta_template    = '{{#active}} - {{current_flow}} {{units}} and {{flow_difference}}{{/active}}</p><p><a href="{{link}}" target="_blank">{{link}}</a></p>';
var run_meta_template      = ' - Class {{rating}}, runnable at {{#gauge}}{{min}} to {{max}}{{/gauge}}';
var items_gauge_template   = ' (currently {{current_flow}}) on the <a onclick="gotoMarker({{id}}, gauges_markers_array)">{{river_name}} Gauge</a>';
var playspot_meta_template = ' - {{#gauge}}{{#active}}In at {{min}} to {{max}}{{/active}}{{/gauge}}';

var description_template = '<p>{{{description}}}</p>';

var meta_template = '<p class="muted"><small>Added by <a href="/user/{{created_user}}">{{created_user}}</a> on {{created_date}}.{{#updated_user}} Updated by <a href="/user/{{updated_user}}">{{updated_user}}</a> by {{updated_date}}.{{/updated_user}}</small></p> <p class="edit"><button class="btn btn-small submit" onclick="edit(\'{{type}}\', {{id}})">Edit {{type}}</button></p> ';

var rapid_template     = meta_start + rapid_meta_template + description_template + meta_template;
var marker_template    = meta_start + marker_meta_template + description_template + meta_template;
var gauge_template     = meta_start + gauge_meta_template;
var run_template1      = meta_start + run_meta_template;
var run_template2      = description_template + meta_template;
var playspot_template1 = meta_start + playspot_meta_template;
var playspot_template2 = description_template + meta_template;

var video_header = '<h4>Videos</h4>';
var vimeo_embed = '<iframe src="http://player.vimeo.com/video/{{video_code}}" width="560" height="315" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
var youtube_embed = '<iframe width="560" height="315" src="http://www.youtube.com/embed/{{video_code}}" frameborder="0" allowfullscreen></iframe>';
var facebook_embed = '<object width="560" height="315"><param name="allowfullscreen" value="true"></param><param name="movie" value="https://www.facebook.com/v/{{video_code}}"></param><embed src="https://www.facebook.com/v/{{video_code}}" type="application/x-shockwave-flash" allowfullscreen="1" width="560" height="315"></embed></object>';
var video_meta = '<p><small>Video added by <a href="/user/{{user}}">{{user}}</a> on {{created_date}}{{#flow}} and taken at {{level}} {{units}}{{/flow}}.</small></p>';

var photo_header = '<h4>Photos</h4><div class="photos clearfix">';
var photo_embed = '<div class="photo" data-photo-id="{{id}}.{{file_type}}" data-title="{{name}}" data-description="<p>{{description}}</p><p><small>Photo added by <a href=&quot;/user/{{user}}&quot;>{{user}}</a> on {{created_date}}{{#flow}} and taken at {{level}} {{units}}{{/flow}}.</small></p>"><img src="/img/user/uploaded/thumb/{{id}}.{{file_type}}" height="150" width="{{thumb_width}}">{{#flow}}<span class="meta">{{level}} {{units}}</span>{{/flow}}</div>';