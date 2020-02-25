<?php
require_once("dbconnect.php") ;
require_once("dbfunctions.php") ;
require_once("config.php") ;
$actionmanager = new Actionmanager() ;

if(isset($_POST['register']))
{
	$actionmanager->createUser() ;
}
elseif(isset($_POST['submitotp']) && $_POST['submitotp'] == 'otpconfirm')
{
	$actionmanager->checkotp() ;
}
elseif(isset($_POST['submitotp']) && $_POST['submitotp'] == 'otpconfirmlogin')
{
	$actionmanager->checkotpforlogin() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "saveprofile") {
	$actionmanager->updateProfile() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "login") {
	$actionmanager->Login() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "f_requests") {
	$actionmanager->friendRequests() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "get_friends") {
	$actionmanager->getFriends() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "respondRequest") {
	$actionmanager->respondRequest() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "s_friends") {
	$actionmanager->s_friends() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "f_suggest") {
	$actionmanager->f_suggest() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "undoRequest") {
	$actionmanager->undoRequest() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "sendRequest") {
	$actionmanager->sendRequest() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "addChurch") {
	$actionmanager->addChurch() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "fetchComments") {
	$actionmanager->fetchComments() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "clearNotification") {
	$actionmanager->clearNotification() ;
}
elseif (isset($_FILES['pp'])){
	$actionmanager->uploadPP() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "postTimeline") {
	$actionmanager->postTimeline() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "postComment") {
	$actionmanager->postComment() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "infinityPosts") {
	$actionmanager->infinityPosts() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "like-unlike") {
	$actionmanager->likeUnlike() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "statusPost") {
	$actionmanager->statusPost() ;
}
elseif (isset($_FILES['songbook'])){
	$actionmanager->songBook() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "addevent") {
	$actionmanager->addEvent() ;
}
elseif (isset($_FILES['audiobible'])){
	$actionmanager->audiobible() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "sendInvites") {
	$actionmanager->sendInvites() ;
}
elseif (isset($_GET['command']) && $_GET['command'] == "respond-invite") {
	$actionmanager->respondInvite() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "sendMsg") {
	$actionmanager->sendMsg() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "searchChurch") {
	$actionmanager->searchChurch() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "navSearch") {
	$actionmanager->navSearch() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "sendReport") {
	$actionmanager->sendReport() ;
}
elseif (isset($_POST['command']) && $_POST['command'] == "replyReports") {
	$actionmanager->replyReports() ;
}
// else{
// 	redirect("../") ;like-unlike
// }


//begin of actionmanager class
class Actionmanager
{
	function createUser()
	{
		$connect = dbconnect() ;
		$dataWrite = new dataWrite() ;
		$dataRead = new dataRead() ;
		$phone = $_POST['phone'] ;
		$checkUser = Check_If_Exists($connect , 'members' , 'username' , $phone) ;
		// echo $checkUser;
		if($checkUser == 1)
		{
			session_start() ;
			$otp = rand(1000 , 9999) ;
			// send $otp to number
			$message = "Your one time password is ".$otp." please do not disclose" ;
			sendSMS($phone, $message) ;
			$_SESSION['secret'] = passwordHash($otp) ;
			$_SESSION['otp'] = $otp ;
			$_SESSION['phone'] = $phone ;
			/*$do_job = $dataWrite->createUser($connect , $phone , $otp) ;*/
			redirect("../login.php?status=otp-sent") ;
			// echo $do_job;
		}
		else
		{
			// redirect("../?msg=userExists") ;
			// $_POST['error'] = "UserExists" ;
			redirect("../login.php?status=userexists") ;
		}
	}

	function checkotp()// user is created here
	{
		$dataRead = new DataWrite() ;
		$connect = dbconnect() ;
		$phone = cleanText($_POST['phone']) ;
		$otp = passwordHash($_POST['otp']) ;
		$secret = $_POST['secret'] ;
		if($otp === $secret)
		{
			$do_job = $dataRead->createUser($connect , $phone) ;
			if($do_job)
			{
				$user_id = fetcher(mysqli_query($connect , "SELECT * FROM members WHERE username = '$phone' "));
				session_start() ;
				session_destroy() ;
				session_start() ;
				$_SESSION['user_id'] = $user_id['id'] ;
				// $status = $user_id['profile'] 
				redirect("../profile-setting.php") ;
				/*if($user_id['profile'] == 0)
				{
					redirect("../profile-setting.php") ;
				}else{redirect('../home.php') ;}*/
				
			}
			else
			{
				echo mysqli_error($connect);
			}
		}
		else
		{
			session_start() ;
			$_SESSION['error'] = "Incorrect OTP, please check again and re-enter." ;
			redirect("../login.php?status=otp-error") ;
		}
		
	}

