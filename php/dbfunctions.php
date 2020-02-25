<?php
require_once("dbconnect.php") ;
require_once("sqliQueries.php") ;
require_once("actionmanager.php") ;
require_once("config.php") ;

/* $dataRead = new DataRead() ;
$dataWrite = new DataWrite() ; */

$connect = dbconnect() ;

class DataWrite
{
	function createUser($connect , $phone)
	{
		$query = mysqli_query($connect , "INSERT INTO members (username , status) VALUES ('$phone' , '2') ") ;
		if($query)
		{
			$id = mysqli_insert_id($connect) ;
			$dataWrite = new DataWrite() ;
			$profile = $dataWrite->saveProfile($connect , $id) ;
			if($profile)
			{
				return true;
			}
			
		}
		else
		{
			return false ;
		}
	}

	function updateProfile($connect , $user_id , $fname , $lname , $gender , $dob , $country , $state , $city , $church , $lang)
	{
		$query = mysqli_query($connect , "UPDATE profiles SET firstname = '$fname' , lastname = '$lname' , dob = '$dob' , country = '$country' , state = '$state' , city = '$city' , language = '$lang' , gender = '$gender' , church_name = '$church' WHERE user_id = '$user_id' ") ;
		if($query)
		{
			return true ;
		}
		else
		{
			return mysqli_error($connect) ;
		}
	}

	function saveProfile($connect , $id)
	{
		$query = mysqli_query($connect , "INSERT INTO profiles (user_id , login) VALUES ('$id' , '1') ") ;
		if($query)
		{
			return true ;
		}
		else
		{
			return false ;
		}
	}

	function setLogin($connect , $id)
	{
		$query = mysqli_query($connect , "UPDATE profiles SET login = '1' WHERE user_id = '$id' ") ;
		if($query)
		{
			return true ;
		}
	}

	function sendNotification($notify)
	{
		$connect = dbconnect() ;
		$owner_id = $notify['owner_id'] ;
		$message = $notify['message'] ;
		$link = $notify['link'] ;
		$notified = $notify['notified'] ;
		$query = mysqli_query($connect , "INSERT INTO notifications (owner_id,message,link,notified) VALUES ('$owner_id','$message','$link','$notified') ") ;
		if($query){ return true; }else{die(mysqli_error($connect)) ;}
	}

	function clearNotification($connect , $notifyId)
	{
		$query = $connect->query("UPDATE notifications SET status = '1' WHERE id = '$notifyId' ") ;
		if($query){return true ;}
		else{return false ;}
	}

	function sendRequest($connect , $rType , $sendTo , $requester , $relation)
	{
		if($rType == "redo")
		{
			$query = mysqli_query($connect , "UPDATE relationship SET user1 = '$sendTo' , user2 = '$requester' , requester ='$requester' , status = '0' WHERE id = '$relation' ") ;
			$dataRead = new DataRead ;
			$getUser = $dataRead->getUserDet($requester) ;
			$username = ucfirst($getUser['lastname'])." ".ucfirst($getUser['firstname']) ;
			$link = base64_encode("friends.php?tab=requests");
			$notify = array('owner_id' => $sendTo , 'message' => $username.' sent you a friend request' , 'link' => $link , 'notified' => time()) ;
			$sendNotification = $this->sendNotification($notify);
			// if(!$se)
		}elseif($rType == "addNew")
		{
			$query = mysqli_query($connect , "INSERT INTO relationship (user1 , user2 , requester) VALUES ('$sendTo' , '$requester' , '$requester') ") ;
		}
		return $query ;
	}

	function addChurch($connect , $cName , $admin , $denom , $email , $country , $state , $city , $social , $activity , $times , $map , $filename)
	{
		$query = $connect->query("INSERT INTO churches (church_name , denom , email , country , state , city , church_location , social_links , dp , admin) VALUES ('$cName' , '$denom' , '$email' , '$country' , '$state' , '$city' , '$map' , '$social' , '$filename' , '$admin') ") ;
		if($query)
		{
			$churchId = mysqli_insert_id($connect) ;
			$query2 = $connect->query("INSERT INTO activities (church_id , activity , activity_time) VALUES ('$churchId' , '$activity' , '$times') ") ;
			if($query2)
			{
				return $churchId ;
			}
			else
			{
				return mysqli_error($connect) ;
			}
		}
		else
		{
			return mysqli_error($connect) ;
		}
	}

