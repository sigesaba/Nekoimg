<?php

App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('NekoComponent', 'Neko.Controller/Component');

// A fake controller to test against
class TestNekoController extends Controller {

}

class NekoimgComponentTest extends CakeTestCase {
	public $NekoComponent = null;
	public $Controller = null;
	public $photosData = array(
			'photos' => array(
				'page' => 1,
				'pages' => 1,
				'perpage' => 3,
				'total' => '3',
				'photo' => array(
					0 => array(
						'id' => '0000000',
						'owner' => '00000000@ON',
						'secret' => '0000000000',
						'server' => '0',
						'farm' =>  0,
						'title' => 'title0',
						'ispublic' =>  1,
						'isfriend' =>  0,
						'isfamily' =>  0
					),
					1 => array(
						'id' => '0000001',
						'owner' => '00000001@ON',
						'secret' => '0000000001',
						'server' => '1',
						'farm' =>  1,
						'title' => 'title1',
						'ispublic' =>  1,
						'isfriend' =>  0,
						'isfamily' =>  0
					),
					2 => array(
						'id' => '0000002',
						'owner' => '00000002@ON',
						'secret' => '0000000002',
						'server' => '2',
						'farm' =>  2,
						'title' => 'title2',
						'ispublic' =>  1,
						'isfriend' =>  0,
						'isfamily' =>  0
					)
				),
				'stat' => 'ok'
			)
		);
	public $userData = array(
		'person' => array(
			'id' => '00000001@N00',
			'nsid' => '00000001@N00',
			'ispro' =>  1,
			'iconserver' => '1011',
			'iconfarm' =>  1,
			'path_alias' => 'hoge',
			'username' => array(
				'_content' => 'Hoge'
			),
			'realname' => array(
				'_content' => 'hogehoge'
			),
			'location' => array(
				'_content' => 'Osaka, Japan'
			),
			'timezone' => array(
				'label' => 'Osaka',
				'offset' => '+09:00'
			),
			'description' => array(
				'_content' => 'content'
			),
			'photosurl' => array(
				'_content' => 'http://www.flickr.com/photos/example/'
			),
			'profileurl' => array(
				'_content' => 'http://www.flickr.com/people/example/'
			),
			'mobileurl' => array(
				'_content' => 'http://m.flickr.com/photostream.gne?id=000001'
			),
			'datecreate' => '1114226003',
			'photos' => array(
				'firstdatetaken' => array(
					'_content' => '1970-01-01 07:59:59'
				),
				'firstdate' => array(
					'_content' => '1117080102'
				),
				'count' => array(
					'_content' => 3
				)
			)
		),
		'stat' => 'ok'
	);

	public $errPhotoData = array(
		'stat' => 'fail',
		'code' => 100,
		'message' => 'Invalid API Key (Key has invalid format)'
	);

	public $errUserData = array(
		'stat' => 'fail',
		'code' => 1,
		'message' => 'User not found'
	);

	public function setUp() {
		parent::setUp();
		$Collection = new ComponentCollection();
		$this->NekoComponent = new NekoComponent($Collection);
		$CakeRequest = new CakeRequest();
		$CakeResponse = new CakeResponse();
		$this->Controller = new TestNekoController($CakeRequest, $CakeResponse);
		$this->NekoComponent->startup($this->Controller);
	}

	/**
	 * @expectedException
	 */
	function testPhotoDataException() {
		$this->NekoComponent->chkFlickrErr($this->photosData);
	}

	/**
	 * @expectedException NotFoundException
	 */
	function testInvalidAPIKeyException() {
		$this->NekoComponent->chkFlickrErr($this->errPhotoData);
	}

	function testSetPhotosData() {
		$this->NekoComponent->setPhotoData($this->photosData);
		$this->assertNotEmpty ($this->NekoComponent->photo);
	}

	/**
	 * @expectedException NotFoundException
	 */
	function testUserInfoException() {
		$this->NekoComponent->chkFlickrErr($this->errUserData);
	}

	function testSetUserData() {
		$this->NekoComponent->setUserData($this->userData);
		$this->assertNotEmpty ($this->NekoComponent->userName);
		$this->assertEqual ('Hoge', $this->NekoComponent->userName);
	}

	function testCreateImgUri() {
		$this->NekoComponent->setPhotoData($this->photosData);
		$this->NekoComponent->setUserData($this->userData);
		$this->NekoComponent->createImgUri();
		$this->assertNotEmpty ($this->NekoComponent->imgUri);
		$pattern = '/http:\/\/farm[0-2]\.staticflickr\.com\/[0-2]\/000000[0-2]_000000000[0-2]\.jpg/';
		$this->assertRegExp($pattern, $this->NekoComponent->imgUri);
	}

	function testSetLicenseInfo() {
		$this->NekoComponent->setPhotoData($this->photosData);
		$this->NekoComponent->setUserData($this->userData);
		$this->NekoComponent->setLicenseInfo();
		$this->assertNotEmpty ($this->NekoComponent->licenseInfo);
	}

	function testCreateImgTag() {
		$this->NekoComponent->setPhotoData($this->photosData);
		$this->NekoComponent->setUserData($this->userData);
		$this->NekoComponent->createImgUri();
		$this->NekoComponent->setLicenseInfo();
		$this->NekoComponent->imgSize = array(0 => 100, 1 => 200);
		$this->NekoComponent->createImgTag();
		$matcher = array(
			'tag'        => 'img',
			'attributes' => array(
				'src' => $this->NekoComponent->imgUri,
				'width' => '100',
				'height' => '200')
		);
		$this->assertTag($matcher, $this->NekoComponent->imgNeko);
	}
}