	function checkotpforlogin()// user is created here
	{
		$dataRead = new DataRead() ;
		$dataWrite = new DataWrite() ;
		$connect = dbconnect() ;
		$phone = cleanText($_POST['phone']);
		$otp = passwordHash($_POST['otp']) ;
		$secret = $_POST['secret'] ;
		if($otp === $secret)
		{
			// $user_id = fetcher();
			$do_job = $dataRead->getUser($connect , $phone) ;
			if($do_job)
			{
				session_start() ;
				session_unset() ;
				session_destroy() ;
				session_start() ;
				$_SESSION['user_id'] = fetcher($do_job)['id'] ;
				if($dataWrite->setLogin($connect , $_SESSION['user_id']))
				{
					/*if(fetcher($do_job)['profile'] == 0)
					{
						redirect("../profile-setting.php") ;
					}else{redirect('../home.php') ;}*/
					redirect("../home.php") ;
				}
				else
				{
					redirect("../logout.php") ;
				}
				
			}
			else
			{
				session_start() ;
				$_SESSION['error'] = "Request Could not be completed" ;
				redirect("../login.php?status=otp-error") ;
			}
			
		}
		else
		{
			session_start() ;
			$_SESSION['error'] = "Incorrect OTP, please check again and re-enter." ;
			redirect("../login.php?status=otp-error") ;
		}
		
	}

	function updateProfile()
	{
		$dataWrite = new DataWrite() ;
		$connect = dbconnect() ;
		// print_r($_POST) ;
		$user_id = cleanText($_POST['user_id']) ;
		$fname = cleanText($_POST['fname']) ;
		$lname = cleanText($_POST['lname']) ;
		$gender = cleanText($_POST['gender']) ;
		$dob = cleanText($_POST['dob']) ;
		$country = cleanText($_POST['country']) ;
		$state = cleanText($_POST['state']) ;
		$city = cleanText($_POST['city']) ;
		$church = cleanText($_POST['church']) ;
		$lang = cleanText($_POST['lang']) ;
		$do_job = $dataWrite->updateProfile($connect , $user_id , $fname , $lname , $gender , $dob , $country , $state , $city , $church , $lang) ;
		if($do_job)
		{
			// echo $do_job;
			session_start() ;
			$_SESSION['return'] = "Profile updated successfully!" ;
			redirect("../profile-setting.php") ;
		}
		else{

			session_start() ;
			$_SESSION['return'] = "Sorry please try again!" ;
			redirect("../profile-setting.php") ;
		}
		
	}

	function Login()
	{
		// when user logs in two things happen the login stat is updated and the temp_cart is created also but deleted on logout
		$connect = dbconnect() ;
		// $dataWrite = new DataWrite() ;
		$dataRead = new DataRead() ;

		$phone = $_POST['phone'] ;
		$checkUser = Check_If_Exists($connect , 'members' , 'username' , $phone) ;
		// print_r($checkUser);
		if($checkUser == 0)
		{
			session_start() ;
			$otp = rand(1000 , 9999) ;
			$message = "Your one time password is ".$otp." please do not disclose" ;
			// $sendSMS = 
			sendSMS($phone, $message) ;
			/*if($sendSMS)
			{
				echo "done<br>";
				echo $sendSMS;
			}
			else
			{
				echo "error<br>";
				echo $sendSMS;
			}
			die() ;*/
			// send $otp to number
			$_SESSION['secret'] = passwordHash($otp) ;
			$_SESSION['otp'] = $otp ;
			$_SESSION['phone'] = $phone ;
			$_SESSION['command'] = "login" ;
			redirect("../login.php?status=otp-sent") ;
			// echo $do_job;
		}
		else
		{
// 			echo $checkUser;
			redirect("../login.php?status=usernotexist") ;
		}
	}

	function friendRequests()
	{
		require_once("settings.php") ;
		$user_id = $_POST['user_id'] ;
		$dojob = $user->getFriendsRequests($user_id) ;
		if(!array_key_exists("error", $dojob))
		{
		?>
			<div class="company-title">
						<h3>Friend Requests</h3>
					</div><!--company-title end-->
					<div class="companies-list">
						<div class="row">
							<?php
								if(count($dojob) == 0){ showAlert("success" , "Seems you've got no friend request yet") ; }
								foreach($dojob as $friend_id) {
									$dataRead = new DataRead ;
									$profiles = $dataRead->getUserDet($friend_id) ;
									$pp = $profiles['profile_pic'] ;
									$name = ucfirst($profiles['firstname'])." ".ucfirst($profiles['lastname']) ;
									$location = ucfirst($profiles['city']).", ".ucfirst($profiles['country']) ;
							?>
								<div class="col-lg-3 col-md-4 col-sm-6">
									<div class="company_profile_info">
										<div class="company-up-info">
											<img src="images/users/<?php echo $pp ?>" class="img img-responsive" height="90" width="90" alt="">
											<h3><?php echo $name; ?></h3>
											<h4><i class="fa fa-map-signs"></i> <?php echo $location; ?></h4>
											<ul>
												<li><a href="#" title="" class="follow bg-purple" other="#respond2<?php echo $friend_id; ?>" id="respond1<?php echo $friend_id; ?>" onclick="repondRequest(1,<?php echo $friend_id; ?>,'respond1<?php echo $friend_id; ?>')"><i class=""></i> Accept</a></li>
												<li><a href="#" title="" class="follow bg-danger" other="#respond1<?php echo $friend_id; ?>" id="respond2<?php echo $friend_id; ?>" onclick="repondRequest(2,<?php echo $friend_id; ?>,'respond2<?php echo $friend_id; ?>')"><i class=""></i> Decline</a></li>
												<!-- <li><a href="#" title="" class="message-us"><i class="fa fa-envelope"></i></a></li> -->
											</ul>
										</div>
										<?php $wrong = base64_encode(time().$friend_id); ?>
										<a href="profiles.php?f=<?= $name; ?>&qurt=<?= urltxtmix($wrong); ?>&mdi=<?= urltxtmix($friend_id); ?>" title="" class="view-more-pro">View Profile</a>
									</div><!--company_profile_info end-->
								</div>
							<?php
								}
							?>
						</div>
					</div><!--companies-list end-->
		<?php
		}
	}

