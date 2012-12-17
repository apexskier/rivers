<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/head.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php");
	
	$no_river = false;
	$river_name = strtolower($_GET['river']);
	$river_name = str_replace('-', ' ', $river_name);
	$river_name = str_replace('_', ' ', $river_name);
	$river_name = str_replace('%20', ' ', $river_name);
	$alt_river_name = str_replace(' river', '', $river_name);
	$get_river_query = mysql_query("SELECT * FROM rivers WHERE river_name = '$river_name' OR river_name = '$alt_river_name'");
	if ($get_river_query && mysql_num_rows($get_river_query) > 0) {
		while ($river = mysql_fetch_array($get_river_query, MYSQL_ASSOC)) {
			$river_id = $river['id'];
		}
	} else {
		$no_river = true;
	}
	
	$printable_river_name = ucwords($river_name);
	if (!strpos($printable_river_name, "River") && !strpos($printable_river_name, "Creek")) {
		$printable_river_name .= " River";
	}
?>
	<title>Rivers | <?php echo $printable_river_name; ?></title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="/css/style.css">
	<link type="text/css" href="/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
</head>
<body>
	<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
		 chromium.org/developers/how-tos/chrome-frame-getting-started -->
	<!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
	
	<?php
		if ( isset($_SESSION['SUCCESS']) ) {
			echo '<div class="success">';
			echo '<p>', $_SESSION['SUCCESS'], '</li>';
			echo '</div>';
			unset($_SESSION['SUCCESS']);
		}
	?>
	<?php
		if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
			echo '<div class="error">';
			echo '<ul class="err">';
			foreach($_SESSION['ERRMSG_ARR'] as $msg) {
				echo '<li>',$msg,'</li>';
			}
			echo '</ul>';
			echo '</div>';
			unset($_SESSION['ERRMSG_ARR']);
		}
	?>

	<header>
		<h1><a href="http://rivers.camlittle.com">Rivers</a></h1>
		<?php if ($logged_in): ?><div class="welcome"><p><?php echo "Welcome, " . $_SESSION['SESS_FIRST_NAME']; ?></p></div><?php endif; ?>
	</header>
	
	<div id="full-content">
		<?php
			if ($no_river) {
				echo "<h2>No " . $printable_river_name . " exists.</h2>";
			} else {
				echo "<h2>" . $printable_river_name . "</h2>";
				
				$gauges_query = mysql_query("SELECT * FROM gauges WHERE river = $river_id");
				echo "<h3>";
				if (mysql_num_rows($gauges_query) == 0) {
					echo "No ";
				}
				echo "Gauges</h3>";
				echo "<ul>";
				while ($gauge = mysql_fetch_array($gauges_query, MYSQL_ASSOC)) {
					echo "<li>";
					echo "<h4>" . strtoupper($gauge['type']) . " Gauge " . $gauge['code'] . "</h4>";
					echo "</li>";
				}
				echo "</ul>";
				
				$markers_query = mysql_query("SELECT * FROM markers WHERE river = $river_id");
				echo "<h3>";
				if (mysql_num_rows($markers_query) == 0) {
					echo "No ";
				}
				echo "Markers</h3>";
				echo "<ul>";
				while ($marker = mysql_fetch_array($markers_query, MYSQL_ASSOC)) {
					echo "<li>";
					if ($marker['marker_name'] != "") {
						echo "<h4>" . $marker['marker_name'] . "</h4>";
					}
					echo "<p>" . $marker['marker_type'] . "</p>";
					if ($marker['description'] != "") {
						echo "<p>" . $marker['description'] . "</p>";
					}
					echo getMedia($marker['id'], 'marker', 'h5');
					echo "</li>";
				}
				echo "</ul>";
				
				$rapids_query = mysql_query("SELECT * FROM rapids WHERE river = $river_id");
				echo "<h3>";
				if (mysql_num_rows($rapids_query) == 0) {
					echo "No ";
				}
				echo "Rapids</h3>";
				echo "<ul>";
				while ($rapid = mysql_fetch_array($rapids_query, MYSQL_ASSOC)) {
					echo "<li>";
					if ($rapid['rapid_name'] != "") {
						echo "<h4>" . $rapid['rapid_name'] . "</h4>";
					}
					echo "<p>Class " . $rapid['class'] . "</p>";
					if ($rapid['description'] != "") {
						echo "<p>" . $rapid['description'] . "</p>";
					}
					echo getMedia($rapid['id'], 'rapid', 'h5');
					echo "</li>";
				}
				echo "</ul>";
				
				$playspots_query = mysql_query("SELECT * FROM playspots WHERE river = $river_id");
				echo "<h3>";
				if (mysql_num_rows($playspots_query) == 0) {
					echo "No ";
				}
				echo "Playspots</h3>";
				echo "<ul>";
				while ($playspot = mysql_fetch_array($playspots_query, MYSQL_ASSOC)) {
					echo "<li>";
					if ($playspot['playspot_name'] != "") {
						echo "<h4>" . $playspot['playspot_name'] . "</h4>";
					}
					if ($playspot['gauge_max'] != 0 && $playspot['gauge_min'] != 0) {
						echo "<p>In from  " . $playspot['gauge_min'] . " to " . $playspot['gauge_max'] . "</p>";
					}
					if ($playspot['description'] != "") {
						echo "<p>" . $playspot['description'] . "</p>";
					}
					echo getMedia($playspot['id'], 'playspot', 'h5');
					echo "</li>";
				}
				echo "</ul>";
			}
		?>
	</div>
		
	<div id="main">
		<div class="minimize">v</div>
		<div class="content"></div>
	</div>
	<div id="footer-container">
		<footer>Built by <a href="http://camlittle.com">Cameron Little</a>.</footer>
	</div>


<!-- JavaScript at the bottom for fast page loading -->

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="/js/libs/jquery-1.7.1.min.js"><\/script>')</script>
<script type="text/javascript" src="/js/libs/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="/js/libs/jquery-ui-1.8.23.custom.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?sensor=true"></script>

<!-- scripts concatenated and minified via build script -->
<script src="/js/plugins.js"></script>
<script src="/js/script.js"></script>
<!-- end scripts -->

<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/end.php"); ?>