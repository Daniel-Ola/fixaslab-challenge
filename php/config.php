<?php

function Check_If_Exists($connect , $table , $column , $value)
{
  // include_once("dbconnect.php") ;
  $query = mysqli_query($connect , "SELECT * FROM ".$table." WHERE ".$column." = '$value' ") ;
  if($query)
  {
    $num = numQuery($query) ;
    if($num == 0)
    {
      return 1 ; // true no user
    }
    else
    {
      return 0 ; // false user exists
    }
  }else{
    return mysqli_error($connect) ;
  }
}

function sqlError($con)
{
  return mysqli_error($con) ;
}

function Check_Like_Exists($connect , $table , $column , $value , $column2 , $which)
{
  include_once("dbconnect.php") ;
  $query = mysqli_query($connect , "SELECT * FROM ".$table." WHERE ".$column." = '$value' AND ".$column2." = '$which' ") ;
  if($query)
  {
    $num = numQuery($query) ;
    if($num == 0)
    {
      return 1 ; // no user
    }
    else
    {
      return 0 ; // user exists
    }
  }
}

function Check_Like_Exists2($connect , $table , $column , $value , $column2 , $which)
{
  include_once("dbconnect.php") ;
  $query = mysqli_query($connect , "SELECT * FROM ".$table." WHERE ".$column." = '$value' AND ".$column2." = '$which' AND status = '1' ") ;
  if($query)
  {
    $num = numQuery($query) ;
    if($num == 0)
    {
      return 1 ; // no user
    }
    else
    {
      return 0 ; // user exists
    }
  }
}

function showAlert($type , $message)
{
?>
  <div class="alert col-12 text-center alert-<?= $type ; ?>" role="alert">
      <strong class="h6"><?= $message; ?></strong>
  </div>

<?php
}

/*function regEX($string)
{
  $result = preg_match("*", $string) ;
  return $return ;
}*/

function passwordHash($password)
{
  $salt = "Jesus-Paid-It-All".$password ;
  $password = $salt.$password.$salt ;
	$pass = hash("sha256", md5($password)) ;
	return $pass ;
}

function numQuery($query)
{
	$num = mysqli_num_rows($query) ;
	return $num ;
}

function getGrade($percent)
{
  $percent = explode("%", $percent)[0] ; //substr($percent , -2) ;
  // $grade = "" ;
  switch ($percent) {
    case $percent >= 70:
      $grade = "A (Excellent)" ;
      break;
    case $percent >= 60 && $percent <= 69:
      $grade = "B (Very Good)" ;
      break;
    case $percent >= 50 && $percent <= 59:
      $grade = "C (Good)" ;
      break;
    case $percent >= 45 && $percent <= 49:
      $grade = "D (Satisfactory)" ;
      break;
    case $percent >= 40 && $percent <= 44:
      $grade = "E (Weak Pass)" ;
      break;
    case $percent >= 0 && $percent <= 39:
      $grade = "F (Failure)" ;
      break;
    
    default:
      $grade = "F" ;
      break;
  }
  if($percent == 0){ $grade = "F (Failure)" ; }
  return $grade ;
}

function over30($score , $tot)
{
  // $return = round(($score/$tot)*30) ;
  $return = ($score/$tot)*30 ;
  return $return ;
}

function getLastSeen($time , $now)
{
  // $time = $now-86300 ;
  $diff = $now - $time ;
  $day = 0 ;
  switch ($diff) {
    case $diff < 60:
      $return = $diff." secs" ;
      break;
    case $diff >= 60 && $diff < 3600:
      $min = floor($diff/60) ;
      $return = $min." mins" ;
      break;
    case $diff >= 3600 && $diff < 86400:
      $hr = floor($diff/3600) ;
      $return = $hr." hrs" ;
      break;
    case $diff >= 86400:
      $day = floor($diff/86400) ;
      $return = $day." days" ;
      break;
    
    default:
      $return = $diff." secs" ;
      break;
  }
  return $return ;
}

