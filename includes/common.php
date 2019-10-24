<?php
//~ session must start before any other code is run
session_start();

//~ changes mode to developer mode
global $dev;
$dev = false;

global $project_name;
$project_name = 'One Thing';

include('classes/class_defaultObject.php');
include('classes/class_defaultListObject.php');
include('classes/class_user.php');
include('classes/class_thing.php');
include('classes/class_thingList.php');

//~ user functions
//~ returns true if a user is logged in
function userIsLoggedIn(){return isset($_SESSION['usr_id']);}
function getUserFirstName(){ return (userIsLoggedIn() ? $_SESSION['usr_first_name'] : ''); }
function getUserLastName(){ return (userIsLoggedIn() ? $_SESSION['usr_last_name'] : ''); }
function getUserEmail(){ return (userIsLoggedIn() ? $_SESSION['usr_email'] : ''); }
function getUserId(){ return (userIsLoggedIn() ? $_SESSION['usr_id'] : '0'); }


//~ genericFunctions - used all/any page 
//~ returns html for the header bar
function getHeadTags($title=''){
	global $project_name;
	return '
		<meta name="description" content="This is a default login page that can be used as a starting point for all future projects">
		<meta name="viewport" content="width=device-width initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">
		<title>'.$project_name.' | '.$title .'</title>'
		.getCss()
		.getJs();
}

