<?php
/**
* PHP Face Recognition Class using the Microsoft Face API
* 
* ____________________________________________________________________________________
* DatabaseConnection.class.php must be included
* constant BASE_URL must me defined in index.php
* session must be started with session_start();
* ____________________________________________________________________________________
* 
* @param 	string 	$user
* @return 	boolean
* @author 			Jules Rau <admin@jules-rau.de>
* @copyright 			Jules Rau
* @license 			MIT license
* @origin 			https://github.com/ammerzone/webcam-login
* @version 	1.0		09.03.2017
*/

class FaceRecognition{
	/**
	* Base URL to the Microsoft Face API
	* @const 	API_BASE_URL
	*/
	const API_BASE_URL = 'https://westus.api.cognitive.microsoft.com/face/v1.0/';
	
	/**
	* Key for the Microsoft Face API
	* @const 	API_PRIMARY_KEY
	*/
	const API_PRIMARY_KEY = '{Your Microsoft API key}';
	
	/**
	* Name of the Microsoft Face API person group
	* @const 	API_PERSON_GROUP
	*/
	const API_PERSON_GROUP = '{Your group-name}';
	
	/**
	* Directory Path where face-images will be saved
	* @const 	IMG_PATH_PERSON
	*/
	const IMG_PATH_PERSON = '{relative path for temp uploads}';
	
	/**
	* @var 		object
	* @access 	private
	*/
	private $db;
	
	/**
	* @var 		string
	* @access 	private
	*/
	private $img;
	
	/**
	* @var 		string
	* @access 	private
	*/
	private $user;
	
	/**
	* @var 		string
	* @access 	private
	*/
	private $email;
	
	/**
	* @var 		string
	* @access 	public
	*/
	public $responseText;
	
	/**
	* Constructs the Object
	* 
	* @access 	public
	* @param 	string 	$user
	* @return 	boolean
	*/
	public function __construct($user){
		$this->user = $user;
		
		$this->setDefaults();
		
		$this->db = new DatabaseConnection();
		
		if(!$this->checkTable())
			$this->initTable();
		
		$file_headers = @get_headers(BASE_URL . self::IMG_PATH_PERSON);
		if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found')
			return $this->setResponseText('pathError', array('path' => self::IMG_PATH_PERSON));
		
		return true;
	}
	
	/**
	* Set $mail and $img to NULL
	* 
	* @access 	private
	* @return 	void
	* @see		setDefaults()
	*/
	private function setDefaults(){
		$this->email = NULL;
		$this->img = NULL;
	}
	
	/**
	* Check if table `user` exists
	* 
	* @access 	private
	* @return 	boolean
	* @see		checkTable()
	*/
	private function checkTable(){
		$row = $this->db->query("SELECT `personId`, `faceId`, `name`, `email` FROM `user`");
		
		if(!$row)
			return false;
		
		return true;
	}
	
