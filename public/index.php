<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>tweet category</title>

		<!-- Bootstrap -->
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
		<link href="css/helper.css" rel="stylesheet">
		<link href="css/layout.css" rel="stylesheet">

		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
		<script src="js/chart.js"></script>
		<script src="js/index.js"></script>
	</head>
	<body>
		<div class="jumbotron pan">
			<div class="container txt_c">
				<div class="row twit-head">
					<h1>Tweet category </h1>
<!--					<p>I love bootstrap</p>
					<p><a class="btn btn-primary btn-lg" role="button">Learn more »</a></p>-->
				</div>
			</div>
		</div>

		<div class="container c">
			<div class="row txt_c pbl mbl">
				<canvas id="canvas" height="450" width="1300"></canvas>
			</div>
			<div class="row txt_c">
				<table id="twit-cat" class="table">
					<thead>
						<tr class="filters heading">
							<th>
								<span class='twit-legend-tbl' style="background-color:#FF0000"></span>
								<span class="cat" id="culture">Other</span>
							</th>
							<th>
								<span class='twit-legend-tbl' style="background-color:#6699FF"></span>
								<span  class="cat" id="language">Other</span>
							</th>
							<th>
								<span class='twit-legend-tbl' style="background-color:#33CC33"></span>
								<span  class="cat" id="law">Other</span>
							</th>
							<th>
								<span class='twit-legend-tbl' style="background-color:#33CC33"></span>
								<span  class="cat" id="other">Other</span>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr id="data-row">
							<td>
								<div class="twit-row">

								</div>

							</td>

							<td>
								<div class="twit-row">
								</div>
							</td>
							<td>
								<div class="twit-row">
								</div>
							</td>
							<td>
								<div class="twit-row">
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div id="footer" class="txt_c">
			<div class="row">
				<p class="ptm">
					the geeky named team (deep inside)
				</p>
			</div>
		</div>
	</body>

</html>