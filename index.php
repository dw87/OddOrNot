<?php
	require_once('oddornot.php');
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<title>Are you Odd or Not?</title>
		<meta name="robots" content="all">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.75, maximum-scale=2.5, user-scalable=yes">
		<link rel="stylesheet" href="css/normalize.css" type="text/css" media="all">
		<link rel="stylesheet" href="css/style.css" type="text/css" media="all">
		<link rel="stylesheet" href="webfonts/ss-social-regular.css" type="text/css" media="all"/>
	</head>
	<body>
		<?php include_once("analyticstracking.php") ?>
		<div class="container">
			<div id="question">
				Are you Odd or Not on Twitter?
			</div>
			<div id="ask">
				<form method="post" id='askform' action="<?php echo $PHP_SELF;?>">
					<label for="name">Your Twitter Name:</label> 
					<input id="asktext" type="text" name="name" placeholder="@Username" <?php if (isset($_POST['name'])){ echo 'value="' . $_POST['name'] . '"';} ?> />
					<button class="ss-icon" id="askbutton" type="submit" href="#ask" >Ask &#xF611;?</button>
				</form>
			</div>
			<div id='result'>
				<div id='chart'></div>
				<div id='label'></div>
				<div id='explain'><button id='explainbutton' type="button">Click for explanation.</button></div>
				<div id='explanation'></div>	
				<div id='error'></div>
			</div>
			<div id="footer">Created by <a href="http://dw87.co.uk" target="_blank">DW87</a>.</div>
			<?php 
				if (isset($_POST['error'])){
					echo '<div id="result"><div id="error">';
					echo $_POST['error'];
					echo '</div></div>';
				};
				if (isset($_POST['odd']) && isset($_POST['even'])){
					echo '<div id="result"><div id="label">';
					echo 'You are ' . $_POST['odd'] . '% odd on Twitter!';
					echo '</div></div>';
				};
			?>
		</div>
		
		<script type="text/javascript" src="js/modernizr.js"></script>
		<script type="text/javascript" src="js/jquery-1.10.2.js"></script>
		<script type="text/javascript" src="http://code.highcharts.com/highcharts.js"></script>
		<script type="text/javascript" src="webfonts/ss-social.js"></script>

		<script type="text/javascript">
			var $submit = $("#askbutton");
			var $twitter = $("#asktext");
			
			function twitterValid(){
				$username = $.trim($twitter.val());
				if (!$username || $username.charAt(0) != "@" || !$username.slice(1).match(/^\w{1,32}$/))
				{
					$submit.attr("disabled",true);
					return false;
				}
				else {
					$submit.removeAttr("disabled");
					return true;
				}
			}
			
			function validateTwitter(){
				if (twitterValid()) $(this).removeClass("invalid").addClass("valid");
				else $(this).removeClass("valid").addClass("invalid");
			}
			
			$twitter.bind("keyup", validateTwitter);
			twitterValid();

			$("#explainbutton").click(function() {
				$("#explanation").toggle("fast");
			});

			$(function(){
				var request;
				$("#askform").submit(function(e) 
				{
				    $submit.attr("disabled","disabled");
					$twitter.unbind("keyup");
					if (request) {
						request.abort();
						$submit.removeAttr("disabled");
						$twitter.bind("keyup", validateTwitter);
						validateTwitter();
					}
					
					var postData = $(this).serialize();
					var formURL = "oddornot.php";
					request = $.ajax(
					{
						url : formURL,
						type : "POST",
						dataType: 'json',
						data : postData
					});
					
					request.done(function( response, textStatus, jqXHR ) {
						if ($('.container #result').css('display') == 'none'){
							$('.container #result').show();
						}
						//If the result is an error (protected account, or no account etc.)
						if(typeof(response.error) !== "undefined" && response.error !== null) {
							$('.container #result #error').html(response.error);

							if ($('.container #result #explanation').css('display') !== 'none'){
								$('.container #result #explanation').slideUp(500, 'linear');
							}				
							
							if ($('.container #result #explain').css('display') !== 'none'){
								$('.container #result #explain').slideUp(500, 'linear');
							}
							
							if ($('.container #result #label').css('display') !== 'none'){
								$('.container #result #label').slideUp(500, 'linear');
							}
							
							if ($('.container #result #chart').css('display') !== 'none'){
								$('.container #result #chart').slideUp(500, 'linear');
							}
							
							if ($('.container #result #error').css('display') == 'none'){
								$('.container #result #error').slideDown(500, 'linear');
							}
						}
						//Else, if it's a good response & there is no chart, draw for the first time if appropriate
						else {
							$('.container #result #label').html("You are " + response.odd + "% odd on Twitter!");
							
							$('.container #result #explanation').html("Of your last 1000 Tweets, " + response.odd + "% were Tweeted in odd minutes e.g. 10:45, not 10:46.   ");
							
							if ($('.container #result #error').css('display') !== 'none'){
								$('.container #result #error').hide();
							}							

							//If #chart already present, update it, else draw it.
							if ($('.container #result #chart').css('display') !== 'none'){
								updateChart(response.odd,response.even);
							}
							else {
								$('.container #result #chart').show();
								//Draw new chart
								drawChart(response.odd, response.even);
								$('.container #result #label').slideDown(500, 'linear', function() {$('.container #result #explain').slideDown(500, 'linear')} );
							}
						}
						$submit.removeAttr("disabled");
						$twitter.bind("keyup", validateTwitter);
						validateTwitter();
					});
					request.fail(function( jqXHR, textStatus, errorThrown ) {
						alert("Error status: " + textStatus + " and Error thrown: " + errorThrown);
					});
					e.preventDefault();
					request = null;
				});
			});

			function drawChart(odd,even){
				$('#chart').highcharts({
					chart: {
						type: 'pie',
						backgroundColor: '#ccd6dd',
			            animation: true,
						reflow: true,
						spacing: [0,0,0,0] 
					},
					credits: {
						enabled: false
					},
					title: {
						text: null
					},
					legend: {
						enabled: false
					},
					plotOptions: {
						pie: {
							allowPointSelect: false,
							animation: {
								duration: 1250,
								easing: 'linear'
							},
							dataLabels: {
								enabled: false
							},
							enableMouseTracking: false,
							colors: ['#55acee', '#292f33'],
							size: '100%'
						}
					},
					tooltip: {
						enabled: false
					},
					series: [{
						type: 'pie',
						data: [[even],[odd]]
					}]
				});
			}
			
			function reflowChart(){
				var chart = $('#chart').highcharts();
				chart.reflow();
			}
			
			function updateChart(odd,even){
				var chart = $('#chart').highcharts();
				chart.series[0].data[0].update([even],true,true);
				chart.series[0].data[1].update([odd],true,true);
			}
		</script>
	</body>
</html>