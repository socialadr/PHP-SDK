SocialAdr API PHP SDK
=======


## Purpose

This document describes how to use the SocialAdr PHP SDK (Software Development Kit) as a starting point to develop SocialAdr applications in PHP.

## Getting Started

You need to have already followed these steps from the [SocialAdr API Documentation](https://socialadr.com/pg/apps/api-docs) doc:

1. <a href="https://socialadr.com/pg/apps/api-docs#h.uxe0wg1ahrf4" target="_blank">Sign up for the Developer Program</a>

2. <a href="https://socialadr.com/pg/apps/api-docs#h.z2dtrdflr73p" target="_blank">Create an App</a>

3. <a href="https://socialadr.com/pg/apps/api-docs#h.3s6b52buligi" target="_blank">Get the Necessary IDs</a>


## Download SDK

Clone or download the SocialAdr PHP SDK Github repository to get started:

`git clone https://github.com/socialadr/PHP-SDK.git`

or

https://github.com/socialadr/PHP-SDK/archive/master.zip

## Start Developing

### Update Variables
Once you’ve downloaded the SDK, and extracted its contents somewhere within your application, you need to update a few variables inside various files:

#### [SocialAdrAPI.php](../master/SocialAdrAPI.php)
This is the main API class file.
* `$redirectURI` _(line 8)_:  the URL on your server that users are redirected to after authorizing your app
* `$scope` _(line 9)_:  a space-separated string of app permissions, example: ‘basic url account’

#### [authorized.php](../master/authorized.php)
The Authorized page for your app, as described here.
* `$clientID` _(line 12)_:  the Client ID of your app
* `$clientSecret` _(line 13)_: the Client Secret Key for your app
* `$appId` _(line 14)_: your app’s unique ID

#### [index.php](../master/index.php)
The page where you have the button/link for your users to grant your app access to their SocialAdr data.
* `$appId` _(line 9)_:  your app’s unique ID

#### [authorized.php](../master/examples.php)
Some examples usages of the various API methods.
* `$clientID` _(line 13)_:  the Client ID of your app
* `$clientSecret` _(line 14)_: the Client Secret Key for your app
* `$appId` _(line 15)_: your app’s unique ID
* `$accessToken` _(line 16)_:  the Access Token for the current user, typically retrieved from your database

## Basic SDK Setup
Now you’re ready to include the SDK into your project. Below is a basic example of including the SocialAdr SDK and instantiating the SocialAdr API class.

```php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdr = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');
```
Alternatively, you can specify your Client ID, App ID, and Client Secret as shown in the example below:
```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdr = new SocialAdrAPI();
$SocialAdr->setAppId('YOUR APP ID');
$SocialAdr->setClientId('YOUR CLIENT ID');
$SocialAdr->setClientSecret('YOUR CLIENT SECRET');
?>
```
### Authorize App Button or Link
In addition to the ability for users to grant your application access to their data through the SocialAdr App Store, you can create a button or link on your website to authorize your app. When clicked, users are temporarily taken to SocialAdr to authorize access, and are then sent back to your website. Here is an example of how to create an Authorization link using our PHP SDK.
```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdr = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');
echo '<a href="' . $SocialAdr->getInstallURL() . '">Install</a>';
?>
```
### Authorized Page
The Authorized page is a landing page that users are sent to after granting your app access to their SocialAdr data. This page is passed a query parameter with an Authorization Code from SocialAdr. For example:

`https://www.yourapp.com/authorized?code={AUTHORIZATIONCODE}`.

It needs to exchange this Authorization Code for an Access Token. Every API call you make (with the exception of authorization API calls) requires an Access Token.

You’ll want to store the Access Token in your applications database for making future API calls on behalf of the authorized user. Below is an example of exchanging the Authorization Code for an Access Token using the SDK.

```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdr = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');
$SocialAdr->debug = false;
$result = $SocialAdr->handleAuth(); //Exchanges Auth Code for Access Token
if($result){
    if($result->error){
        //error handling
    }
    elseif($result->access_token){
        //save access token
        //save refresh token $result->refresh_token as well, you will need it later.
    }
}
?>
```

### Refreshing Access Tokens
Access tokens expire after a set period of time. When you receive an Access Token, you should also be given a Refresh Token. You will want to save both. The Refresh Token is used to get a new Access Token when it has expired.
```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdr = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');
$result = $SocialAdr->refresh($refresh_token);
if($result){
    if($result->error){
        //error handling
    }
    elseif($result->access_token){
        //save access token
        //save refresh token $result->refresh_token as well, you will need it later.
    }
}
?>
```

### Start making API Calls
Once you have stored the Access Token, you are ready to make your first API call. In the example below, we are using our Access Token to make the `urlValidate()` API call. The complete list of API methods is available [here](https://socialadr.com/pg/apps/api-methods).
```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdr = new SocialAdrAPI('YOUR_APP_ID', 'YOUR_CLIENT_SECRET', 'YOUR_CLIENT_ID');

/* Normally $storedAccessToken
 * would be pulled from your database.
 * We're going to set it inline for this example */
$storedAccessToken = '82d1cd8228104babce0292497ebbf26fd21fa93c';
$SocialAdr->setAccessToken($storedAccessToken);
$result = $SocialAdr->urlValidate('http://somesite.com');

?>
```
### Debugging
If your run into problems, or things don’t appear to be working correctly, you can use the built in SDK debugging functionality to help figure out where the point of failure is.
```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdr = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');
$SocialAdr->debug=true;
$SocialAdr->urlValidate('http://somesite.com'); //Make an API call
//Output will contain cURL and Request/Response data
```
# SDK Examples

## URL (Bookmarks)

### URL - Validate
Find out if it’s possible to add a URL into the system, before attempting to do so.

`urlValidate($url)`

```php
$validateURL = $SocialAdr->urlValidate('http://somesite.com');
if($validateURL->success){
    //URL Validated
}
else{
    echo $validateURL->message; //Why it failed
}
```
### URL - List
Return a list of bookmarks in an account

`urlList($limit=100,$offset=0,$sort=’guid’,$sort_direction=’asc’)`

```php
$getUrlList = $SocialAdr->urlList(10);
if($getUrlList->success){
    $list = $getUrlList->response;
    foreach($list as $url){
        ...
    }
}
```

### URL - Add
Add a new URL that you want to promote.

`urlAdd(SocialAdrURL $url)`

```php
$myURL = new SocialAdrURL;
$myURL->url = 'http://www.something.com/path/';
$myURL->title = 'API Test';
$myURL->descr = 'Here is some kind of description or something. I need to make it long enough.';
$myURL->tags = 'here,are,some,tags';
$myURL->category = 'arts';
$myURL->microblog = 'i hp ths fts on twtr';
$myURL->submitRate = 'fast';
$myURL->submitLimit = 20;
$addURL = $SocialAdr->urlAdd($myURL);
if($addURL->success){
    $url_guid = $addURL->response;
}
```

### URL - Update
Update an existing URL,

`urlAdd(SocialAdrURL $url)`

```php
$myURL = new SocialAdrURL;
$myURL->guid = 12345;
$myURL->url = 'http://www.something.com/newpath/';
$myURL->title = 'API Test';
$myURL->descr = 'Here is some kind of description or something. I need to make it long enough.';
$myURL->tags = 'here,are,some,tags';
$myURL->category = 'arts';
$myURL->microblog = 'i hp ths fts on twtr';
$myURL->submitRate = 'fast';
$myURL->submitLimit = 20;
$updateURL = $SocialAdr->urlUpdate($myURL);
if($updateURL->success){
    ...
}
```

### URL - Delete
Delete a URL

`urlDelete($url_guid)`

```php
$delete = $SocialAdr->urlDelete(399212);
if($delete->success){
    ...
}
```

### URL - Undelete
Undelete a URL

`urlUndelete($url_guid)`

```php
$undelete = $SocialAdr->urlUndelete(399212);
if($undelete->success){
    ...
}
```

### URL - List Archived
Get a list of archived URLs

`urlArchived($limit=100, $offset=0)`

```php
$archived = $SocialAdr->urlArchived(50,0);
if($archived->success){
    foreach($archived->response as $url){
        ...
    }
}
```



## Report

### Report - Overview
Gives an overview of the latest social submissions for all URLs in an account.

`reportOverview($offset=0, $limit=10)`

```php
$report = $SocialAdr->reportOverview(0, 50);
if($report->success){
    $overview = $report->response;
}
```
### Report - Detailed
Gives detailed history of social submissions for a specific URL

`reportDetail($url,$limit=10,$offset=0)`

```php
$result = $SocialAdr->reportDetail('http://somesite.com/somepage',0,20);
if($result->success){
    $detailed = $result->response;
}
```

## Facebook Likes

### Facebook Likes - Get Packages
Lists all available packages for Facebook Likes Campaigns, including number of Likes, and credits required.

`fblikesPackages()`

```php
$getPackages = $SocialAdr->fblikesPackages();
if($getPackages->success){
    $packages = $getPackages->response;
}
```
### Facebook Likes - Add Campaign
Creates a Facebook Likes Campaign for a specific package and bookmark

`fblikesAdd($bookmark_guid, $package_id)`

```php
$getPackages = $SocialAdr->fblikesPackages();
if($getPackages->success){
    $packages = $getPackages->response;
    $bookmark_guid = 12345;
    $addCampaign = $SocialAdr->fblikesAdd($bookmark_guid,$packages[0]->id);
    if($addCampaign->success){
        $campaign_id = $addCampaign->response;
    }
}
```

## Reseller

### Reseller - Add Subaccount
Creates a Socialadr Account under the control of the reseller

`resellerAdd($SocialAdrSubaccount)`
```php
$subaccount = new SocialAdrSubaccount();
$subaccount->name='mysubaccount';
$subaccount->credits=5000;
$subaccount->twitter=true;
$subaccount->facebook_likes=true;
$subaccount->google_plus=true;
$subaccount->linkedin=true;
$subaccount->pinterest=true;
$subaccount->disable=true;

$addSub = $SocialAdr->resellerAdd($subaccount);
if($addSub->success){
    $sub = $addSub->response;
}
```

### Reseller - Transfer Credits to Subaccount
Transfers credits from the reseller account to the subaccount

`resellerCredits($subaccount_guid, $credits)`

```php
$subaccount_guid = 12345;
$sendCredits = $SocialAdr->resellerCredits($subaccount_guid, 1000); //Transfers 1000 credits to subaccount
if($sendCredits->success){
    $credits = $sendCredits->response;
}
```

### Reseller - Control Subaccount
You can execute almost any API call as one of your subaccounts. Once `setSubaccountGUID()` has been set, every API call from that point in the code will be executed as your subaccount.
`setSubaccountGUID($guid)`

```php
$SocialAdr->setSubaccountGUID(12345);
$SocialAdr->twitterFollowersAdd('https://twitter.com/someaccount', 1);
```

If you want to go back to making API calls from your account, set the subaccount GUID to null.

```php
$SocialAdr->setSubaccountGUID(null);
$SocialAdr->twitterFollowersAdd('https://twitter.com/someaccount', 1);
```

## Twitter Followers

### Twitter Followers - Packages
Get a list of packages 

`twitterFollowersPackages()`

```php
$getPackages = $SocialAdr->twitterFollowersPackages();
if($getPackages->success){
    $packages = $getPackages->response;
    foreach($packages as $package){
        ...
    }
}
```

### Twitter Followers - Add Campaign
Creates a Facebook Likes Campaign for a specific package and url

`twitterFollowersAdd($url,$package_id)`

```php
$getPackages = $SocialAdr->twitterFollowersPackages();
if($getPackages->success){
    $packages = $getPackages->response;
    $url = 'https://twitter.com/myaccountname';
    $addCampaign = $SocialAdr->twitterFollowersAdd($url,$packages[0]->id);
    if($addCampaign->success){
        $campaign_id = $addCampaign->response;
    }
}

```

### Twitter Followers - History
Get list of twitter followers campaigns 

`twitterFollowersHistory($limit=100, $offset=0)`

```php
$campaigns = $SocialAdr->twitterFollowersHistory(50,10); //50 campaigns, starting at 10th record
foreach($campaigns as $campaign){
    ...
}
```
