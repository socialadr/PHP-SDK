<?php

/* 
	Some examples usages of the various API methods
	
	The API methods are described in https://docs.google.com/document/d/1Sai2VVAYCcMilB02EzpoTcBbpb0YSNxW_YbCZeJrHlk/edit?usp=sharing
	
	To find your Client ID, Client Secret, and App ID, go to http://socialadr.com/pg/apps/manage and click "Manage" for your app
*/

	require('SocialAdrAPI.php');
	
	$clientId = ''; // Your Client ID
	$clientSecret = ''; // Your Client Secret
	$appId = ''; // Your App ID
	$accessToken = ''; // The Access Token for this user, typically retrieved from your database
	
	$SocialAdr = new SocialAdrAPI($clientId, $clientSecret, $appId);
	$SocialAdr->debug = true;
	$SocialAdr->setAccessToken($accessToken); 

	// Validate method
	// Find out if it's possible to add a URL into the system, before attempting to do so.
	$result = $SocialAdr->urlValidate('http://somesite.com/somepage');	
	print_r($result);	
	
	// urlAdd method
	// Add a new URL that you want to promote.
	$myURL = new SocialAdrURL;
	$myURL->url = 'http://somesite.com/somepage';
	$myURL->title = 'A {great|awesome|cool|fun} webpage';
	$myURL->descr = 'This is the {best|most amazing} {webpage|website} I think I have ever {viewed|looked at|seen} in my entire life! {Highly|Definitely} recommend.';
	$myURL->tags = 'here,are,some tags';
	$myURL->category = 'inte';
	$myURL->microblog = 'A {great|awesome|cool|fun} webpage';
	$myURL->submitRate = 'normal';
	$myURL->submitLimit = 100;
	$result = $SocialAdr->urlAdd($myURL);
	print_r($result);	
	
	// reportOverview method
	// Gives an overview of the latest social submissions for all URLs in an account.
	$result = $SocialAdr->reportOverview(0,20);
	print_r($result);	
	
	// reportDetail method
	// Gives detailed history of social submissions for a specific URL
	$result = $SocialAdr->reportDetail('http://somesite.com/somepage',0,20);
	print_r($result);
	
	//fblikesAdd method
	// Creates a Facebook Likes Campaign with given package for given bookmark
	$result = $SocialAdr->fblikesAdd('794101', 1);
	print_r($result);
	
	//fblikesPackages method
	// Lists all available packages for Facebook Likes Campaigns, including number of likes, and credits required
	$result = $SocialAdr->fblikesPackages();
	print_r($results);
	
	
	

?>