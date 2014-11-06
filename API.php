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
	class Likes{
		public $result;
		public function __construct($ID) {	
	    	$this->ID = $ID;
			global $DBusername,$DBurl,$DBpassword,$DBname;
	        $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
	        $result = mysqli_query($mysql_connection,"SELECT * FROM likes WHERE BookID = '".mysqli_real_escape_string($mysql_connection,$ID)."'");
	        $this->result = $result;
	    }
	    public function getLikes(){
	    	return mysqli_num_rows($this->result);
	    }
	    public function setLike(){
	    	$user = new CurrentUser();
	    	if($user->isLoggedIn()){
	    		$alreadyLiked = false;
	    		while($row = mysqli_fetch_array($this->result)){
	    			if($row['UserID']==$user->getCookieID()){
	    				$alreadyLiked = true;
	    			}
	    		}
	    		if($alreadyLiked){
	    			return;
	    		}else{
	    			self::DBConnect("likes",$this->ID,$user->getCookieID());
	    		}
	    	}else{
	    		echo "not logged in";
	    	}
	    }
	    private function DBConnect($coloum,$BookID,$UserID){
	    	global $DBusername,$DBurl,$DBpassword,$DBname;
	        $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
	        $result = mysqli_query($mysql_connection,"INSERT INTO ".$coloum." (BookID,UserID) VALUES (".$BookID.",".$UserID.")");
	    }
	}

	class Notifications{
		public $result;
		public $ID;

		public function __construct($Limit) {	
			if(isset($_COOKIE['ID'])){
				$this->ID = $_COOKIE['ID'];
			}else{
				return;
			}
			global $DBusername,$DBurl,$DBpassword,$DBname;
	        $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
	        $result = mysqli_query($mysql_connection,"SELECT * FROM notifications WHERE PrimaryUser = '".mysqli_real_escape_string($mysql_connection,$_COOKIE['ID'])."' LIMIT ".$Limit);
	        $this->result = $result;
	    }
	    public function constructNotifications(){
	    	$notifications = array();
	    	while($row = mysqli_fetch_array($this->result)){
	    		if($row['PrimaryUser']==$this->ID){
	    			$notificationArray = array();
	    			$user = new User($row['SecondaryUser']);
	    			$username = $user->getUsername();
	    			$bookname = "";
	    			if($row['BookID']!=0){
	    				$book = new Book($row['BookID']);
	    				$bookname = $book->getTitle();
	    			}
	    			$notificationArray["Message"] = self::stringCrafter($row['NotificationType'],$username,$bookname);
	    			$notificationArray["BookID"] = $row['BookID'];
	    			$notificationArray["Mode"] = $row['NotificationType'];
	    			$notificationArray["UserID"] = $row['SecondaryUser'];
	    			$notifications[] = $notificationArray;
	    		}
	    	}
	        print json_encode($notifications);
	    }
	    public function stringCrafter($ID, $username, $book){
	    	switch($ID){
	    		case 1:
	    			return $username." Liked your book ".$book;
	    			break;
	    		case 2:
	    			return $username." Followed you";
	    			break;
	    		case 3:
	    			return $username." Liked your book ".$book;
	    			break;
	    		case 4:
	    			return $username." Has contributed to your book ".$book;
	    			break;
	    	}
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
	    		$this->CookieSSID = $_COOKIE['MD5'];
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
	    public function isLoggedIn(){
	    	if(isset($_COOKIE['username'])&&isset($_COOKIE['ID'])&&self::isSSIDCorrect()){
	    		return true;
	    	}else{
	    		return false;
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
	function unique_md5() {
         mt_srand(microtime(true)*100000 + memory_get_usage(true));
         return md5(uniqid(mt_rand(), true));
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
					case 'username':
						if(isset($_COOKIE['username'])){
							echo $_COOKIE['username'];
						}
					break;
					case 'notifications':
						$data2 = array_shift($elements);
						if($data2!=""){
							$notifications = new Notifications($data2);
							$notifications->constructNotifications();
						}
						break;
					case 'register':
						//register
		                $Username = "";
		                $Email = "";
		                $DOB;
		                $Password1 = "";
		                $Password2 = "";
		                $FirstName = "";
		                $LastName = "";
		                if(isset($_POST['Username'])&&$_POST['Username']!=""){
		                    $Username = $_POST['Username'];
		                }else if(isset($_GET['Username'])){
		                    $Username = $_GET['Username'];
		                }else{
		                    echo 0;
		                    return;
		                }
		                if(isset($_POST['Email'])&&$_POST['Email']!=""){
		                    $Email = $_POST['Email'];
		                }else if(isset($_GET['Email'])){
		                    $Email = $_GET['Email'];
		                }else{
		                    echo 1;
		                    return;
		                }
		                if(isset($_POST['DOB'])&&$_POST['DOB']!=""){
		                    $DOB = $_POST['DOB'];
		                }else if(isset($_GET['DOB'])){
		                    $DOB = $_GET['DOB'];
		                }else{
		                    echo 2;
		                    return;
		                }
		                if(isset($_POST['Password1'])&&$_POST['Password1']!=""){
		                    $Password1 = $_POST['Password1'];
		                }else if(isset($_GET['Password1'])){
		                    $Password1 = $_GET['Password1'];
		                }else{
		                    echo 3;
		                    return;
		                }
		                if(isset($_POST['Password2'])&&$_POST['Password2']!=""){
		                    $Password2 = $_POST['Password2'];
		                }else if(isset($_GET['Password2'])){
		                    $Password2 = $_GET['Password2'];
		                }else{
		                    echo 4;
		                    return;
		                }
		                if(isset($_POST['FirstName'])&&$_POST['FirstName']!=""){
		                    $FirstName = $_POST['FirstName'];
		                }else if(isset($_GET['FirstName'])){
		                    $FirstName = $_GET['FirstName'];
		                }else{
		                    echo 5;
		                    return;
		                }
		                if(isset($_POST['LastName'])&&$_POST['LastName']!=""){
		                    $LastName = $_POST['LastName'];
		                }else if(isset($_GET['LastName'])){
		                    $LastName = $_GET['LastName'];
		                }else{
		                    echo 6;
		                    return;
		                }
		                if($Password1 != $Password2){
		                    echo 7;
		                    return;
		                }
		                if(usernameEgsists($Username)){
		                    echo 8;
		                    return;
		                }
		                if(emailEgsists($Email)){
		                    echo 9;
		                    return;
		                }
		                global $DBusername,$DBurl,$DBpassword,$DBname;
		                $md5 = unique_md5();
		                sendEmail("Welcome to Writyfy",$Email,"From: No-Reply@Writyfy.com","Welcome to Writyfy, please use the following link to verify your account!<br>".$md5);
		                $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
		                $result = mysqli_query($mysql_connection,"INSERT INTO users (Username,Email,DOB,Password,FirstName,LastName,EmailMD5) VALUES ('".mysqli_real_escape_string($mysql_connection,$Username)."','".mysqli_real_escape_string($mysql_connection,$Email)."','".mysqli_real_escape_string($mysql_connection,$DOB)."','".mysqli_real_escape_string($mysql_connection,$Password1)."','".mysqli_real_escape_string($mysql_connection,$FirstName)."','".mysqli_real_escape_string($mysql_connection,$LastName)."','".mysqli_real_escape_string($mysql_connection,$md5)."');");
		                echo 10; 
		                return;
					case 'valid_user':
						if(isset($_COOKIE['username'])&&isset($_COOKIE['ID'])&&isset($_COOKIE['MD5'])){
				            $md5 = $_COOKIE['MD5'];
				            global $DBusername,$DBurl,$DBpassword,$DBname;
				            $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
				            $result = mysqli_query($mysql_connection,"SELECT * FROM users WHERE username = '".mysqli_real_escape_string($mysql_connection,$_COOKIE['username'])."' AND ID = '".mysqli_real_escape_string($mysql_connection,$_COOKIE['ID'])."'");
				            $valid = false;
				            while($row = mysqli_fetch_array($result)) {
				                if($row['SessionID']==$md5){
				                	echo "1";
				                	$valid = true;
				                }
				            }
				            if(!$valid){
				            	logout();
				            	echo "0";
				            }
				            
				        }else{
				            logout();
				            echo "0";
				        }
					break;
					case 'logout':
						logout();
						echo 1;
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
		                                }else{
		                                    setcookie('username',$username,time()+(60*60*24*2), '/');
		                                    setcookie('ID',$row['ID'],time()+(60*60*24*2), '/');
		                                    $md5 = unique_md5();
		                                    setcookie('MD5',$md5,time()+(60*60*24*2), '/');
		                                    mysqli_query($mysql_connection,"UPDATE users SET SessionID='".$md5."' WHERE ID='".$row['ID']."';");
		                                    echo 1;
		                                }
		                            }
		                        }else{
		                            echo 2;
		                        }
		                    }else{
		                    	echo 2;
		                    }
		                }else{
		                	echo 2;
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
		    case 'search':
		    	switch($data){
		    		case 'r':
		    		//random
		    			$data2 = array_shift($elements);
		    			switch($data2){
		    				case 'c':
		    					//compleate
		    					$data2 = array_shift($elements);
				                $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
				                $result = mysqli_query($mysql_connection,"SELECT * FROM books WHERE Status = '2' ORDER BY RAND()");
				                $rows = array();
				                while($r = mysqli_fetch_array($result)) {
				                	$array1 = $r;
				                	$likes = new Likes($r['ID']);
				                	$array1['Likes'] =  $likes->getLikes();
				                	$rows[] = $array1;
				                }
				                print json_encode($rows);
				                break;
		    				case 'n':
		    					//not started
		    					$data2 = array_shift($elements);
				                $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
				                $result = mysqli_query($mysql_connection,"SELECT * FROM books WHERE Status ='0' ORDER BY RAND()");
				                $rows = array();
				                while($r = mysqli_fetch_array($result)) {
				                	$array1 = $r;
				                	$likes = new Likes($r['ID']);
				                	$array1['Likes'] =  $likes->getLikes();
				                	$rows[] = $array1;
				                }
				                print json_encode($rows);
				                break;
		    				case 'w':
		    					//in progress
			    				$data2 = array_shift($elements);
				                $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
				                $result = mysqli_query($mysql_connection,"SELECT * FROM books WHERE Status = '1' ORDER BY RAND()");
				                $rows = array();
				                while($r = mysqli_fetch_array($result)) {
				                	$array1 = $r;
				                	$likes = new Likes($r['ID']);
				                	$array1['Likes'] =  $likes->getLikes();
				                	$rows[] = $array1;
				                }
				                print json_encode($rows);
				                break;
		    			}
		    		break;
		    		case 'user':
		    		//gets users books, example /user/1/r/c
		    			$data2 = array_shift($elements);
		    			if($data2==-1){
							$data2 = array_shift($elements);
		    				switch($data2){
		    					case 'c':
			    					$user = new CurrentUser();
				    				if(isset($_COOKIE['ID'])&&$user->isLoggedIn()){
				    					$mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
						                $result = mysqli_query($mysql_connection,"SELECT * FROM books WHERE UserID = '".mysqli_real_escape_string($mysql_connection,$_COOKIE['ID'])."' AND Status = '1' ORDER BY RAND()");
						                $rows = array();
						                while($r = mysqli_fetch_array($result)) {
						                    $array1 = $r;
						                	$likes = new Likes($r['ID']);
						                	$array1['Likes'] =  $likes->getLikes();
						                	$rows[] = $array1;
						                }
						                print json_encode($rows);
				    				}
		    						break;
		    					case 's':
		    						$user = new CurrentUser();
				    				if(isset($_COOKIE['ID'])&&$user->isLoggedIn()){
				    					$mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
						                $result = mysqli_query($mysql_connection,"SELECT * FROM books WHERE UserID = '".mysqli_real_escape_string($mysql_connection,$_COOKIE['ID'])."' ORDER BY RAND()");
						                $rows = array();
						                while($r = mysqli_fetch_array($result)) {
						                    $array1 = $r;
						                	$likes = new Likes($r['ID']);
						                	$array1['Likes'] =  $likes->getLikes();
						                	$rows[] = $array1;
						                }
						                print json_encode($rows);
				    				}
		    						break;
		    					default:
									$user = new CurrentUser();
				    				if(isset($_COOKIE['ID'])&&$user->isLoggedIn()){
				    					$mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
						                $result = mysqli_query($mysql_connection,"SELECT * FROM books WHERE UserID = '".mysqli_real_escape_string($mysql_connection,$_COOKIE['ID'])."' ORDER BY RAND()");
						                $rows = array();
						                while($r = mysqli_fetch_array($result)) {
						                    $array1 = $r;
						                	$likes = new Likes($r['ID']);
						                	$array1['Likes'] =  $likes->getLikes();
						                	$rows[] = $array1;
						                }
						                print json_encode($rows);
				    				}
		    						break;
		    				}


		    				
		    			}else{
		    				if(is_numeric($data2)){
								$mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
				                $result = mysqli_query($mysql_connection,"SELECT * FROM books WHERE UserID = '".mysqli_real_escape_string($mysql_connection,$data2)."' ORDER BY RAND()");
				                $rows = array();
				                while($r = mysqli_fetch_array($result)) {
				                    $rows[] = $r;
				                }
				                print json_encode($rows);
		    				}else{
								$mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
				                $result = mysqli_query($mysql_connection,"SELECT * FROM books WHERE CreatorsUsername = '".mysqli_real_escape_string($mysql_connection,$data2)."' ORDER BY RAND()");
				                $rows = array();
				                while($r = mysqli_fetch_array($result)) {
				                    $rows[] = $r;
				                }
				                print json_encode($rows);
		    				}
		    				
		    			}
		                
		    		break;
		    		case 'field':
		    			//serch bar
		    		    $data2 = array_shift($elements);
		                $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
		                $result = mysqli_query($mysql_connection,"SELECT * FROM books WHERE Title Like '%".mysqli_real_escape_string($mysql_connection,$data2)."%' 
		                    OR Description Like '%".mysqli_real_escape_string($mysql_connection,$data2)."%'
		                    OR Genre1 Like '%".mysqli_real_escape_string($mysql_connection,$data2)."%'
		                    OR Genre2 Like '%".mysqli_real_escape_string($mysql_connection,$data2)."%'
		                    OR Genre3 Like '%".mysqli_real_escape_string($mysql_connection,$data2)."%'
		                    OR CreatorsUsername Like '%".mysqli_real_escape_string($mysql_connection,$data2)."%'  ORDER BY RAND()");
		                $rows = array();
		                while($r = mysqli_fetch_array($result)) {
		                    $rows[] = $r;
		                }
		                print json_encode($rows);
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
		
		if(isset($_COOKIE['username'])){
			ServerLog("Logging out %userid%","Logging out");
		}else{
			ServerLog("Logging out unknowen user","Logging out");
		}
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
    function sendEmail($subject,$res,$headers,$content){
        mail($res, $subject, $content, $headers);
    }
    function usernameEgsists($username){
        global $DBusername,$DBurl,$DBpassword,$DBname;
        $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
        $result = mysqli_query($mysql_connection,"SELECT * FROM users WHERE Username = '".mysqli_real_escape_string($mysql_connection,$username)."'");
        if(mysqli_num_rows($result)>0){
            return true;
        }else{
            return false;
        }
    }
    function emailEgsists($email){
        global $DBusername,$DBurl,$DBpassword,$DBname;
        $mysql_connection = mysqli_connect($DBurl,$DBusername,$DBpassword,$DBname);
        $result = mysqli_query($mysql_connection,"SELECT * FROM users WHERE Email = '".mysqli_real_escape_string($mysql_connection,$email)."'");
        if(mysqli_num_rows($result)>0){
            return true;
        }else{
            return false;
        }
    }
?>