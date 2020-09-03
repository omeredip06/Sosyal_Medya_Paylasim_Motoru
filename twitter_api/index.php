<?php 


include "twitteroauth/twitteroauth.php";

$consumer_key='';
$consumer_secret='';
$access_token='';
$access_token_secret='';


$twitter = new TwitterOAuth($consumer_key,$consumer_secret,$access_token,$access_token_secret);

//$tweets = $twitter->get('https://api.twitter.com/1.1/search/tweets.json?q=merhaba&result_type=recent&count=20');

?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Twitter API SEARCH</title>
</head>
<body background="./images/arkaplan.jpg" style="background-size: 100%">
<form action="" method="post">
<b>Aranacak Kelime: 
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
Görüntülenecek Tweet Sayısı:</b><br><input type="text" name="kelime" style="opacity: 0.9;"> 
&nbsp&nbsp&nbsp&nbsp
<input type="text" name="sayi" style="opacity: 0.9;"> <br><br>
<input type="submit" name="gonder" value="Ara">
</form>
</body>
</html>
<?php

	if($_POST){ 

	if(isset($_POST['kelime']) && isset($_POST['sayi'])){
		$tweets= $twitter->get('https://api.twitter.com/1.1/search/tweets.json?q='.$_POST['kelime'].'&result_type=mixed&count='.$_POST['sayi'].'&lang=tr');
		$sayac=1;
	}
	echo '<h4> "'.$_POST['kelime'].'" Sözcüğü İçin Bulunan Sonuçlar:</h4>';

	foreach ($tweets as $tweet) {
		foreach ($tweet as $t) {
			if($t->text!= null){
			echo '<h3> '.$sayac.'. Paylaşım </h3>';
			echo 'Oluşturulma Tarihi= '.$t->created_at.'<br>';
			echo $t->text;
			error_reporting(0);
			echo '<br>';
			$sayac++;
			}
			else continue;
		}

	}
		//print_r($tweets);

	}



?>