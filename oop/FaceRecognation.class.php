<?php
/**
* PHP Face Recognation Class using the Microsoft Face API
* 
* ____________________________________________________________________________________
* DatabaseConnection.class.php must be included
* constant BASE_URL must me defined at index.php
* ____________________________________________________________________________________
* 
* @param 	string 	$user
* @return 	boolean
* @author 			Jules Rau <admin@jules-rau.de>
* @copyright 		Jules Rau
* @version 	1.0	07.03.2017
*/

class FaceRecognation{
	/**
	* Base URL to the Microsoft Face API
	* @const 	API_BASE_URL
	*/
	const API_BASE_URL = 'https://westus.api.cognitive.microsoft.com/face/v1.0/';
	
	/**
	* Key for the Microsoft Face API
	* @const 	API_PRIMARY_KEY
	*/
	const API_PRIMARY_KEY = '{Microsoft API Key}';
	
	/**
	* Name of the Microsoft Face API person group
	* @const 	API_PERSON_GROUP
	*/
	const API_PERSON_GROUP = '{Group name}';
	
	/**
	* Directory Path where face-images will be saved
	* @const 	IMG_PATH_PERSON
	*/
	const IMG_PATH_PERSON = 'media/user/';
	
	/**
	* Directory Name where original face-images will be saved
	* @const 	IMG_PATH_PERSON_ORIGINAL
	*/
	const IMG_PATH_PERSON_ORIGINAL = 'login';
	
	/**
	* Directory Name where temporar login face-images will be saved
	* @const 	IMG_PATH_PERSON_LOGIN
	*/
	const IMG_PATH_PERSON_LOGIN = 'tmp';
	
	/**
	* Name of the login session cookie
	* @const 	LOGIN_SESSION_NAME
	*/
	const LOGIN_SESSION_NAME = 'webcam';
	
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
		
		$file_headers = @get_headers(self::IMG_PATH_PERSON);
		if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found')
			return $this->setResponseText('pathError', array('path' => self::IMG_PATH_PERSON));
		
		$file_headers = @get_headers(self::IMG_PATH_PERSON . self::IMG_PATH_PERSON_ORIGINAL);
		if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found')
			return $this->setResponseText('pathError', array('path' => self::IMG_PATH_PERSON . self::IMG_PATH_PERSON_ORIGINAL));
		
