<?php
require(__DIR__ . '/functions.php');
?>

<!DOCTYPE html>
<html>
<head>
	<title>I am couch potato | IMCP</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Bootstrap -->
	<link href="./lib/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">

	<style type="text/css">
		.ws-logo {
			text-align: center;
			font-size: 30pt;
			font-weight: bold;
			padding-top: 60px;
			padding-bottom: 60px;
		}

		.ws-frame {
			width: 80%;
			margin: 0 auto;
		}

		.ws-form {
			text-align: center;
			margin-bottom: 50px;
		}

		.ws-tile {
			margin-bottom: 40px;
			height: 290px;
		}

		.ws-texttile {
			text-align: center;
			padding-top: 100px;
			padding-left: 10px;
			padding-right: 10px;
			text-transform: uppercase;
			background-color: #eee;
			font-weight: bold;
			margin-bottom: 40px;
			height: 290px;
		}
	</style>
</head>
<body>
	<div class="row-fluid">
		<div class="span8 offset2 ws-logo">I'M Couch Potato</div>
	</div>
	<div class="row-fluid">
		<div class="span8 offset2 ws-form">
			<form class="form-horizontal" action="search.php" method="post">
				<fieldset>
				    <input id="query" name="query" class="input-xxlarge" type="text">
				    <button id="ok" name="ok" class="btn btn-primary"><b>Go 4 it :)</b></button>
				</fieldset>
			</form>
		</div>
	</div>
	<div class="row-fluid">
		<?php 
			$counter = 1;
			$myOMDB = new OMDB( );
			if( isset( $_POST['query'] ) ) {
				$results = search_OMDB( $_POST['query'] );
				foreach ( $results as $result ) {
					$item = $myOMDB->find_by_IMDB_ID( $result['imdbID'] );

					if ( $item['Poster'] !== "N/A" ) {
						if ( $counter == 1 || ( ( $counter - 1 ) % 4 ) == 0 ) {
		?>
						</div>
						<div class="row-fluid">
							<div class="span2 offset2 ws-tile">
								<img src= <?php echo '"' . $item['Poster'] . '"'; ?> width="300" height="458"/>
							</div>
		<?php
						} else {
		?>
							<div class="span2 ws-tile">
								<img src= <?php echo '"' . $item['Poster'] . '"'; ?> width="300" height="458"/>
							</div>
		<?php
						} 
					} else {
						if ( $counter == 1 || ( ( $counter - 1 ) % 4 ) == 0 ) {
		?>
						</div>
						<div class="row-fluid">
							<div class="span2 offset2 ws-tile">
								<img src="noimage.png" width="300" height="458"/>
							</div>
		<?php
						} else {
		?>
							<div class="span2 ws-tile">
								<img src="noimage.png" width="300" height="458"/>
							</div>
		<?php
						} 
					}
					$counter++;
				}
			}
		?>
	</div>

	<script src="//code.jquery.com/jquery.js"></script>
	<script src="./lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>