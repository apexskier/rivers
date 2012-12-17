<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/head.php");
	
	$no_user = false;
	$username = $_GET['username'];
	$get_user_query = mysql_query("SELECT * FROM users WHERE username = '$username'");
	if ($get_user_query && mysql_num_rows($get_user_query) > 0) {
		while ($user = mysql_fetch_array($get_user_query, MYSQL_ASSOC)) {
			$user_id = $user['id'];
		}
	} else {
		$no_user = true;
	}
?>
	<title>Rivers | <?php echo $username; ?></title>
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
			if ($no_user) {
				echo "<h2>User " . $username . " does not exist.</h2>";
			} else {
				$query = mysql_query("SELECT * FROM users WHERE id = $user_id");
				while ($details = mysql_fetch_array($query, MYSQL_ASSOC)) {
					echo "<h2>" . $details['firstname'] . " " . $details['lastname'] . "</h2>";
					if ($details['photo_type'] != "") {
						echo "<img src='/img/user/profile/thumb/" . $details['id'] . "." . $details['photo_type'] . "' height='150' />";
					}
					echo "<p><strong>$username</strong> has " . $details['contributions'] . " contribution points.</p>";
				}
				
				$videos = array();
				$photos = array();
				$video_query = mysql_query("SELECT * FROM videos WHERE user = '$username'");
				if ($video_query) {
					while ($video = mysql_fetch_array($video_query, MYSQL_ASSOC)) {
						$video_html = "";
						switch ($video['video_type']) {
							case 'vimeo':
								$video_html .= "<iframe src='http://player.vimeo.com/video/" . $video['video_id'] . "' width='560' height='315' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
								break;
							case 'youtube':
								$video_html .= "<iframe width='560' height='315' src='http://www.youtube.com/embed/" . $video['video_id'] . "' frameborder='0' allowfullscreen></iframe>";
								break;
							case 'facebook':
								$video_html .= "<object width='560' height='315'><param name='allowfullscreen' value='true'></param><param name='movie' value='https://www.facebook.com/v/" . $video['video_id'] . "'></param><embed src='https://www.facebook.com/v/" . $video['video_id'] . "' type='application/x-shockwave-flash' allowfullscreen='1' width='560' height='315'></embed></object>";
								break;
							default:
								null;
								break;
						}
						$video_html .= "<p><small>Video added on " . date("F j, Y g:i a", strtotime($video['date_added']));
						if ($video['flow'] != "" && $video['flow'] != "0") {
							$video_html .= " and taken at " . $video['flow'] . " " . $video['flow_units'];
						}
						$video_html .= ".</small></p>";
						array_push($videos, $video_html);
					}
				}
				@mysql_free_result($video_query);
				
				$photo_query = mysql_query("SELECT * FROM photos WHERE user = '$username'");
				if ($photo_query) {
					while ($photo = mysql_fetch_array($photo_query, MYSQL_ASSOC)) {
						$photo_html = "";
						if ($photo['title'] != "") {
							$photo_html .= "<h4>" . $photo['title'] . "</h4>";
						}
						$photo_html .= "<p><a href='/img/user/uploaded/web/" . $photo['id'] . "." . $photo['file_type'] . "' target='_blank'><img src='/img/user/uploaded/thumb/" . $photo['id'] . "." . $photo['file_type'] . "' height='150'></a></p>";
						if ($photo['description'] != "") {
							$photo_html .= "<p>" . $photo['description'] . "</p>";
						}
						
						$photo_html .= "<p><small>Photo added on " . date("F j, Y g:i a", strtotime($photo['date_added']));
						if ($photo['flow'] != "" && $photo['flow'] != "0") {
							$photo_html .= " and taken at " . $photo['flow'] . " " . $photo['flow_units'];
						}
						$photo_html .= ".</small></p>";
						array_push($photos, $photo_html);
					}
				}
				@mysql_free_result($photo_query);
				
				if (count($photos) > 0) {
					$output .= "<h3>Photos</h3>";
					foreach ($photos as $photo) {
						$output .= $photo . "<hr>";
					}
					$output = substr($output, 0, -4);
				}
				
				if (count($videos) > 0) {
					$output .= "<h3>Videos</h3>";
					foreach ($videos as $video) {
						$output .= $video . "<hr>";
					}
					$output = substr($output, 0, -4);
				}
				
				echo $output;
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