		$file_headers = @get_headers(self::IMG_PATH_PERSON . self::IMG_PATH_PERSON_LOGIN);
		if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found')
			return $this->setResponseText('pathError', array('path' => self::IMG_PATH_PERSON . self::IMG_PATH_PERSON_LOGIN));
		
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
			CREATE TABLE `user` (
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
				$this->responseText = 'Der Pfad "' . ((array_key_exists('path', $param)) ? $param['path'] : NULL) . '" scheint nicht zu existieren.'; 
				break;
			case 'userLength' : 
				$this->responseText = 'Username muss mind. 5 Zeichen lang sein.'; 
				break;
			case 'userExists' : 
				$this->responseText = 'Username bereits vergeben.';
				break;
			case 'mailUnset' : 
				$this->responseText = 'Es wurde keine E-Mail gewählt.'; 
				break;
			case 'mailInvalid' : 
				$this->responseText = 'E-Mail Adresse ist ungültig.'; 
				break;
			case 'mailExists' : 
				$this->responseText = 'E-Mail Adresse bereits vergeben.'; 
				break;
			case 'uploadNoimage' : 
				$this->responseText = 'Es wurde kein Bild zum Hochladen gewählt.'; 
				break;
			case 'uploadFail' : 
				$this->responseText = 'Bildupload fehlgeschlagen, bitte versuche es erneut.'; 
				break;
			case 'curlError' : 
				$this->responseText = 'Fehler beim Aufau zur Microsoft face API: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL); 
				break;
			case 'methodImg' : 
				$this->responseText = 'Das Bild muss "original" oder "login" zugewiesen werden.'; 
				break;
			case 'deleteError' : 
				$this->responseText = 'Das Bild konnte nicht gelöscht werden.';
				break;
			case 'detectNoFace' : 
				$this->responseText = 'Auf dem Bild konnte kein Gesicht identifiziert werden.' . $param; 
				break;
			case 'detectMultipleFaces' : 
				$this->responseText = 'Es wurde mehr als ein Gesicht identifiziert.'; 
				break;
			case 'getPersonGroupEmpty' : 
				$this->responseText = 'Microsoft face API konnte die Personen-Gruppe nicht finden.'; 
				break;
			case 'getPersonGroupError' : 
				$this->responseText = 'Fehler beim Finden der Personen-Gruppe: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL); 
				break;
			case 'personGroupEmpty' : 
				$this->responseText = 'Microsoft face API konnte keine Personen-Gruppe erstellen.'; 
				break;
			case 'personGroupError' : 
				$this->responseText = 'Fehler beim Erstellen der Personen-Gruppe: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL); 
				break;
			case 'addPersonEmpty' : 
				$this->responseText = 'Microsoft face API konnte die Personen nicht erstellen.'; 
				break;
			case 'addPersonError' : 
				$this->responseText = 'Fehler beim Erstellen der Person: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL); 
				break;
			case 'updatePersonError' : 
				$this->responseText = 'Fehler beim API-Update der Person: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL);
				break;
			case 'getPersonDatabase' : 
				$this->responseText = 'User konnte nicht in der Datenbank gefunden werden.'; 
				break;
			case 'getPersonEmpty' : 
				$this->responseText = 'Microsoft face API konnte die Personen nicht finden.'; 
				break;
			case 'getPersonError' : 
				$this->responseText = 'Fehler beim Finden der Person: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL); 
				break;
			case 'personFaceEmpty' : 
				$this->responseText = 'Microsoft face API konnte der Personen das Bild nicht zuweisen.'; 
				break;
			case 'personFaceError' : 
				$this->responseText = 'Fehler beim zuweisen des Bildes zur Person: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL); 
				break;
			case 'updatePersonFaceError' : 
				$this->responseText = 'Fehler beim API-Update des Bildes zu Person: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL);
				break;
			case 'verifyEmpty' : 
				$this->responseText = 'Microsoft face API konnte keine Identifizierung erstellen.'; 
				break;
			case 'verifyError' : 
				$this->responseText = 'Fehler beim Identifizieren der Person: ' . ((array_key_exists('msg', $param)) ? $param['msg'] : NULL); 
				break;
			case 'verifyNotIdentical' :
				$this->responseText = 'Diese Person entspricht nicht der zu identifizierenden.';
				break;
			case 'dbAddError' : 
				$this->responseText = 'User konnte nicht in der Datenbank angelegt werden.'; 
				break;
			case 'dbUpdateError' : 
				$this->responseText = 'User konnte nicht in der Datenbank aktualisiert werden.'; 
				break;
			case 'dbGetError' : 
				$this->responseText = 'User-Wert konnte nicht aus der Datenbank geladen werden.'; 
				break;
			default : 
				$this->responseText = 'Keine Fehlermeldung angegeben.'; 
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
		
		if(file_exists('../media/user/login/' . $this->user . '.jpeg'))
			return $this->setResponseText('userExists');
		
		$row = $this->db->query(
			'SELECT `name` FROM `user` WHERE `name` = :user', 
			array('user' => $this->user)
		);
		
		if($row)
			return $this->setResponseText('userExists');
		
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
		
		$row = $this->db->query(
			'SELECT `email` FROM `user` WHERE `email` = :email', 
			array('email' => $this->email)
		);
		
		if($row)
			return $this->setResponseText('mailExists');
		
		return true;
	}
	
