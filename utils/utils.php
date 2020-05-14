<?php

//cookie settings definations
//adjust your session cookie name
define("MY_COOKIE_NAME" ,"my-session-cookie" );
//adjust your temporary cookie name
define("MY_TEMP_COOKIE_NAME" ,"temporary-cookie");
// adjust the cookie domain to your domain
define("MY_COOKIE_DOMAIN" , "MY_COOKIE_DOMAIN");
// adjust the secure flag, incase your page is https
define("MY_SECURE_COOKIE" , false) ;
define("MY_HTTP_ONLY_COOKIE" , true);
//adjust the path
define("MY_COOKIE_PATH" , "/");

class MyEncryption {	
	//  This is a dummy class which should be considered as a place-holder for an encryption, but the class does not implement any real encryption logic
	//	The encrypt and decrypt methods only return the reverse of a given text.
	//	Please use your own encryption and decryption implementation to protect the session cookie and its value
		
	public function MyEncryption() {		
		
	}	
	
	//	The method only returns the reverse of a given text, but does not implement a real encryption/decryption logic.
	public function encrypt(/*String*/ $string2BeEncrypted) {
		return strrev( $string2BeEncrypted );
	}	
	
	//	The method only returns the reverse of a given text, but does not implement a real encryption/decryption logic.
	public function decrypt(/*String*/ $encryptedString ) {
		return strrev( $encryptedString  );
	}
}

class CartHandler {
	// this is a dummy class to represent the cart
	// this class should be consider only as a place-holder for a real cart (data model and controller)
	static private $instance = false;
	
	static public function getInstance() {
		if (!self::$instance) {
			self::$instance = new self;
		}		
		return self::$instance;
	}	
	
	private function __CartHanlder() { }	
	
	public function addItem() {
		//just simple dummy code set a cookie to indicate that the cart is not empty		
		setcookie(MY_COOKIE_NAME,"somevalue",time() + 3600,MY_COOKIE_PATH,MY_COOKIE_DOMAIN,MY_SECURE_COOKIE,MY_HTTP_ONLY_COOKIE);	
		//return a boolean on whether cart is empty
		return false;
	}	
	
	public function removeItem() {
		//delete the cookie
		setcookie(MY_COOKIE_NAME,"zzzzz",time() - 1200 * 3600,MY_COOKIE_PATH,MY_COOKIE_DOMAIN,MY_SECURE_COOKIE,MY_HTTP_ONLY_COOKIE);	
		//return a boolean on whether cart is empty
		return true;
	}	
	
	public function isEmpty() {
		//a dummy check whether shopping cart empty is
		return !(isset($_COOKIE[MY_COOKIE_NAME]) && $_COOKIE[MY_COOKIE_NAME] == "somevalue");
	}

};

class SessionMigrationHanlder {
	// this class is a guide to illustrate a simple implementation of the logic required to migrate a session cookie into unblu universal session
	// the class is not considered to be deployed on any productive environment, the class and code is only and only a simple example to assist you understand the logic and steps to implement your own code
	
	static private $instance = false;
	
	const MY_ACCOUNT_SECRET_KEY = "MY_ACCOUNT_SECRET_KEY";
	
	static public function getInstance() {
		if ( !self::$instance ) {
			self::$instance = new self;
		}		
		return self::$instance;
	}	

	private function __SessionMigrartion() { }	
	
	//checks whether 
	// the temporary cookie exist and whether the cookie represents a real exisiting session.
	//account secret cookie matches
	public function isTrustable() {
		$cookieValue = isset($_COOKIE[MY_TEMP_COOKIE_NAME]) ? $_COOKIE[MY_TEMP_COOKIE_NAME] : null;
		if (self::$instance->isValidSessionCookie($cookieValue)) {
			//get acoount secret key from cookies
			$secretKeyCookieValue = isset($_COOKIE["x-unblu-account-secret"]) ? $_COOKIE["x-unblu-account-secret"] : null;
			//compare the value coming from cookie with your account secret string
			if ($secretKeyCookieValue == self::MY_ACCOUNT_SECRET_KEY) {
				return true;				
			}	
		}
		return false;
	}
	
	public function isValidSessionCookie($cookieValue) {
		//do a check against DB/Session-store to make sure that the session cookie/data are valid
		//the code  below are some dummy checks
		if ($cookieValue != null) {
			$cryp = new MyEncryption();
			$decryptCookieValue = $cryp->decrypt($cookieValue);
			$decryptCookieValue = rtrim($decryptCookieValue, "\0");			
			return  $decryptCookieValue != "xxx";
		}
		return false;
	}
	
	//sets the temporary cookie for migration
	public function prepare() {
		$crp = new MyEncryption();
		//read the orig session cookie
		$cookieValue = isset($_COOKIE[MY_COOKIE_NAME]) ? $_COOKIE[MY_COOKIE_NAME] : null;
		//set a temporary cookie with encrypted value of orig cookie, no httponly flag, cookie expires in 2 min
		setcookie(MY_TEMP_COOKIE_NAME,$crp->encrypt($cookieValue),time() + 120,MY_COOKIE_PATH,MY_COOKIE_DOMAIN,MY_SECURE_COOKIE,false);
		return "yes";			
	}
	
	//does the migration of the cookie into universal cobrowsing
	public function migrate() {	
		if (!self::$instance->isTrustable()) {			
			return false;
		}
		$cryp = new MyEncryption();
		$cookieValue = isset($_COOKIE[MY_TEMP_COOKIE_NAME]) ? $_COOKIE[MY_TEMP_COOKIE_NAME] : null;
		$cookieValue = $cryp->decrypt($cookieValue);
		$cookieValue = rtrim($cookieValue, "\0");
		//remove the temporary cookie so that there is no a redirect loop
		setcookie(MY_TEMP_COOKIE_NAME,$cryp->encrypt("xxx"),time() - 1200 * 3600,MY_COOKIE_PATH,MY_COOKIE_DOMAIN,MY_SECURE_COOKIE,MY_HTTP_ONLY_COOKIE);
		//set the orig session cookie into co-browsing session
		setcookie(MY_COOKIE_NAME,$cookieValue,time()+ 3600,MY_COOKIE_PATH,MY_COOKIE_DOMAIN,MY_SECURE_COOKIE,MY_HTTP_ONLY_COOKIE);
		return true;				
	}	
};
?>