	function getFriends()
	{
		require_once("settings.php") ;
		$user_id = $_POST['user_id'] ;
		$dojob = $user->getFriends($user_id) ;
		if(is_null($dojob)){exit((showAlert("success" , "Seems like you have made no friends yet"))) ;}
		if(!array_key_exists("error", $dojob))
		{
		?>
			<div class="company-title">
						<h3>All Friends</h3>
					</div><!--company-title end-->
					<div class="companies-list">
						<div class="row">
							<?php
								if(numQuery($dojob) == 0){ showAlert("success" , "Seems you've got no friend request yet") ; }
								while ($profiles = fetcher($dojob)) {
									$pp = $profiles['profile_pic'] ;
									$name = ucfirst($profiles['firstname'])." ".ucfirst($profiles['lastname']) ;
									$location = ucfirst($profiles['city']).", ".ucfirst($profiles['country']) ;
							?>
								<div class="col-lg-3 col-md-4 col-sm-6">
									<div class="company_profile_info">
										<div class="company-up-info">
											<img src="images/users/<?php echo $pp ?>" class="img img-responsive" height="90" width="90" alt="">
											<h3><?php echo $name; ?></h3>
											<h4><i class="fa fa-map-signs"></i> <?php echo $location; ?></h4>
											<ul>
												<li><a href="#" title="" class="follow"><i class="fa fa-check"></i> Friends</a></li>
												<!-- <li><a href="#" title="" class="message-us"><i class="fa fa-envelope"></i></a></li> -->
											</ul>
										</div>
										<!-- <a href="church-details.php" title="" class="view-more-pro">Check Activities</a> -->
									</div><!--company_profile_info end-->
								</div>
							<?php
								}
							?>
						</div>
					</div><!--companies-list end-->
		<?php
		}
		else
		{
			showAlert("warning" , '<a href="#requests" class="get-friendship" command="f_requests" title="">Try Again</a>') ;
		}
	}

	function s_friends()
	{
		$search = cleanText($_POST['search']) ;
		$user_id = $_POST['user_id'] ;
		$connect = dbconnect() ;
		$dataRead = new DataRead ;
		$dojob = $dataRead->searchMember($connect , $search , $user_id) ;
		if($dojob == NULL)
		{
		?>
			<div class="alert alert-warning mt-5" role="alert">
			    <strong>No values march your search '<?php echo $search ;?>'.</strong>
			</div>
		<?php
		}
		else
		{
		?>
			<div class="company-title">
				<h3>Search return for '<?php echo $search; ?>'</h3>
			</div><!--company-title end-->
			<div class="companies-list">
				<div class="row">
		<?php
			require_once 'settings.php' ;
			foreach ($dojob as $row) {
			$pp = $row['profile_pic'] ;
			$name = ucfirst($row['firstname'])." ".ucfirst($row['lastname']) ;
			$location = ucfirst($row['city']).", ".ucfirst($row['country']) ;
		?>
			<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
				<div class="company_profile_info">
					<div class="company-up-info">
						<img src="images/users/<?php echo $pp ?>" class="img img-responsive" height="90" width="90" alt="">
						<h3 id="myName"><?php echo $name; ?></h3>
						<h4><i class="fa fa-map-signs"></i> <?php echo $location; ?></h4>
						<ul>
							<?php $relationship->getBtn($row['user_id']) ; ?>
						</ul>
					</div>
				</div><!--company_profile_info end-->
			</div>				
			
		<?php
			}
		?>
				</div>
			</div><script type="text/javascript" src="js/custom.js"></script><!--companies-list end-->
		<?php
			// echo "<script src='js/custom.js'></script>";
		}
	}

