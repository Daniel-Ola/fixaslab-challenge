<?php
require_once("dbconnect.php") ;
require_once("dbfunctions.php") ;
require_once("config.php") ;
$actionmanager = new Actionmanager() ;

if(isset($_POST['command']) && $_POST['command'] == 'register'){
	$actionmanager->registerUser() ;
}
elseif(isset($_POST['command']) && $_POST['command'] == 'login'){
	$actionmanager->login() ;
}



class Actionmanager
{
	function registerUser(){
		$con = dbconnect() ;
		$dataWrite = new DataWrite ;
		$email = getPost('email') ;
		$fullname = getPost('fullname') ;
		$check = Check_If_Exists($con, 'users' , 'email' , $email) ;
		if($check == 0){
			returnMsg('regerror','Sorry this email has been registered') ;
			redirect('../register.php') ;
		}else{
			$password = passwordHash(getPost('password')) ;
			$registerMe = $dataWrite->createUser($con , $email , $password , $fullname) ;
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

	function login()
	{
		$con = dbconnect() ;
		$dataRead = new DataRead ;
		$email = getPost('email') ;
		$password = passwordHash(getPost('password')) ;
		$check = Check_If_Exists($con, 'users' , 'email' , $email) ;
		if($check == 1){
			returnMsg('logerror','Sorry this email is not registered') ;
			redirect('../login.php') ;
		}else{
			$password = passwordHash(getPost('password')) ;
			$login = $dataRead->login($con , $email) ;
			if(is_array($login)){
				if($password === $login['password']){
					session_start() ;
					$_SESSION['user_id'] = $login['user_id'] ;
					redirect('../home.php') ;
				}else{
					returnMsg('logerror' , 'Incorrect username or password') ;
					redirect('../login.php') ;
				}
			}else{
				returnMsg('logerror' , 'Sorry an error occur') ;
				redirect('../login.php') ;
			}
		}
	}
}


?>