	function imageFileSave($connect , $filename , $user , $type)
	{
		$time = time() ;
		$query = $connect->query("INSERT INTO userimages (user_id , ptype , filename , uploaded_at) VALUES ('$user' , '$type' , '$filename' , '$time') ") ;
		if($query)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function postTimeline($connect , $poster , $permit , $content , $filename)
	{
		$time = time() ;
		$query = $connect->query("INSERT INTO posts (posted_by,post,briefing,image,policy,posted) VALUES ('$poster','$content','null','$filename','$permit','$time') ") ;
		if($query)
		{
			redirect('../home.php') ;
		}
		else
		{
			redirect('../home.php') ;
		}
	}

	function postComment($connect , $postId , $comment , $poster)
	{
		$timer = time() ;
		$query = $connect->query("INSERT INTO comments (post_id,comment,commented,comment_by) VALUES ('$postId' , '$comment' , '$timer' , '$poster') ") ;
		if($query)
		{
			return true ;
		}else{return false ;}
	}

	function likePost($connect , $liker , $which , $todo , $likeness)
	{
		$time = time() ;
		if($likeness == 0)//exist so update
		{
			$query = $connect->query("UPDATE likes SET status = '$todo' WHERE liker = '$liker' AND liked = '$which' ") ;
		}
		else{
			$query = $connect->query("INSERT INTO likes (liker,liked,status,liked_time) VALUES ('$liker' , '$which' , '$todo' , '$time') ") ;
		}
		if($query){
			return true ;
		}else{return mysqli_error($connect) ;}
		
	}

	function saveStatus($con , $name , $owner , $type)
	{
		$time = time() ;
		$query = $con->query("INSERT INTO status (content , owner , type , uploaded) VALUES ('$name' , '$owner' , '$type' , '$time') ") ;
		if($query)
		{
			return true ;
 		}
 		else
 		{
 			return false ;
 		}
	}

	function SaveSongBook($con , $name , $filename , $owner)
	{
		$query = $con->query("INSERT INTO songbooks (name , filename , uploader) VALUES ('$name' , '$filename' , '$owner') ") ;
		if($query)
		{
			return true ;
		}
		else
		{
			return false ;
		}
	}

	function SaveAudioBible($con , $name , $filename , $owner)
	{
		$query = $con->query("INSERT INTO audiobibles (name , filename , uploader) VALUES ('$name' , '$filename' , '$owner') ") ;
		if($query)
		{
			return true ;
		}
		else
		{
			return false ;
		}
	}

	function saveEvent($con , $filename , $name , $loc , $desc , $uploader , $stime , $etime)
	{
		/*id	uploader	event_name	event_desc	event_image	event_loc	status	upload_time	upload_dateTime*/
		$time = time() ;
		$query = $con->query("INSERT INTO events (uploader , event_name , event_desc , event_image , event_loc , upload_time , stime , etime) VALUES ('$uploader' , '$name' , '$desc' ,'$filename' , '$loc' , '$time' , '$stime' , '$etime') ") ;
		if($query){
			return true ;
		}else{
			return false ;
		}
	}

	function sendInvites($con , $invited , $invitee , $event_id)
	{
		foreach ($invited as $id) {
			$time = time() ;
			// invitee	invited	event_id	invited_on	invite_on	status	responded_on
			$query = $con->query("INSERT INTO invitations (invited , invitee , event_id , invite_on) VALUES ('$id' , '$invitee' , '$event_id' , '$time') ") ;
		}	
			if($query){
				return true ;
			}
			else{
				return false ;
			}
		// print_r($invited) ;
	}

	function respondInvite($con , $user , $todo , $which)
	{
		$which = urlStrip($which) ;
		$query = $con->query("UPDATE invitations SET status = '$todo' WHERE event_id = '$which' AND invited = '$user' ") ;
		return $query ;
	}

	function saveMsg($con , $sid , $rid , $msg , $type)
	{
		$query = $con->query("INSERT INTO chats (sender_id , receiver_id , message , msg_type) VALUES ('$sid' , '$rid' , '$msg' , '$type') ") ;
		if($query){
			return true ;
		}else{
			return false ;
		}
	}

	function sendReport($con , $table , $postId , $user)
	{
		$query = $con->query("INSERT INTO reports (col , which , reported_by) VALUES ('$table' , '$postId' , '$user') ") ;
		if($query){
			return true ;
		}else{
			return false ;
		}
	}

	function replyReports($con , $table , $postId , $todo)
	{
		if($todo == 3){
			$todos = 1 ;
		}else{ $todos = $todo ; }
		$query = $con->query("UPDATE ".$table." SET status = '$todos' WHERE id = '$postId' ") ;
		if($query){
			$updateReport = $con->query("UPDATE reports SET status = '$todo' WHERE which = '$postId' ") ;
			if($updateReport){
				return true ;
			}else{
				return sqlError($con) ;
			}
		}else{
			return sqlError($con) ;
		}
	}
}
//end of dataWrite class

class DataRead
{
	function get_otp($connect , $id)
	{
		$query = mysqli_query($connect , "SELECT * FROM members WHERE id= '$id' ") ;
		if($query)
		{
			//$query_array = mysqli_fetch_assoc($query) ;
			$data = fetcher($query ) ; //$query_array ;
			return $data['otp'] ;
		}
		else
		{
			return mysqli_error($connect) ;
		}
	}

