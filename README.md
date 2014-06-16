SocialAdr API PHP SDK
=======


## Purpose

This document describes how to use the SocialAdr PHP SDK (Software Development Kit) as a starting point to develop SocialAdr applications in PHP.

## Getting Started

You need to have already followed these steps from the [SocialAdr API Documentation](http://socialadr.com/pg/apps/api-docs) doc:

1. <a href="http://socialadr.com/pg/apps/api-docs#h.uxe0wg1ahrf4" target="_blank">Sign up for the Developer Program</a>

2. <a href="http://socialadr.com/pg/apps/api-docs#h.z2dtrdflr73p" target="_blank">Create an App</a>

3. <a href="http://socialadr.com/pg/apps/api-docs#h.3s6b52buligi" target="_blank">Get the Necessary IDs</a>


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
$SocialAdrAPI = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');
```
Alternatively, you can specify your Client ID, App ID, and Client Secret as shown in the example below:
```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdrAPI = new SocialAdrAPI();
$SocialAdrAPI->setAppId('YOUR APP ID');
$SocialAdrAPI->setClientId('YOUR CLIENT ID');
$SocialAdrAPI->setClientSecret('YOUR CLIENT SECRET');
?>
```
### Authorize App Button or Link
In addition to the ability for users to grant your application access to their data through the SocialAdr App Store, you can create a button or link on your website to authorize your app. When clicked, users are temporarily taken to SocialAdr to authorize access, and are then sent back to your website. Here is an example of how to create an Authorization link using our PHP SDK.
```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdrAPI = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');
echo '<a href="' . $SocialAdrAPI->getInstallURL() . '">Install</a>';
?>
```
### Authorized Page
The Authorized page is a landing page that users are sent to after granting your app access to their SocialAdr data. This page is passed a query parameter with an Authorization Code from SocialAdr. For example:

`http://www.yourapp.com/authorized?code={AUTHORIZATIONCODE}`.

It needs to exchange this Authorization Code for an Access Token. Every API call you make (with the exception of authorization API calls) requires an Access Token.

You’ll want to store the Access Token in your applications database for making future API calls on behalf of the authorized user. Below is an example of exchanging the Authorization Code for an Access Token using the SDK.

```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdrAPI = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');
$SocialAdrAPI->debug = false;
$result = $SocialAdrAPI->handleAuth(); //Exchanges Auth Code for Access Token
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
$SocialAdrAPI = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');
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
Once you have stored the Access Token, you are ready to make your first API call. In the example below, we are using our Access Token to make the `urlValidate()` API call. The complete list of API methods is available [here](http://socialadr.com/pg/apps/api-methods).
```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdrAPI = new SocialAdrAPI('YOUR_APP_ID', 'YOUR_CLIENT_SECRET', 'YOUR_CLIENT_ID');

/* Normally $storedAccessToken
 * would be pulled from your database.
 * We're going to set it inline for this example */
$storedAccessToken = '82d1cd8228104babce0292497ebbf26fd21fa93c';

$SocialAdrAPI->setAccessToken($storedAccessToken);
$result = $SocialAdrAPI->urlValidate('http://somesite.com');

?>
```
### Debugging
If your run into problems, or things don’t appear to be working correctly, you can use the built in SDK debugging functionality to help figure out where the point of failure is.
```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdrAPI = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');
$SocialAdrAPI->urlValidate('http://somesite.com');

$SocialAdrAPI->errors('html'); //Outputs errors to page as HTML
$SocialAdrAPI->errors(); //Output defaults to HTML as above
$SocialAdrAPI->errors('text'); //Outputs errors to page as text
$errorsObj = $SocialAdrAPI->errors('object'); //Returns an array of error objects

// The code above would produce
// [Error 0] App ID must be set with SocialAdrAPI->setAppId() or in API object instantiation
// Error Trace:
// debug_backtrace output from where error was logged
?>
```
## Examples

### Validate URL
Find out if it’s possible to add a URL into the system, before attempting to do so.

`urlValidate($url)`

```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdrAPI = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');
$result = $SocialAdrAPI->urlValidate('http://somesite.com');
if($result['canAdd']){
    //URL Validated
}
else{
    echo $result['reason'];
}
?>
```
### List Bookmarks
Return a list of bookmarks in an account

`urlList($limit=100,$offset=0,$sort=’guid’,$sort_direction=’asc’)`

```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdrAPI = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');
$result = $SocialAdrAPI->urlList(10);
print_r($result);
?>
```

### Add URL
Add a new URL that you want to promote.

`urlAdd(SocialAdrURL $url)`

```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdrAPI = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');

$myURL = new SocialAdrURL;
$myURL->url = 'http://www.something.com/path/';
$myURL->title = 'API Test';
$myURL->descr = 'Here is some kind of description or something. I need to make it long enough.';
$myURL->tags = 'here,are,some,tags';
$myURL->category = 'arts';
$myURL->microblog = 'i hp ths fts on twtr';
$myURL->submitRate = 'fast';
$myURL->submitLimit = 20;
$result = $SocialAdr->urlAdd($myURL);
?>
```

### Overview Report
Gives an overview of the latest social submissions for all URLs in an account.

`reportOverview($offset=0, $limit=10)`

```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdrAPI = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');

$report = $SocialAdrAPI->reportOverview(0, 50);
?>
```
### Detail Report
Gives detailed history of social submissions for a specific URL

`reportDetail($url,$limit=10,$offset=0)`

```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdrAPI = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');

$result = $SocialAdrAPI->reportDetail('http://somesite.com/somepage',0,20);
print_r($result);

?>
```

### Facebook Likes - Get Packages
Lists all available packages for Facebook Likes Campaigns, including number of Likes, and credits required.

`fblikesPackages()`

```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdrAPI = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');

$report = $SocialAdrAPI->fblikesPackages();
?>
```
### Facebook Likes - Add Campaign
Creates a Facebook Likes Campaign for a specific package and bookmark

`fblikesAdd($bookmark_guid, $package_id)`

```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdrAPI = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');

$report = $SocialAdrAPI->fblikesAdd(102832, 2);
?>
```
### Reseller - Add Subaccount
Creates a Socialadr Account under the control of the reseller

`resellerAdd($SocialAdrSubaccount)`
```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdrAPI = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');

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
?>
```

### Reseller - Transfer Credits to Subaccount
Transfers credits from the reseller account to the subaccount

`resellerCredits($subaccount_guid, $credits)`

```php
<?php
require_once("/path/to/SocialAdrAPI.php");
$SocialAdrAPI = new SocialAdrAPI('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'YOUR_APP_ID');

$SocialAdr->resellerCredits(103425, 1000); //Transfers 1000 credits to subaccount
?>
```