	function f_suggest()
	{
		require_once("settings.php") ;
		$user_id = $_POST['user_id'] ;
		$dojob = $relationship->suggestedFriends($user_id) ;
		if(is_object($dojob)){
		?>
			<div class="company-title">
				<h3>Suggested Friends</h3>
			</div><!--company-title end-->
			<div class="companies-list">
				<div class="row">
					<?php
						if(numQuery($dojob) == 0){
							// showAlert("success" , "Seems you've got no suggestions yet") ;
							// $dojob = $relationship->getAllMembersNotFriends($user_id) ;
						}
						foreach($dojob as $row) {
							$pp = $row['profile_pic'] ;
							$name = ucfirst($row['firstname'])." ".ucfirst($row['lastname']) ;
							$location = ucfirst($row['city']).", ".ucfirst($row['country']) ;
					?>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="company_profile_info">
								<div class="company-up-info">
									<img src="images/users/<?php echo $pp ?>" class="img img-responsive" height="90" width="90" alt="">
									<h3><?php echo $name; ?></h3>
									<h4><i class="fa fa-map-signs"></i> <?php echo $location; ?></h4>
									<ul>
										<?= $relationship->getBtn($row['user_id']) ; ?>
										<!-- <li><a href="#" title="" class="message-us"><i class="fa fa-envelope"></i></a></li> -->
									</ul>
								</div>
								<?php $wrong = base64_encode(time()); ?>
								<a href="profiles.php?f=<?= $name; ?>&qurt=<?= urltxtmix($wrong); ?>&mdi=<?= urltxtmix($row['user_id']); ?>" title="" class="view-more-pro">View Profile</a>
							</div><!--company_profile_info end-->
						</div>
					<?php
						}
					?>
				</div>
			</div><!--companies-list end-->
		<?php
			/*foreach ($dojob as $key) {
				// print_r($key);
			}*/
			/*while($fetch = mysqli_fetch_assoc($dojob))
			{
				print_r($fetch) ;
			}*/
		}
			
		// showAlert("info" , "Sorry! There are no suggestions at the moment please try again later.") ;
	}

	function respondRequest() 
	{
		/*action ,
    fr_id: fr_id*/
    	$action = $_POST['action'] ;
    	$id = $_POST['id'] ;
    	$fr_id = $_POST['fr_id'] ;
    	require_once("settings.php") ;
    	$dojob = $user->repondRequest($action , $id , $fr_id) ;
    	echo $dojob;
	}

	function undoRequest() 
	{
    	$id = $_POST['id'] ;
    	require_once("settings.php") ;
    	$dojob = $user->undoRequest($id) ;
    	echo $dojob;
	}

	function sendRequest() 
	{
		$rType = $_POST['rType'] ;
		$sendTo = $_POST['sendTo'] ;
		$requester = $_POST['requester'] ;
		$relation = $_POST['relation'] ;
    	require_once("settings.php") ;
    	$dojob = $user->sendRequest($rType , $sendTo , $requester , $relation) ;
    	echo $dojob;
	}

	function fetchComments()
	{
		$commentId = cleanText($_POST['commentId']) ;
    	require_once("settings.php") ;
		$dojob = $posts->fetchComments($commentId) ;
		// print_r($dojob) ;
	}

	function clearNotification()
	{
		$notifyId = getPost("notifyId") ;
		$returnUrl = getPost("returnUrl") ;
		$dataWrite = new DataWrite ;
		$connect = dbconnect() ;
		$dojob = $dataWrite->clearNotification($connect , $notifyId) ;
		if($dojob)
		{
			// redirect($returnUrl) ;
			echo $returnUrl;
		}
		else
		{
			echo "notifications.php" ;
		}
	}

	function addChurch()
	{
		// print_r($_POST) ;
		$dataWrite = new DataWrite ;
		$connect = dbconnect() ;
		$cName = getPost("fname") ;
		$admin = getPost("user_id") ;
		$denom = getPost("dob") ;
		$email = getPost("email") ;
		$country = getPost("country") ;
		$state = getPost("state") ;
		$city = getPost("city") ;
		$key = base64_encode("__key__") ;
		$social = cleanText(implode($key, $_POST['social'])) ;
		$activity = cleanText(implode($key, $_POST['activity'])) ;
		$times = cleanText(implode($key, $_POST['timesch'])) ;
		$map = cleanText(implode($key, $_POST['map'])) ;
		if(count($_FILES) == 0)
		{
			$filename = "No file" ;
		}
		else{
			$filename = "file.file" ;
		}
		
		$dojob = $dataWrite->addChurch($connect , $cName , $admin , $denom , $email , $country , $state , $city , $social , $activity , $times , $map , $filename) ;
		if($dojob > 0)
		{
			redirect("../church-details.php?church=".$cName."20%&sem&20%&wh=".urltxtmix($dojob)) ;
		}
		else{
			session_start() ;
			$_SESSION['return'] = "Sorry please try again!<br>".$dojob ;
			redirect("../profile-setting.php?section=add-church") ;
		}
	}

