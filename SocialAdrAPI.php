<?php
require ('SocialAdrErrors.php');

class SocialAdrAPI {
	private $appId = '';
	private $clientId = '';
	private $clientSecret = '';
	public $api = 'http://socialadr.com/api';
	public $authPage = 'http://socialadr.com/pg/apps/details?id=';
	public $redirectURI = ''; // The full URL to your Authorized page (redirect URI) goes here
	public $scope = 'basic url account fblikes'; // The space-separated string of app permissions
	public $debug = false;
	public $accessToken;
	public $Error;
	public function __construct($clientId = null, $clientSecret = null, $appId = null) {
		if (! empty($clientId)) {
			$this->clientId = $clientId;
		}
		if (! empty($clientSecret)) {
			$this->clientSecret = $clientSecret;
		}
		if (! empty($appId)) {
			$this->appId = $appId;
		}
		$this->Error = new SocialAdrErrors();
	}
	public function getInstallURL() {
		if (empty($this->appId)) {
			$this->throwError('WARN', 'APP_ID_REQUIRED');
		}
		return $this->authPage . $this->appId;
	}
	public function setClientSecret($clientSecret) {
		$this->clientSecret = $clientSecret;
	}
	public function setClientId($clientId) {
		$this->clientId = $clientId;
	}
	public function setAppId($appId) {
		$this->appId = $appId;
	}
	public function setAccessToken($token) {
		$this->accessToken = $token;
	}
        public function setSubaccountGUID($guid){
                $this->subaccountGUID=$guid;
        }
        public function addSubaccountData($postData){
            if(!empty($this->subaccountGUID)){
                if(empty($postData)){
                    $postData = array();
                }
                $postData['subaccount_guid'] = $this->subaccountGUID;
            }
            return $postData;
        }
	public function handleAuth() {
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
	public function refresh($token) {
		$endpoint = $this->api . '/oauth/grant';
		$postData = array (
				'grant_type' => 'refresh_token',
				'refresh_token' => $token,
				'client_id' => $this->clientId,
				'client_secret' => $this->clientSecret,
				'redirect_uri' => $this->redirectURI,
				'scope' => $this->scope  //The scope of any API calls you want access to
		); // The scope of any API calls you want access to
		$result = $this->_postRequest($endpoint, $postData);
		return $result;
	}
	public function grant($code) {
		$endpoint = $this->api . '/oauth/grant';
		$postData = array (
				'grant_type' => 'authorization_code',
				'code' => $code,
				'client_id' => $this->clientId,
				'client_secret' => $this->clientSecret,
				'redirect_uri' => $this->redirectURI,
				'scope' => $this->scope  //The scope of any API calls you want access to
		); // The scope of any API calls you want access to
		$result = $this->_postRequest($endpoint, $postData);
		return $result;
	}
        public function accountCredits(){
		$this->requireAccessToken();
		$endpoint = $this->api . '/oauth/account/credits?access_token=' . $this->accessToken;
		$result = $this->_getRequest($endpoint);
		return $result;
        }
	public function fblikesPackages() {
		$this->requireAccessToken();
		$endpoint = $this->api . '/oauth/fblikes/packages?access_token=' . $this->accessToken;
		$result = $this->_getRequest($endpoint);
		return $result;
	}
	public function fblikesAdd($bookmark_guid, $package_id) {
		$this->requireAccessToken();
		$postData = array (
				'bookmark_guid' => $bookmark_guid,
				'package' => $package_id 
		);
		$endpoint = $this->api . '/oauth/fblikes/add?access_token=' . $this->accessToken;
		$result = $this->_postRequest($endpoint, $postData);
		return $result;
	}
        public function fblikesHistory($limit=100, $offset=0){
            $this->requireAccessToken();
            $endpoint = $this->api . '/oauth/fblikes/history?access_token=' . $this->accessToken;
            $postData = array (
				'limit' => $limit,
				'offset' => $offset
            );
            $result = $this->_postRequest($endpoint, $postData);
            return $result;
        }
        public function resellerSubaccounts(){
            $this->requireAccessToken();
            $endpoint = $this->api . '/oauth/reseller/subaccounts?access_token=' . $this->accessToken;
            $postData = array (
				'limit' => $limit,
				'offset' => $offset
            );
            $result = $this->_postRequest($endpoint, $postData);
            return $result;
        }
        
	public function urlValidate($url) {
		$this->requireAccessToken();
		$postData = array (
				'url' => $url 
		);
		$endpoint = $this->api . '/oauth/url/validate?access_token=' . $this->accessToken;
		$result = $this->_postRequest($endpoint, $postData);
		return $result;
	}
        public function urlDelete($bookmark_id){
		$this->requireAccessToken();
		$postData = array (
				'bookmark_id' => $bookmark_id
		);
		$endpoint = $this->api . '/oauth/url/delete?access_token=' . $this->accessToken;
		$result = $this->_postRequest($endpoint, $postData);
		return $result;
        }
        public function urlUndelete($bookmark_id){
		$this->requireAccessToken();
		$postData = array (
				'bookmark_id' => $bookmark_id
		);
		$endpoint = $this->api . '/oauth/url/undelete?access_token=' . $this->accessToken;
		$result = $this->_postRequest($endpoint, $postData);
		return $result;
        }
	public function urlAdd(SocialAdrURL $url) {
		$this->requireAccessToken();
		$postData = array (
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
        public function urlUpdate(SocialAdrURL $url) {
		$this->requireAccessToken();
		$postData = array (
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
	public function urlList($limit = 100, $offset = 0, $sort = 'guid', $sort_direction = 'asc') {
		$endpoint = $this->api . '/oauth/url/list?access_token=' . $this->accessToken;
		$postData = array (
				'limit' => $limit,
				'offset' => $offset,
				'sort' => $sort,
				'sort_direction' => $sort_direction 
		);
		$result = $this->_postRequest($endpoint, $postData);
		return $result;
	}
        public function urlArchived($limit = 100, $offset = 0, $sort = 'guid', $sort_direction = 'asc'){
		$endpoint = $this->api . '/oauth/url/archived?access_token=' . $this->accessToken;
		$postData = array (
				'limit' => $limit,
				'offset' => $offset,
				'sort' => $sort,
				'sort_direction' => $sort_direction 
		);
		$result = $this->_postRequest($endpoint, $postData);
		return $result;
        }
	public function reportDetail($url, $offset = 0, $limit = 50) {
		$this->requireAccessToken();
		$postData = array (
				'url' => $url,
				'offset' => $offset,
				'limit' => $limit 
		);
		$endpoint = $this->api . '/oauth/reports/detail?access_token=' . $this->accessToken;
		$result = $this->_postRequest($endpoint, $postData);
		return $result;
	}
	public function reportOverview($offset = 0, $limit = 10) {
		$this->requireAccessToken();
		$postData = array (
				'offset' => $offset,
				'limit' => $limit 
		);
		$endpoint = $this->api . '/oauth/reports/overview?access_token=' . $this->accessToken;
		$result = $this->_postRequest($endpoint, $postData);
		return $result;
	}
	public function requireAccessToken() {
		if (empty($this->accessToken)) {
			echo 'A valid Access Token is required to make API calls';
			return false;
		} else {
			return true;
		}
	}
	public function throwError($level, $error) {
		$this->Error->logError($level, $error);
	}
	public function errors($responseType = 'html') {
		return $this->Error->outputLog($responseType);
	}
	private function _apiCall($rType = 'GET', $endpoint, $data = null) {
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
	private function _curlOptions($ch, $rType, $endpoint, $data) {
                $data = $this->addSubaccountData($data);
		curl_setopt_array($ch, array (
				CURLOPT_URL => $endpoint,
				CURLOPT_HEADER => false,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => count($data),
				CURLOPT_POSTFIELDS => $data,
				CURLOPT_SSL_VERIFYPEER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_TIMEOUT => 20,
				CURLOPT_PORT => 80 
		));
                return $data;
	}
	private function _putRequest($endpoint, $data = null) {
		return $this->_apiCall('PUT', $endpoint, $data);
	}
	private function _postRequest($endpoint, $data = null) {
		return $this->_apiCall('POST', $endpoint, $data);
	}
	private function _getRequest($endpoint, $data = null) {
		return $this->_apiCall('GET', $endpoint, $data);
	}
	private function _deleteRequest($endpoint, $data = null) {
		return $this->_apiCall('DELETE', $endpoint, $data);
	}
}
class SocialAdrURL {
	public $url, $title, $descr, $tags, $category, $microblog; // Require Parameters
	// Require Parameters
	public $submitLimit, $submitRate; //Optional Parameters
}
