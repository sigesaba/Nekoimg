<?php

App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('NekoimgComponent', 'Nekoimg.Controller/Component');

// A fake controller to test against
class TestNekoimgController extends Controller {

}

class NekoimgComponentTest extends CakeTestCase {
	public $NekoimgComponent = null;
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
		$this->NekoimgComponent = new NekoimgComponent($Collection);
		$CakeRequest = new CakeRequest();
		$CakeResponse = new CakeResponse();
		$this->Controller = new TestNekoimgController($CakeRequest, $CakeResponse);
		$this->NekoimgComponent->startup($this->Controller);
	}

	/**
	 * @expectedException
	 */
	function testPhotoDataException() {
		$this->NekoimgComponent->chkFlickrErr($this->photosData);
	}

	/**
	 * @expectedException NotFoundException
	 */
	function testInvalidAPIKeyException() {
		$this->NekoimgComponent->chkFlickrErr($this->errPhotoData);
	}

	function testSetPhotosData() {
		$this->NekoimgComponent->setPhotoData($this->photosData);
		$this->assertNotEmpty ($this->NekoimgComponent->photo);
	}

	/**
	 * @expectedException NotFoundException
	 */
	function testUserInfoException() {
		$this->NekoimgComponent->chkFlickrErr($this->errUserData);
	}

	function testSetUserData() {
		$this->NekoimgComponent->setUserData($this->userData);
		$this->assertNotEmpty ($this->NekoimgComponent->userName);
		$this->assertEqual ('Hoge', $this->NekoimgComponent->userName);
	}

	function testCreateImgUri() {
		$this->NekoimgComponent->setPhotoData($this->photosData);
		$this->NekoimgComponent->setUserData($this->userData);
		$this->NekoimgComponent->createImgUri();
		$this->assertNotEmpty ($this->NekoimgComponent->imgUri);
		$pattern = '/http:\/\/farm[0-2]\.staticflickr\.com\/[0-2]\/000000[0-2]_000000000[0-2]\.jpg/';
		$this->assertRegExp($pattern, $this->NekoimgComponent->imgUri);
	}

	function testSetCreditInfo() {
		$this->NekoimgComponent->setPhotoData($this->photosData);
		$this->NekoimgComponent->setUserData($this->userData);
		$this->NekoimgComponent->setCreditInfo();
		$this->assertNotEmpty ($this->NekoimgComponent->creditInfo);
	}

	function testCreateImgTag() {
		$this->NekoimgComponent->setPhotoData($this->photosData);
		$this->NekoimgComponent->setUserData($this->userData);
		$this->NekoimgComponent->createImgUri();
		$this->NekoimgComponent->setCreditInfo();
		$this->NekoimgComponent->imgSize = array(0 => 100, 1 => 200);
		$this->NekoimgComponent->createImgTag();
		$matcher = array(
			'tag'        => 'img',
			'attributes' => array(
				'src' => $this->NekoimgComponent->imgUri,
				'width' => '100',
				'height' => '200')
		);
		$this->assertTag($matcher, $this->NekoimgComponent->imgNeko);
	}
}
