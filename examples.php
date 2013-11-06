<?php

/* 
	Some examples usages of the various API methods
	
	The API methods are described in http://socialadr.dev/pg/apps/api-methods
	
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

        // Available Credits method
        // Get current available credit balance
        // $SocialAdr->accountCredits();
        $result = $SocialAdr->accountCredits();
        print_r($result);
        
	// Validate method
	// Find out if it's possible to add a URL into the system, before attempting to do so.
        // $SocialAdr->urlValidate($url)
	$result = $SocialAdr->urlValidate('http://somesite.com/somepage');	
	print_r($result);
	
	// List method
	// Return a list of bookmarks in an account
        // $SocialAdr->urlList($limit)
	$result = $SocialAdr->urlList(10);
	print_r($result);
	
	// urlAdd method
	// Add a new URL that you want to promote.
        // $SocialAdr->urlAdd(SocialAdrURL $url)
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
	
	// urlUpdate method
	// Updates an existing URL.
        // $SocialAdr->urlUpdate(SocialAdrURL $url)
	$myURL = new SocialAdrURL;
        $myURL->guid = 12345;
	$myURL->url = 'http://somesite.com/somedifferentpage';
	$myURL->title = 'A new {great|awesome|cool|fun} webpage';
	$myURL->descr = 'This is now the {best|most amazing} {webpage|website} I think I have ever {viewed|looked at|seen} in my entire life! {Highly|Definitely} recommend.';
	$myURL->tags = 'here,are,some,new tags';
	$myURL->category = 'inte';
	$myURL->microblog = 'A {great|awesome|cool|fun} updated webpage';
	$myURL->submitRate = 'fast';
	$myURL->submitLimit = 200;
	$result = $SocialAdr->urlUpdate($myURL);
	print_r($result);	
        
        // urlDelete method
	// Deletes URL with given guid
        // $SocialAdr->urlDelete($guid)
	$result = $SocialAdr->urlDelete(12345);
	print_r($result);
        
        // urlUndelete method
	// Restores deleted URL with given guid
        // $SocialAdr->urlUndelete($guid)
	$result = $SocialAdr->urlUndelete(12345);
	print_r($result);
        
        // urlList method
	// Lists URLs
        // $SocialAdr->urlList($limit, $offset, $sort, $sort_direction)
	$result = $SocialAdr->urlList(15, 0, 'guid', 'asc');
	print_r($result);
        
        // urlArchived method
	// Lists archived URLs
        // $SocialAdr->urlArchived($limit, $offset, $sort, $sort_direction)
	$result = $SocialAdr->urlArchived(15, 0, 'guid', 'asc');
	print_r($result);
        
	// reportOverview method
	// Gives an overview of the latest social submissions for all URLs in an account.
        // $SocialAdr->reportOverview($limit,$offset)
	$result = $SocialAdr->reportOverview(20,0);
	print_r($result);	
	
	// reportDetail method
	// Gives detailed history of social submissions for a specific URL
        // $SocialAdr->reportDetail($url,$limit,$offset)
	$result = $SocialAdr->reportDetail('http://somesite.com/somepage',20,0);
	print_r($result);
	
	// fblikesAdd method
	// Creates a Facebook Likes Campaign with given package for given bookmark
        // $SocialAdr->fblikesAdd($url_guid, $package_id)
	$result = $SocialAdr->fblikesAdd('794101', 1);
	print_r($result);
	
	// fblikesPackages method
	// Lists all available packages for Facebook Likes Campaigns, including number of likes, and credits required
	$result = $SocialAdr->fblikesPackages();
	print_r($results);
        
        // fblikesHistory method
	// List Facebook Likes campaigns
        // $SocialAdr->fblikesHistory($limit, $offset)
	$result = $SocialAdr->fblikesHistory(10, 0);
	print_r($results);
	
        // resellerSubaccounts method
        // Lists all subaccounts controlled by reseller
        // $SocialAdr->resellerSubaccounts()
        $results = $SocialAdr->resellerSubaccounts();
        print_r($results);
        
        // Reseller Subaccount API call example
        // You can call any API call for a subaccount by doing the following
        // $SocialAdr->setSubaccountGUID($subaccount_guid);
        $SocialAdr->setSubaccountGUID(12345);
        $result = $SocialAdr->fblikesHistory(10, 0); //Returns fblikesHistory results for reseller subaccount with guid of 12345
	print_r($results);
        
        
?>