	function allCountry()
	{
		$connect = dbconnect() ;
		$query = mysqli_query($connect , "SELECT * FROM countries ") ;
		if($query)
		{
			return $query ;
		}
		else
		{
			return array('error' => mysqli_error($connect) );
		}
		
	}

	function getUserDet($user_id)
	{
		$connect = dbconnect() ;	
		$query = mysqli_query($connect , "SELECT * FROM members m INNER JOIN profiles p ON(m.id = p.user_id) WHERE m.id = '$user_id' LIMIT 1 ") ;
		if($query)
		{
			//$query_array = mysqli_fetch_assoc($query) ;
			// return mysqli_fetch_assoc($query) ; //$query_array ;
			return fetcher($query) ;
		}
		else
		{
			return false ;
		}
	}

	function getMultipleUsers($connect , $ids)
	{
		$query = mysqli_query($connect , "SELECT * FROM profiles WHERE user_id IN ($ids) ") ;
		if($query)
		{
			return $query ;
		}
		else
		{
			return null ;
		}
	}

	function checkotp($connect , $phone , $otp)
	{
		$query = mysqli_query($connect , "SELECT otp FROM members WHERE username = '$phone' ") ;
		$data = fetcher($query)['otp'] ;
		if($data === $otp)
		{
			return true ;
		}else{return false ;}
	}

	function notFriends($id)
	{
		$query = mysqli_query($connect , "SELECT * FROM members m INNER JOIN relationship r ON m.id = r.user1 ") ;
		if($query)
		{
			return $query ;
		}
		else
		{
			return array('error' => mysqli_error($connect));
		}
	}

	function searchMember($connect , $search , $id)
	{
		$param = strtolower($search) ;
		$query = mysqli_query($connect , "SELECT * FROM profiles WHERE ((lower(firstname) LIKE '%".$param."%') OR (lower(lastname) LIKE '%".$param."%')) AND user_id <> '$id' ");
		if($query)
		{
			$return = array() ;
			while ($fetch = fetcher($query)) {
				array_push($return, $fetch) ;
			}
			return $return ;
		}
	}