	function uploadPP()
	{
		$connect = dbconnect() ;
		$dataWrite = new DataWrite ;
		$file = $_FILES['pp'] ;
		$user = getPost('user_id') ;
		$type = "pp" ;
		$pathToUpload = "../uploads/usersImages/profile/" ;
		$upload = uploadAuth($file , $pathToUpload) ;
		session_start();
		if(is_array($upload))
		{
			$filename = explode("../", $upload['filename'])[1];
			$dojob = $dataWrite->imageFileSave($connect , $filename , $user , $type) ;
			if($dojob){
				$_SESSION['retPP'] = "Profile picture uploaded successfully!" ;
				redirect("../../profile-setting.php?section=profile-photo") ;
			}
			else
			{
				$_SESSION['retPP'] = "You file could not be uploaded!" ;
				redirect("../../profile-setting.php?section=profile-photo") ;
			}
		}
		else
		{
			$_SESSION['retPP'] = "Your file could not be uploaded!";
			redirect("../../profile-setting.php?section=profile-photo") ;
		}
	}

	function postTimeline()
	{
		$dataWrite = new DataWrite ;
		$connect = dbconnect() ;
		$poster = getPost('user_id') ;
		$permission = getPost('whoIs') ;
		$content = getPost('content') ;
		$debrief = substr($content, 0, 100) ;
		if(isset($_FILES['timelineImage']) && $_FILES['timelineImage']['error'] == 0)
		{
			$file = $_FILES['timelineImage'] ;
			$pathToUpload = "../uploads/timelineImages/" ;
			$upload = uploadAuth($file , $pathToUpload) ;
			if(is_array($upload))
			{
				$filename = explode("../", $upload['filename'])[1];
			}
			else
			{
				$filename = 'null' ;
			}
			
		}
		else
		{
			$filename = 'null' ;
		}
		$dojob = $dataWrite->postTimeline($connect , $poster , $permission , $content , $filename) ;
	}

	function postComment()
	{
		$dataWrite = new DataWrite ;
		$connect = dbconnect() ;
		$postId = getPost('toCommentId') ;
		$comment = getPost('comment') ;
		$poster = getPost('commenterId') ;
		$dojob = $dataWrite->postComment($connect , $postId , $comment , $poster) ;
		if($dojob){echo 1;}else{echo 0;}
	}

	function infinityPosts()
	{
		$dataRead = new DataRead ;
		$connect = dbconnect() ;
		$user_id = getPost('user_id') ;
		$offset = getPost('offset') ;
		$limit = getPost('limit') ;
		require_once('settings.php') ;
		$dojob = $posts->getPosts($user_id,$offset,$limit) ;
		echo $dojob;
	}

	function likeUnlike()
	{
		$connect = dbconnect() ;
		$dataWrite = new DataWrite ;
		$liker = getPost('liker') ;
		$which = getPost('which') ;
		$todo = getPost('todo') ;
		$likeness = Check_Like_Exists($connect , 'likes' , 'liker' , $liker , 'liked' , $which) ;
		$dojob = $dataWrite->likePost($connect , $liker , $which , $todo , $likeness) ;
		// echo $dojob;
		print_r($_POST);
	}

	function statusPost()
	{
		$con = dbconnect() ;
		$dataWrite = new DataWrite ;
		$owner = getPost('owner') ;
		$type = getPost('posTtype') ;
		if(isset($_FILES['status-file']))
		{
			$file = $_FILES['status-file'] ;
			$name = setNameExt(time() , $file) ;
			$pathToUpload = "../uploads/status/".$name ;
			$filetype = 'jpg|jpeg|gif|mp4|png|3gp' ;
			$fileUpload =  fileUpload($file , $pathToUpload , $filetype) ;
			if(!is_array($fileUpload))
			{
				$dojob = $dataWrite->saveStatus($con , $name , $owner , $type) ;
				if($dojob)
				{
					returnMsg('statusmsg' , 'Status Uploaded Successfully!') ;
					redirect('../home.php') ;
				}
				else{
					returnMsg('statusmsg' , 'Oops!. Something went wrong. Please try again') ;
					redirect('../home.php') ;
				}
			}
			else{
				returnMsg('statusmsg' , $fileUpload['msg']) ;
				redirect('../home.php') ;
			}
		}
		else{
			$bg = getPost('bgcolor') ;
			$txt = getPost('txtcolor') ;
			$text = getPost('text') ;
			$content = $bg.'=>'.$txt.'=>'.$text ;
			$dojob = $dataWrite->saveStatus($con , $content , $owner , $type) ;
			if($dojob)
			{
				returnMsg('statusmsg' , 'Status Uploaded Successfully!') ;
				redirect('../home.php') ;
			}
			else{
				returnMsg('statusmsg' , 'Oops!. Something went wrong. Please try again') ;
				redirect('../home.php') ;
			}
		}
	}

