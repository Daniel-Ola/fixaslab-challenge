<?php
require_once '../php/dbconnect.php' ;
require_once '../php/config.php' ;

$con = bankdb() ;

$api_return = array('account' => 0 , 'balance' => '0.00' , 'accountCreate' => 'null' , 'fundaction' => 'null' , 'withdrawAction' => 'null') ;

// 31 => create account {c-3 a-1}
// 61 => fund account {f-6 a-1}
// 2361 => withdraw from account {w-23 f-6 a-1}
if(isset($_GET['guest'])){
    global $api_return ;
    $guest = $_GET['guest'] ;
    $guest_id = md5(passwordHash(cleanText($_GET['guest']))) ;
    $check = Check_If_Exists($con , 'users' , 'acct_id' , $guest_id) ;
    if($check == 0){ //user exist
        $api_return['account'] = 1 ;
        $querySelect = $con->query("SELECT balance FROM users WHERE acct_id = '$guest_id' ") ;
        $oldAmt = fetcher($querySelect)['balance'] ;
        $api_return['balance'] = strval($oldAmt) ;
        if(isset($_GET['action']) && isset($_GET['amount']))
        {
            $action = cleanText($_GET['action']) ;
            $amount = cleanText($_GET['amount']) ;
            if($action == '61')
            {
                $fund_det = array('amt' => $amount , 'guest_id' => $guest_id) ;
                fundAccount($fund_det) ;
            }
            elseif($action == '2361')
            {
                $fund_det = array('amt' => $amount , 'guest_id' => $guest_id) ;
                withdrawAccount($fund_det) ;
            }
        }
    }
    else
    {
        if(isset($_GET['action']) && isset($_GET['username']) && isset($_GET['password'])) // wants to create an account
        {
            $action = cleanText($_GET['action']) ;
            $fullname = cleanText($_GET['username']) ;
            $password = cleanText($_GET['password']) ;
            $acct_id = md5(passwordHash(cleanText($_GET['guest']))) ;
            if($action == 31)
            {
                // http://localhost/fix-a-challenge/fixasbank/api.php?guest=example@domain.com&action=31&username=name&password=pword ;
                $guest_det = array('email' => $guest , 'fullname' => $fullname , 'password' => $password , 'acct_id' => $acct_id) ;
                createAccount($guest_det) ;
            }
        }
        // else{
            // does not want to create an account
        // }
        $api_return['account'] = 0 ;
    }
}


function createAccount($guest_det){
    $con = bankdb() ;
    global $api_return ;
    $email = $guest_det['email'] ; $fullname = $guest_det['fullname'] ; $password = passwordHash($guest_det['password']) ; $acct_id = $guest_det['acct_id'] ;
    $query = $con->query("INSERT INTO users (email , fullname , password , acct_id) VALUES ('$email' , '$fullname' , '$password' , '$acct_id') ") ;
    if($query){
        $api_return['accountCreate'] = 'true' ;
    }else{
        $api_return['accountCreate'] = 'false'.mysqli_error($con) ;
    }
}

function fundAccount($fund_det)
{
    global $api_return ;
    $con = bankdb() ;
    $guest_id = $fund_det['guest_id'] ;
    $amt = $fund_det['amt'] ;
    // use transaction here to save amount and keep track of transaction
    $querySelect = $con->query("SELECT balance FROM users WHERE acct_id = '$guest_id' ") ;
    $oldAmt = fetcher($querySelect)['balance'] ;
    $newAmt = intval($oldAmt) + intval($amt) ;
    $query = $con->query("UPDATE users SET balance = '$newAmt' WHERE acct_id = '$guest_id' ") ;
    if($query){
        $api_return['fundaction'] = 'true' ;
        $api_return['balance'] = strval($newAmt) ;
    }else{
        $api_return['fundaction'] = 'false' ;
        $api_return['balance'] = strval($oldAmt) ;
    }
}

function withdrawAccount($fund_det)
{
    global $api_return ;
    $con = bankdb() ;
    $guest_id = $fund_det['guest_id'] ;
    $amt = $fund_det['amt'] ;
    // use transaction here to save amount and keep track of transaction
    $querySelect = $con->query("SELECT balance FROM users WHERE acct_id = '$guest_id' ") ;
    $oldAmt = fetcher($querySelect)['balance'] ;
    $newAmt = intval($oldAmt) - intval($amt) ;
    if($newAmt >= 0){
        $query = $con->query("UPDATE users SET balance = '$newAmt' WHERE acct_id = '$guest_id' ") ;
        if($query){
            $api_return['withdrawAction'] = '1' ; // good to go
            $api_return['balance'] = strval($newAmt) ;
        }else{
            $api_return['withdrawAction'] = '0' ; // error occured
            $api_return['balance'] = strval($oldAmt) ;
        }
    }else{
        $api_return['withdrawAction'] = '-1' ; // can't withdraw that much... withdrawal amount greater than user's balance
        $api_return['balance'] = strval($oldAmt) ;
    }
}

print_r(json_encode($api_return)) ;
// echo "[
//     {
//         'name': 'ore'
//     }
// ]" ;





?>