	function searchChurch($con , $param , $val)
	{
		$val = strtolower($val) ;
		if($param == 1){
			// location
			$loc = explode(',', $val) ;
			$nom = count($loc) ;
			$country = $loc[0] ;
			$state = '' ;
			$city = '' ;
			if($nom > 1){
				$state = $loc[1] ;
			}if($nom > 2){
				$city = $loc[2] ;
			}
			$query = $con->query("SELECT * FROM churches WHERE lower(country) LIKE '%".$country."%' OR lower(state) LIKE '%".$state."%' OR lower(city) LIKE '%".$city."%' ") ;
		}elseif($param == 2){
			// name
			$query = $con->query("SELECT * FROM churches WHERE lower(church_name) LIKE '%".$val."%' ") ;
		}elseif($param == 3){
			// denomination
			$query = $con->query("SELECT * FROM churches WHERE lower(denom) LIKE '%".$val."%' OR lower(church_name) LIKE '%".$val."%' ") ;
		}
		if($query){
			return $query ;
		}else{ return false ; }
	}

	function navSearch($con , $navSearch , $id)
	{
		$param = strtolower($navSearch) ;
		$query = mysqli_query($con , "SELECT * FROM profiles WHERE ((lower(firstname) LIKE '%".$param."%') OR (lower(lastname) LIKE '%".$param."%')) AND user_id <> '$id' ");
		if($query)
		{
			return $query ;
		}else{
			return false ;
		}
	}

	function fetchComments($commentId)
	{
		$connect = dbconnect() ;
		$query = $connect->query("SELECT * FROM comments WHERE post_id = '$commentId' ") ;
		if($query){return $query ;}
		else
		{
			if(is_null($query)){return "null" ;}
			else
			{
				return mysqli_error($connect) ;
			}
		}
	}

	function getChurch($chId)
	{
		$connect = dbconnect() ;
		$query = $connect->query("SELECT * FROM churches c INNER JOIN activities a ON (c.id = a.church_id) WHERE c.id = '$chId'") ;
		if($query)
		{
			if(numQuery($query) == 0)
			{
				redirect("post-not-exist.php") ;
			}
			$fetch = fetcher($query) ;
			$fetch = array_reverse($fetch) ;
			/*
			$fetch['id'] = $chId ;
			$fetch['church_id'] = $chId ;*/
			return $fetch ;
		}
		else
		{
			redirect("post-not-exist.php") ;
		}
	}

	function getSongBooks()
	{
		$con = dbconnect() ;
		$query = $con->query("SELECT * FROM songbooks WHERE status = '1' ORDER BY uploaded_at DESC ") ;
		if($query){
			while ($fetch = mysqli_fetch_assoc($query)) {
				$filename = cleanSpecialCharacters(substr($fetch['filename'], 0 , -4)) ;
		?>
			<div class="usr-question">
				<div class="usr_img">
					<img src="images/icons/pdf.png" alt="">
				</div>
				<div class="usr_quest">
					<h3><a href="pdf-archive/song-books/download/<?= $filename ?>" target="_blank"><?= $fetch['name'] ?></h3>
					<ul class="react-links">
						<li><a href="#" title=""><i class="fa fa-user"></i> <?= $fetch['uploader'] ?></a></li>
					</ul>
				</div><!--usr_quest end-->
			</div><!--usr-question end-->
		<?php
			}
		}
	}

	function getAudioBibles()
	{
		$con = dbconnect() ;
		$query = $con->query("SELECT * FROM audiobibles WHERE status = '1' ORDER BY uploaded_at DESC ") ;
		if($query){
			while ($fetch = mysqli_fetch_assoc($query)) {
				// $filename = cleanSpecialCharacters(substr($fetch['filename'], 0 , -4)) ;
		?>
			<div class="usr-question">

				<div class="usr_img">
					<img src="images/disc.png" alt="">
				</div>
				<div class="usr_quest">
					<h3><?= $fetch['name'] ?></h3>
					<ul class="react-links">
						<li><a href="#" title=""><i class="fa fa-user"></i> <?= $fetch['uploader'] ?></a></li>
						<li><a href="#" title=""><i class="fa fa-audio"></i> 
							<audio preload="none" controls style="max-width: 100%">
				                <source src="uploads/audiobibles/<?= $fetch['filename'] ?>" type="audio/mp3">
			              	</audio>
						</a></li>
					</ul>
				</div><!--usr_quest end-->
			</div><!--usr-question end-->
		<?php
			}
		}
	}





