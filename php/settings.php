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

	/**
	 * 
	 */
	class User
	{
		function getAccess($user_id)
		{ //0-superAdmin //1-admin //2-user
			$con = dbconnect() ;
			$query = $con->query("SELECT access FROM members WHERE id = $user_id ") ;
			if($query){
				return $query ;
			}else{
				return sqlError($con) ;
			}
		}

		function getUserDet($user_id)
		{
			$connect = dbconnect() ;
			$query = $connect->query("SELECT * FROM users WHERE user_id = '$user_id' ") ;
			if($query)
			{
				$fetch = fetcher($query) ; //mysqli_fetch_assoc($query) ;
			}
			return  $fetch ;
		}

		function getUserPicture($limit , $userID)
		{
			// $user = $_SESSION['user_id'] ;
			$connect = dbconnect() ;
			$query = $connect->query("SELECT * FROM userimages WHERE user_id = '$userID' ORDER BY uploaded_on DESC LIMIT $limit ") ;
			if($query)
			{
				if(numQuery($query) != 0)
				{
					foreach ($query as $row) {
						if(stripos($_SERVER['PHP_SELF'], "profiles.php") != false)
						{
				?>
							<div class="col-lg-4 col-md-4 col-sm-4 col-6">
								<div class="gallery_pt">
									<img src="<?= $row['filename']; ?>" alt="" width="271" height="174">
									<a href="#" title=""><img src="images/all-out.png" alt=""></a>
								</div><!--gallery_pt end-->
							</div>
				<?php
						}
						else
						{
				?>
							<div class="col-lg-4 col-md-6 col-sm-12 col-12 pd-l-1">
	  							<div class="card-box">
	  								<img src="<?= $row['filename']; ?>" class="img img-responsive img-thumbnail" alt="">
	  							</div>
		  					</div>
				<?php
						}
					}
				}
				else
				{
					showAlert("info" , "You have not uploaded any picture here") ;
				}
			}
			else
			{
				showAlert("warning" , mysqli_error($connect)) ;
			}
		}

		function getImage($user_id)
		{
			$query = dbconnect()->query("SELECT filename FROM userimages WHERE user_id = '$user_id' ORDER BY uploaded_on DESC ");
			if($query && numQuery($query) != 0)
			{
				print_r(fetcher($query)['filename']);
			}
			else{
				echo "images/users/user2.jpg";
			}
		}

		function retImage($user_id)
		{
			$query = dbconnect()->query("SELECT filename FROM userimages WHERE user_id = '$user_id' ORDER BY uploaded_on DESC ");
			if($query && numQuery($query) != 0)
			{
				return fetcher($query)['filename'];
			}
			else{
				echo "images/users/user2.jpg";
			}
		}
		/*function getUserStatus($user)
		{
			$connect = dbconnect() ;
			$query = mysqli_query($connect , "SELECT login, logout FROM profiles WHERE user_id = '$user' ") ;
			$return = while ($fetch = fetcher($query)) {
				
			}
		}*/

		function getFriendIds($user_id)
		{
			$connect = dbconnect() ;
			$query1 = mysqli_query($connect , "SELECT user2 user FROM relationship WHERE user1 = '$user_id' AND status = '1' ") ;
			$query2 = mysqli_query($connect , "SELECT user1 user FROM relationship WHERE user2 = '$user_id' AND status = '1' ") ;
			if($query1 && $query2)
			{
				$return = array() ;
				while ($fetch2 = mysqli_fetch_assoc($query2)) {
					$return2 = array_push($return, $fetch2['user']) ;
				}
				while ($fetch1 = mysqli_fetch_assoc($query1)) {
					$return1 = array_push($return, $fetch1['user']) ;
				}
				if(count($return) != 0)
				{
					$connect = dbconnect() ;
					$ids = implode(",", $return) ;
					return $ids ;
				}
			}
		}

		function getFriends($user_id)
		{
			$connect = dbconnect() ;
			$ids = $this->getFriendIds($user_id) ;
			$dataRead = new DataRead ;
			$query = $dataRead->getMultipleUsers($connect , $ids) ;
			if($query)
			{
				return $query ;
				// return fetcher($query) ;	
			}
			else
			{
				return null ;
			}
		}

		function getFriendsRequests($user_id)
		{
			$connect = dbconnect() ;
			$query1 = mysqli_query($connect , "SELECT user2 user FROM relationship WHERE user1 = '$user_id' AND status = '0' AND requester != '$user_id' ") ;
			$query2 = mysqli_query($connect , "SELECT user1 user FROM relationship WHERE user2 = '$user_id' AND status = '0' AND requester != '$user_id' ") ;
			if($query1 && $query2)
			{
				$return = array() ;
				while ($fetch2 = mysqli_fetch_assoc($query2)) {
					$return2 = array_push($return, $fetch2['user']) ;
				}
				while ($fetch1 = mysqli_fetch_assoc($query1)) {
					$return1 = array_push($return, $fetch1['user']) ;
				}
				return $return ;
			}
			else
			{
				return array('error' => mysqli_error($connect))  ;
			}
		}

		function repondRequest($action , $id , $fr_id)
		{
			$connect = dbconnect() ;
			$query = mysqli_query($connect , "UPDATE relationship SET status = '$action' WHERE (user1 = '$id' AND user2 = '$fr_id') OR (user1 = '$fr_id' AND user2 = '$id') ") ;
			if($query)
			{
				return true ;
			}
			else
			{
				// return false ;
				return mysqli_error($connect) ;
			}
		}

		function undoRequest($id)
		{
			$connect = dbconnect() ;
			$query = mysqli_query($connect , "UPDATE relationship SET status = '3' WHERE id = '$id' ") ;
			if($query)
			{
				return true ;
			}
			else
			{
				// return false ;
				return mysqli_error($connect) ;
			}
		}

		function sendRequest($rType , $sendTo , $requester , $relation)
		{
			$connect = dbconnect() ;
			$dataWrite = new DataWrite ;
			$query  = $dataWrite->sendRequest($connect , $rType , $sendTo , $requester , $relation) ;
			// return $query ;
			if($query)
			{
				return "Request Sent" ;
			}
			else{
				return "Send Request" ;
			}
		}

		function getNotifications($limit)
		{
			$connect = dbconnect() ;
			$owner = $_SESSION['user_id'] ;
			$query = mysqli_query($connect , "SELECT * FROM notifications WHERE owner_id = '$owner' AND status = '0' ") ;
			if($query)
			{
				?>
				<div class="sd-title bg-purple">
					<h3>Notifications</h3>
					<span class="badge badge-dark pull-right"><?= numQuery($query); ?></span>
				</div><!--sd-title end-->
				<div class="suggestions-list">
				<?php
				if(numQuery($query) == 0)
				{
				?>
					<div class="suggestion-usd">
								<!-- <img src="images/users/user1.jpg" alt="" height="35" width="35"> -->
								<div class="sgt-text">
									<?= showAlert("info" , "You have no notifications at this moment"); ?>
									<!-- <h4><a href="#" class="text-dark clearNotification"></a></h4> -->
									<!-- $row['notified'] -->
								</div>
								<!-- <span title="remove" class="viewStatus" status-target="#gallery"><i class="fa fa-eye"></i></span> -->
							</div>
				<?php
				}
				foreach ($query as $row)
				{ 
				?>
							<div class="suggestion-usd">
								<img src="images/users/user1.jpg" alt="" height="35" width="35">
								<div class="sgt-text">
									<h4><a href="<?= base64_decode($row['link']); ?>" class="text-dark clearNotification" notifyId="<?= $row['id']; ?>" target="_blank"><?= $row['message']; ?></a></h4>
									<span><?= getLastSeen($row['notified'] , time()); ?> ago</span>
									<!-- $row['notified'] -->
								</div>
								<!-- <span title="remove" class="viewStatus" status-target="#gallery"><i class="fa fa-eye"></i></span> -->
							</div>
				<?php
				}
				if(numQuery($query) > 5){
				?>
					<div class="view-more">
						<a href="#" title="">View More</a>
					</div>
				<?php } ?>
				</div><!--suggestions-list end-->
					<?php
				
			}
		}

		function getReports()
		{
			$con = dbconnect() ;
			$query = $con->query("SELECT * , r.id AS user_id , p.id AS post_id , r.id rid FROM reports r JOIN posts p ON(r.which = p.id) WHERE r.status = '0' ") ;
			if($query){
				// return $query ;
				$posts = new Posts ;
				foreach ($query as $row) {
					// print_r($row) ;
					$userimage = $this->retImage($row['reported_by']) ;
					$reporter = $this->getUserProfile($row['reported_by']) ;
					$reporter = ucwords($reporter['lastname']." ".$reporter['firstname']) ;
		?>
				<div class="request-details">
  					<div class="noty-user-img">
  						<img src="<?= $userimage ?>" alt="" height="35" width="35">
  					</div>
  					<div class="request-info">
  						<h3><?= $reporter ?></h3>
  						<!-- <span>Graphic Designer</span> -->
  					</div>
  					<div class="accept-feat">
  						<ul>
  							<li><button type="button" class="accept-req" target="#report<?= $row['rid'] ?>">View</button></li>
  							<li><button type="button" class="accept-req" style="display: none;">Collapse</button></li>
  							<!-- <li><button type="submit" class="close-req"><i class="la la-close"></i></button></li> -->
  						</ul>
  					</div><!--accept-feat end-->
  					<div class="card text-center mt-5">
  						<!-- <img class="card-img-top" src="images/bibles/english.png" alt="Card image cap"> -->
  						<div class="card-body" id="report<?= $row['rid'] ?>" style="display: none;">
  							<h4 class="card-title">Report in posts</h4>
  							<?= $posts->getSinglePost($row['which']) ?>
  							<!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
  							<!-- 1-fit 0-unaddresses 2-block 3-seen -->
  							<a href="#" class="btn btn-primary reportAction" todo='2' tab="<?= $row['col'] ?>" targId="<?= $row['which'] ?>">Block Post</a>
  							<a href="#" class="btn btn-primary reportAction" todo='3' tab="<?= $row['col'] ?>" targId="<?= $row['which'] ?>">Do Nothing</a>
  						</div>
  					</div>
  				</div><!--request-detailse end-->
		<?php
				}
			}else{
				return sqlError($con) ;
			}
		}
	}

	/**

	<li><a href="#" title="" class="follow bg-purple" other="#respond2<?php echo $friend_id; ?>" id="respond1<?php echo $friend_id; ?>" onclick="repondRequest(1,<?php echo $friend_id; ?>,'respond1<?php echo $friend_id; ?>')"><i class=""></i> Accept</a></li>
												<li><a href="#" title="" class="follow bg-danger" other="#respond1<?php echo $friend_id; ?>" id="respond2<?php echo $friend_id; ?>" onclick="repondRequest(2,<?php echo $friend_id; ?>,'respond2<?php echo $friend_id; ?>')"><i class=""></i> Decline</a></li>
	 * 
	 */
	class Relationship
	{
		//0->pending
		//1->accepted
		//2->decline
		//3->undo request
		function getBtn($id)
		{
			$user_id = $_SESSION['user_id'] ;
			$connect = dbconnect() ;
			$query = mysqli_query($connect , "SELECT * FROM relationship WHERE (user1 = '$id' AND user2 = '$user_id') OR (user2 = '$id' AND user1 = '$user_id') LIMIT 1 ") ;
			if($query && numQuery($query) != 0)
			{
				$fetch = fetcher($query) ;
				$status = $fetch['status'] ;
				$requester = $fetch['requester'] ;
				switch ($status) {
					case '0': //pending
						if($requester == $user_id){
							?>
								<li><a href="#" title="" class="follow btn bg-danger undoRequest" id="undo<?= $fetch['id']; ?>" related-with="<?= $fetch['id']; ?>">Undo Request</a></li>
							<?php
						}
						else
						{
							?>
								<li><a href="#" title="" class="follow bg-purple" other="#respond2<?= $id ?>" id="respond1<?= $id; ?>" onclick="repondRequest(1,<?= $id; ?>,'respond1<?= $id; ?>')"><i class=""></i> Accept</a></li>
												<li><a href="#" title="" class="follow bg-danger" other="#respond1<?= $id; ?>" id="respond2<?= $id; ?>" onclick="repondRequest(2,<?= $id; ?>,'respond2<?= $id; ?>')"><i class=""></i> Decline</a></li>
							<?php
						}
						
						break;
					case '1': //accepted
						?> <li><a href="#" title="" class="follow"><i class="fa fa-check"></i> Friends</a></li> <?php
						break;
					case '3': //undone
						?> <li><a href="#" title="" class="follow bg-info sendRequest" id="sendId<?= $id; ?>" r-type="redo" sent-to="<?= $id ?>" requester="<?= $user_id; ?>" related-with="<?= $fetch['id']; ?>">Send Request</a></li> <?php
						break;
					case '2': //declined
						?> <li><a href="#" title="" class="follow bg-info sendRequest" id="sendId<?= $id; ?>" r-type="redo" sent-to="<?= $id ?>" requester="<?= $user_id; ?>" related-with="<?= $fetch['id']; ?>">Send Request</a></li> <?php
						break;
					
					default:
						?> <li><a href="#" title="" class="follow"><i class="fa fa-check"></i> Friends</a></li> <?php
						break;
				}
			}
			else
			{
				?> <li><a href="#" title="" class="follow bg-info sendRequest" id="sendId<?= $id; ?>" r-type="addNew" sent-to="<?= $id ?>" requester="<?= $user_id; ?>" related-with="<?= $id; ?>">Send Request</a></li> <?php
			}
			// return $btn ;
		}


		function suggestedFriends($user_id)
		{
			$con = dbconnect() ;
			$user = new User ;
			$ids = $user->getFriendIds($user_id) ;
			if(strlen($ids) == 0){$ids = $user_id ;}else{$ids = $ids.','.$user_id;}
			$thisUser = $user->getUserProfile($user_id) ;
			$church = $thisUser['church_name'] ;
			$city = $thisUser['city'] ;
			$country = $thisUser['country'] ;
			$state = $thisUser['state'] ;
			$idsArray = explode(',', $ids) ;
			$ff = array() ;
			foreach ($idsArray as $key) {
				$ffids = explode(',', $user->getFriendIds($key)) ;
				array_push($ff, $ffids) ;
			}
			$ffNum = count($ff) ;
			$mine = $ff[$ffNum-1]; //my own frineds ;
			array_push($mine, $user_id) ;
			$newwine = array() ;
			for ($i=$ffNum-2; $i >= 0 ; $i--) { 
				$newwine = array_merge($newwine,$ff[$i]) ;
			}
			$newwine = array_diff($newwine, $mine) ;
			if(count($newwine) == 0){$newwine = array(0);}
			$ffidstr = implode(',', $newwine) ;
			$query = $con->query("SELECT * FROM profiles WHERE user_id NOT IN ($ids) AND (church_name = '$church' OR city = '$city') OR user_id IN ($ffidstr) OR user_id NOT IN ($ids) ");//OR state = '$state' OR country = '$country' OR user_id IN (SELECT user_id FROM members WHERE ) ") ;
			if($query){
				/*while($fetch = mysqli_fetch_assoc($query))
				{
					print_r($fetch) ;
				}	*/
				return $query ;
			}else{return mysqli_error($con);}
		}

		/*function getAllMembersNotFriends($user_id)
		{
			$con = dbconnect() ;
			$user = new User ;
			$ids = $user->getFriends($user_id) ;
			if(strlen($ids) == 0){$ids = $user_id ;}else{$ids = $ids.','.$user_id;}
			$query = $con->query("SELECT * FROM profiles WHERE user_id NOT IN ($ids) ") ;
			if($query){
				return $query ;
			}else{return mysqli_error($con);}
		}*/

		function undoRequest()
		{
			echo "string";
		}

		function notFriends($user_id)
		{
			$connect = dbconnect() ;
			$query1 = mysqli_query($connect , "SELECT user2 user FROM relationship WHERE user1 = '$user_id' AND () ") ;
			$query2 = mysqli_query($connect , "SELECT user1 user FROM relationship WHERE user2 = '$user_id' ") ;
			if($query1 && $query2)
			{
				$return = array() ;
				while ($fetch2 = mysqli_fetch_assoc($query2)) {
					$return2 = array_push($return, $fetch2['user']) ;
				}
				while ($fetch1 = mysqli_fetch_assoc($query1)) {
					$return1 = array_push($return, $fetch1['user']) ;
				}
				if(count($return) != 0)
				{
					$connect = dbconnect() ;
					$ids = implode(",", $return) ;
					$query = mysqli_query($connect , "SELECT * FROM profiles WHERE user_id NOT IN ($ids) ") ;
					if($query)
					{
						return $query ;
					}
					else
					{
						return false ;
					}
				}
			}
			// return $query ;
		}

		function sendRequest($rType , $sendTo , $requester , $relation)
		{
			
		}
	}

	/**
	 * 
	 */
	class Posts
	{
		//policy -> public - 0 friends - 1
		function getPosts($user_id,$offset,$limit)
		{
			$connect = dbconnect() ;
			$user = new User ;
			$ids = $user->getFriendIds($user_id) ;
			$ids = $ids.','.$user_id ;
			$query = $connect->query("SELECT * FROM posts WHERE (policy = '0' OR posted_by IN ($ids)) AND status = '1' ORDER BY posted DESC LIMIT $limit OFFSET $offset ") ;
			// $query = $connect->query("SELECT * FROM posts p LEFT OUTER JOIN reports r ON(p.id = r.which) WHERE (policy = '0' OR posted_by IN ($ids)) AND p.status = '1' AND r.status = 0 ORDER BY posted DESC LIMIT $limit OFFSET $offset ") ;
			if($query)
			{
				foreach ($query as $row) {
					$thisPost = $row['id'] ;
					$poster = $user->getUserProfile($row['posted_by']) ;
					$posterName = $poster['lastname']." ".$poster['firstname'] ;
					$postImage = $row['image'] ;
					$like = Check_Like_Exists2($connect , 'likes' , 'liker' , $user_id , 'liked' , $row['id']) ;
					if($like == 0){
						$likeClass1 = 'likeme liked' ;
						$likeClass2 = 'fa fa-heart' ;
					}else{
						$likeClass1 = 'likeme' ;
						$likeClass2 = 'fa fa-heart-o' ;
					}
					
				?>
					<div class="post-container" id="posty<?= $row['id']; ?>">
						<div class="post-bar">
							<div class="post_topbar">
								<div class="usy-dt">
									<img src="<?php $user->getImage($poster['id']); ?>" class="img-fluid" width="50" height="50" alt="">
									<div class="usy-name">
										<h3><?= $posterName; ?></h3>
										<span><img src="images/clock.png" alt="">
											<?php
												$ago = getLastSeen($row['posted'] , time());
												$check = explode(" ", $ago) ;
												if($check[1] == "days" && $check[0] >= '3')
												{
													$ago = getDateWord($row['posted_on'] , "dS M, Y h:ia") ;
												}
												else{$ago = $ago." ago";}
												$post = $row['post'];
												echo $ago;
												//check the string returned filter it and get your number for the xdays ago returned and read another date fromat if its greater than 3 if two echo yesteday at a time else eho date-th month and year
											?></span>
									</div>
								</div>
								<div class="ed-opts">
									<a href="#" title="" class="ed-opts-open"><i class="la la-ellipsis-v"></i></a>
									<ul class="ed-options">
										<li><a href="#" title="" class="blockPost" postId="<?= $row['id'] ?>">Block</a></li>
										<li><a href="#" title="" class="hidePost" postId="<?= $row['id'] ?>">Hide</a></li>
										<li><a href="#" title="" class="reportPost sendReport" tab="posts" postId="<?= $row['id'] ?>">Report</a></li>
									</ul>
								</div>
							</div>
							<div class="epi-sec">
								<ul class="descp">
									<li><img src="images/icon9.png" alt=""><span><?= $poster['country']; ?></span></li>
								</ul>
							</div>
							<div class="job_descp">
								<ul class="job-dt">
									<?php
										if($postImage != 'null')
										{
									?>
										<li><img src="<?= $postImage; ?>" class="img-fluid img-thumbnail" style="height: 200px !important;" /></li>
									<?php
										}
									?>
								</ul>
								<p><span><?= substr($post, 0,100) ?></span> 
									<?php
										if(strlen($post) > 100)
										{
									?><a href="#" class="viewMoreOfPost" onclicks="showMore('#moreOfPost<?= $row['id'] ?>')" title="">view more</a><span id="moreOfPost<?= $row['id'] ?>" style="display: none;"><?= substr($post, 100) ?></span></p>
								<?php } ?>
							</div>
							<div class="job-status-bar">
								<ul class="like-com">
									<li><a href="#" title="" post-id="<?= $row['id']; ?>" class="com" id="comment<?= $row['id'] ?>" append-to="posty<?= $row['id']; ?>"><img src="images/com.png" alt=""> Comment <?= numQuery($connect->query("SELECT * FROM comments WHERE post_id = '$thisPost' ")); ?></a></li>
								</ul>
								<a class="<?= $likeClass1 ?>" which="<?= $row['id'] ?>" style="*float: left;"><i class="<?= $likeClass2 ?>" style="font-size: 15px; font-style: bolder;"></i> <span><?= numQuery($connect->query("SELECT * FROM likes WHERE liked = '$thisPost' AND status = 1 ")); ?></span></a>
							</div>
							<div class='comment-section d-none' id="cs<?= $row['id']; ?>">
							</div>
						</div>
					</div>
				<?php
				}
			}
			else
			{
				showAlert("info" , "You have no posts in your timeline <a href='friends.php'>Add Friends</a> to stay up to date with them.") ;
				// echo sqlError($connect) ;
			}
		}

		function getSinglePost($id)
		{
			$connect = dbconnect() ;
			$user = new User ;
			$query = $connect->query("SELECT * FROM posts WHERE id = '$id' LIMIT 2 ") ;
			if($query){
				$row = fetcher($query) ;
				$thisPost = $row['id'] ;
				$poster = $user->getUserProfile($row['posted_by']) ;
				$posterName = $poster['lastname']." ".$poster['firstname'] ;
				$postImage = $row['image'] ;
		?>
				<div class="post-container" id="posty<?= $row['id']; ?>">
					<div class="post-bar">
						<div class="post_topbar">
							<div class="usy-dt">
								<img src="<?php $user->getImage($poster['id']); ?>" class="img-fluid" width="50" height="50" alt="">
								<div class="usy-name">
									<h3><?= $posterName; ?></h3>
									<span><img src="images/clock.png" alt="">
										<?php
											$ago = getLastSeen($row['posted'] , time());
											$check = explode(" ", $ago) ;
											if($check[1] == "days" && $check[0] >= '3')
											{
												$ago = getDateWord($row['posted_on'] , "dS M, Y h:ia") ;
											}
											else{$ago = $ago." ago";}
											$post = $row['post'];
											echo $ago;
										?></span>
								</div>
							</div>
						</div>
						<div class="epi-sec">
							<ul class="descp">
								<li><img src="images/icon9.png" alt=""><span><?= $poster['country']; ?></span></li>
							</ul>
						</div>
						<div class="job_descp">
							<ul class="job-dt">
								<?php
									if($postImage != 'null')
									{
								?>
									<li><img src="<?= $postImage; ?>" class="img-fluid img-thumbnail" style="height: 200px !important;" /></li>
								<?php
									}
								?>
							</ul>
							<p><span><?= substr($post, 0,100) ?></span> 
								<?php
									if(strlen($post) > 100)
									{
								?><a href="#" class="viewMoreOfPost" onclicks="showMore('#moreOfPost<?= $row['id'] ?>')" title="">view more</a><span id="moreOfPost<?= $row['id'] ?>" style="display: none;"><?= substr($post, 100) ?></span></p>
							<?php } ?>
						</div>
					</div>
				</div>
		<?php
			}else{
				echo "No post";
			}
		}

		function fetchComments($commentId)
		{
			$dataRead = new DataRead ;
			$query = $dataRead->fetchComments($commentId) ;
			if($query)
			{
				$user = new User ;
				$commenter = $user->getUserProfile($_SESSION['user_id']) ;
				$commenterName = $commenter['lastname']." ".$commenter['firstname'] ;
			?>
				Comments<hr>
					<div class='plus-ic hide-comment' to-hide="#cs<?= $commentId; ?>">
						<i class='la la-minus' title="minimize"></i>
					</div>
				<div id="tweakedCommentDiv<?= $commentId; ?>">
			<?php
				foreach ($query as $row) {
				$poster = $user->getUserProfile($row['comment_by']) ;
				$posterName = $poster['lastname']." ".$poster['firstname'] ;
				?>
					
					<div class='comment-sec'>
						<ul>
							<li>
								<div class='comment-list'>
									<div class='bg-img'>
										<img src='<?php $user->getImage($commenter['id']); ?>' alt='' height="40" width="40">
									</div>
									<div class='comment'>
										<h3><?= $posterName; ?></h3>
										<span><img src='images/clock.png' alt=''> <?= getLastSeen($row['commented'] , time()); ?> ago</span>
										<p><?= $row['comment']; ?></p>
										<!-- <a href='#' title='' class='active'><i class='fa fa-reply-all'></i>Reply</a> -->
									</div>
								</div><!--comment-list end-->
								<ul class="d-none">
									<li>
										<div class='comment-list'>
											<div class='bg-img'>
												<img src='images/users/user2.jpg' alt='' height="40" width="40">
											</div>
											<div class='comment'>
												<h3>John Doe</h3>
												<span><img src='images/clock.png' alt=''> 3 min ago</span>
												<p>Hi John </p>
												<a href='#' title=''><i class='fa fa-reply-all'></i>Reply</a>
											</div>
										</div><!--comment-list end-->
									</li>
								</ul>
							</li>
						</ul>
					</div><!--comment-sec end-->
					
				<?php
				}
				?></div><!-- tweakedCommentDiv end -->
					<div class='post-comment'>
						<div class='cm_img'>
							<img src='<?php $user->getImage($_SESSION['user_id']); ?>' alt='' height="40" width="40">
						</div>
						<div class='comment_box'>
							<form method="post" class="commentForm" id="commentForm<?= $commentId; ?>">
								<input type='text' name="commentText" placeholder='Post a comment'>
								<input type="hidden" value="<?= $commenterName; ?>" name="commenter" readonly="">
								<input type="hidden" value="<?= $_SESSION['user_id']; ?>" name="commenterId" readonly="">
								<input type="hidden" value="<?= $commentId; ?>" name="commentId" readonly="">
								<input type="hidden" value="<?php $user->getImage($commenter['id']); ?>" name="commenterImage">
								<button type='submit'>Send</button>
							</form>
						</div>
					</div><!--post-comment end-->
				<?php
			}
			else
			{
				echo "Be the first to comment on this post";
			}
		}
		/*

		function notFriends($user_id)
		{
			$connect = dbconnect() ;
			$user = new User ;
			$ids = $user->getFriendIds($user_id) ;
			$query = mysqli_query($connect , "SELECT * FROM posts WHERE posted_by NOT IN ($ids) ") ;
			return $query ;
		}*/
	}


	/**
	 * 
	 */
	class chatMessages
	{
		
		/*function __construct()
		{
			
		}*/

		function getMessages($user)
		{
			require_once 'config.php' ;
			$connect = dbconnect() ;
			$query = mysqli_query($connect , "SELECT * FROM chats WHERE sender_id = '$user' OR receiver_id = '$user' ") ;
			$return = array() ;
			while ($fetch = fetcher($query)) {
				$user = new User ;
				$f2 = $user->getUserProfile($fetch['id']) ;
				array_merge($fetch , $f2) ;
				$f1 = array_push($return, $fetch) ;
			}
			return $return ;
			/*if($query){
				// return fetches($query) ;
				foreach ($query as $row) {
					print_r($row) ;
					print_r("<br>") ;
					print_r("<br>") ;
				}
			}
			else
			{
				echo mysqli_error($connect);
			}*/
		}
	}


	/**
	 * 
	 */
	class Status extends User
	{
		
		/*function __construct()
		{
			global $user_id  $this->getUserProfile($_SESSION['user_id'])['id'] ;
		}*/
		//1 yes
		//0 timeout
		//2 blocked
		function getStatus()
		{
			$user_id = $_SESSION['user_id'] ;
			$ids = $this->getFriendIds($user_id) ;
			$ids = $user_id.','.$ids ;
			$con = dbconnect() ;
			$query = $con->query("SELECT * FROM status WHERE owner IN ($ids) AND stat = 1 ORDER BY owner") ;
			return $query ;
		}

		function offStatus($id)
		{
			$con = dbconnect() ;
			$query = $con->query("UPDATE status SET stat = '0' WHERE id = '$id'  ") ;
		}

		function getEachStatus($val)
		{
			//1-text , 0-image , 2-video
			$con = dbconnect() ;
			$query = $con->query("SELECT * FROM status WHERE id IN ($val) AND stat = 1 ") ;
			if($query){
				return $query ;
			}else{
				return array('error' => mysqli_error($con)) ;
			}
		}

	}

	function getChurch()
	{
		if(isset($_GET['wh']) && isset($_GET['church']))
		{
			$rub = $_GET['wh'] ;
			$num = strlen($rub) ;
			$chId = substr(strrev(substr($rub, 32 , $num)) , 32 , 32) ;
			$dataRead = new DataRead ;
			$getData = $dataRead->getChurch($chId) ;
			return $getData ;
			// echo $chId;
		}
		else
		{
			redirect("churches.php") ;
		}
	}

	function getAllChurch()
	{
		$connect = dbconnect() ;
		$query = $connect->query("SELECT * FROM churches ORDER BY rand() LIMIT 12") ;
		if($query && numQuery($query) != 0)
		{
			foreach ($query as $row) {
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
		else
		{
			showAlert("info" , "No chuurches returned for your region. Would you like to <a href='profile-setting.php?section=add-church'>add</a> one?") ;
		}
	}

	function getEvents()
	{
		$user_id = $_SESSION['user_id'] ;
		$connect = dbconnect() ;
		$query = $connect->query("SELECT * FROM events ") ;
		return $query ;
	}

	function getInvites()
	{
		// 1 accepted , 0 not responding , 3 declined , 2event cancelled/passed,
		$user_id = $_SESSION['user_id'] ;
		$con = dbconnect() ;
		$query = $con->query("SELECT *, i.status , i.id, e.id FROM invitations i JOIN events e ON(i.event_id = e.id) WHERE invited = '$user_id' AND e.status <> '-1' ") ;
		return $query ;
	}

	$user = new User ;
	$relationship = new Relationship ;
	$posts = new Posts ;
	$chats = new chatMessages ;
	// print_r($chats->getMessages(1)) ;

	



	function noneFriends($id)
	{
		$connect = dbconnect() ;
		$query = mysqli_query($connect , "SELECT * FROM members m INNER JOIN relationship r ON ($id = r.user1 OR $id = r.user2) AND r.status = '1' ") ;
		if($query)
		{
			return $query ;
		}
		else
		{
			return array('error' => mysqli_error($connect));
		}
	}






	function user0($id)
	{
		if($id != 0)
		{
		    // redirect("login.php") ;
		}
	}

	function user1($id)
	{
		if($id != 1)
		{
		    // redirect("login.php") ;
		}
	}

	function user2($id)
	{
		if($id != 2)
		{
		    // redirect("login.php") ;
		}
	}

	function user3($id)
	{
		if($id != 3)
		{
		    // redirect("login.php") ;
		}
	}

	function staffDet()
	{
		if(isset($_GET['s']))
		{
			$sid = $_GET['s'] ;
			$connect = dbconnect() ;
			$dataRead = new DataRead() ;
			$query = $dataRead->getuser($connect , $sid) ;
			if($query)
			{
				return $query ;
			}
		}
		else
		{
			$user = userDet() ;
			if($user['access_id'] == 3)
			{
				// redirect("searchstaff.php") ;
			}
		}
	}

	function getAccess($akses)
	{
		switch ($akses) {
			case 0:
				$return = "Admin" ;
				break;
			case 1:
				$return = "Courtesy Officer" ;
				break;
			case 2:
				$return = "Dean" ;
				break;
			case 3:
				$return = "Disciplinary unit staff" ;
				break;
			default:
				$return = "Nill" ;
				break;
		}
		return $return ;
	}

	function getPunishment($punish)
	{
		switch ($punish) {
			case 0:
				$return = "No punishment was attached to this case" ;
				break;
			case 1:
				$return = "Warning" ;
				break;
			case 5:
				$return = "Expulsion" ;
				break;
			case 21:
				$return = "One week councelling" ;
				break;
			case 22:
				$return = "Two weeks councelling" ;
				break;
			case 23:
				$return = "Three weeks councelling" ;
				break;
			case 24:
				$return = "Four weeks councelling" ;
				break;
			case 25:
				$return = "Five weeks councelling" ;
				break;
			case 26:
				$return = "Six weeks councelling" ;
				break;
			case 27:
				$return = "As advised by the councellor" ;
				break;
			case 31:
				$return = "One week suspension" ;
				break;
			case 32:
				$return = "Two weeks suspension" ;
				break;
			case 33:
				$return = "Suspended for one semester" ;
				break;
			case 34:
				$return = "Suspended for two semesters" ;
				break;
			case 41:
				$return = "Rusticated for one semester" ;
				break;
			case 42:
				$return = "Rusticated for two semesters" ;
				break;
			default:
				$return = "No punishment is attached to this case yet" ;
				break;
		}
		return $return ;
	}

	function sideBar($bar)
	{
		switch ($bar) {
			case 0:
				$return = "sidebar0.php" ;
				break;
			case 1:
				$return = "sidebar1.php" ;
				break;
			case 2:
				$return = "sidebar2.php" ;
				break;
			case 3:
				$return = "sidebar3.php" ;
				break;
			default:
				$return = "Nill" ;
				break;
		}
		return $return ;
	}

	function  viewCase()
	{
		if(isset($_GET['p']))
		{
			$dataRead = new DataRead() ;
			$connect = dbconnect() ;
			$id = $_GET['p'] ;
			$query = $dataRead->viewcase($connect , $id) ;
			if($query)
			{
				return $query ;
			}
		}
		else
		{
			$user = userDet() ;
			if($user['access_id'] == 1)
			{
				// redirect("searchcase.php") ;
			}
			else
			{
				// redirect("viewrecords.php") ;
			}
		}
	}

	function viewPunish($id)
	{
			$dataRead = new DataRead() ;
			$connect = dbconnect() ;
			$query = $dataRead->viewpunish($connect , $id) ;
			if($query)
			{
				return $query ;
			}
	}

	function casestatus()
	{
		if(isset($_GET['p']))
		{
			$dataRead = new DataRead() ;
			$connect = dbconnect() ;
			$id = $_GET['p'] ;
			$query = $dataRead->viewcase($connect , $id) ;
			if($query)
			{
				$stat = $query['status'] ;
				if($stat == 0)
				{
					$return = "<i class='text-warning'>Pending</i>" ;
					return $return ;
				}
				if($stat == 1)
				{
					$return = "<i class='text-success'>Approved</p>" ;
					return $return ;
				}
				if($stat == 3)
				{
					$return = "<i class='text-primary'>Punished</p>" ;
					return $return ;
				}
				if($stat == 4)
				{
					$return = "<i class='text-danger'>Declined</p>" ;
					return $return ;
				}
			}
		}
	}

	function casestat($stat)
	{
		if($stat == 0)
		{
			$return = "<i class='text-warning'>Pending</i>" ;
			return $return ;
		}
		if($stat == 1)
		{
			$return = "<i class='text-success'>Approved</p>" ;
			return $return ;
		}
		if($stat == 3)
		{
			$return = "<i class='text-primary'>Punished</p>" ;
			return $return ;
		}
		if($stat == 4)
		{
			$return = "<i class='text-danger'>Declined</p>" ;
			return $return ;
		}
	}

	function getusername($user_id)
	{
		$dataRead = new DataRead() ;
		$connect = dbconnect() ;
		$query = $dataRead->getuser($connect , $user_id) ;
		if($query)
		{
			$name = $query['fullname'] ;
			return $name ;
		}
	}
}



?>