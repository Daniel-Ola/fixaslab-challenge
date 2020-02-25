<?php
session_start() ;
require 'php/config.php' ;
if(isset($_SESSION['user_id'])){
    redirect('home.php') ;
}else{
    redirect('login.php') ;
}



?>