	function songBook()
	{
		$dataWrite = new DataWrite ;
		$con = dbconnect() ;
		$file = $_FILES['songbook'] ;
		$owner = getPost('owner') ;
		$filename = cleanSpecialCharacters(time().basename($file['name'])) ;
		$pathToUpload = '../uploads/song-books/'.$filename ;
		$name = substr($file['name'], 0,-4);
		$filetype = 'pdf' ;
		$fileUpload =  fileUpload($file , $pathToUpload , $filetype) ;
		if(!is_array($fileUpload)){
			redirect('../song-books.php') ;
			$dojob = $dataWrite->SaveSongBook($con , $name , $filename , $owner) ;
			if($dojob){
				returnMsg('sbmsg' , 'Upload Successful') ;
			}
			else{
				returnMsg('sbmsg' , 'Upload not Successful') ;
			}
		}
		else{
			returnMsg('sbmsg' , 'Not a Valid File') ;
		}
		redirect('../song-books.php') ;
	}


	function audiobible()
	{
		$dataWrite = new DataWrite ;
		$con = dbconnect() ;
		$file = $_FILES['audiobible'] ;
		$owner = getPost('owner') ;
		$filename = cleanSpecialCharacters(time().basename($file['name'])) ;
		$pathToUpload = '../uploads/audiobibles/'.$filename ;
		$name = substr($file['name'], 0,-4);
		$filetype = 'mp3' ;
		$fileUpload =  fileUpload($file , $pathToUpload , $filetype) ;
		if(!is_array($fileUpload)){
			redirect('../audio-bible.php') ;
			$dojob = $dataWrite->SaveAudioBible($con , $name , $filename , $owner) ;
			if($dojob){
				returnMsg('sbmsg' , 'Upload Successful') ;
			}
			else{
				returnMsg('sbmsg' , 'Upload not Successful') ;
			}
		}
		else{
			returnMsg('sbmsg' , 'Not a Valid File') ;
		}
		redirect('../audio-bible.php') ;
	}


	function addEvent()
	{
		$file = $_FILES['eventImage'] ;
		$filetype = 'jpg|jpeg|png|PNG|JPG|JPEG' ;
		$filename = setNameExt(cleanSpecialCharacters(time()) , $file) ;
		$pathToUpload = '../uploads/events/'.$filename ;
		$upload = fileUpload($file , $pathToUpload , $filetype) ;
		if(!is_array($upload)){
			$dataWrite = new DataWrite ;
			$con = dbconnect() ;
			$name = getPost('name') ;
			$loc = getPost('location') ;
			$desc = getPost('desc') ;
			$stime = getPost('e-start') ;
			$etime = getPost('e-stop') ;
			$uploader = getPost('uploader') ;
			$dojob = $dataWrite->saveEvent($con , $filename , $name , $loc , $desc , $uploader , $stime , $etime) ;
			if($dojob){
				returnMsg('eventMsg' , 'Event saved successfully') ;
				redirect('../events.php') ;
			}
			else{
				returnMsg('eventMsg' , mysqli_error($con)) ;
				redirect('../events.php') ;
			}
		}
	}

	function sendInvites()
	{
		$dataWrite = new DataWrite ;
		$con = dbconnect() ;
		$invitee = getPost('invitee') ;
		$event_id = getPost('event_id') ;
		$invited = $_POST['sendInvite'] ;
		// print_r($invited) ;
		$dojob = $dataWrite->sendInvites($con , $invited , $invitee , $event_id) ;
		if($dojob){
			returnMsg('invitesent' , 'Invitaions sent to '.count($invited).' people!') ;
		}
		else{
			returnMsg('invitesent' , 'Your request could not be completed, please try again') ;
		}
		redirect('../events.php') ;
	}

	function respondInvite()
	{
		$dataWrite = new DataWrite ;
		$con = dbconnect() ;
		require_once 'settings.php' ;
		$user = $_SESSION['user_id'] ;
		$todo = $_GET['todo'] ;
		$which = $_GET['eid'] ;
		$dojob = $dataWrite->respondInvite($con , $user , $todo , $which) ;
		redirect('../../../events.php') ;
	}

	function sendMsg()
	{
		$dataWrite = new DataWrite ;
		$con = dbconnect() ;
		$sid = getPost('sender') ;
		$rid = getPost('receiver') ;
		$msg = getPost('message') ;
		$type = 0 ;
		$dojob = $dataWrite->saveMsg($con , $sid , $rid , $msg , $type) ;
		if($dojob){
			echo 1;
		}else{
			echo mysqli_error($con);
			// redirect('../chat.php') ;
		}
	}

