<?php
	ini_set('display_errors', 1);
	require_once('TwitterAPIExchange.php');

	$settings = array(
		'oauth_access_token' => file_get_contents('twitter/oauth_access_token'),
		'oauth_access_token_secret' => file_get_contents('twitter/oauth_access_token_secret'),
		'consumer_key' => file_get_contents('twitter/consumer_key'),
		'consumer_secret' => file_get_contents('twitter/consumer_secret')
	);

	if (isset($_POST['name'])){
		$twittername = $_POST["name"];
		oddornot($twittername);
	};
	
	function oddornot($twittername){
		$remaining = rate_check();
		$twittername = clean_username($twittername);
		$even = 0;
		$odd = 0;
		$error = null;
	
		if (validate_username($twittername) == true){
			global $settings;
			$limit = 1000;
			
			$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
			$requestMethod = 'GET';		
			
			$getfield = '?screen_name='.$twittername.'&include_rts=1&count=200';

			$twitter = new TwitterAPIExchange($settings);
			$response = $twitter->setGetfield($getfield)
								->buildOauth($url, $requestMethod)
								->performRequest();
			
			$output = json_decode($response,true);
			$total = count($output);
			$remaining--;

			if (!$output[0]['user']['screen_name']){
				if($output['errors'][0]['code'] == 34){
					$error = "The Twitter account doesn't exist!";
					$_POST['error'] = "The Twitter account doesn't exist!";
				}
				
				if($output['errors'][0]['code'] == 88){
					$error = "The Twitter Rate limit exceeded!";
					$_POST['error'] = "The Twitter Rate limit exceeded.  Oops!";
				}
				
				if($output['error'] == "Not authorized."){
					$error = "The Twitter account is protected!";
					$_POST['error'] = "The Twitter account is protected!";
				}
			}
			else {
				$numResults = $total;		
				$maxId = $output[$numResults-1]['id_str'];
				while ($maxId !== null && $total < $limit && $numResults >= 199 && $remaining > 0) {
					if ( $limit - $total > 200) {
						$count = 200;
					}
					else {
						$count = $limit - $total;
					}
					$getfield = '?screen_name='.$twittername.'&include_rts=1&count='.$count.'&max_id='.$maxId;

					$response = $twitter->setGetfield($getfield)
										->buildOauth($url, $requestMethod)
										->performRequest();	
					
					$remaining--;
					
					$new_response = json_decode($response, true);
					$numResults = count($new_response);
					$maxId = $new_response[$numResults-1]['id_str'];
					$output = array_merge($output,$new_response);
					$total = count($output);
				}
				for ($i = 0; $i < $total; $i++){
					$date = date_parse($output[$i]['created_at']);
					if ($date['minute'] % 2 == 0){
						$even++;
					}
				}
				
				$even_percent = round(($even / $total) * 100);
				$odd_percent = 100 - $even_percent;			
				$_POST['odd'] = $odd_percent;
				$_POST['even'] = $even_percent;
			}						
		}
		else {
			$error = "The Twitter username is invalid!";
			$_POST['error'] = "The Twitter username is invalid!";
		}	
		
		if (is_ajax()){
			echo json_encode(array('error' => $error, 'odd' => $odd_percent, 'even' => $even_percent));
		}
	}
	
	function clean_username($username) {
		$username=preg_replace('/\s+/', '', $username);
		if ($username[0] == '@') {
			$username = substr($username, 1);
		}
		return $username;
	}
	
	function validate_username($username){
		if (preg_match('/^[A-Za-z0-9_]{1,15}$/', $username)){
			return true;
		}
		else {
			return false;
		}
	}
	
	function rate_check(){
		global $settings;
		$url = 'https://api.twitter.com/1.1/application/rate_limit_status.json';
		$requestMethod = 'GET';		
		
		$getfield = '?resources=statuses';

		$twitter = new TwitterAPIExchange($settings);
		$response = $twitter->setGetfield($getfield)
							->buildOauth($url, $requestMethod)
							->performRequest();
		
		$output = json_decode($response,true);
		return $output['resources']['statuses']['/statuses/user_timeline']['remaining'];
	}
	
	function is_ajax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}

?>