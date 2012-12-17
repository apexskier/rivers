var river_meta_template = '<h3>{{name}}</h3> <p class="meta"><strong><a href="/river/{{river_url}}">{{river_name}}</a></strong> - Class {{rating}} rapid </p>';

var description_template = '<p>{{description}}</p>';

var meta_template = '<p><small>Added by <a href="/user/{{created_user}}">{{created_user}}</a> on {{created_date}}.{{#updated_user}} Updated by <a href="/user/{{updated_user}}">{{updated_user}}</a> by {{updated_date}}.{{/updated_user}}</small></p> <div class="edit"><form method="POST" action="edit_{{type}}.php"> 	<input type="submit" value="Edit {{type}}"> 	<input type="hidden" name="id" value="{{id}}"> </div> ';

var river_template = river_meta_template + description_template + meta_template;