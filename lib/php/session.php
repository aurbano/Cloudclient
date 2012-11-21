<?php
session_start();
error_reporting(E_ERROR | E_WARNING);
//set_include_path('/home8/djsmusic/php');
/*	Session handler, must be included anywhere you want to track users
 *  it also handles all includes and such. So it's the only file that
 *  is really needed.
 *
 *	By Alejandro U. Alvarez (http://urbanoalvarez.es)
*/
include('locale.php');		// Local settings
include('db.class.php');	// MySQL abstraction layer
include('dropbox.php');		// Dropbox API layer

class Session{
	private $logged = false;
	private $usid;
	private $db = false;	// Database connection holder
	
	public function Session(){
		// Constructor, no params
		if($_SESSION['user']['logged']){
			$this->usid = $_SESSION['user']['id'];
			$this->logged = true;
		}
	}
	
	// Database connection
	public function db(){
		if($this->db) return $this->db;
		// Load private data file
		include('db.ignore.php');
		return $this->db = new DB($database,$host,$user,$pass);	
	}
	
	// Returns true if user logged in
	public function logged(){
		return $this->logged;
	}
	
	public function login($service,$id){
		// Setup login for the given service and id
		// Also pull the necessary variables from DB
		// like Cloudclient ID
		switch($service){
			case 'cloudclient':
				// Our standard login system
				$usid = $id;
				break;
			case 'dropbox':
				// Dropbox login
				// Fetch user id, if there is any. If not, create a new one.
				$db = $this->db();
				$usid = $db->preparedQuery('SELECT id FROM users WHERE dropbox = ? LIMIT 1',array($id));
				$usid = $db->fetchNext($usid);
				if($usid && $usid>0){
					// User already existed
				}else{
					// Users doesn't exist, insert new
					$usid = $db->preparedInsert('users',array('dropbox'=>$id,'jtime'=>time()));	
				}
				// We now have the usid, create session vars
				$this->logged = $_SESSION['user']['logged'] = true;
				$_SESSION['user']['id'] = $usid;
				break;
		}
	}
	
	public function usid(){
		return $this->usid;	
	}
	
	public function loginLink(){
		$loginURI = 'https://www.dropbox.com/1/oauth/authorize';
		$tokenURI = 'https://api.dropbox.com/1/oauth/access_token';
		$token = $this->getToken();
        return $loginURI . '?oauth_token=' . $token['token'] . '&oauth_callback=http://cloudclient.es/dpAuth.php';
	}
};
$sess = new Session();
$dp = new Dropbox();