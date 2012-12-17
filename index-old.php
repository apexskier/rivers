<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/head.php");
?>
	<title>Rivers</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="css/h5bp-style.css">
	<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link rel="stylesheet" href="css/style.css">
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
					<?php if ($logged_in): ?><li><a href="#"><?php echo "Welcome, " . $_SESSION['SESS_FIRST_NAME']; ?></a></li><?php endif; ?>
				</ul><!-- <?php echo "Welcome, <a href=\"/user/" . $_SESSION['SESS_USERNAME'] . "\">" . $_SESSION['SESS_FIRST_NAME'] . "</a>"; ?> -->
				<ul class="nav pull-right">
					<li class="divider-vertical"></li>
					<li class="dropdown">
						<a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#"><i class="icon-user icon-white"></i> User tools <b class="caret"></b></a>
						<ul class="dropdown-menu" role="menu">
							<?php if ($logged_in): ?>
								<li><a onclick="loadForm('add-river.php', false)">Add River</a></li>
								<li><a onclick="loadForm('add-marker.php', true)">Add Marker</a></li>
								<li><a onclick="loadForm('add-playspot.php', true)">Add Playspot</a></li>
								<li><a onclick="loadForm('add-rapid.php', true)">Add Rapid</a></li>
								<li><a onclick="loadForm('add-run.php', false)">Add Run</a></li>
								<li class="divider"></li>
								<li><a onclick="loadForm('add-video.php', false)">Add Video</a></li>
								<li><a onclick="loadForm('add-photo.php', false)">Add Photo</a></li>
								<li class="divider"></li>
								<li><a href="log-out.php">Log out</a></li>
							<?php else: ?>
								<li><a href="register.php">Sign Up</a></li>
								<li><a onclick="loadForm('includes/log-in.php', false)">Sign In</a></li>
							<?php endif; ?>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</header>

	<div class="all-alerts container">
		<?php
			if ( isset($_SESSION['SUCCESS']) ) {
				echo '<div class="alert alert-success">';
				echo '<p>', $_SESSION['SUCCESS'], '</li>';
				echo '</div>';
				unset($_SESSION['SUCCESS']);
			}
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
			Colors indicate if a river is running, zoom in to see more.
		</div>
		<?php endif; ?>
	</div>
	
	
	<div id="map_canvas"><div class="loading">Loading...</div></div>
	
	
	<div class="container">
		<div id="main">
			<div class="close minimize">v</div>
			<div class="content"></div>
		</div>
	</div>
	<footer class="navbar navbar-fixed-bottom">
		<div class="navbar-inner">
			<div class="container">
				<ul class="nav">
					<li><a href="http://camlittle.com" target="_blank">by Cameron Little</a></li>
				</ul>
				<ul class="nav pull-right">
					<li class="divider-vertical"></li>
					<li class="dropdown">
						<a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#">Map Options <b class="caret"></b></a>
						<ul class="dropdown-menu controls" role="menu">
							<li><label class="checkbox"><input type="checkbox" onclick="showGauges()" name="gauges-control" class="gauges-control-cb">Show all gauges</label></li>
							<li><label class="checkbox"><input type="checkbox" onclick="showPlayspots()" name="playspots-control" class="playspots-control-cb">Show all playspots</label></li>
							<li><label class="checkbox"><input type="checkbox" onclick="showRapids()" name="rapids-control" class="rapids-control-cb">Show all rapids</label></li>
							<li><label class="checkbox"><input type="checkbox" onclick="showMarkers()" name="markers-control" class="markers-control-cb">Show all markers</label></li>
						</ul>
					</li>
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

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>
<script type="text/javascript" src="js/libs/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="js/libs/jquery-ui-1.8.23.custom.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?sensor=true"></script>
<script src="js/libs/bootstrap.min.js"></script>
<script src="js/libs/mustache.js"></script>

<!-- scripts concatenated and minified via build script -->
<script src="js/plugins.js"></script>
<script src="js/script.js"></script>
<script src="js/templates.js"></script>
<script src="js/rapid_markers.js"></script>
<script type="text/javascript" src="js/load-markers.js.php"></script>
<script type="text/javascript" src="js/load-runs.js.php"></script>
<script type="text/javascript" src="js/load-runs-points.js.php"></script>
<script type="text/javascript">
initialize();

google.maps.event.addListener(map, 'zoom_changed', function() {
	if (!showGaugesBool) {
		if (map.getZoom() >= 11) {
			for (var i = 0; i < gauges_array.length; i++) {
				gauges_array[i].setMap(map);
			}
		} else {
			for (var i = 0; i < gauges_array.length; i++) {
				gauges_array[i].setMap(null);
			}
		}
	}
	if (!showPlayspotsBool) {
		if (map.getZoom() >= 12) {
			for (var i = 0; i < playspots_array.length; i++) {
				playspots_array[i].setMap(map);
			}
		} else {
			for (var i = 0; i < playspots_array.length; i++) {
				playspots_array[i].setMap(null);
			}
		}
		if (map.getZoom() >= 10) {
			for (var i = 0; i < playspots_in_array.length; i++) {
				playspots_in_array[i].setMap(map);
			}
		} else {
			for (var i = 0; i < playspots_in_array.length; i++) {
				playspots_in_array[i].setMap(null);
			}
		}
	}
	if (!showMarkersBool) {
		if (map.getZoom() >= 12) {
			for (var i = 0; i < markers_array.length; i++) {
				markers_array[i].setMap(map);
			}
		} else {
			for (var i = 0; i < markers_array.length; i++) {
				markers_array[i].setMap(null);
			}
		}
	}
	if (!showRapidsBool) {
		if (map.getZoom() >= 13) {
			for (var i = 0; i < rapids_array.length; i++) {
				rapids_array[i].setMap(map);
			}
		} else {
			for (var i = 0; i < rapids_array.length; i++) {
				rapids_array[i].setMap(null);
			}
		}
	}
});
</script>
<!-- end scripts -->

<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/end.php"); ?>