	/**
	* Create table `user`
	* 
	* @access 	private
	* @return 	boolean
	* @see		initTable()
	*/
	private function initTable(){
		$row = $this->db->query('
			CREATE TABLE IF NOT EXISTS `user` (
				`personId` varchar(36) NOT NULL,
				`faceId` varchar(36) NOT NULL,
				`name` varchar(64) NOT NULL,
				`email` varchar(64) NOT NULL
			);
		');
		if(!$row)
			return $this->setResponseText('dbInit...');
		
		$row = $this->db->query('ALTER TABLE `user` ADD PRIMARY KEY (`personId`), ADD UNIQUE KEY `faceId` (`faceId`), ADD UNIQUE KEY `email` (`email`);');
		
		if(!$row)
			return false;
		
		return true;
	}
	
	/**
	* Set $mail to given parameter
	* 
	* @access 	private
	* @param 	string 	$mail
	* @return 	void
	* @see		setMail()
	*/
	private function setMail($mail){
		$this->email = $mail;
	}
	
	/**
	* Set $img to given parameter
	* 
	* @access 	private
	* @param 	string 	$img
	* @return 	void
	* @see		setBase64Img()
	*/
	private function setBase64Img($img){
		$this->img = $img;
	}
	
	/**
	* Set $responseText to a customized error comment based on given parameter
	* 
	* @access 	private
	* @param 	string 	$msg
	* @param 	array 	$param
	* @return 	boolean
	* @see		setResponseText()
	*/
	private function setResponseText($msg, $param = array()){
		switch($msg){
			case 'pathError' : 
				$this->responseText = 'The Path "' . ((array_key_exists('path', $param)) ? $param['path'] : NULL) . '" doesn`t seem to exist.'; 
				break;
			case 'userLength' : 
				$this->responseText = 'Usernames minimum length have to be 5 characters long.'; 
				break;
			case 'userExists' : 
				$this->responseText = 'Username already exists.';
				break;
			case 'mailUnset' : 
				$this->responseText = 'No E-Mail adress entered.'; 
				break;
			case 'mailInvalid' : 
				$this->responseText = 'E-Mail adress is invalid.'; 
				break;
			case 'mailExists' : 
				$this->responseText = 'E-Mail adress already exists.'; 
				break;
			case 'uploadNoimage' : 
				$this->responseText = 'No image for upload selected.'; 
				break;
			case 'uploadFail' : 
				$this->responseText = 'Image upload failed, please try again.'; 
				break;
			case 'uploadSizeMin' : 
				$this->responseText = 'This image is to small for correct recognition of a face.';
				break;
			case 'uploadSizeMax' : 
				$this->responseText = 'This image is too big for data-upload.';
				break;
			case 'curlError' : 
				$this->responseText = 'Failure while connecting to the microsoft face API: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL); 
				break;
			case 'deleteError' : 
				$this->responseText = 'The image could not be deleted.';
				break;
			case 'detectError' : 
				$this->responseText = 'Failure while uploading face-recognition to the API: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL);
				break;
			case 'detectNoFace' : 
				$this->responseText = 'No face recogniced on this image.'; 
				break;
			case 'detectMultipleFaces' : 
				$this->responseText = 'More than one face recogniced on this image.'; 
				break;
			case 'getPersonGroupEmpty' : 
				$this->responseText = 'Microsoft face API could not find the API persons group for this area.'; 
				break;
			case 'getPersonGroupError' : 
				$this->responseText = 'Failure while trying to find the API persons group: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL); 
				break;
			case 'personGroupEmpty' : 
				$this->responseText = 'Microsoft face API could not create the persons group.'; 
				break;
			case 'personGroupError' : 
				$this->responseText = 'Failure while creating the persons group: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL); 
				break;
			case 'addPersonEmpty' : 
				$this->responseText = 'Microsoft face API could not create the persons group.'; 
				break;
			case 'addPersonError' : 
				$this->responseText = 'Failure while creating the person in the API: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL); 
				break;
			case 'updatePersonError' : 
				$this->responseText = 'Failure while updating the person in the API: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL);
				break;
			case 'getPersonDatabase' : 
				$this->responseText = 'User could not be found in the database.'; 
				break;
			case 'getPersonEmpty' : 
				$this->responseText = 'Microsoft face API could not find the person.'; 
				break;
			case 'getPersonError' : 
				$this->responseText = 'Failure while finding the person: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL); 
				break;
			case 'personFaceEmpty' : 
				$this->responseText = 'Microsoft face API could not assign the face image to the person.'; 
				break;
			case 'personFaceError' : 
				$this->responseText = 'Failure while assigning the image to the person in the API: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL); 
				break;
			case 'updatePersonFaceError' : 
				$this->responseText = 'Failure while updating the persons face image in the API: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL);
				break;
			case 'deletePersonFaceError' : 
				$this->responseText = 'Failure while deleting the persons face image in the API: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL);
				break;
			case 'deletePersonError' : 
				$this->responseText = 'Failure while deleting the person in the API: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL);
				break;
			case 'verifyEmpty' : 
				$this->responseText = 'Microsoft face API could not verify this face.'; 
				break;
			case 'verifyError' : 
				$this->responseText = 'Failure while verifying the user in the API: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL); 
				break;
			case 'verifyNotIdentical' :
				$this->responseText = 'This face doesn`t match to the one to verify.';
				break;
			case 'dbAddError' : 
				$this->responseText = 'User was inserted into the database.'; 
				break;
			case 'dbUpdateError' : 
				$this->responseText = 'User could not be updated into the database.'; 
				break;
			case 'dbGetError' : 
				$this->responseText = 'User value could not be loaded from the database.'; 
				break;
			case 'dbDeleteError' : 
				$this->responseText = 'User could not be deleted from the database.';
				break;
			case 'emptyField' : 
				$this->responseText = 'There are required fields that are empty.';
			case 'loginSuccess' : 
				$this->responseText = 'Login granted, similarity is at ' . ((array_key_exists('confidence', $param)) ? $param['confidence'] : NULL) . '%.';
				return true;
				break;
			case 'logoutSuccess' : 
				$this->responseText = 'Logout successful.';
				return true;
				break;
			case 'registerSuccess' : 
				$this->responseText = 'User could be created successfully.';
				return true;
				break;
			case 'changeMailSuccess' : 
				$this->responseText = 'E-mail address has been successfully updated.';
				return true;
				break;
			case 'changeFaceImageSuccess' : 
				$this->responseText = 'Face image has been successfully updated.';
				return true;
				break;
			case 'deleteUserSuccess' : 
				$this->responseText = 'User has been deleted sucessfully.';
				return true;
				break;
			default : 
				$this->responseText = 'No response message input given.'; 
				break;
		}
		return false;
	}
	
	/**
	* Throws a new Exception based on given parameter
	* 
	* @access 	private
	* @param 	string 	$e
	* @return 	void
	* @see		setException()
	*/
	private function setException($e){
		switch($e){
			case 'methodAPI' : 		throw new Exception('Es muss mit POST, PUT, DELETE, GET oder PATCH zugegriffen werden'); break;
			case 'connectAPI' : 	throw new Exception('Fehler bei der Verbindung zur API.'); break;
			case 'returnAPI' : 		throw new Exception('Fehler bei der Rückgabe der API.'); break;
			default : 				throw new Exception('Keine Fehlermeldung angegeben.'); break;
		}
	}
	
	/**
	* Connect to the Microsoft Face API based on given parameters
	* 
	* @access 	private
	* @param 	string 	$method
	* @param 	string 	$url
	* @param 	array 	$postField
	* @return 	array
	* @see		curl()
	*/
	private function curl($method, $url, $postField = NULL){
		try{
			$url = self::API_BASE_URL . $url;
			
			$method = strtoupper($method);
			if($method != 'POST' && $method != 'PUT' && $method != 'DELETE' && $method != 'GET' && $method != 'PATCH')
				$this->setException('methodAPI');
			
			$ch = curl_init();
			
			if(!$ch)
				$this->setException('connectAPI');
			
			if($postField !== NULL){
				$postField = json_encode($postField);
				
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
						'Content-Type: application/json',   
						'Ocp-Apim-Subscription-Key:' . self::API_PRIMARY_KEY, 
						'Content-Length: ' . strlen($postField)
					)
				);
				
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);
			}else{
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Ocp-Apim-Subscription-Key:' . self::API_PRIMARY_KEY));
			}
			