	/**
	* Upload $img to given directory based on parameter
	* 
	* @access 	private
	* @param 	string 	$method
	* @return 	boolean
	* @see		uploadImage()
	*/
	private function uploadImage($method){
		if(strtoupper($method) != 'ORIGINAL' && strtoupper($method) != 'LOGIN')
			return $this->setResponseText('methodImg');
		
		if($this->img === NULL)
			return $this->setResponseText('uploadNoimage');
		
		$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->img));
		
		if(strtoupper($method) == 'ORIGINAL')
			$destURL = self::IMG_PATH_PERSON . self::IMG_PATH_PERSON_ORIGINAL . '/' . $this->user . '.jpeg';
		else
			$destURL = self::IMG_PATH_PERSON . self::IMG_PATH_PERSON_LOGIN . '/' . $this->user . '.jpeg';
		
		$link = str_replace(BASE_URL, '', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		$depth = sizeof(explode("/", $link)) - 1;
		
		$prefix = '';
		for($i = 0; $i < $depth; $i++){
			$prefix .= '../' . $prefix;
		}
		
		$destURL = $prefix . $destURL;
		
		if(!file_put_contents($destURL, $data))
			return $this->setResponseText('uploadFail');
		
		return true;
	}
	
	/**
	* Delete image related to user from given directory based on parameter
	* 
	* @access 	private
	* @param 	string 	$method
	* @return 	boolean
	* @see		deleteImage()
	*/
	private function deleteImage($method){
		if(strtoupper($method) != 'ORIGINAL' && strtoupper($method) != 'LOGIN')
			return $this->setResponseText('methodImg');
		
		if(strtoupper($method) == 'ORIGINAL')
			$path = self::IMG_PATH_PERSON . self::IMG_PATH_PERSON_ORIGINAL . '/' . $this->user . '.jpeg';
		else
			$path = self::IMG_PATH_PERSON . self::IMG_PATH_PERSON_LOGIN . '/' . $this->user . '.jpeg';
		
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
	* @param 	string 	$method
	* @return 	mixed
	* @see		detectFace()
	*/
	private function detectFace($method){
		if(strtoupper($method) != 'ORIGINAL' && strtoupper($method) != 'LOGIN'){
			//$this->deleteImage('LOGIN'); // nur login weil register kann überschrieben werden, wenn bei einem eigentlichen Login verschwindet wär blöd
			return $this->setResponseText('methodImg');
		}
		
		if(strtoupper($method) == 'ORIGINAL')
			$img = BASE_URL . self::IMG_PATH_PERSON . self::IMG_PATH_PERSON_ORIGINAL . '/' . $this->user . '.jpeg';
		else
			$img = BASE_URL . self::IMG_PATH_PERSON . self::IMG_PATH_PERSON_LOGIN . '/' . $this->user . '.jpeg';
		
		$res = $this->curl('POST', 'detect?returnFaceId=true', array('url' => $img));
		
		if($res === false)
			return false;
		
		if(!array_key_exists(0, $res)){
			//$this->deleteImage($method);
			return $this->setResponseText('detectNoFace');
		}
		
		if(!array_key_exists('faceId', $res[0])){
			//$this->deleteImage($method);
			return $this->setResponseText('detectNoFace');
		}
		
		if(sizeof($response) > 1){
			//$this->deleteImage($method);
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
			'url' => BASE_URL . self::IMG_PATH_PERSON . self::IMG_PATH_PERSON_ORIGINAL . '/' . $this->user . '.jpeg'
		);
		$res = $this->curl('POST', $url, $data);
		
		if($res === false)
			return false;
		
		if(strlen($res) === 0)
			return $this->setResponseText('personFaceEmpty');
		
		if(array_key_exists('error', $res))
			return $this->setResponseText('personFaceError', array('msg' => $res['error']['message'] . BASE_URL . self::IMG_PATH_PERSON . self::IMG_PATH_PERSON_ORIGINAL . '/' . $this->user . '.jpeg'));
		
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
		
		$row = $this->db->query(
			'INSERT INTO `user` (`personId`, `faceId`, `name`, `email`) VALUES (:personId, :faceId, :name, :email)', 
			array(
				'personId' => $personId, 
				'faceId' => $faceId, 
				'name' => $this->user,
				'email' => (($this->email === NULL) ? $email : $this->email)
			)
		);
		
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
		$row = $this->db->query(
			'UPDATE `user` SET `' . $attr . '` = :val WHERE `name` = :user', 
			array('val' => $value, 'user' => $this->user)
		);
		
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
		$row = $this->db->query(
			'SELECT `' . $data . '` FROM `user` WHERE `name` = :user', 
			array('user' => $this->user)
		);
		
		if(strlen($row) === 0)
			return $this->setResponseText('dbGetError');
		
		return $row[0][$data];
	}
	
	/**
	* Login procedure and set session
	* 
	* ______________________________________________________________
	* Function calls for a login:
	*   checkUser(); setBase64Img();
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
				'responseText' => 'Es wurden nicht alle Felder ausgefüllt.'
			);
		}

		$this->setBase64Img($img);

		$res = $this->checkUser();
		
		if($res != false){ 
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		$res = $this->uploadImage('LOGIN');
		
		if($res === false){ 
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		$res = $this->detectFace('LOGIN');
		
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
				$res2 = $this->deleteImage('ORIGINAL');
				
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
			$res2 = $this->deleteImage('LOGIN');
			
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
			$res2 = $this->deleteImage('LOGIN');
			
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

		$res = $this->deleteImage('LOGIN');
		
		if($res === false){ 
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}
		
		$_SESSION[self::LOGIN_SESSION_NAME] = md5($this->user) . ";" . $this->user;

		return array(
			'val' => true, 
			'responseText' => 'Übereinstimmung zu ' . floor($confidence * 100) . '%, Login gewährt.'
		);
	}
	
	/**
	* Function to destroy session
	* 
	* @access 	public
	* @return 	void
	* @see 		logout()
	*/
	public function logout(){
		$_SESSION[self::LOGIN_SESSION_NAME] = NULL;
		session_destroy();
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
				'responseText' => 'Es wurden nicht alle Felder ausgefüllt.'
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

		$res = $this->uploadImage('ORIGINAL');

		if($res === false){
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}

		$res = $this->detectFace('ORIGINAL');

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
				$res2 = $this->deleteImage('ORIGINAL');
				
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
			$res2 = $this->deleteImage('ORIGINAL');
			
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
			$res2 = $this->deleteImage('ORIGINAL');
			
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
			$res2 = $this->deleteImage('ORIGINAL');
			
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

		return array(
			'val' => true, 
			'responseText' => 'User konnte erfolgreich angelegt werden.'
		);
	}
	
	/**
	* Mail changement procedure
	* 
	* _______________________________________________________
	* Function calls for a mail changement:
	*   setMail(); databaseUpdateUser('email', $this->email);
	* _______________________________________________________
	*/
	public function changeMail($newEmail){
		if(strlen($newEmail) == 0){ 
			return array(
				'val' => false, 
				'responseText' => 'Es wurden nicht alle Felder ausgefüllt.'
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
			'responseText' => 'E-Mail Adresse wurde erfolgreich erneuert.'
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
	*/
	public function changeOrgImage($newImg){
		if(strlen($newImg) == 0){ 
			return array(
				'val' => false, 
				'responseText' => 'Es wurden nicht alle Felder ausgefüllt.'
			);
		}
		
		$this->setBase64Img($newImg);
		
		$res = $this->uploadImage('ORIGINAL');

		if($res === false){
			return array(
				'val' => $res, 
				'responseText' => $this->responseText
			);
		}

		$res = $this->detectFace('ORIGINAL');

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
				$res2 = $this->deleteImage('ORIGINAL');
				
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
			'responseText' => 'Identifikations-Bild wurde erfolgreich geupdatet.'
		);
	}
}
?>
