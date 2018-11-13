<?php

/* 
	The Authorized page for your app, 
	as described https://docs.google.com/document/d/1Un3XLEg0PtCqho68pCGIFJriSn5E9SFm928iOhQ7LCM/edit#heading=h.xscpu2wx3tmn 
	
	To find your Client ID, Client Secret, and App ID, go to https://socialadr.com/pg/apps/manage and click "Manage" for your app
*/

	require('SocialAdrAPI.php');
	
	$clientId = ''; // Your Client ID
	$clientSecret = ''; // Your Client Secret
	$appId = ''; // Your App ID
	
	$SocialAdr = new SocialAdrAPI($clientId, $clientSecret, $appId);
	$SocialAdr->debug = true;
	$SocialAdr->getInstallURL();
	$result = $SocialAdr->handleAuth();
	if($result){
		print_r($result);
		if($result->error){
			
		}
		elseif($result->access_token){
			$authData = $result;
			$SocialAdr->setAccessToken($result->access_token); // You'd then typically want to save the Access Token to your database, associated with this user
		}
	}
	
	exit;

?>