	function searchChurch()
	{
		$con = dbconnect() ;
		$dataRead = new DataRead ;
		$param = getPost('param') ;
		$val = getPost('searchVal') ;
		$dojob = $dataRead->searchChurch($con , $param , $val) ;
		switch ($param) {
			case '1':
				$param = 'in Location' ;
				break;
			case '2':
				$param = 'with Name' ;
				break;
			case '3':
				$param = 'in Denomination' ;
				break;
			
			default:
				# code...
				break;
		}
		if(!$dojob)
		{
			showAlert('success' , 'Nothing returned for churches '.$param.' '.ucwords($val)) ;
		}else{
			if(numQuery($dojob) == 0){
				showAlert('success' , 'Nothing returned for churches '.$param.' '.ucwords($val)) ;
			}else{
				foreach ($dojob as $row) {
				?>
					<div class="col-lg-3 col-md-4 col-sm-6">
						<div class="company_profile_info">
							<div class="company-up-info">
								<img src="images/church.png" class="img img-responsive" height="90" width="90" alt="">
								<h3><?= $row['church_name'] ?>.</h3>
								<h4><i class="fa fa-map-signs"></i> <?= $row['city'].", ".$row['country']; ?></h4>
								<ul>
									<li><a href="#" title="" class="follow">Get Directions</a></li>
									<!-- <li><a href="#" title="" class="message-us"><i class="fa fa-envelope"></i></a></li> -->
								</ul>
							</div>
							<a href="church-details.php?church=<?= urlencode($row['church_name']).'+'.urlencode($row['city']).'+'.urlencode($row['state']).'+'.urlencode($row['country']).'20%&sem&20%&wh='.urltxtmix($row['id']) ;?>" title="" class="view-more-pro">Check Activities</a>
						</div><!--company_profile_info end-->
					</div>
				<?php
				}
			}
		}
	}

	function navSearch()
	{
		$con = dbconnect() ;
		$dataRead = new DataRead ;
		$id = getPost('uID') ;
		$navSearch = getPost('navSearch') ;
		$dojob = $dataRead->navSearch($con , $navSearch , $id) ;
	?>
		<ul class="list-group">
	<?php
		if(!$dojob || numQuery($dojob) == 0){
			echo "string";
			echo '<li class="list-group-item d-flex justify-content-between align-items-center">No one matches your search</li>';
		}else{
			$wrong = base64_encode(time());
			foreach ($dojob as $row) {
				$name = ucfirst($row['firstname'])." ".ucfirst($row['lastname']) ;
	?>
				<li class="list-group-item d-flex justify-content-between align-items-center"><a href="profiles.php?f=<?= $name; ?>&qurt=<?= urltxtmix($wrong); ?>&mdi=<?= urltxtmix($row['user_id']); ?>"><?= $name ?></a></li>
	<?php
			}
		}
	?>
		</ul>
	<?php
	}

	function sendReport()
	{
		$con = dbconnect() ;
		$dataWrite = new DataWrite ;
		$table = getPost('table') ;
		$postId = getPost('postid') ;
		$user = getPost('userId') ;
		$dojob = $dataWrite->sendReport($con , $table , $postId , $user) ;
		echo $dojob;
	}

	function replyReports()
	{
		$con = dbconnect() ;
		$dataWrite = new DataWrite ;
		$table = getPost('tableRep') ;
		$postId = getPost('targId') ;
		$todo = getPost('todoRep') ;
		$dojob = $dataWrite->replyReports($con , $table , $postId , $todo) ;
		echo $dojob;
	}



	// function acceptRequet()
	// {
	// 	$id = $_POST['id'] ;
	// }













	function add_examiner()
	{
		$connect = dbconnect() ;
		$dataRead = new DataRead ;
		$dataWrite = new DataWrite ;
		
		$contact = $_POST['contact'] ;
		$fname = $_POST['fname'] ;
		$error_check = isset($contact) && $contact !== "" && isset($fname) && $fname !== "" ;
		if($error_check)
		{
			$password = passwordHash("password") ;//hash("sha256" , "password1234") ;
			$do_job = $dataWrite->add_examiner($connect , $contact , $fname  , $password) ;
			if($do_job)
			{
				echo $do_job ;
			}
			else{
				echo $do_job ;//"Could not regiser examiner ".$fname." seems this email is already in use" ;
			}
		}
		else
		{
			echo "Some fields seems to be empty" ;
		}
	}
	
	function xam_create()
	{
		$connect = dbconnect() ;
		$dataWrite = new DataWrite ;
		
		$subject = $_POST['subject'] ;
		$klass = $_POST['klass'] ;
		$type = $_POST['type'] ;
		$pword = passwordHash($_POST['pword']) ;
		
		$do_job = $dataWrite->xam_create($connect , $subject , $klass , $type , $pword) ;
		if($do_job)
		{
			// echo ;
		}
	}

