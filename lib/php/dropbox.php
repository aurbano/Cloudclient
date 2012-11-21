<?php
/**
	CUSTOM Dropbox Wrapper class
		It will help talking with dropbox
		also making sure that api calls work the same way
		as other services, making it easier to work with
		multiple services
	
	It uses the Dropbox-PHP API by Evert Pot (http://www.rooftopsolutions.nl/)
	with some modifications done by me. 
	
	No Server cache of Dropbox files, cache will be handled by JavaScript only.
	PHP only acts as proxy with Dropbox for easier OAuth calls.
	
	Built by Alejandro U. Alvarez <alejandro@urbanoalvarez.es>
	http://urbanoalvarez.es
*/
include('dropbox/autoload.php');

class Dropbox{
	// ---------  Setup variables --------- //
	private $key;							// App Key
	private $secret;						// App Secret
	// --------- Program variables -------- //
	private $oauth;							// oauth container
	private $uid;							// Dropbox User ID
	private $root;							// Dropbox root folder ID in database
	public	$dp;							// Dropbox API handle
	private $state = false;					// Logged in or not
	private $cursor = '';					// Delta cursor, for updates
	// ------------------------------------ //
	
	public function Dropbox(){
		// Load keys from private file
		include('keys.ignore.php');
		if(!$key || !$secret) throw new Exception('App keys not found');
		$this->key = $key;
		$this->secret = $secret;
		try{
			$this->oauth = new Dropbox_OAuth_PEAR($this->key,$this->secret);
			$this->dp = new Dropbox_API($this->oauth);
			if($_SESSION['dropbox']['state']){
				$this->state = $_SESSION['dropbox']['state'];
				$this->oauth->setToken($_SESSION['dropbox']['oauth_tokens']);
				$this->uid = $_SESSION['dropbox']['id'];
			}
		}catch(Exception $e){
			throw new Exception($e);	
		}
	}
	
	private function requestToken(){
		try{
			$tokens = $this->oauth->getRequestToken();
			$_SESSION['dropbox']['oauth_tokens'] = $tokens;	
		}catch(Exception $e){
			die($e);
		}
	}
	
	public function login(){
		global $sess;
		if(!($sess instanceof Session)) throw Exception('Session not initialised');
		if(!$_SESSION['dropbox']['oauth_tokens']) return false;
		try{
			$this->oauth->setToken($_SESSION['dropbox']['oauth_tokens']);
			$tokens = $this->oauth->getAccessToken();
			if($tokens){
				$this->setState(true);
				$_SESSION['dropbox']['oauth_tokens'] = $tokens;
				$this->oauth->setToken($_SESSION['dropbox']['oauth_tokens']);
				// Get info and store
				$info = $this->info();
				$this->uid = $_SESSION['dropbox']['id'] = $info['uid'];
				$_SESSION['dropbox']['email'] = $info['email'];
				// Log in via session
				$sess->login('dropbox',$_SESSION['dropbox']['id']);
				return true;
			}
		}catch(Exception $e){
			die($e);	
		}
	}
	
	public function loginLink(){
		if($this->state) return '#logged';
		try{
			$this->requestToken();
			return $this->oauth->getAuthorizeUrl('http://cloudclient.es/dpAuth.php');
		}catch(Exception $e){
			die($e);	
		}
		
	}
	
	public function info(){
		try{
			return $this->dp->getAccountInfo();	
		}catch(Exception $e){
			die($e);	
		}
	}
	
	public function setState($state){
		$this->state = $_SESSION['dropbox']['state'] = $state;	
	}
	
	public function logged(){
		return $this->state;	
	}
	
	// Returns dropbox id
	public function uid(){
		if(!$this->logged()) return false;
		if(!$this->uid || $this->uid <1) $this->uid = $_SESSION['dropbox']['id'];
		return $this->uid;
	}
	
	/** Returns numerical array for current directory
	  * Contains all possible info about each item
	  * It will poll Dropbox
	  * It will also check for updates and tell the client that there are new things
	  * so that the client can call the update system
	  */
	
	public function dir($dir=false){
		if(!$dir) $dir = '/';
		// OK, fetch dir info (from DB)
		return $this->fetchDirDropbox($dir); 
	}
	
	// Create/Update local cache for a given directory
	// The directory must be given as an ID, unless it's the root folder
	private function fetchDirDropbox($dir){
		try{
			$content = $this->dp->getMetaData($dir, true);
		}catch(Exception $e){
			throw new Exception($e);
		}
		$total = sizeof($content['contents']);
		// $content 0 will be used for root if root was requested
		$start = 0;
		if($dir === '/'){
			$start = 1;
			$return[0]['uid'] = $this->uid();
			$return[0]['parent'] = 'root';
			$return[0]['name'] = '/';
			$return[0]['icon'] = 'folder';
			$return[0]['type'] = 0;
			$return[0]['revision'] = 0;
			$return[0]['rev'] = 0;
			$return[0]['modified'] = 0;
			$return[0]['size'] = 0;
			$return[0]['mime'] = '';
			$return[0]['hash'] = $content['hash'];	
		}
		// By now I will just iterate and store
		for($i=$start;$i<$total+$start;$i++){
			// I have all info for each file. Prettify for javascript
			$cur = $content['contents'][$i-$start];
			$type = 1;
			if($cur['is_dir']==1) $type = 0;
			$name = substr($cur['path'],strrpos($cur['path'],'/')+1);
			
			$mime = '';
			if(isset($cur['mime_type'])) $mime = $cur['mime_type'];
			
			$return[$i]['uid'] = $this->uid();
			$return[$i]['parent'] = $dir;
			$return[$i]['name'] = $name;
			$return[$i]['icon'] = $cur['icon'];
			$return[$i]['type'] = $type;
			$return[$i]['revision'] = $cur['revision'];
			$return[$i]['rev'] = $cur['rev'];
			$return[$i]['modified'] = $this->date($cur['modified']);
			$return[$i]['size'] = $this->toBytes($cur['size']);
			$return[$i]['mime'] = $mime;
			$return[$i]['hash'] = NULL;
		}
		return $return;
	}
	
	// Convert a given file size to bytes
	private function toBytes($size){
		//	Size in {A B} where A is size and B is unit
		$s = explode(' ',$size);
		if(sizeof($s)==1) return $s[0];
		switch($s[1]){
			case 'bytes':
				return $s[0];
				break;
			case 'KB':
				return $s[0]*1024;
				break;
			case 'MB':
				return $s[0]*1024*1024;
				break;
			case 'GB':
				return $s[0]*1024*1024*1024;
				break;
			default:
				return $s[0];
		}
	}
	
	public function delete($id){
		global $sess;
		
	}
	
	public function move($elemName,$destName,$path=false){
		if(strlen($elemName)<1 || strlen(destName)<1){
			throw new Exception('Wrong parameters');
			return false;
		}
		// Fix path and add to file names
		$path = rtrim($path, '/');
		if($path){
			$elemName = $path.'/'.$elemName;
			$destName = $path.'/'.$destName;
		}
		// We need to move in dropbox, and if it works, change the parent ID in our local db
		try{
			$this->dp->move($elemName,$destName.'/'.$elemName);
			return true;
		}catch(Exception $e){
			throw $e;
			return false;	
		}
	}
	
	private function date($date,$timestamp=false){
		// Convert a date to mysql format
		if(!$timestamp) $time = strtotime($date);
		else $time = $date;
		return date("Y-m-d H:i:s", $time);	
	}
};