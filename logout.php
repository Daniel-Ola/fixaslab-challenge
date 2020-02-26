<?php
require_once 'php/config.php' ;
session_start() ;
unset($_SESSION['user_id']) ;
session_destroy() ;
redirect('login.php') ;
?>