function getCss(){
	ob_start();
	include('css.php');
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

function getJs(){
	return '<script type="text/javascript" src="includes/js.js"></script>';
}

function getHeaderBarHtml(){
	if(userIsLoggedIn()){
		$loginOptionsHtml = '<button name="logoutbutton" onclick="ajaj({\'file\':\'login.nav.php\', \'nav\':\'submitLogout\', \'runOnReturn\':handleLogoutResponse})">Logout</button>';
		$listArray = ['index.php'=>'Home','thing.php'=>'Things','thingList.php'=>'Thing List'];
	} else {
		$loginOptionsHtml = (getCurrentFilename()=='login.php' 
							? ''
							:'<span id="loginFormHeader">
								<input type="text" name="usr_email" placeholder="Email">
								<input type="password" name="usr_password" placeholder="Password">
								<button name="loginButton" onclick="ajaj({\'file\':\'login.nav.php\', \'nav\':\'submitLogin\',\'getValuesFrom\':\'loginFormHeader\',\'runOnReturn\':handleLoginResponse});">Login</button>
							</span>'
						)
						.'<a href="signup.php">Sign Up</a>';
        $listArray = ['index.php'=>'Home'];
	}
    
    $listOptionsHtmlArray = [];
    foreach($listArray as $page=>$label){
        $listOptionsHtmlArray[] = '<li><a href="'.$page.'">'.$label.'</a></li>';
    }
	return '<header>
		<nav>
			<a href="index.php"><img src="img/logoContrast.png" alt="romolo logo"></a>
			<ul>'.implode($listOptionsHtmlArray).'</ul>
			<div>
				'.$loginOptionsHtml.'
			</div>
		</nav>
	</header>';
}

function getCurrentFilename(){
	$arr = explode('/',$_SERVER['SCRIPT_FILENAME']);
	return end($arr);
}

function array_contains($array,$value){
	$matches = 0;
	foreach($array as $k=>$v){
		$matches = ($v==$value ? $matches+1 : $matches);
	}
	return $matches;
}

function trigger_notice($notice){
    $type = gettype($notice);
    $noticeString = ($type=='string' ? $notice : json_encode($notice));
    $noticeString = str_replace(
                                ['<',   ','],
                                ['&lt', ', '],
                                //~ ['<',   ',',  '{',     '[',     '}',     ']'],
                                //~ ['&lt', ', ', '{<br>', '[<br>', '<br>}' ,'<br>]'],
                                $noticeString
                    );
    
    trigger_error('<span class="triggerNotice">'.strtoupper($type).': <br>'.$noticeString.'</span>');
}
  
function openDb(){
	$host = "localhost";
	$dbUser = "rob";
	$password = "robberrydb";
	$database = "oneThingDB";

	$dbConn = new mysqli($host,$dbUser,$password,$database);

	if($dbConn->connect_error){
		die("Database Connection Error, Error No.: ".$dbConn->connect_errno." | ".$dbConn->connect_error);
		return;
	} else {
		return $dbConn;
	}
}

function getTableRowUsingId($tableName,$tablePrimaryKey,$id){
	$db = openDb();
	$stmt = $db->prepare("SELECT * FROM ".$tableName." WHERE ".$tablePrimaryKey."=? LIMIT 1;");
	$stmt = bindParameters($stmt,$id);
	$stmt->execute();
	
	$result = $stmt->get_result();
	$db->close(); 
	
	if($result->num_rows==1){
		return $result->fetch_assoc();
	} else {
		return False;
	}
}

function getEmptyTableRow($tableName,$tablePrimaryKey){
	$db = openDb();
	$stmt = $db->prepare("SELECT * ,IFNULL(max(".$tablePrimaryKey."),'') as thisid FROM ".$tableName." LIMIT 1");
	$stmt->execute();
	 
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	
	unset($row['thisid']);
	foreach($row as $k=>$v){$row[$k] = '';}
	
	$db->close();
	return $row;
}

function bindParameters($stmt,$originalArray=[]){
    if(count($originalArray)==0){return $stmt;}
	$originalArray = (gettype($originalArray)=='string' || gettype($originalArray)=='integer' ? [$originalArray] : $originalArray);
	$newArray = [];
	foreach($originalArray as $k=>$v){
		$newArray[] = $v;
	}
	
	//~ log_dump($originalArray);
	//~ log_dump($newArray);
	$string = str_repeat('s',count($newArray));
	
	//~ log_dump(count($newArray));
	switch(count($newArray)){
		case 0:
			$stmt->bind_param($string);
		break;
        
		case 1:
			$stmt->bind_param($string,$newArray[0]);
		break;
		
		case 2:
			$stmt->bind_param($string,$newArray[0],$newArray[1]);
		break;
		
		case 3:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2]);
		break;
		
		case 4:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3]);
		break;
		
		case 5:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4]);
		break;
		
		case 6:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4],$newArray[5]);
		break;
		
		case 7:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4],$newArray[5],$newArray[6]);
		break;
		
		case 8:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4],$newArray[5],$newArray[6],$newArray[7]);
		break;
		
		case 9:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4],$newArray[5],$newArray[6],$newArray[7],$newArray[8]);
		break;
		
		case 10:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4],$newArray[5],$newArray[6],$newArray[7],$newArray[8],$newArray[9]);
		break;
		
		case 11:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4],$newArray[5],$newArray[6],$newArray[7],$newArray[8],$newArray[9],$newArray[10]);
		break;
		
		case 12:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4],$newArray[5],$newArray[6],$newArray[7],$newArray[8],$newArray[9],$newArray[10],$newArray[11]);
		break;
		
		case 13:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4],$newArray[5],$newArray[6],$newArray[7],$newArray[8],$newArray[9],$newArray[10],$newArray[11],$newArray[12]);
		break;
		
		case 14:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4],$newArray[5],$newArray[6],$newArray[7],$newArray[8],$newArray[9],$newArray[10],$newArray[11],$newArray[12],$newArray[13]);
		break;
		
		case 15:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4],$newArray[5],$newArray[6],$newArray[7],$newArray[8],$newArray[9],$newArray[10],$newArray[11],$newArray[12],$newArray[13],$newArray[14]);
		break;
		
		case 16:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4],$newArray[5],$newArray[6],$newArray[7],$newArray[8],$newArray[9],$newArray[10],$newArray[11],$newArray[12],$newArray[13],$newArray[14],$newArray[15]);
		break;
		
		case 17:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4],$newArray[5],$newArray[6],$newArray[7],$newArray[8],$newArray[9],$newArray[10],$newArray[11],$newArray[12],$newArray[13],$newArray[14],$newArray[15],$newArray[16]);
		break;
		
		case 18:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4],$newArray[5],$newArray[6],$newArray[7],$newArray[8],$newArray[9],$newArray[10],$newArray[11],$newArray[12],$newArray[13],$newArray[14],$newArray[15],$newArray[16],$newArray[17]);
		break;
		
		case 19:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4],$newArray[5],$newArray[6],$newArray[7],$newArray[8],$newArray[9],$newArray[10],$newArray[11],$newArray[12],$newArray[13],$newArray[14],$newArray[15],$newArray[16],$newArray[17],$newArray[18]);
		break;
		
		case 20:
			$stmt->bind_param($string,$newArray[0],$newArray[1],$newArray[2],$newArray[3],$newArray[4],$newArray[5],$newArray[6],$newArray[7],$newArray[8],$newArray[9],$newArray[10],$newArray[11],$newArray[12],$newArray[13],$newArray[14],$newArray[15],$newArray[16],$newArray[17],$newArray[18],$newArray[19]);
		break;
	}

	return $stmt;
	
}
 
//~ date functions
/*use only timestamps for php - convert with js*/



?>