function getPastTime($time , $now)
{
  // $time = $now-86300 ;
  $diff = $now - $time ;
  $day = 0 ;
  switch ($diff) {
    case $diff < 60:
      $return = array('diff' => 'sec' , 'val' => $diff) ; // $diff." secs" ;
      break;
    case $diff >= 60 && $diff < 3600:
      $min = floor($diff/60) ;
      $return = array('diff' => 'min' , 'val' => $min) ;
      break;
    case $diff >= 3600 && $diff < 86400:
      $hr = floor($diff/3600) ;
      $return = array('diff' => 'hr' , 'val' => $hr) ;
      break;
    case $diff >= 86400:
      $day = floor($diff/86400) ;
      $return = array('diff' => 'day' , 'val' => $day) ;
      break;
    
    default:
      $return = array('diff' => 'sec' , 'val' => $diff) ;
      break;
  }
  return $return ;
}

function getDateWord($date , $format)
{
  $d = date_create($date) ;
  $return = date_format($d , $format);
  return $return ;
}

function cleanText($txt)
{
  require_once 'dbconnect.php' ;
  $con = dbconnect() ;
  return mysqli_real_escape_string($con , trim($txt)) ;
}

function cleanSpecialCharacters($string)
{
  $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
  return preg_replace('/[^A-Za-z0-9.\-]/', '' , $string); // Removes special chars.
}

function getPost($data)
{
  $return = cleanText($_POST[$data]) ;
  return $return ;
}

function  getSection($section)
{
    $return = '' ;
  if (isset($_GET['section']) && $_GET['section'] != '') {
    $sect = $_GET['section'] ;
    switch ($sect) {
      case '$section':
          return $section ;
        break;
      
      default:
        # code...
        break;
    }
  }
  else
  {
    return 'not set' ;
  }
  // else{ getSection('set-profile') ; }
  // return $return ;
}

function returnMsg($name , $msg)
{
  session_start() ;
  $_SESSION[$name] = $msg ;
}

function validateSignup()
{
  if(isset($_GET['status']))
  {
    $stat = cleanText($_GET['status']) ;
    switch ($stat) {
      /*case 'usernameerror':
        $reply = "* Username already exist." ;
        break;*/
      case 'success':
        $reply = '<div class="alert alert-info alert-dismissible fade show" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                  </button>
                  <strong>* You are almost done. Enter otp sent to your number to continue'.$_GET['otp'].'</strong>
                </div>' ;
        break;
      case 'userexists':
        $reply = '<div class="alert alert-info alert-dismissible fade show" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                  </button>
                  <strong>* This phone number has been registered, you may consider logging in.</strong>
                </div>' ;
        break;
      case 'usernotexist':
        $reply = '<div class="alert alert-info alert-dismissible fade show" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                  </button>
                  <strong>* This phone number has not been registered, you may consider signing up.</strong>
                </div>' ;
        break;
      /*case 'passworderror':
        $reply = "* Password does not match." ;
        break;*/
      
      default:
        $reply = "" ;
        // redirect("./index.php") ;
        break;
    }
  }
  else
  {
    $reply = "" ;
  }
  return $reply ;
}

function validateLogin()
{
  if(isset($_GET['status']))
  {
    $stat = $_GET['status'] ;
    switch ($stat) {
      case 'usernameerror':
        $reply = "* Username does not exist." ;
        break;
      case 'passworderror':
        $reply = "* Incorrect Password." ;
        break;
      
      default:
        $reply = "" ;
        break;
    }
  }
  else
  {
    $reply = "" ;
  }
  return $reply ;
}

function fetcher($query)
{
	return mysqli_fetch_assoc($query) ;
}

function fetches($query)
{
  $ret = array() ;
  while ($fetch = fetcher($query)) {
    $return = array_push($ret, $fetch) ;
  }
  return $ret ;
}

function redirect($page)
{
// 	header("location:".$page) ;

//   echo '<script> window.location.href="'.$page.'";</script>';
// echo "<script> window.location.assign('".$page."') ; </script>" ;
echo "<script> window.location.replace('".$page."') ; </script>" ;
// echo '<script type="text/javascript">';
        // echo 'window.location.assign("'.$page.'");';
        // echo '</script>';
        // echo '<noscript>';
        // echo '<meta http-equiv="refresh" content="0;url='.$page.'" />';
        // echo '</noscript>'; exit;
}