	function getQuestions($sub , $limit , $type)
	{
		$connect = dbconnect() ;	
		$query = mysqli_query($connect , "SELECT * FROM ".$sub." WHERE type = '$type' ORDER BY RAND() LIMIT $limit ") ;
		if($query)
		{
			return $query ; //fetcher($query) ;
		}
		else
		{
			return mysqli_error($connect) ;
		}
	}

	function getUser($connect , $phone)
	{
		$query = mysqli_query($connect , "SELECT * FROM members WHERE username = '$phone' ") ;
		if($query)
		{
			return $query ;
		}else{return false ;}
	}

	function getStudDet($connect , $matricNo , $course , $username)
	{
		$query = mysqli_query($connect , "SELECT * FROM users WHERE matric = '$matricNo' AND course = '$course' AND username = '$username' ORDER BY user_id DESC LIMIT 1 ") ;
		if($query)
		{
			//$query_array = mysqli_fetch_assoc($query) ;
			return mysqli_fetch_assoc($query) ; //$query_array ;
			// return "true" ;
		}
		else
		{
			return mysqli_error($connect) ;
		}
	}
	
	function get_subjects($connect)
	{
		$query = mysqli_query($connect , "SELECT * FROM subjects") ;
		if($query)
		{
			return $query ;
		}
		else{
			return mysqli_error($query) ;
		}
	}
	
	function get_type($connect)
	{
		$query = mysqli_query($connect , "SELECT * FROM exam_type") ;
		if($query)
		{
			return $query ;
		}
		else{
			return mysqli_error($query) ;
		}
	}
	
	// function to get class ids
	function switchClass($klass)
	{
		switch ($klass) {
			case "JSS1": $klass_id = 1 ;
			break ;
			case "JSS2": $klass_id = 2 ;
			break ;
			case "JSS3": $klass_id = 3 ;
			break ;
			case "SSS1": $klass_id = 4 ;
			break ;
			case "SSS2": $klass_id = 5 ;
			break ;
			case "SSS3": $klass_id = 6 ;
			dafault: $klass_id = 1 ;
		}
		return $klass_id ;
	}

	function getScores($course , $lim)
	{
		$connect = dbconnect() ;
		if($course == "all")
		{
			if(strlen($lim) != 0)
			{
				$query = mysqli_query($connect , "SELECT * FROM users WHERE checkif = '1' ORDER BY user_id DESC  LIMIT $lim") ;
			}
			else
			{
				$query = mysqli_query($connect , "SELECT * FROM users WHERE checkif = '1' ORDER BY user_id DESC ") ;
			}
			
		}
		else
		{
			if(strlen($lim) != 0)
			{
				$query = mysqli_query($connect , "SELECT * FROM users WHERE course = '$course' AND checkif = '1' ORDER BY user_id DESC  LIMIT $lim") ;
			}
			else
			{
				$query = mysqli_query($connect , "SELECT * FROM users WHERE course = '$course' AND checkif = '1' ORDER BY user_id DESC ") ;
			}
		}
		// $query = mysqli_query($connect , "SELECT * FROM users WHERE course = '$course' AND checkif = '1' ORDER BY user_id DESC  LIMIT $lim") ;
		if($query)
		{
			return $query ;
		}
		else
		{
			return mysqli_error($connect) ;
		}
	}

	function getEachScores($matricNo)
	{
		$connect = dbconnect() ;
		$query = mysqli_query($connect , "SELECT * FROM users WHERE matric = '$matricNo' AND checkif = '1' ORDER BY course ") ;
		// return mysqli_fetch_assoc($query) ;
		return $query ;
	}

	function checkAvailable($connect , $val)
	{
		$query = mysqli_query($connect , "SELECT * FROM settings WHERE exam_code = '$val' ") ;
		$num = numQuery($query) ;
		if($num == 0)
		{
			$return = 2 ;
		}
		else
		{
			$return = 1 ;
		}
		return $return ;
	}
	
/* 	function getsubid($connect , $subject)
	function getklasid($connect , $klass) */
	// function getypeid($connect , $type)
}
//end of dataRead class


?>