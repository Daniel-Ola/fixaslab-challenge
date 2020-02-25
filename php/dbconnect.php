 <?php
/*
	$MYSQL_Server = "localhost";
	$MYSQL_Username = "root";
	$MYSQL_Password = "";
	$MYSQL_Database = "db_name";
	echo "dbconnect<br>";*/

/*function dbconnect()
{
	$connect = mysqli_connect("localhost" , "root" , "") ;
	if($connect)
	{
		$handle = mysqli_select_db($connect , "oladan_churchy") ;
		if($handle)
		{

		}
		else
		{
			echo "could not connect to  db" ; //header("location:error.php") ;
		}
	}
	else
	{
		header("location:databaseerror.php") ;
	}
	return $connect ;
}*/

//heroku

function dbconnect()
{
	$connect = mysqli_connect("remotemysql.com" , "On6d6EkOv7" , "qJ8aCVmek9") ;
	if($connect)
	{
		$handle = mysqli_select_db($connect , "On6d6EkOv7") ;
		if($handle)
		{

		}
		else
		{
			echo "could not connect to  db" ; //header("location:error.php") ;
		}
	}
	else
	{
		header("location:databaseerror.php") ;
	}
	return $connect ;
}


/*function dbconnect()
{
	$connect = mysqli_connect("localhost" , "christinsocial" , "christinsocial@@") ;
	if($connect)
	{
		$handle = mysqli_select_db($connect , "christinsocial") ;
		if($handle)
		{

		}
		else
		{
			echo "could not connect to  db" ; //header("location:error.php") ;
		}
	}
	else
	{
		header("location:databaseerror.php") ;
	}
	return $connect ;
}*/
	
	//$TRIMS_Bridge_url = "http://ec2-54-221-147-64.compute-1.amazonaws.com/trims-middleware/api/trims_bridge.php";

?>