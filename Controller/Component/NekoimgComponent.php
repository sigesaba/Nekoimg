<?php
App::uses('Component','Controller');
App::uses('HttpSocket', 'Network/Http');
define('FLICKR_API_URL', 'http://api.flickr.com/services/rest/');
define('PHOTOSSEARCH' , 'flickr.photos.search');
define('USERINFO', 'flickr.people.getInfo');
define('DATAFORMAT', 'php_serial');
define('SORTTYPE', 'interestingness-desc');
define('FLICKR_FARM', 'http://farm');
define('STATICFLICKR', 'staticflickr.com/');
define('IMGEXT', 'jpg');
define('ACCURACY', 1);
define('CONTENTTYPE', 1);
define('LICENSE', 5);
define('PRIVACYFILTER', 1);
define('PERPAGE', 100);
define('SAFESEARCH', 1);
define('TAGS','kitten');

class NekoimgComponent extends Component {

	protected $api_key = YOUR_FLICKR_API_KEY;
	protected $Controller = null;
	protected $HttpSocket = null;
	protected $photo = array();
	protected $userName = null;
	protected $imgUri = null;
	protected $lisenceInfo = null;
	protected $imgNeko = null;
	public $imgSize = array();

	/*
	 * __construct
	 */
	public function __construct(ComponentCollection $collection, $settings = array())
	{
		parent::__construct($collection, $settings);
		$this->HttpSocket = new HttpSocket();
	}

	/*
	 * initialize
	 */
	public function initialize(Controller $controller) {
		$this->Controller = $controller;
	}

	/**
	 * getImgNeko Method
	 * passing variables to a view
	 *
	 * @param
	 * @return void
	 */
	public function getNekoImg() {
		$imgData = $this->getPhotosData();
		$this->chkFlickrErr($imgData);

		$this->setPhotoData($imgData);

		$userInfo = $this->getUserInfo();
		$this->chkFlickrErr($userInfo);

		$this->setUserData($userInfo);

		$this->createImgUri();
		$this->getImgSize();

		$this->setLicenseInfo();
		$this->createImgTag();

		$imgNeko = $this->imgNeko;
		$licenseInfo = $this->licenseInfo;
		$this->Controller->set(compact("imgNeko", "licenseInfo"));
	}

/**
 * getPhotosData Method
 * Retrieve photos data from Flickr
 *
 * @param
 * @return array
 */
	public function getPhotosData() {
		// 画像データ取得
		$results = $this->HttpSocket->get(FLICKR_API_URL,
			array(
				'method' => PHOTOSSEARCH,
				'format' => DATAFORMAT,
				'api_key' => $this->api_key,
				'sort' => SORTTYPE,
				'accuracy' => ACCURACY,
				'content_type' => CONTENTTYPE,
				'license' => LICENSE,
				'privacy_filter' => PRIVACYFILTER,
				'per_page' => PERPAGE,
				'safe_search' => SAFESEARCH,
				'tags' => TAGS,
			));

		$body = $results->body;
		return unserialize($body);
	}

/**
 * setPhotoData Method
 * set single photo data to variable
 *
 * @param array $body
 * @return void
 */
	public function setPhotoData($body) {
		$cnt = count($body['photos']['photo']);
		$num = rand(0, $cnt-1);
		$this->photo = $body['photos']['photo'][$num];
	}

/**
 * getUserInfo Method
 * Retrieve user info from Flickr
 *
 * @param
 * @return array
 */
	public function getUserInfo() {
		$userInfo = $this->HttpSocket->get(FLICKR_API_URL,
			array(
				'method' => USERINFO,
				'format' => DATAFORMAT,
				'api_key' => $this->api_key,
				'user_id' => $this->photo['owner'],
			));
		return unserialize($userInfo->body);
	}

/**
 * setUserData Method
 * set user data to a variable
 *
 * @param
 * @return void
 */
	public function setUserData($userInfo) {
		$this->userName = $userInfo['person']['username']['_content'];
	}

/**
 * createImgUri Method
 * create image uri and set to a variable
 *
 * @param
 * @return void
 */
	public function createImgUri() {
		$this->imgUri = FLICKR_FARM;
		$this->imgUri .=  $this->photo['farm'];
		$this->imgUri .= '.';
		$this->imgUri .= STATICFLICKR;
		$this->imgUri .= $this->photo['server'];
		$this->imgUri .= '/';
		$this->imgUri .= $this->photo['id'];
		$this->imgUri .= '_';
	       	$this->imgUri .= $this->photo['secret'];
		$this->imgUri .= '.';
	       	$this->imgUri .= IMGEXT;
	}

/**
 * getImgSize Method
 * get a photo image size then set to a variable
 *
 * @param
 * @return void
 */
	public function getImgSize() {
		$this->imgSize = getimagesize($this->imgUri);
	}

/**
 * setLicenseInfo
 * set photo license infomation to a variable
 *
 * @param
 * @return void
 */
	public function setLicenseInfo() {
		$this->licenseInfo = $this->photo['title'] . "&nbsp;by&nbsp;" . $this->userName;
	}

/**
 * createImgTag
 * create <img>tag then set to a variable
 *
 * @param
 * @return void
 */
	public function createImgTag() {
		$this->imgNeko = '<img src="';
		$this->imgNeko .= $this->imgUri;
		$this->imgNeko .= '" width="';
		$this->imgNeko .= $this->imgSize[0];
		$this->imgNeko .= '" height="';
		$this->imgNeko .= $this->imgSize[1];
		$this->imgNeko .= '" alt="';
		$this->imgNeko .= $this->licenseInfo;
		$this->imgNeko .= '">';
	}

/**
 * flickrErrChk
 * check if there is an Error in data
 *
 * @param array $data
 * @return NotFoundException
 */
	public function chkFlickrErr($data) {
		if (array_key_exists('code', $data)) {
			throw new NotFoundException($data['message']);
		}
	}
}
