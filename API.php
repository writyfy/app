<?php
	include 'pw.php';
    $DBusername = a_pw();
    $DBurl = b_pw();
    $DBpassword = c_pw();
    $DBname = d_pw();
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
	    public function getUsername(){
	    	return $this->Username;
	    }
	    //set
	    public function setUsername($value){
	    	$this->Username = $value;
	    	self::DBConnect("Username",$value,$this->ID);
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
		    case 'books':
				////////////////////Get Request Type/////////////////////////////
				$method = $_SERVER['REQUEST_METHOD'];
				$request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));
				switch ($method) {
				  case 'PUT':
				    echo "1";  
				    break;
				  case 'POST':
				    echo "2";  
				    break;
				  case 'GET':
					$book = new Book($data);
					print $book->getBook();
					echo $book->getTitle();
				    break;
				  case 'HEAD':
				    echo "4";  
				    break;
				  case 'DELETE':
				    echo "5";   
				    break;
				  case 'OPTIONS':
				    echo "6";    
				    break;
				  default:
				    echo "7";  
				    break;
				}
		        break;
		    case 'user':

		    	break;
		    default:
		    echo "default";
		    $a = new CurrentUser();
		    echo $a->isUserValid();
		    break;
		}
	}
		
?>