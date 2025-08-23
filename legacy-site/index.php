<?php
	include('con.php');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<title>CTA APP</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="refresh" content="30" />

	<meta property="og:url" content="http://cta.zachschneider.com/">
	<meta property="og:title" content="CTA APP">
	<meta property="og:type" content="website"/>
	<meta property="og:description" content="Personal project and playing with the CTA's API">
	<meta property="og:image" content="http://cta.zachschneider.com/images/ico/apple-touch-icon-144-precomposed.png">
	<link href="./styles/css/app.css" media="screen, projector, print" rel="stylesheet" type="text/css" />
	<link href='./styles/css/style.css' rel='stylesheet'>
	<script src="./js/foundation/modernizr.foundation.js"></script>
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="./images/ico/apple-touch-icon-144-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="./images/ico/apple-touch-icon-114-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="./images/ico/apple-touch-icon-72-precomposed.png">
	<link rel="apple-touch-icon-precomposed" href="./images/ico/apple-touch-icon-57-precomposed.png">
	<link rel="shortcut icon" href="./images/ico/favicon.png">
	<!-- [if lt IE 8]>
	<link rel="shortcut icon" href="../images/ico/favicon.ico">
	<![endif]-->
</head>
<body>
<div class="row">
	<div class="twelve columns">
		<div class="one columns">
			<a href="index.php"><img src="./images/ico/apple-touch-icon-114-precomposed.png" alt="CTA" /></a>
		</div>
		<div class="eleven columns">
			<?php echo '<div class="twelve columns"><h1>Pick a stop, get arrivals.</h1></div>'; ?>
		</div>
	</div>
