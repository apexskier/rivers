	<title>Rivers</title>
	<meta name="description" content="">
</head>
<body>
	<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
		 chromium.org/developers/how-tos/chrome-frame-getting-started -->
	<!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
	
	
	<header class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a href="http://rivers.camlittle.com" class="brand">Rivers</a>
				<ul class="nav">
					<li class="divider-vertical"></li>
					<?php if ($logged_in): ?><li><a href="#"><?php echo "Welcome, " . $_SESSION['SESS_FIRST_NAME']; ?></a></li><?php endif; ?>
				</ul>
				<ul class="nav pull-right">
					<li><a href="#" onclick="goToMe()" title="Go to my position"><i class="icon-screenshot"></i></a></li>
					<li class="divider-vertical"></li>
					<li class="dropdown">
						<a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#"><i class="icon-user"></i> User tools <b class="caret"></b></a>
						<ul class="dropdown-menu" role="menu">
							<?php if ($logged_in): ?>
								<li><a onclick="loadForm('/app/php/forms/add/river.php', false)">Add River</a></li>
								<li><a onclick="loadForm('/app/php/forms/add/marker.php', true)">Add Marker</a></li>
								<li><a onclick="loadForm('/app/php/forms/add/playspot.php', true, 'gauge')">Add Playspot</a></li>
								<li><a onclick="loadForm('/app/php/forms/add/rapid.php', true)">Add Rapid</a></li>
								<li><a onclick="loadForm('/app/php/forms/add/run.php', false, 'gauge')">Add Run</a></li>
								<li class="divider"></li>
								<li><!--<a onclick="loadForm('app/php/forms/add/video.php', false, 'media')">--><a href="#" class="disabled">Add Video</a></li>
								<li><!--<a onclick="loadForm('app/php/forms/add/photo.php', false, 'media')">--><a href="#" class="disabled">Add Photo</a></li>
								<li class="divider"></li>
								<li><a href="https://github.com/apexskier/rivers/issues/new" target="_blank">Submit a Bug</a></li>
								<li><a href="/app/php/actions/log-out.php">Log out</a></li>
							<?php else: ?>
								<li><a onclick="loadRegistration()">Sign Up</a></li>
								<li><a onclick="loadForm('/app/php/forms/log-in.php', false)">Sign In</a></li>
								<li class="divider"></li>
								<li><a href="https://github.com/apexskier/rivers/issues/new" target="_blank">Submit a Bug</a></li>
							<?php endif; ?>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</header>

	<div class="all-alerts container">
		<?php if (isset($_SESSION['SUCCESS'])): ?>
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo $_SESSION['SUCCESS']; ?>
		</div>
		<?php
		unset($_SESSION['SUCCESS']);
		endif;	
		?>
		<?php if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0): ?>
		<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<ul class="err">
			<?php
				foreach($_SESSION['ERRMSG_ARR'] as $msg) {
					echo '<li>',$msg,'</li>';
				}
				unset($_SESSION['ERRMSG_ARR']);
			?>
			</ul>
		</div>
		<?php endif; ?>
		
		<?php if (!$logged_in): ?>
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			Colors indicate if a river is running: black is no gauge information, red is too low, blue is too high, the spectrum in between is varying levels of running.<br><br>
			Zoom in to see more, click to get more information.<br><br>
			If nothing is showing up, check the status bar below.
		</div>
		<?php endif; ?>
	</div>
	
	<div id="map_canvas"><div class="loading">Loading...</div></div>
	
		<div class="container main-container">
			<div id="main">
				<div class="close minimize" title="esc">v</div>
				<div class="content"></div>
			</div>
		</div>

	
	<footer class="navbar navbar-fixed-bottom">
		<div class="navbar-inner">
			<div class="container">
				<ul class="nav">
					<li><a href="#">Status: <span class="status"></span></a></li>
				</ul>
				<ul class="nav pull-right">
					<li><a href="http://camlittle.com" target="_blank">by Cameron Little</a></li>
				<!--	<li class="divider-vertical"></li>
					<li class="dropdown">
						<a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#"><i class="icon-cog"></i> Map Options <b class="caret"></b></a>
						<ul class="dropdown-menu controls" role="menu">
							<li><a class="map-link" href="#">Permalink to current page</a></li>
							<li>
								<label class="checkbox" name="gauges-control">
								<input type="checkbox" onclick="showByType(this, gauges_markers_array, 11)" name="gauges-control">Show all gauges</label>
							</li>
							<li>
								<label class="checkbox" name="playspots-control">
								<input type="checkbox" onclick="showByType(this, playspots_markers_array, 13)" name="playspots-control">Show all playspots</label>
							</li>
							<li>
								<label class="checkbox" name="rapids-control">
								<input type="checkbox" onclick="showByType(this, rapids_markers_array, 13)" name="rapids-control">Show all rapids</label>
							</li>
							<li>
								<label class="checkbox" name="markers-control">
								<input type="checkbox" onclick="showByType(this, markers_markers_array, 12)" name="markers-control">Show all markers</label>
							</li>
						</ul>
					</li>-->
				</ul>
			</div>
		</div>
	</footer>
	
	<div id="lightbox-bg">
		<div id="lightbox">
			<span class="close">X</span>
		</div>
	</div>

<!-- JavaScript at the bottom for fast page loading -->
<!-- 3rd party javascript libraries -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="/app/js/libs/jquery-1.8.0.min.js"><\/script>')</script>
<script type="text/javascript" src="/app/js/libs/jquery-ui-1.8.23.custom.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=true"></script>
<script type="text/javascript" src="/app/js/libs/bootstrap.min.js"></script>
<script type="text/javascript" src="/app/js/libs/mustache.js"></script>

<!-- scripts concatenated and minified via build script -->
<script type="text/javascript" src="/app/js/main.js"></script>
<script type="text/javascript" src="/app/js/templates/content.js"></script>
<script type="text/javascript" src="/app/js/load_json.js"></script>

<script type="text/javascript">
<?php
$lat = $_GET['lat'];
$lng = $_GET['lng'];
$zoom = $_GET['zoom'];
$type = $_GET['type'];
$id = $_GET['id'];

if ($lat == null) {
	$lat = "null";
}
if ($lng == null) {
	$lng = "null";
}
if ($zoom == null) {
	$zoom = "null";
}
if ($type == null) {
	$type = "null";
}
if ($id == null) {
	$id = "null";
}

echo "initialize($lat, $lng, $zoom, '$type', $id)";
?>

</script>
<!-- end scripts -->