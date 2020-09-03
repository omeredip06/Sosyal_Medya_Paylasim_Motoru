<?php
session_start();
require_once __DIR__ . '/src/Facebook/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => '456356851375577',
  'app_secret' => '6bee89f39da40927f8f3a26ba9af6d3d',
  'default_graph_version' => 'v2.9',
  ]);
$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; // optional
	
try {
	if (isset($_SESSION['facebook_access_token'])) {
		$accessToken = $_SESSION['facebook_access_token'];
	} else {
  		$accessToken = $helper->getAccessToken();
	}
} catch(Facebook\Exceptions\FacebookResponseException $e) {
 	// When Graph returns an error
 	echo 'Graph returned an error: ' . $e->getMessage();
  	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
 	// When validation fails or other local issues
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
  	exit;
 }
if (isset($accessToken)) {
	if (isset($_SESSION['facebook_access_token'])) {
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	} else {
		// getting short-lived access token
		$_SESSION['facebook_access_token'] = (string) $accessToken;
	  	// OAuth 2.0 client handler
		$oAuth2Client = $fb->getOAuth2Client();
		// Exchanges a short-lived access token for a long-lived one
		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
		$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
		// setting default access token to be used in script
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	}
	// redirect the user back to the same page if it has "code" GET variable
	if (isset($_GET['code'])) {
		header('Location: ./index2.php');
	}
	// getting basic info about user
	try {
		$profile_request = $fb->get('/me?fields=name,first_name,last_name,email');
		$profile = $profile_request->getGraphNode()->asArray();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		session_destroy();
		// redirecting user back to app login page
		header("Location: ./");
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	
	// printing $profile array on the screen which holds the basic info about user
	//print_r($profile);
	
	// type can be user, group, page or event
			//header ("Location:facebook_api/index2.php"); 
	?>
		<!DOCTYPE html>
		<html lang="tr">
		<head>
		<meta charset="UTF-8">
		<title>FACEBOOK API SEARCH</title>
		</head>
		<body bgcolor ="#0099FF">
		<form action="" method="post">
		<b><font color="black">Aranacak Kelime:</font></b> <br><input type="text" name="kelime" style="opacity: 0.9;"> <br>
		<input type="submit" name="gonder" value="Ara">
		</form>
		</body>
		</html>
	<?php
		if($_POST){ 

			if(isset($_POST['kelime'])) {
				$sayac=0;
			$search = $fb->get('/search?q='.$_POST['kelime'].'&type=event&');
			$search = $search->getGraphEdge()->asArray();
			foreach ($search as $key) {
				$sayac++;
			}
			echo '<font color="black"><h4> "'.$_POST['kelime'].'" Sözcüğü İçin '.$sayac.' Tane Sonuç Bulundu.</h4></font>';
			$sayac=1;
			foreach ($search as $key) {
				echo '<font color="black"><h3> '.$sayac.'. Paylaşım </h3></font><font color="black">'.
				$key['description'] . '<br>'.
				$key['name'] . '</font><br>';
				error_reporting(0);
				$sayac++;
			}
			//print_r($search);
		}
		else
		{
			echo '<font color="black"><h4> Boş Arama Yapılamaz!! </h4></font>';
		}
  	// Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
}
} 
else {
	// replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
	$loginUrl = $helper->getLoginUrl('http://localhost/donem_projesi/facebook_api/index2.php', $permissions);
	echo '
	<html lang="tr">
	<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="./css/facebook_login.css">
	<title>Facebooka Giriş Yapın!</title>
	</head>
	<style type="text/css">

	a {
	color: white;
	font-style: oblique;
	font-family: Arial, Helvetica, sans-serif;
	text-decoration: none;
	vertical-align: baseline;
	}

	a:hover {
	color: maroon;
	font-style: oblique;
	font-family: Arial, Helvetica, sans-serif;
	text-decoration: none;
	vertical-align: baseline;
	}

	</style>
	<body background="./images/arkaplan.jpg" >
	<br><br><br>
	<h1 align="center"> <font color="white">UPPS! Facebook Hesabınız Açık Değil!!</font></h1>
	<div class="social-wrap a"  style="margin: 3%; margin-left: 40%;  "> 
    <button id="facebook"><a href="'.$loginUrl.'" >Facebooka Giriş Yap</a></button>
	</div>
	</body>
	</html>
	';
}

?>