</div>
<div class="row">
		<div class="twelve columns">
			<?php

				if (empty($_GET["display"])) $action = '';
				else $action = strtoupper($_GET['display']);

				switch($action) {
					case 'BLUE': 
					$lineColor = "Blue";
 					// Query Database
					$query = "SELECT  `sid` ,  `station` FROM  `" . strtolower($lineColor) . "`";
					$paths = @mysql_query($query); 

					if (!$paths) {
						echo "<p><strong>Query error:</strong><br /> $query</p>"; // query error
						break; // terminate case
					}
					echo '<div class="row"><div class="twelve stations columns ' . $lineColor . '"><h1>' . $lineColor . ' Line</h1></div></div>';
					// loop through
					while($train = mysql_fetch_array($paths, MYSQL_BOTH)) {
					$mapID = $train['sid'];
					echo '<div class="four stations columns"><a class="panel" href="?display=' . $lineColor . '&sid=' . $mapID . '">' . $train['station'] . '</a></div>';
					} // while train
					echo "</div>";
					break;

					case 'BROWN': 
					$lineColor = "Brown";
					// Query Database
					$query = "SELECT  `sid` ,  `station` FROM  `" . strtolower($lineColor) . "`";
					$paths = @mysql_query($query); 

					if (!$paths) {
						echo "<p><strong>Query error:</strong><br /> $query</p>"; // query error
						break; // terminate case
					}
					echo '<div class="row"><div class="twelve stations columns ' . $lineColor . '"><h1>' . $lineColor . ' Line</h1></div></div>';
					// loop through
					while($train = mysql_fetch_array($paths, MYSQL_BOTH)) {
					$mapID = $train['sid'];
					echo '<div class="four stations columns"><a class="panel" href="?display=' . $lineColor . '&sid=' . $mapID . '">' . $train['station'] . '</a></div>';
					} // while train
					echo "</div>";
					break;

					case 'GREEN': 
					$lineColor = "Green";
					// Query Database
					$query = "SELECT  `sid` ,  `station` FROM  `" . strtolower($lineColor) . "`";
					$paths = @mysql_query($query); 

					if (!$paths) {
						echo "<p><strong>Query error:</strong><br /> $query</p>"; // query error
						break; // terminate case
					}
					echo '<div class="row"><div class="twelve stations columns ' . $lineColor . '"><h1>' . $lineColor . ' Line</h1></div></div>';
					// loop through
					while($train = mysql_fetch_array($paths, MYSQL_BOTH)) {
					$mapID = $train['sid'];
					echo '<div class="four stations columns"><a class="panel" href="?display=' . $lineColor . '&sid=' . $mapID . '">' . $train['station'] . '</a></div>';
					} // while train
					echo "</div>";
					break;

					case 'ORANGE': 
					$lineColor = "Orange";
					// Query Database
					$query = "SELECT  `sid` ,  `station` FROM  `" . strtolower($lineColor) . "`";
					$paths = @mysql_query($query); 

					if (!$paths) {
						echo "<p><strong>Query error:</strong><br /> $query</p>"; // query error
						break; // terminate case
					}
					echo '<div class="row"><div class="twelve stations columns ' . $lineColor . '"><h1>' . $lineColor . ' Line</h1></div></div>';
					// loop through
					while($train = mysql_fetch_array($paths, MYSQL_BOTH)) {
					$mapID = $train['sid'];
					echo '<div class="four stations columns"><a class="panel" href="?display=' . $lineColor . '&sid=' . $mapID . '">' . $train['station'] . '</a></div>';
					} // while train
					echo "</div>";
					break;

					case 'PINK': 
					$lineColor = "Pink";
					// Query Database
					$query = "SELECT  `sid` ,  `station` FROM  `" . strtolower($lineColor) . "`";
					$paths = @mysql_query($query); 

					if (!$paths) {
						echo "<p><strong>Query error:</strong><br /> $query</p>"; // query error
						break; // terminate case
					}
					echo '<div class="row"><div class="twelve stations columns ' . $lineColor . '"><h1>' . $lineColor . ' Line</h1></div></div>';
					// loop through
					while($train = mysql_fetch_array($paths, MYSQL_BOTH)) {
					$mapID = $train['sid'];
					echo '<div class="four stations columns"><a class="panel" href="?display=' . $lineColor . '&sid=' . $mapID . '">' . $train['station'] . '</a></div>';
					} // while train
					echo "</div>";
					break;

					case 'PURPLE': 
					$lineColor = "Purple";
					// Query Database
					$query = "SELECT  `sid` ,  `station` FROM  `" . strtolower($lineColor) . "`";
					$paths = @mysql_query($query); 

					if (!$paths) {
						echo "<p><strong>Query error:</strong><br /> $query</p>"; // query error
						break; // terminate case
					}
					echo '<div class="row"><div class="twelve stations columns ' . $lineColor . '"><h1>' . $lineColor . ' Line</h1></div></div>';
					// loop through
					while($train = mysql_fetch_array($paths, MYSQL_BOTH)) {
					$mapID = $train['sid'];
					echo '<div class="four stations columns"><a class="panel" href="?display=' . $lineColor . '&sid=' . $mapID . '">' . $train['station'] . '</a></div>';
					} // while train
					echo "</div>";
					break;

					case 'RED': 
					$lineColor = "Red";
					// Query Database
					$query = "SELECT  `sid` ,  `station` FROM  `" . strtolower($lineColor) . "`";
					$paths = @mysql_query($query); 

					if (!$paths) {
						echo "<p><strong>Query error:</strong><br /> $query</p>"; // query error
						break; // terminate case
					}
					echo '<div class="row"><div class="twelve stations columns ' . $lineColor . '"><h1>' . $lineColor . ' Line</h1></div></div>';
					// loop through
					while($train = mysql_fetch_array($paths, MYSQL_BOTH)) {
					$mapID = $train['sid'];
					echo '<div class="four stations columns"><a class="panel" href="?display=' . $lineColor . '&sid=' . $mapID . '">' . $train['station'] . '</a></div>';
					} // while train
					echo "</div>";
					break;

					case 'YELLOW': 
					$lineColor = "Yellow";
					// Query Database
					$query = "SELECT  `sid` ,  `station` FROM  `" . strtolower($lineColor) . "`";
					$paths = @mysql_query($query); 

					if (!$paths) {
						echo "<p><strong>Query error:</strong><br /> $query</p>"; // query error
						break; // terminate case
					}
					echo '<div class="row"><div class="twelve stations columns ' . $lineColor . '"><h1>' . $lineColor . ' Line</h1></div></div>';
					// loop through
					while($train = mysql_fetch_array($paths, MYSQL_BOTH)) {
					$mapID = $train['sid'];
					echo '<div class="four stations columns"><a class="panel" href="?display=' . $lineColor . '&sid=' . $mapID . '">' . $train['station'] . '</a></div>';
					} // while train
					echo "</div>";
					break;


					default:
					case '':
						echo '<div class="six columns color Blue"><h2><a class="panel" href="?display=blue">Blue Line <span>O\'Hare-Forest Park</span></a></h2></div>';
						echo '<div class="six columns color Brown"><h2><a class="panel" href="?display=brown">Brown Line <span>Kimball-Loop</span></a></h2></div>';
						echo '<div class="six columns color Green"><h2><a class="panel" href="?display=green">Green Line <span>Harlem/Lake-Ashland/63rd-Cottage Grv</span></a></h2></div>';
						echo '<div class="six columns color Orange"><h2><a class="panel" href="?display=orange">Orange Line <span>Midway-Loop</span></a></h2></div>';
						echo '<div class="six columns color Pink"><h2><a class="panel" href="?display=pink">Pink Line <span>54th/Cermak-Loop</span></a></h2></div>';
						echo '<div class="six columns color Purple"><h2><a class="panel" href="?display=purple">Purple Line <span>Linden-Howard-Loop</span></a></h2></div>';
						echo '<div class="six columns color Red"><h2><a class="panel" href="?display=red">Red Line <span>Howard-95th/Dan Ryan</span></a></h2></div>';
						echo '<div class="six columns color Yellow"><h2><a class="panel" href="?display=yellow">Yellow Line <span>Skokie-Howard</span></a></h2></div>';


						break;

				} // END DISPLAY switch

				// START SID switch
				if (empty($_GET["sid"])) $ctaTime = '';
				else $ctaTime = strtoupper($_GET['sid']);

				switch ($ctaTime) {
					case '':
					// code...
						break;
					default:
						echo "<style>.stations{display:none;}</style>";
						include('printCTA.php');
					break;
					
				}
				?>
</div>
<?php include_once("analyticstracking.php") ?>
</body>
</html>