	function regUser()
	{
		$dataWrite = new DataWrite() ;
		$connect = dbconnect() ;
		$username = $_POST['username'] ;
		$course = $_POST['course'] ;
		$examCode = $_POST['examCode'] ;
		$matricNo = $_POST['matricNo'] ;
		$started = time() ;
		$do = $dataWrite->regUser($connect , $username , $course , $matricNo , $started) ;
		if($do != 0)
		{
			$query = mysqli_query($connect , "SELECT * FROM settings WHERE exam_code = '$examCode' LIMIT 1") ;
			if(numQuery($query) == 1)
			{
				session_start() ;
				$_SESSION['user_id'] = $do ;
				$_SESSION['examCode'] = mysqli_fetch_assoc($query) ;
				echo $do;
				// print_r(mysqli_fetch_assoc($query));
			}
			else
			{
				echo -1 ;
			}
			
		}
		else
		{
			echo $do;
		}
		
	}

	function solved()
	{
		$dataWrite = new DataWrite() ;
		$connect = dbconnect() ;
		$answers = $_POST['answers'] ;
		$correctAnswers = $_POST['correctAnswers'] ;
		$course = $_POST['course'] ;
		$user = $_POST['user'] ;
		$key = $_POST['key'] ;
		$ans_arr = explode($key, $answers) ;
		$corans_arr = explode($key, $correctAnswers) ;
		$score = 0 ;
		$total = count($ans_arr) - 1 ;
		// echo $ans_arr[6];
		for ($i=1; $i <= $total ; $i++) { 
			if (trim(passwordHash($ans_arr[$i])) == trim($corans_arr[$i])) {
				$score += 1 ;
			}
		}
		$percent = round(($score/$total)*100) ;
		$do = $dataWrite->solved($connect , $answers , $correctAnswers , $user , $score , $percent , $course) ;
		if($do = 1)
		{
			echo $score."et".$percent."%";
		}
		else
		{
			echo $do;
		}
	}

	function enterQuestions()
	{
		$connect = dbconnect() ;
		$dataWrite = new DataWrite() ;
		$question = $_POST['question'] ;
		$user = $_POST['user'] ;
		$course = $_POST['course'] ;
		$test = $_POST['test'] ;
		$key = $_POST['key'] ;
		$eachQue = explode($key, $question) ;
		$que = cleanText($connect , $eachQue[0]) ;
		$a = cleanText($connect , $eachQue[1]) ;
		$b = cleanText($connect , $eachQue[2]) ;
		$c = cleanText($connect , $eachQue[3]) ;
		$d = cleanText($connect , $eachQue[4]) ;
		// $e = cleanText($connect , $eachQue[5]) ;
		$ans = cleanText($connect , $eachQue[5]) ;
		$img = cleanText($connect , $eachQue[6]) ;
		// $check = substr_count($question, $ans) ;
		// if($ans == $a){echo "string ".$check;}else{echo "stringer ".$check;}
		// echo strlen($ans)." ".strlen($a) ;
		//echo $check; //print_r($eachQue) ; //echo $question ;//." <br> ".$eachQue;
		if($ans == $a || $ans == $b || $ans == $c || $ans == $d)
		{
			$do = $dataWrite->enterQuestions($connect , $user , $course , $que , $a , $b , $c , $d , $test , $ans , $img) ;
			echo $do;
		}
		else
		{
			echo 3;
			// return ;
		}
		//3 === ans error
		
		
	}

	function setQues()
	{
		$connect = dbconnect() ;
		$dataWrite = new DataWrite() ;
		$user = $_POST['user'] ;
		$course = $_POST['course'] ;
		$test = $_POST['test'] ;
		$que = cleanText($connect , $_POST['question']) ;
		$a = cleanText($connect , $_POST['opta']) ;
		$b = cleanText($connect , $_POST['optb']) ;
		$c = cleanText($connect , $_POST['optc']) ;
		$d = cleanText($connect , $_POST['optd']) ;
		// $e = cleanText($connect , $_POST['opte'])) ;
		$ans = cleanText($connect , $_POST['answer']) ;
		$img = cleanText($connect , $_POST['image']) ;
		if($ans == $a || $ans == $b || $ans == $c || $ans == $d)
		{
			$do = $dataWrite->enterQuestions($connect , $user , $course , $que , $a , $b , $c , $d , $test , $ans , $img) ;
			if($do)
			{
				echo $do;
			}
			else
			{
				echo $do;
			}
		}
		else
		{
			echo 3;
		}
		//3 === ans error	
	}

	function checkAvailable()
	{
		$connect = dbconnect() ;
		$dataRead = new DataRead() ;
		$val = $_POST['val'] ;
		$do = $dataRead->checkAvailable($connect , $val) ;
		echo $do ;
	}

	function saveCode()
	{
		$connect = dbconnect() ;
		$dataWrite = new DataWrite() ;
		$code = $_POST['code'] ;
		$xamTime = $_POST['xamTime'] ;
		$xamQues = $_POST['xamQues'] ;
		$test = $_POST['test'] ;
		$do = $dataWrite->saveCode($connect , $code , $xamTime , $xamQues , $test) ;
		echo $do;
	}
}
//end of actionmanager class


?>


