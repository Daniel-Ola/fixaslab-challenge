<?php
require_once("dbconnect.php") ;
require_once("dbfunctions.php") ;
require_once("config.php") ;
$actionmanager = new Actionmanager() ;

if(isset($_POST['command']) && $_POST['command'] == 'register'){
	$actionmanager->registerUser() ;
}



class Actionmanager
{
	public function registerUser(){
		$con = dbconnect() ;
		$dataWrite = new DataWrite ;
		$email = getPost('email') ;
		$check = Check_If_Exists($con, 'users' , 'email' , $email) ;
		if($check == 0){
			returnMsg('regerror','Sorry this email has been registered') ;
			redirect('../register.php') ;
		}else{
			$password = passwordHash(getPost('password')) ;
			$registerMe = $dataWrite->createUser($con , $email , $password) ;
			if($registerMe == -1){
				returnMsg('regerror' , 'Sorry an error occur') ;
				redirect('../register.php') ;
			}else{
				session_start() ;
				$_SESSION['user_id'] = $registerMe ;
				redirect('../home.php') ;
			}
		}
	}
}


?>


