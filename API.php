<?php
	include 'pw.php';
    $DBusername = a_pw();
    $DBurl = b_pw();
    $DBpassword = c_pw();
    $DBname = d_pw();
	function ServerLog($Message,$Type){
		if(isset($Message)){
			if(isset($Type)){
				//right to file
				$fn = "";
				if($Type=="error"||$Type=="Error"){
					$fn = "error_log.txt";
				}else{
					$fn = "log.txt";
				}
				$file = fopen($fn, "a+"); 
				$size = filesize($fn); 
				$date = date('Y-m-d H:i:s');
				$Username = "Undefined User";
				$user = new CurrentUser();
				if(isset($_COOKIE['username'])){
					$Username = $_COOKIE['username'];
				}
				$Message1 = str_replace("%userid%",$Username,$Message);
				fwrite($file, ("[".$Type."]|".$Message1."|".$date."]\r\n"));
			}else{
				return "no type set";
			}
		}else{
			return "no message";
		}
	}
	class Book {
	    // define properties
	    public $ID;
	    public $Title;
	    public $Description;
	    public $Date;
	    public $Genre1;
	    public $Genre2;
	    public $Genre3;
	    public $MaxContributors;
	    public $Status;
	    public $BookCoverLocation;
	    public $TextToSpeech;
	    public $GeneraBasedThyme;
	    public $CreatorsUsername;
	    public $UserID;
	    public $WholeBook;
	    // constructor
	    public function __construct($ID) {
	    	$this->ID = $ID;
			global $DBusername,$DBurl,$DBpassword,$DBname;
	        $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
	        $result = mysqli_query($mysql_connection,"SELECT * FROM books WHERE ID = '".mysqli_real_escape_string($mysql_connection,$ID)."'");
	        while($row = mysqli_fetch_array($result)) {	  
	        	$this->WholeBook = $row;          
	        	$this->Title = $row['Title'];
	        	ServerLog("User '%userid%' is pulling information on the book \"".$row['Title']."\"","Book Request");
	            $this->Description = $row['Description'];
	            $this->Date = $row['CreationDate'];
	            $this->Genre1 = $row['Genre1'];
	            $this->Genre2 = $row['Genre2'];
	            $this->Genre3 = $row['Genre3'];
	            $this->MaxContributors = $row['MaxContributors'];
	            $this->Status = $row['Status'];
	            $this->BookCoverLocation = $row['BookCoverLocation'];
	            $this->TextToSpeech = $row['EnableText2Speech'];
	            $this->GeneraBasedThyme = $row['EnableGeneraBasedThyme'];
	            $this->CreatorsUsername = $row['CreatorsUsername'];
	            $this->UserID = $row['UserID'];
	        }
	    }
	    //Get
	    public function getTitle(){
	    	return $this->Title;
	    }
	    public function getDescription(){
	    	return $this->Description;
	    }
	    public function getDate(){
	    	return $this->Date;
	    }
	    public function getGenre1(){
	    	return $this->Genre1;
	    }
	    public function getGenre2(){
	    	return $this->Genre2;
	    }
	    public function getGenre3(){
	    	return $this->Genre3;
	    }
	    public function getMaxControbutors(){
	    	return $this->MaxContributors;
	    }
	    public function getStatus(){
	    	return $this->Status;
	    }
	    public function getBookCoverLocation(){
	    	return $this->BookCoverLocation;
	    }
	    public function getTextToSpeech(){
	    	return $this->TextToSpeech;
	    }
	    public function getGeneraBasedThyme(){
	    	return $this->GeneraBasedThyme;
	    }
	    public function getCreatorsUsername(){
	    	return $this->CreatorsUsername;
	    }
	    public function getUserID(){
	    	return $this->UserID;
	    }
	    public function getBook() {
            return json_encode($this->WholeBook);
	    }
	    //set
	    public function setTitle($value){
	    	$this->Title = $value;
	    	self::DBConnect("Title",$value,$this->ID);
	    }
	    public function setDescription($value){
	    	$this->Description = $value;
	    	self::DBConnect("Description",$value,$this->ID);
	    }
	    public function setDate($value){
	    	$this->Date = $value;
	    	self::DBConnect("CreationDate",$value,$this->ID);
	    }
	    public function setGenre1($value){
	    	$this->Genre1 = $value;
	    	self::DBConnect("Genre1",$value,$this->ID);
	    }
	    public function setGenre2($value){
	    	$this->Genre2 = $value;
	    	self::DBConnect("Genre2",$value,$this->ID);
	    }
	    public function setGenre3($value){
	    	$this->Genre3 = $value;
	    	self::DBConnect("Genre3",$value,$this->ID);
	    }
	    public function setMaxControbutors($value){
	    	$this->MaxContributors = $value;
	    	self::DBConnect("MaxContributors",$value,$this->ID);
	    }
	    public function setStatus($value){
	    	$this->Status = $value;
	    	self::DBConnect("Status",$value,$this->ID);
	    }
	    public function setBookCoverLocation($value){
	    	$this->BookCoverLocation = $value;
	    	self::DBConnect("BookCoverLocation",$value,$this->ID);
	    }
	    public function setTextToSpeech($value){
	    	$this->TextToSpeech = $value;
	    	self::DBConnect("EnableText2Speech",$value,$this->ID);
	    }
	    public function setGeneraBasedThyme($value){
	    	$this->GeneraBasedThyme = $value;
	    	self::DBConnect("EnableGeneraBasedThyme",$value,$this->ID);
	    }
	    public function setCreatorsUsername($value){
	    	$this->CreatorsUsername = $value;
	    	self::DBConnect("CreatorsUsername",$value,$this->ID);
	    }
	    public function setUserID($value){
	    	$this->UserID = $value;
	    	self::DBConnect("UserID",$value,$this->ID);
	    }
	    public function setBook($value) {
	    	$this->$WholeBook = $value;
	    }
	    private function DBConnect($coloum,$value,$ID){
	    	global $DBusername,$DBurl,$DBpassword,$DBname;
	        $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
	        $result = mysqli_query($mysql_connection,"UPDATE books SET ".$coloum."='".mysqli_real_escape_string($mysql_connection,$value)."' WHERE ID = '".mysqli_real_escape_string($mysql_connection,$ID)."'");
	    }
	    public static function doseBookExist($ID){
			global $DBusername,$DBurl,$DBpassword,$DBname;
	        $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
	        $result = mysqli_query($mysql_connection,"SELECT * FROM books WHERE ID = '".mysqli_real_escape_string($mysql_connection,$ID)."'");
	        while($row = mysqli_fetch_array($result)){
	        	return true;
	        }
	        return false;
	    }
	}
	class User {
	    // define properties
	    public $ID;
	    public $Username;
	    public $FirstName;
	    public $LastName;
	    public $Email;
	    public $DBO;
	    public $SessionID;
	    public $EmailMD5;
	    public $Verifide;
	    public $Points;
	    public $Banned;
	    public $GroupID;
	    // constructor
	    public function __construct($ID) {
	    	$this->ID = $ID;
			global $DBusername,$DBurl,$DBpassword,$DBname;
	        $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
	        $result = mysqli_query($mysql_connection,"SELECT * FROM users WHERE ID = '".mysqli_real_escape_string($mysql_connection,$ID)."'");
	        while($row = mysqli_fetch_array($result)) {
			    $this->Username = $row['Username'];
			    $this->FirstName = $row['FirstName'];
			    $this->LastName = $row['LastName'];
			    $this->Email = $row['Email'];
			    $this->DBO = $row['DOB'];
			    $this->SessionID = $row['SessionID'];
			    $this->EmailMD5 = $row['EmailMD5'];
			    $this->Verifide = $row['Verified'];
			    $this->Points = $row['Points'];
			    $this->Banned = $row['Banned'];
			    $this->GroupID = $row['GroupID'];
	        }
	    }
	    //Get
	    public function getUsername(){
	    	return $this->Username;
	    }
	    public function getFirstName(){
	    	return $this->FirstName;
	    }
	    public function getLastName(){
	    	return $this->LastName;
	    }
	    public function getEmail(){
	    	return $this->Email;
	    }
	    public function getDBO(){
	    	return $this->DBO;
	    }
	    public function getSessionID(){
	    	return $this->SessionID;
	    }
	    public function getEmailMD5(){
	    	return $this->EmailMD5;
	    }
	    public function getVerifide(){
	    	return $this->Verifide;
	    }
	    public function getPoints(){
	    	return $this->Points;
	    }
	    public function getBanned(){
	    	return $this->Banned;
	    }
	    public function getGroupID(){
	    	return $this->GroupID;
	    }
	    //set
	    public function setUsername($value){
	    	$this->Username = $value;
	    	self::DBConnect("Username",$value,$this->ID);
	    }
	    public function setFirstName($value){
	    	$this->FirstName = $value;
	    	self::DBConnect("FirstName",$value,$this->ID);
	    }
	    public function setLastName($value){
	    	$this->LastName = $value;
	    	self::DBConnect("LastName",$value,$this->ID);
	    }
	    public function setEmail($value){
	    	$this->Email = $value;
	    	self::DBConnect("Email",$value,$this->ID);
	    }
	    public function setDBO($value){
	    	$this->DBO = $value;
	    	self::DBConnect("DBO",$value,$this->ID);
	    }
	    public function setSessionID($value){
	    	$this->SessionID = $value;
	    	self::DBConnect("SessionID",$value,$this->ID);
	    }
	    public function setEmailMD5($value){
	    	$this->EmailMD5 = $value;
	    	self::DBConnect("EmailMD5",$value,$this->ID);
	    }
	    public function setVerifide($value){
	    	$this->Verifide = $value;
	    	self::DBConnect("Verifide",$value,$this->ID);
	    }
	    public function setPoints($value){
	    	$this->Points = $value;
	    	self::DBConnect("Points",$value,$this->ID);
	    }
	    public function setBanned($value){
	    	$this->Banned = $value;
	    	self::DBConnect("Banned",$value,$this->ID);
	    }
	    public function setGroupID($value){
	    	$this->GroupID = $value;
	    	self::DBConnect("GroupID",$value,$this->ID);
	    }
	    private function DBConnect($coloum,$value,$ID){
	    	global $DBusername,$DBurl,$DBpassword,$DBname;
	        $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
	        $result = mysqli_query($mysql_connection,"UPDATE users SET ".$coloum."='".mysqli_real_escape_string($mysql_connection,$value)."' WHERE ID = '".mysqli_real_escape_string($mysql_connection,$ID)."'");
	    }
	    public static function doseUserExist($ID){
			global $DBusername,$DBurl,$DBpassword,$DBname;
	        $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
	        $result = mysqli_query($mysql_connection,"SELECT * FROM users WHERE ID = '".mysqli_real_escape_string($mysql_connection,$ID)."'");
	        while($row = mysqli_fetch_array($result)){
	        	return true;
	        }
	        return false;
	    }
	}





	class CurrentUser {
	    // define properties
	    public $CookieID;
	    public $CookieSSID;
	    public $CookieUsername;
	    public $DB_SSID;
	    // constructor
	    public function __construct() {
	    	if(isset($_COOKIE['username'])){
	    		$this->CookieUsername = $_COOKIE['username'];
	    	}
	    	if(isset($_COOKIE['MD5'])){
	    		$this->CokkieSSID = $_COOKIE['MD5'];
	    	}
	    	if(isset($_COOKIE['ID'])){
	    		$this->CookieID = $_COOKIE['ID'];
				global $DBusername,$DBurl,$DBpassword,$DBname;
		        $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
		        $result = mysqli_query($mysql_connection,"SELECT * FROM users WHERE ID = '".mysqli_real_escape_string($mysql_connection,$this->CookieID)."'");
		        while($row = mysqli_fetch_array($result)) {
		        	$this->DB_SSID = $row['SessionID'];
		        }
	    	}
	    }
	    //Get
	    public function getCookieUsername(){
	    	return $this->CookieUsername;
	    }
	    public function getCookieSSID(){
	    	return $this->CookieSSID;
	    }
	    public function getDB_SSID(){
	    	return $this->DB_SSID;
	    }
	    public function getCookieID(){
	    	return $this->CookieID;
	    }
	    //
	    public function isSSIDCorrect(){
	    	if(self::getDB_SSID()==self::getCookieSSID()){
	    		return true;
	    	}else{
	    		return false;
	    	}
	    }
	    private function DBConnect($coloum,$value,$ID){
	    	global $DBusername,$DBurl,$DBpassword,$DBname;
	        $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
	        $result = mysqli_query($mysql_connection,"UPDATE users SET ".$coloum."='".mysqli_real_escape_string($mysql_connection,$value)."' WHERE ID = '".mysqli_real_escape_string($mysql_connection,$ID)."'");
	    }
	}
	//$a = new User(1);
	//$a->setUsername("JohnGreen");
	//echo $a->getUsername();
	$path = ltrim($_SERVER['REQUEST_URI'], '/');

	$pos=strpos($path,".php")+strlen(".php");
	$newstring=substr($path,$pos);
	$elements = explode('/', $newstring);
	array_shift($elements);
	$mode = array_shift($elements);
	$data = array_shift($elements);
	if($mode==""){
		echo "no parts";
	}else if($data==""){
		echo "no data"; 
	}else{
		switch($mode){
			case 'core':
				switch ($data) {
					case 'valid_user':
						if(isset($_COOKIE['username'])&&isset($_COOKIE['ID'])&&isset($_COOKIE['MD5'])){
				            $md5 = $_COOKIE['MD5'];
				            global $DBusername,$DBurl,$DBpassword,$DBname;
				            $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
				            $result = mysqli_query($mysql_connection,"SELECT * FROM users WHERE username = '".mysqli_real_escape_string($mysql_connection,$_COOKIE['username'])."' AND ID = '".mysqli_real_escape_string($mysql_connection,$_COOKIE['ID'])."'");
				            while($row = mysqli_fetch_array($result)) {
				                if($row['SessionID']==$md5){
				                    return true;
				                }
				            }
				            logout();
				            return false;
				        }else{
				            logout();
				            return false;
				        }
					break;
					case 'logout':
						logout();
						break;
					case 'login':
						if(isset($_POST['username'])||isset($_GET['username'])){
		                    if(isset($_POST['password'])||isset($_GET['password'])){
		                        $username = "";
		                        $password = "";
		                        if(isset($_POST['username'])){
		                            $username = $_POST['username'];
		                        }else if(isset($_GET['username'])){
		                            $username = $_GET['username'];
		                        }
		                        if(isset($_POST['password'])){
		                            $password = $_POST['password'];
		                        }else if(isset($_GET['password'])){
		                            $password = $_GET['password'];
		                        }
		                        $mysql_connection2 = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
		                        $result2 = mysqli_query($mysql_connection2,"SELECT * FROM users WHERE username = '".mysqli_real_escape_string($mysql_connection2,$username)."' AND password = '".mysqli_real_escape_string($mysql_connection2,$password)."'");
		                        if(mysqli_num_rows($result2)>0){
		                            $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
		                            $result = mysqli_query($mysql_connection,"SELECT * FROM users WHERE username = '".mysqli_real_escape_string($mysql_connection,$username)."'");
		                            while($row = mysqli_fetch_array($result)) {
		                                if($row['Verified']!=1){
		                                    echo 3;
		                                    return 3;
		                                }else{
		                                    setcookie('username',$username,time()+(60*60*24*2));
		                                    setcookie('ID',$row['ID'],time()+(60*60*24*2));
		                                    $md5 = unique_md5();
		                                    setcookie('MD5',$md5,time()+(60*60*24*2));
		                                    mysqli_query($mysql_connection,"UPDATE users SET SessionID='".$md5."' WHERE ID='".$row['ID']."';");
		                                    echo 1;
		                                    return 1;
		                                }
		                            }
		                        }else{
		                            echo 2;
		                            return 2;
		                        }
		                    }
		                }
		                break;
		            case 'debug':
		            	echo "o0=====Debug=====0o<br>";
		            	echo "";
		            break;
		            default:
		            echo "No settings defined";
		            break;
			}
			break;
		    case 'books':
				////////////////////Get Request Type/////////////////////////////
				$method = $_SERVER['REQUEST_METHOD'];
				$request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));
				switch ($method) {
				  case 'PUT':
				  	//PUT- Used to modify an existing object on the server
				    echo "1";  
				    break;
				  case 'POST':
				  	//POST- Used to create a new object on the server
				    echo "2";  
				    break;
				  case 'GET':
				  	//GET - Used for basic read requests to the server
					$book = new Book($data);
					print $book->getBook();
				    break;
				  case 'DELETE':
				  	//DELETE - Used to remove an object on the server
				  	$book = new Book($data);
					$UserID = $book->getUserID();
					$user = new CurrentUser();
					if($user->isSSIDCorrect()){

					}
				    echo "5";   
				    break;
				  default:
				    echo "dunno....";  
				    break;
				}
		        break;
		    case 'user':

		    	break;
		    default:
		    echo "default";
		    break;
		}
	}
		


	function logout(){
       if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }
        }
    }
?>