			if($method !== 'POST')
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
			
			curl_setopt($ch, CURLOPT_URL, $url);
			
			curl_setopt($ch, CURLOPT_POST, true);
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$response = curl_exec($ch);
			
			if(!$response || $response === NULL)
				$this->setException('returnAPI');
			
			curl_close($ch);
		}
		catch(Exception $e){
			return $this->setResponseText('curlError', array('msg' => $e->getMessage()));
		}
		
		return json_decode($response, true);
	}
	
	/**
	* Check if $user is a valid user
	* 
	* @access 	private
	* @return 	boolean
	* @see		checkUser()
	*/
	private function checkUser(){
		if(strlen($this->user) < 5)
			return $this->setResponseText('userLength');
		
		$row = $this->db->query('SELECT `name` FROM `user`');
		
		if(sizeof($row) > 0){
			$res = false;
			foreach($row as $key => $val){
				if($val['name'] == $this->user){
					$res = true;
					break;
				}
			}
			if($res === true)
				$this->setResponseText('userExists');
		}
		
		return true;
	}
	
	/**
	* Check if $mail is a valid mail
	* 
	* @access 	private
	* @return 	boolean
	* @see		checkMail()
	*/
	private function checkMail(){
		if($this->email === NULL)
			return $this->setResponseText('mailUnset');
		
		if(!filter_var($this->email, FILTER_VALIDATE_EMAIL))
			return $this->setResponseText('mailInvalid');
		
		$row = $this->db->query('SELECT `email` FROM `user`');
		
		if(sizeof($row) > 0){
			$res = false;
			foreach($row as $key => $val){
				if($val['email'] == $this->email){
					$res = true;
					break;
				}
			}
			if($res === true)
				return $this->setResponseText('mailExists');
		}
		
		return true;
	}
	
	/**
	* Upload $img to given directory based on parameter
	* 
	* @access 	private
	* @return 	boolean
	* @see		uploadImage()
	*/
	private function uploadImage(){
		if($this->img === NULL)
			return $this->setResponseText('uploadNoimage');
		
		$base64 = preg_replace('#^data:image/\w+;base64,#i', '', $this->img);
		$data = base64_decode($base64);
		
		if($this->getBase64Size($data) === false){
			return false;
		}
		
		$destURL = self::IMG_PATH_PERSON . '/' . $this->user . '.jpeg';
			
		$link = str_replace(BASE_URL, '', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		$depth = sizeof(explode("/", $link)) - 1;
		
		$prefix = '';
		for($i = 0; $i < $depth; $i++){
			$prefix .= '../' . $prefix;
		}
		
		$destURL = $prefix . $destURL;
		
		if(!file_put_contents($destURL, $data))
			return $this->setResponseText('uploadFail');
		
		if($this->getImageSize($destURL) === false){
			$this->deleteImage();
			return false;
		}
		
		return true;
	}
	
	/**
	* Get image size of base64 string
	* 
	* The microsoft face API allows only images with a file size between 1 KB (8 192 bit) and 4 MB (8 388 608 bit).
	* To decrease the server traffic, we check the base64 string, if the uploaded jpeg file will correspond this file size.
	* Knowing that one base64 pixel has a color depth of 8 bit by default, we can easily calculate the propable file size.
	* 
	* @access 	private
	* @param 	string 		$data
	* @return 	boolean
	* @see 		getBase64Size()
	*/
	private function getBase64Size($data){
		list($width, $height, $type, $attr) = getimagesizefromstring($data);
		
		$size = $width * $height * 8; // width pixels * height pixels * 8 bit
		
		$oneKB = 8192;		// 1 KB = 8192 Bit, minimum size for API
		$fourMB = 8388608; 	// 4 MB = 8388608 Bit, maximum size for API
		
		if($size < $oneKB)
			return $this->setResponseText('uploadSizeMin');
		if($size > $fourMB)
			return $this->setResponseText('uploadSizeMax');
		
		return true;
	}
	
	/**
	* Get image size of uploaded image
	* 
	* After having uploaded the base64 image we check the actual image size, because some servers make strange things... 
	* 
	* @access 	private
	* @param 	string 		$img
	* @return 	boolean
	* @see 	getImageSize()
	*/
	private function getImageSize($img){
		$size = floor(filesize($img) * 8); // image size in bit
		
		$oneKB = 8192;		// 1 KB = 8192 Bit, minimum size for API
		$fourMB = 8388608; 	// 4MB = 8388608 Bit, maximum size for API
		
		if($size < $oneKB)
			return $this->setResponseText('uploadSizeMin');
		if($size > $fourMB)
			return $this->setResponseText('uploadSizeMax');
		
		return true;
	}
	
	/**
	* Delete image related to user from given directory based on parameter
	* 
	* @access 	private
	* @return 	boolean
	* @see		deleteImage()
	*/
	private function deleteImage(){
		$path = self::IMG_PATH_PERSON . '/' . $this->user . '.jpeg';
		
		$link = str_replace(BASE_URL, '', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		$depth = sizeof(explode("/", $link)) - 1;
		
		$prefix = '';
		for($i = 0; $i < $depth; $i++){
			$prefix .= '../' . $prefix;
		}
		
		$path = $prefix . $path ;
		
		if(!file_exists($path))
			return $this->setResponseText('pathError', array('path' => $path));
		
		chmod($path, 0777);
		
		if(!unlink($path))
			return $this->setResponseText('deleteError');
	
		return true;
	}
	
	/**
	* Microsoft Face API to detect faces and get a faceId
	* 
	* @access 	private
	* @return 	mixed
	* @see		detectFace()
	*/
	private function detectFace(){
		$img = BASE_URL . self::IMG_PATH_PERSON . '/' . $this->user . '.jpeg';
		
		$res = $this->curl('POST', 'detect?returnFaceId=true', array('url' => $img));
		
		if($res === false){
			$res2 = $this->deleteImage();
			
			if($res2 === false)
				return false;
			
			return false;
		}
		
		if(array_key_exists('error', $res)){
			//$res2 = $this->deleteImage();
			
			//if($res2 === false)
			//	return false;
			
			return $this->setResponseText('detectError', array('msg' => $res['error']['message']));
		}
		
		if(!array_key_exists(0, $res)){
			//$res2 = $this->deleteImage();
			
			//if($res2 === false)
			//	return false;
			
			return $this->setResponseText('detectNoFace',$res);
		}
		
		if(!array_key_exists('faceId', $res[0])){
			$res = $this->deleteImage();
			
			if($res === false)
				return false;
			
			return $this->setResponseText('detectNoFace',$res);
		}
		
		if(sizeof($response) > 1){
			$res2 = $this->deleteImage();
			
			if($res2 === false)
				return false;
			
			return $this->setResponseText('detectMultipleFaces');
		}
		
		$faceId = $res[0]['faceId'];
		
		return $faceId;
	}
	
	/**
	* Microsoft Face API to get data of the person group
	* 
	* @access 	private
	* @return 	mixed
	* @see		getPersonGroup()
	*/
	private function getPersonGroup(){
		$url = 'persongroups/' . self::API_PERSON_GROUP;
		
		$res = $this->curl('GET', $url);
		
		if($res === false)
			return false;
		
		if(strlen($res) === 0)
			return $this->setResponseText('getPersonGroupEmpty');
		
		if(array_key_exists('error', $res))
			return $this->setResponseText('getPersonGroupError', array('msg' => $res['error']['message']));
		
		$persistedFaceIds = $res;
		
		return $persistedFaceIds;
	}
	
	/**
	* Microsoft Face API to create a new person group
	* 
	* @access 	private
	* @return 	boolean
	* @see		createPersonGroup()
	*/
	private function createPersonGroup(){
		$data = array(
			'name' => self::API_PERSON_GROUP, 
			'userData' => 'user-provided data attached to the person group'
		);
		$res = $this->curl('PUT', 'persongroups/' . self::API_PERSON_GROUP, $data);
		
		if($res === false)
			return false;
		
		if(sizeof($res) === 0)
			return $this->setResponseText('personGroupEmpty');
		
		if(array_key_exists('error', $res)){
			if($res['error']['code'] !== 'PersonGroupExists')
				$this->setResponseText('personGroupError', array('msg' => $res['error']['message']));
		}
		
		return true;
	}
	
	/**
	* Microsoft Face API to get date of $user
	* 
	* @access 	private
	* @return 	mixed
	* @see		getPerson()
	*/
	private function getPerson(){
		if(!$this->databaseGetUser('personId'))
			return $this->setResponseText('getPersonDatabase');
		
		$url = 'persongroups/' . self::API_PERSON_GROUP . '/persons/' . $this->databaseGetUser('personId');
		
		$res = $this->curl('GET', $url);
		
		if($res === false)
			return false;
		
		if(strlen($res) === 0)
			return $this->setResponseText('getPersonEmpty');
		
		if(array_key_exists('error', $res))
			return $this->setResponseText('getPersonError', array('msg' => $res['error']['message']));
		
		$persistedFaceIds = $res['persistedFaceIds'];
	}
	
	/**
	* Microsoft Face API to create $user
	* 
	* @access 	private
	* @return 	mixed
	* @see		createPerson()
	*/
	private function createPerson(){
		$url = 'persongroups/' . self::API_PERSON_GROUP . '/persons';
		
		$data = array(
			'name' => $this->user, 
			'userData' => 'User-provided data attached to the person'
		);
		
		$res = $this->curl('POST', $url, $data);
		
		if($res === false)
			return false;
		
		if(strlen($res) === 0)
			return $this->setResponseText('addPersonEmpty');
		
		if(array_key_exists('error', $res))
			return $this->setResponseText('addPersonError', array('msg' => $url . $res['error']['message']));
		
		$personId = $res['personId'];
		
		return $personId;
	}
	
	/**
	* Microsoft Face API to update $user
	* 
	* @access 	private
	* @return 	boolean
	* @see		updatePerson()
	*/
	private function updatePerson(){
		$url = 'persongroup/' . self::API_PERSON_GROUP . '/persons/' . $this->databaseGetUser('personId');
		
		$data = array(
			'name' => $this->user, 
			'userData' => 'user-provided data attached to the person.'
		);
		
		$res = $this->curl('PATCH', $url, $data);
		
		if($res === false)
			return false;
		
		if(array_key_exists('error', $res))
			return $this->setResponseText('updatePersonError', array('msg' => $res['error']['message']));
		
		return true;
	}
	
	/**
	* Microsoft Face API to assign a face to $user
	* 
	* @access 	private
	* @param 	string 	$personId
	* @return 	mixed
	* @see		addPersonFace()
	*/
	private function addPersonFace($personId){
		$url = 'persongroups/' . self::API_PERSON_GROUP . '/persons/' . $personId . '/persistedFaces';
		$data = array(
			'url' => BASE_URL . self::IMG_PATH_PERSON . '/' . $this->user . '.jpeg'
		);
		$res = $this->curl('POST', $url, $data);
		
		if($res === false)
			return false;
		
		if(strlen($res) === 0)
			return $this->setResponseText('personFaceEmpty');
		
		if(array_key_exists('error', $res))
			return $this->setResponseText('personFaceError', array('msg' => $res['error']['message'] . BASE_URL . self::IMG_PATH_PERSON . '/' . $this->user . '.jpeg'));
		
		$persistedFaceId = $res['persistedFaceId'];
		
		return $persistedFaceId;
	}
	
	/**
	* Microsoft Face API to update assigned faces of $user
	* 
	* @access 	private
	* @return 	boolean
	* @see		updatePersonFace()
	*/
	private function updatePersonFace(){
		$url = 'persongroup/' . self::API_PERSON_GROUP . '/persons/' . $this->databaseGetUser('personId') . '/persistedFaces/' . $this->databaseGetUser('faceId');
		
		$data = array('userData' => 'user-provided data attached to the person.');
		
		$res = $this->curl('PATCH', $url, $data);
		
		if($res === false)
			return false;
		
		if(array_key_exists('error', $res))
			return $this->setResponseText('updatePersonFaceError', array('msg' => $res['error']['message']));
		
		return true;
	}
	
	/**
	* Microsoft Face API to verify the image width the user
	* 
	* @access 	private
	* @param 	string 	$faceId
	* @param 	string 	$personId
	* @return 	mixed
	* @see		verify()
	*/
	private function verify($faceId, $personId){
		$url = 'verify';
		
		$data = array(
			'faceId' => $faceId, 
			'personId' => $personId, 
			'personGroupId' => self::API_PERSON_GROUP
		);
		
		$res = $this->curl('POST', $url, $data);
		
		if($res === false)
			return false;
		
		if(strlen($res) === 0)
			return $this->setResponseText('verifyEmpty');
		
		if(array_key_exists('error', $res))
			return $this->setResponseText('verifyError', array('msg' => $res['error']['message']));
		
		if($res['idIdentical'] === false)
			return $this->setResponseText('verifyNotIdentical');
		
		return $res['confidence'];
	}
	
	/**
	* Delete a Face of a Person from the Microsoft Face API
	* 
	* @access 	private
	* @return	boolean
	* @see 		deletePersonFace()
	*/
	private function deletePersonFace(){
		$url = 'persongroups/' . self::API_PERSON_GROUP . '/persons/' . $this->databaseGetUser('personId') . '/persistedFaces/' . $this->databaseGetUser('faceId');
		
		$res = $this->curl('DELETE', $url);
		
		if($res === false)
			return false;
		
		if(array_key_exists('error', $res))
			return $this->setResponseText('deletePersonFaceError', array('msg' => $res['error']['message']));
		
		return true;
	}
	
	/**
	* Delete a Person from the Microsoft Face API
	* 
	* @access 	private
	* @return	boolean
	* @see 		deletePerson()
	*/
	private function deletePerson(){
		$url = 'persongroups/' . self::API_PERSON_GROUP . '/persons/' . $this->databaseGetUser('personId');
		
		$res = $this->curl('DELETE', $url);
		
		if($res === false)
			return false;
		
		if(array_key_exists('error', $res))
			return $this->setResponseText('deletePersonError', array('msg' => $res['error']['message']));
		
		return true;
	}
	
	/**
	* Database query to add a new user
	* 
	* @access 	private
	* @param 	string 	$faceId
	* @param 	string 	$personId
	* @param 	string 	$email
	* @return 	boolean
	* @see		databaseAddUser()
	*/
	private function databaseAddUser($faceId, $personId, $email = NULL){
		if($this->email === NULL || $email === NULL)
			return $this->setResponseText('mailUnset');
		
		$email = (isset($email) ? $email : $this->email);
		
		$row = $this->db->query("INSERT INTO `user` (`personId`, `faceId`, `name`, `email`) VALUES ('" . $personId . "', '" . $faceId . "', '" . $this->user . "', '" . $email . "')");
		
		if(!$row)
			return $this->setResponseText('dbAddError');
		
		return true;
	}
	
	/**
	* Database query to update an attribute of $user
	* 
	* @access 	private
	* @param 	string 	$attr
	* @param 	string 	$value
	* @return 	boolean
	* @see		databaseUpdateUser()
	*/
	private function databaseUpdateUser($attr, $value){
		$row = $this->db->query("UPDATE `user` SET `' . $attr . '` = '" . $value . "' WHERE `name` = '" . $this->user . "'");
		
		if(!$row)
			return $this->setResponseText('dbUpdateError');
		
		return true;
	}
	
	/**
	* Database query to get an attribute date of $user
	* 
	* @access 	private
	* @return 	mixed
	* @see		databaseGetUser()
	*/
	private function databaseGetUser($data){
		$row = $this->db->query("SELECT `" . $data . "` FROM `user` WHERE `name` = '" . $this->user . "'");
		
		if(sizeof($row) === 0)
			return $this->setResponseText('dbGetError');
		
		return $row[0][$data];
	}
	
	/**
	* Delete $user from Database
	* 
	* @access 	private
	* @return 	boolean
	* @see 		databaseDeleteUser()
	*/
	private function databaseDeleteUser(){
		$personId = $this->databaseGetUser('personId');
		$row = $this->db->query("DELETE FROM `user` WHERE `personId` = '" . $personId . "'");
		
		if(!$row)
			return $this->setResponseText('dbDeleteError');
		
		return true;
	}
	
	/**
	* Login procedure and set session
	* 
	* ______________________________________________________________
	* Function calls for a login:
	*   databaseGetUser('name'); setBase64Img();
	*   if: uploadImage();
	*     if: detectFace();
	*       getPersonGroup(); databaseGetUser('personId'); verify();
	*     endif;
	*     deleteImage('login');
	*   endif;
	* ______________________________________________________________
	* 
	* @access 	public
	* @param 	string 	$img
	* @return 	array
	* @see 		login()
	*/
	public function login($img){
		if(strlen($this->user) == 0 || strlen($img) == 0){
			return array(
				'val' => false, 
				'responseText' => $this->setResponseText('emptyField')
			);
		}

		$this->setBase64Img($img);

		$res = $this->databaseGetUser('name');
		
		if($res === false){ 
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		$res = $this->uploadImage();
		
		if($res === false){ 
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		$res = $this->detectFace();
		
		if($res === false){ 
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		$faceId = $res;
		
		$res = $this->getPersonGroup();
		
		if($res === false){
			$res = $this->createPersonGroup();
			
			if($res === false){
				$res2 = $this->deleteImage();
				
				if($res2 === false){ 
					return array(
						'val' => $res2, 
						'responseText' => $this->responseText
					);
				}
				
				return array(
					'val' => $res, 
					'responseText' => $this->responseText
				);
			}
		}

		$res = $this->databaseGetUser('personId');
		
		if($res === false){
			$res2 = $this->deleteImage();
			
			if($res2 === false){ 
				return array(
					'val' => $res2, 
					'responseText' => $this->responseText
				);
			}
			
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		$personId = $res;
		
		$res = $this->verify($faceId, $personId);
		
		if($res === false){
			$res2 = $this->deleteImage();
			
			if($res2 === false){ 
				return array(
					'val' => $res2, 
					'responseText' =>$this->responseText
				);
			}
			
			return array(
				'val' => $res, 
				'responseText' =>$this->responseText
			);
		}
		
		$confidence = $res;

		$res = $this->deleteImage();
		
		if($res === false){ 
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		$_SESSION['webcam_login_session'] = md5($this->user) . ";" . $this->user;

		return array(
			'val' => true, 
			'responseText' => $this->setResponseText('loginSuccess', array('confidence' => floor($confidence * 100)))
		);
	}
	
	/**
	* Function to destroy session
	* 
	* @access 	public
	* @return 	array
	* @see 		logout()
	*/
	public function logout(){
		$_SESSION['webcam_login_session'] = NULL;
		session_destroy();
		
		return array(
			'val' => true, 
			'responseText' => $this->setResponseText('logoutSuccess')
		);
	}
	
	/**
	* Registration procedure
	* 
	* __________________________________________________________
	* Function calls for a registration:
	*   setMail(); checkUser(); checkMail(); setBase64Img();
	*   if: uploadImage();
	*     if: detectFace();
	*       if not: getPersonGroup();
	*         createPersonGroup();
	*       endif;
	*       createPerson(); addPersonFace(); databaseAddUser();
	*     endif;
	*   endif;
	* __________________________________________________________
	* 
	* @access 	public
	* @param 	string 	$email
	* @param 	string 	$img
	* @return 	array
	* @see 		register()
	*/
	public function register($email, $img){
		if(strlen($this->user) == 0 || strlen($email) == 0 || strlen($img) == 0){
			return array(
				'val' => false, 
				'responseText' => $this->setResponseText('emptyField')
			);
		}
		
		$this->setBase64Img($img);

		$this->setMail($email);

		$res = $this->checkUser();

		if($res === false){
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}

		$res = $this->checkMail();

		if($res === false){
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}

		$res = $this->uploadImage();

		if($res === false){
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}

		$res = $this->detectFace();

		if($res === false){
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}

		$faceId = $res;

		$res = $this->getPersonGroup();

		if($res === false){
			$res = $this->createPersonGroup();
			
			if($res === false){
				$res2 = $this->deleteImage();
				
				if($res2 === false){
					return array(
						'val' => $res2, 
						'responseText' => $this->responseText
					);
				}
				
				return array(
					'val' => $res, 
					'responseText' => $this->responseText
				);
			}
		}

		$res = $this->createPerson();

		if($res === false){
			$res2 = $this->deleteImage();
			
			if($res2 === false){
				return array(
					'val' => $res, 
					'responseText' => $this->responseText
				);
			}
			
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}

		$personId = $res;

		$res = $this->addPersonFace($personId);

		if($res === false){
			$res2 = $this->deleteImage();
			
			if($res2 === false){
				return array(
					'val' => $res2, 
					'responseText' => $this->responseText
				);
			}
			
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}

		$res = $this->databaseAddUser($faceId, $personId, $this->email);

		if($res === false){
			$res2 = $this->deleteImage();
			
			if($res2 === false){
				return array(
					'val' => $res2, 
					'responseText' => $this->responseText
				);
			}
			
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}

		$res = $this->deleteImage();
		
		if($res === false){ 
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		return array(
			'val' => true, 
			'responseText' => $this->setResponseText('registerSuccess')
		);
	}
	
	/**
	* Mail changement procedure
	* 
	* _______________________________________________________
	* Function calls for a mail changement:
	*   setMail(); databaseUpdateUser('email', $this->email);
	* _______________________________________________________
	* 
	* @access 	public
	* @param 	string 	$newEmail
	* @return 	array
	* @see 		changeMail()
	*/
	public function changeMail($newEmail){
		if(strlen($newEmail) == 0){
			return array(
				'val' => false, 
				'responseText' => $this->setResponseText('emptyField')
			);
		}
		
		$this->setMail($newEmail);
		
		$res = $this->databaseUpdateUser('email', $this->email);
		
		if($res === false){
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		return array(
			'val' => true, 
			'responseText' => $this->setResponseText('changeMailSuccess')
		);
	}
	
	/**
	* Face-image changement procedure
	* 
	* ______________________________________________
	* Function calls for face-image changement:
	*     setBase64Img();
	*     if: uploadImage();
	*       if: detectFace();
	*         getPersonGroup(); updatePersonFace(); databaseUpdateUser('faceId', $faceId);
	*       endif;
	*     endif;
	* ______________________________________________
	* 
	* @access 	public
	* @param 	string		$newImg
	* @return 	array
	* @see 		changeFaceImage()
	*/
	public function changeFaceImage($newImg){
		if(strlen($newImg) == 0){
			return array(
				'val' => false, 
				'responseText' => $this->setResponseText('emptyField')
			);
		}
		
		$this->setBase64Img($newImg);
		
		$res = $this->uploadImage();

		if($res === false){
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}

		$res = $this->detectFace();

		if($res === false){
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}

		$faceId = $res;
		
		$res = $this->getPersonGroup();

		if($res === false){
			$res = $this->createPersonGroup();
			
			if($res === false){
				$res2 = $this->deleteImage();
				
				if($res2 === false){
					return array(
						'val' => $res2, 
						'responseText' => $this->responseText
					);
				}
				
				return array(
					'val' => $res, 
					'responseText' => $this->responseText
				);
			}
		}
		
		$res = $this->updatePersonFace();
		
		if($res === false){
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		$res = $this->databaseUpdateUser('faceId', $faceId);
		
		if($res === false){
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		return array(
			'val' => true, 
			'responseText' => $this->setResponseText('changeFaceImageSuccess')
		);
	}
	
	/**
	* Deletes an User from Database and Microsoft Face API
	* 
	* ______________________________________________
	* Function calls for user deletement:
	* 	if: deletePersonFace();
	* 		if: deletePerson();
	* 			if: databaseDeleteUser();
	* 				logout();
	* 			endif;
	* 		endif;
	* 	endif;
	* ______________________________________________
	* 
	* @access 	public
	* @return 	array
	* @see 		deleteUser()
	*/
	public function deleteUser(){
		$res = $this->deletePersonFace();
		
		if($res === false){
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		$res = $this->deletePerson();
		
		if($res === false){
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		$res = $this->databaseDeleteUser();
		
		if($res === false){
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		$res = $this->logout();
		
		if($res === false){
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		return array(
			'val' => true, 
			'responseText' => $this->setResponseText('deleteUserSuccess')
		);
	}
}
?>
