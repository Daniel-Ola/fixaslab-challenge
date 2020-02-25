<?php
$user_id = $_SESSION['user_id'] ;
$userDet = $user->getUserProfile($user_id) ;
$friendship = $user->getFriends($user_id) ;
// echo $user_id;
// echo $friendship;
// print_r($userDet) ;

$fname = $userDet['firstname'] ;
$lname = $userDet['lastname'] ;
$dob = $userDet['dob'] ;
$dobShow = $dob ;
$country = $userDet['country'] ;
$state = $userDet['state'] ;
$city = $userDet['city'] ;
$gender = $userDet['gender'] ;
$church = $userDet['church_name'] ;
$pp = $userDet['profile_pic'] ;
$cp = $userDet['cover_pic'] ;
$cps = $userDet['cps'] ;
$pps = $userDet['pps'] ;
$online = $userDet['login'] ;

$uniqueUser = new User ;


//friendship

// $friendno = $friendship['error'] ; //count($friendship) ;

?>
