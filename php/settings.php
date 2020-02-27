<?php
session_start() ;
require_once("dbconnect.php") ;
require_once("dbfunctions.php") ;
require_once("config.php") ;
// require_once("actionmanager.php") ;

if(!isset($_SESSION['user_id']))
{
	redirect("./logout.php") ;
	// echo "string";
}
else
{
	$user_id = $_SESSION['user_id'] ;
	$connect = dbconnect() ;

	class User
	{
		function getUserDet($user_id)
		{
			$connect = dbconnect() ;
			$query = $connect->query("SELECT * FROM users WHERE user_id = '$user_id' ") ;
			if($query && numQuery($query) != 0)
			{
				$fetch = fetcher($query) ; //mysqli_fetch_assoc($query) ;
				return  $fetch ;
			}else{
				redirect('./logout.php') ;
			}
		}
	}

	$user = new User ;
	// $relationship = new Relationship ;
	// $posts = new Posts ;
	// $chats = new chatMessages ;
	// print_r($chats->getMessages(1)) ;
}
?>