 <?php
/*
	$MYSQL_Server = "localhost";
	$MYSQL_Username = "root";
	$MYSQL_Password = "";
	$MYSQL_Database = "db_name";
	echo "dbconnect<br>";*/

function dbconnect()
{
	$connect = mysqli_connect("localhost" , "root" , "", "fixaslab") ;
	if($connect)
	{
		return $connect ;
	}
	else
	{
		header("location:databaseerror.php") ;
	}
	return $connect ;
}
?>