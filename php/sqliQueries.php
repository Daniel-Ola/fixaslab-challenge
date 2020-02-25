<?php

require_once("dbconnect.php") ;
// $connect = dbconnect() ;



function selectMembers($story)
{
	$connect = dbconnect() ;
	$query = mysqli_query($connect , "SELECT * FROM members ".$story) ;
}

function sqlSelect($table,$story)
{
	$connect = dbconnect() ;
	$query = mysqli_query($connect , "SELECT * FROM ".$table." ".$story) ;
	return $query ;
}

?>