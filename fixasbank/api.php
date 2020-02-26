<?php
require_once '../php/dbconnect.php' ;
require_once '../php/config.php' ;

$con = bankdb() ;

$api_return = array('account' => 0 , 'balance' => '0.00' , 'accountCreate' => "null" , 'fundaction' => "null") ;

// 31 => create account {c-3 a-1}
// 61 => fund account {f-6 a-1}

if(isset($_GET['guest'])){
    $guest = $_GET['guest'] ;
    $guest_id = md5(passwordHash(cleanText($_GET['guest']))) ;
    $check = Check_If_Exists($con , 'users' , 'acct_id' , $guest_id) ;
    if($check == 0){ //user exist
        $api_return['account'] = 1 ;
    }else{
        if(isset($_GET['action']) && isset($_GET['username']) && isset($_GET['password'])){
            $action = cleanText($_GET['action']) ;
            $fullname = cleanText($_GET['username']) ;
            $password = cleanText($_GET['password']) ;
            $acct_id = md5(passwordHash(cleanText($_GET['guest']))) ;
            if($action == 31){
                // http://localhost/fix-a-challenge/fixasbank/api.php?guest=example@domain.com&action=31&username=name&password=pword ;
                $guest_det = array('email' => $guest , 'fullname' => $fullname , 'password' => $password , 'acct_id' => $acct_id) ;
                createAccount($guest_det) ;
            }
        }elseif(isset($_GET['action']) && isset($_GET['amount'])){
            $action = cleanText($_GET['action']) ;
            $amount = cleanText($_GET['amount']) ;
            if($action == 61){
                fundAccount($amount , $guest_id) ;
            }
        }
        $api_return['account'] = 0 ;
    }
}


function createAccount($guest_det){
    $con = bankdb() ;
    $email = $guest_det['email'] ; $fullname = $guest_det['fullname'] ; $password = $guest_det['password'] ; $acct_id = $guest_det['acct_id'] ;
    $query = $con->query("INSERT INTO users (email , fullname , password , acct_id) VALUES ('$email' , '$fullname' , '$password' , '$acct_id') ") ;
    if($query){
        $api_return['accountCreate'] = 'true' ;
    }else{
        $api_return['accountCreate'] = 'false'.mysqli_error($con) ;
    }
}

function fundAccount($amt , $guest_id){
    $con = bankdb() ;
    // use transaction here to save amount and keep track of transaction
    $querySelect = $con->query("SELECT balance FROM users WHERE acct_id = '$guest_id' ") ;
    $newAmt = intval(fetcher($querySelect)['balance']) + intval($amt) ;
    $query = $con->query("UPDATE users SET balance = '$newAmt' WHERE acct_id = '$guest_id' ") ;
    if($query){
        $api_return['fundaction'] = 'true' ;
        $api_return['balance'] = fetcher($querySelect)['balance'] ;
    }else{
        $api_return['fundaction'] = 'false' ;
        $api_return['balance'] = fetcher($querySelect)['balance'] ;
    }
}

print_r(json_encode($api_return)) ;
// echo "[
//     {
//         'name': 'ore'
//     }
// ]" ;





?>