function generateRandomString($length = 10) {
  return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

/*function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}*/

// echo  generateRandomString();  // OR: generateRandomString(24)

function urltxtmix($val)
{
  $time = time() ;
  $time = md5($time) ;
  $time2 = time() + 360 ;
  $time2 = md5($time2) ;
  $return = $time.$val.$time2 ;
  return $return ;
}

function urlStrip($data)
{
  $num = strlen($data) ;
  $return = substr(strrev(substr($data, 32 , $num)) , 32 , 32) ;
  return $return ;
}

function uploadAuth($file , $pathToUpload)
{
  $target_dir = $pathToUpload;
  $target_file = $target_dir.time() . cleanSpecialCharacters(basename($file["name"]));
  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
  $check = getimagesize($file["tmp_name"]);
  if($check !== false)
  {
    $saveImage = move_uploaded_file($file["tmp_name"], $target_file) ;
    if($saveImage)
    {
      $uploadOk = array('filename' => $target_file);
    }
    else
    {
      $uploadOk = 0;
    }   
  }
  else
  {
    $uploadOk = 0;
  }
  return $uploadOk ;
}

function setNameExt($name , $file)
{
  return $name.'.'.strtolower(pathinfo($file['name'],PATHINFO_EXTENSION)) ;
}

function fileUpload($file , $pathToUpload , $filetype)
{
  $rand = time() ; //rand(10,100);
  if((!empty($file)) && !empty($file['name']))
  {
    if(preg_match('/[.]('.$filetype.')$/', $file['name']))
    {
      $tempFile = $file['tmp_name'];
      $check = move_uploaded_file($tempFile,$pathToUpload);
      if($check):
        return true ;
      else:
        return array("msg" =>"Could not upload file to db");
      endif;
    }
    else
    {
      return array("msg" => "Invalid file type");
    }
  }
  else
  {
    return array("msg" => "Empty file");
  }
}

function getRest($rand_opt , $optgrp_arr)
{
  switch ($rand_opt) {
    case !in_array(0, $rand_opt):
            $return = $optgrp_arr[0];
        break;
    case !in_array(1, $rand_opt):
            $return = $optgrp_arr[1];
        break;
    case !in_array(2, $rand_opt):
            $return = $optgrp_arr[2];
        break;
    case !in_array(3, $rand_opt):
            $return = $optgrp_arr[3];
        break;
    
    default:
        $return = "option e";
        break;
  }
  return $return ;
}

function sendSMS($sendto, $message)
{
    /*if(strlen($sendto) == 11 || strlen($sendto) == 10)
    {
        if(strpos("#".$sendto,"#0") !== FALSE && strlen($sendto) <= 11) $sendto = "234" . substr($sendto,1);
        if(strpos("#".$sendto,"#0") === FALSE && strlen($sendto) == 11) $sendto = "234" . substr($sendto,1);        
    }*/
/*	
    $url = "http://zoracom.smsrouter.gtsmessenger.com/ws/instant.php?action=sendSMS&login=admin&password=7f1b1592"
	. "&to=" . UrlEncode($sendto)
	. "&from=" . UrlEncode("33811")
	. "&message=" . UrlEncode($message);
*/    
 //http://www.smslive247.com/http/index.aspx?cmd=sendquickmsg&owneremail=xxx&subacct=xxx&subacctpwd=xxx&message=xxx&sender=xxx&sendto=xxx&msgtype=0
    $url = "http://www.smslive247.com/http/index.aspx?"
    . "cmd=sendquickmsg"
    . "&owneremail=" . UrlEncode("danorelanre@gmail.com")
    . "&subacct=" . UrlEncode("ChristIn")
    . "&subacctpwd=" . UrlEncode("ChristIn")
    . "&message=" . UrlEncode($message)
    . "&sender=" . UrlEncode("ChristIn")
    . "&sendto=" . UrlEncode($sendto)
    . "&msgtype=0" ;
    
// echo $url;

//showAlert($url);

    $curl_handle=curl_init();
      curl_setopt($curl_handle,CURLOPT_URL,$url);
      curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
      curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
      $buffer = curl_exec($curl_handle);
      curl_close($curl_handle);
      if (empty($buffer)){
              // print "Nothing returned from url.<p>";
          return false;
      }
      else{
              // print $buffer;
          
          return true;
      }
}

function sendEmail($email,$subject,$message)
{
	$sender = "Student Disciplinary Council<noreply@sdcmail.com>";
	$sent = mail($email,$subject,$message,"From: $sender"."\r\n"."Content-type: text/html; charset=iso-8859-1","-fwebmaster@".$_SERVER["SERVER_NAME"]);
	if($sent) return true;
	return false;
}



?>