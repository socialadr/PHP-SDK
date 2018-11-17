<?php
require('SocialAdrErrors.php');

class SocialAdrAPI
{
    public $api = 'https://socialadr.com/api';
    public $authPage = 'https://socialadr.com/pg/apps/details?id=';
    public $redirectURI = '';
    public $scope = 'basic url account fblikes twitter';
    public $debug = false;
    public $accessToken; // The full URL to your Authorized page (redirect URI) goes here
    public $Error; // The space-separated string of app permissions
    private $appId = '';
    private $clientId = '';
    private $clientSecret = '';

    public function __construct($clientId = null, $clientSecret = null, $appId = null)
    {
        if (!empty($clientId)) {
            $this->clientId = $clientId;
        }
        if (!empty($clientSecret)) {
            $this->clientSecret = $clientSecret;
        }
        if (!empty($appId)) {
            $this->appId = $appId;
        }
        $this->Error = new SocialAdrErrors();
    }

    public function getInstallURL()
    {
        if (empty($this->appId)) {
            $this->throwError('WARN', 'APP_ID_REQUIRED');
        }
        return $this->authPage . $this->appId;
    }

    public function throwError($level, $error)
    {
        $this->Error->logError($level, $error);
    }

    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }

    public function setSubaccountGUID($guid)
    {
        $this->subaccountGUID = $guid;
    }

    public function handleAuth()
    {
        $code = $_GET ['code'];
        if (empty($code)) {
            $error = $_GET ['error'];
            $error_description = $_GET ['error_description'];
            echo '<div>Error ' . $error . ': ' . $error_description . '</div>';
            return false;
        } else {
            $result = $this->grant($code);
            if ($result->access_token) {
                $this->accessToken = $result->access_token;
            }
            return $result;
        }
    }

    public function grant($code)
    {
        $endpoint = $this->api . '/oauth/grant';
        $postData = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectURI,
            'scope' => $this->scope //The scope of any API calls you want access to
        ); // The scope of any API calls you want access to
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    private function _postRequest($endpoint, $data = null)
    {
        return $this->_apiCall('POST', $endpoint, $data);
    }

    private function _apiCall($rType = 'GET', $endpoint, $data = null)
    {
        $ch = curl_init();
        $data = $this->_curlOptions($ch, $rType, $endpoint, $data);
        $curlResponse = curl_exec($ch);
        $this->lastError = curl_error($ch);
        $this->curlInfo = curl_getinfo($ch);
        curl_close($ch);

        if ($this->debug) {
            echo '<li>Sending ' . $rType . ' Request to ' . $endpoint . '</li>';
            echo 'Request Data:';
            echo '<pre>' . print_r($data, true) . '</pre>';
            echo 'Response:';
            echo '<pre>' . $curlResponse . '</pre>';
            echo 'Response JSON:';
            echo '<pre>' . print_r(json_decode($curlResponse), true) . '</pre>';
            echo 'CURL Errors:';
            echo '<pre>' . $this->lastError . '</pre>';
            echo '<pre>' . print_r($this->curlInfo) . '</pre>';
        }

        return json_decode($curlResponse);
    }

    private function _curlOptions($ch, $rType, $endpoint, $data)
    {
        $data = $this->addSubaccountData($data);
        $curlOptions = array(
            CURLOPT_URL => $endpoint,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => $rType == "POST",
            CURLOPT_POSTFIELDS => count($data) > 0 ? $data : false,
            CURLOPT_HTTPGET => $rType == "GET",
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 20            
        );
        curl_setopt_array($ch, $curlOptions);
        return $data;
    }

    public function addSubaccountData($postData)
    {
        if (!empty($this->subaccountGUID)) {
            if (empty($postData)) {
                $postData = array();
            }
            $postData['subaccount_guid'] = $this->subaccountGUID;
        }
        return $postData;
    }

    public function refresh($token)
    {
        $endpoint = $this->api . '/oauth/grant';
        $postData = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $token,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectURI,
            'scope' => $this->scope //The scope of any API calls you want access to
        ); // The scope of any API calls you want access to
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function accountCredits()
    {
        $this->requireAccessToken();
        $endpoint = $this->api . '/oauth/account/credits?access_token=' . $this->accessToken;
        $result = $this->_postRequest($endpoint);
        return $result;
    }

    public function requireAccessToken()
    {
        if (empty($this->accessToken)) {
            echo 'A valid Access Token is required to make API calls';
            return false;
        } else {
            return true;
        }
    }

    private function _getRequest($endpoint, $data = null)
    {
        return $this->_apiCall('GET', $endpoint, $data);
    }

    public function fblikesPackages()
    {
        $this->requireAccessToken();
        $endpoint = $this->api . '/oauth/fblikes/packages?access_token=' . $this->accessToken;
        $result = $this->_getRequest($endpoint);
        return $result;
    }

    public function fblikesAdd($bookmark_guid, $package_id)
    {
        $this->requireAccessToken();
        $postData = array(
            'bookmark_guid' => $bookmark_guid,
            'package' => $package_id
        );
        $endpoint = $this->api . '/oauth/fblikes/add?access_token=' . $this->accessToken;
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function fblikesHistory($limit = 100, $offset = 0)
    {
        $this->requireAccessToken();
        $endpoint = $this->api . '/oauth/fblikes/history?access_token=' . $this->accessToken;
        $postData = array(
            'limit' => $limit,
            'offset' => $offset
        );
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function resellerSubaccounts()
    {
        $this->requireAccessToken();
        $endpoint = $this->api . '/oauth/reseller/subaccounts?access_token=' . $this->accessToken;
        $postData = array(
            'limit' => $limit,
            'offset' => $offset
        );
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function resellerAdd(SocialAdrSubaccount $subaccount){
        $this->requireAccessToken();
        $postData = array(
            'name' => $subaccount->name,
            'credits' => $subaccount->credits,
            'twitter' => $subaccount->twitter,
            'google_plus' => $subaccount->google_plus,
            'facebook_likes' => $subaccount->facebook_likes,
            'linkedin' => $subaccount->linkedin,
            'pinterest' => $subaccount->pinterest,
            'disable' => $subaccount->disable
        );
        $endpoint = $this->api . '/oauth/reseller/add?access_token=' . $this->accessToken;
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function resellerCredits($subaccount_guid, $credits){
        $this->requireAccessToken();
        $postData = array(
            'subaccount_guid' => $subaccount_guid,
            'credits' => $credits
        );
        $endpoint = $this->api . '/oauth/reseller/credits?access_token=' . $this->accessToken;
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function urlValidate($url)
    {
        $this->requireAccessToken();
        $postData = array(
            'url' => $url
        );
        $endpoint = $this->api . '/oauth/url/validate?access_token=' . $this->accessToken;
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function urlDelete($bookmark_id)
    {
        $this->requireAccessToken();
        $postData = array(
            'bookmark_id' => $bookmark_id
        );
        $endpoint = $this->api . '/oauth/url/delete?access_token=' . $this->accessToken;
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function urlUndelete($bookmark_id)
    {
        $this->requireAccessToken();
        $postData = array(
            'bookmark_id' => $bookmark_id
        );
        $endpoint = $this->api . '/oauth/url/undelete?access_token=' . $this->accessToken;
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function urlAdd(SocialAdrURL $url)
    {
        $this->requireAccessToken();
        $postData = array(
            'url' => $url->url,
            'title' => $url->title,
            'descr' => $url->descr,
            'tags' => $url->tags,
            'category' => $url->category,
            'microblog' => $url->microblog,
            'submitRate' => $url->submitRate,
            'submitLimit' => $url->submitLimit
        );
        $endpoint = $this->api . '/oauth/url/add?access_token=' . $this->accessToken;
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function urlUpdate(SocialAdrURL $url)
    {
        $this->requireAccessToken();
        $postData = array(
            'guid' => $url->guid,
            'url' => $url->url,
            'title' => $url->title,
            'descr' => $url->descr,
            'tags' => $url->tags,
            'category' => $url->category,
            'microblog' => $url->microblog,
            'submitRate' => $url->submitRate,
            'submitLimit' => $url->submitLimit
        );
        $endpoint = $this->api . '/oauth/url/update?access_token=' . $this->accessToken;
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function urlList($limit = 100, $offset = 0, $sort = 'guid', $sort_direction = 'asc')
    {
        $endpoint = $this->api . '/oauth/url/list?access_token=' . $this->accessToken;
        $postData = array(
            'limit' => $limit,
            'offset' => $offset,
            'sort' => $sort,
            'sort_direction' => $sort_direction
        );
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function urlArchived($limit = 100, $offset = 0, $sort = 'guid', $sort_direction = 'asc')
    {
        $endpoint = $this->api . '/oauth/url/archived?access_token=' . $this->accessToken;
        $postData = array(
            'limit' => $limit,
            'offset' => $offset,
            'sort' => $sort,
            'sort_direction' => $sort_direction
        );
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function reportDetail($url, $offset = 0, $limit = 50, $subaccount_guid = '')
    {
        $this->requireAccessToken();
        $postData = array(
            'url' => $url,
            'offset' => $offset,
            'limit' => $limit,
            'subaccount_guid' => $subaccount_guid,
        );
        $endpoint = $this->api . '/oauth/reports/detail?access_token=' . $this->accessToken;
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function reportOverview($offset = 0, $limit = 10, $subaccount_guid = '')
    {
        $this->requireAccessToken();
        $postData = array(
            'offset' => $offset,
            'limit' => $limit,
            'subaccount_guid' => $subaccount_guid,
        );
        $endpoint = $this->api . '/oauth/reports/overview?access_token=' . $this->accessToken;
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function twitterFollowersAdd($url, $package){
        $this->requireAccessToken();
        $postData = array(
            'url' => $url,
            'package' => $package
        );
        $endpoint = $this->api . '/oauth/twitter/followers/add?access_token=' . $this->accessToken;
        $result = $this->_postRequest($endpoint, $postData);
        return $result;
    }

    public function twitterFollowersPackages(){
        $this->requireAccessToken();
        $endpoint = $this->api . '/oauth/twitter/followers/packages?access_token=' . $this->accessToken;
        $result = $this->_getRequest($endpoint);
        return $result;
    }

    public function twitterFollowersHistory(){
        $this->requireAccessToken();
        $endpoint = $this->api . '/oauth/twitter/followers/history?access_token=' . $this->accessToken;
        $result = $this->_postRequest($endpoint);
        return $result;
    }

    public function errors($responseType = 'html')
    {
        return $this->Error->outputLog($responseType);
    }

    private function _putRequest($endpoint, $data = null)
    {
        return $this->_apiCall('PUT', $endpoint, $data);
    }

    private function _deleteRequest($endpoint, $data = null)
    {
        return $this->_apiCall('DELETE', $endpoint, $data);
    }
}

class SocialAdrSubaccount{
    public $name;
    public $credits=0;
    public $twitter = false;
    public $google_plus = false;
    public $facebook_likes = false;
    public $linkedin = false;
    public $pinterest = false;
    public $disable = false;

    public function enableAll(){
        $this->twitter=true;
        $this->google_plus=true;
        $this->facebook_likes=true;
        $this->linkedin=true;
        $this->pinterest=true;
    }
}

class SocialAdrURL
{
    public $url, $title, $descr, $tags, $category, $microblog; // Require Parameters
    public $submitLimit, $submitRate; //Optional Parameters
}
