
// http://localhost/fix-a-challenge/fixasbank/api.php?guest=example@domain.com
let userMail = $("#userMail").val() ;
    function loadUserDetails(){
        $.get("http://localhost/fix-a-challenge/fixasbank/api.php?guest="+userMail ,{

        }).done(function(data){
            let jsonData = JSON.parse(data) ;
            acctExist = jsonData.account ;
            acctBal = jsonData.balance ;
            if(acctExist == 0){ // user has no account
                $("#hasAccount").show() ;
                $("#navAcctCreator").show() ;
                $("#fundAccout").hide() ;
                $("#withdrawFund").hide() ;
            }else{ // user has account 
                $("#hasAccount").hide() ;
                $("#navAcctCreator").hide() ;
                $("#fundAccout").show() ;
                $("#withdrawFund").show() ;
                $("#hasBalance").show() ;
                $("#balance").html(acctBal) ;
            }
            $("#preload").remove() ;
            console.log(jsonData);
            
        });
    }
    
    doc = $(document) ;

    /********************************  Create new User Account  **********************************/

    doc.on('click' , '#createAcct' , function (e) {
        $(this).html('Working on it...') ;
        e.preventDefault() ;
        // http://localhost/fix-a-challenge/fixasbank/api.php?guest=example@domain.com&action=31&username=name&password=pword ;
        let username = $("#fullname").val() ;
        let pword = $("#password").val() ;
        let url = "http://localhost/fix-a-challenge/fixasbank/api.php?guest="+userMail+"&action=31&username="+username+"&password="+pword ;
        $.get(url , {

        }).done(function(data){
            $("#ifCreated").val(1) ;
            let acctCreateData = JSON.parse(data) ;
            let retVal = acctCreateData.accountCreate
            console.log(retVal);
            let retDiv = $("#acctcreateReturn") ;
            if(retVal == 'true'){
                retDiv.html("Your account has been created successfully") ;
            }else{
                retDiv.html("An error occured") ;
            }
            retDiv.toggleClass('d-none' , 'd-block') ;
        }) ;
        $("#okayBtn").show() ;
        $(this).html('Create my Account') ;
    }) ;

    doc.on('click' , '#okayBtn' , function(e){
        e.preventDefault() ;
        $("#modelId").modal('hide') ;
        loadUserDetails() ;
    }) ;

    /********************************  End Create new User Account  **********************************/

    /********************************  User Funds Account  **********************************/
    doc.on('click' , '#fundBtn' , function(e){
        e.preventDefault() ;
        let amount = $("#fundAmount").val() ;
        // http://localhost/fix-a-challenge/fixasbank/api.php?guest=example@domain.com&action=61&amount=amt
        let url = "http://localhost/fix-a-challenge/fixasbank/api.php?guest="+userMail+"&action=61&amount="+amount ;
        $.get(url , {

        }).done(function(data){
            let jsonFundData = JSON.parse(data) ;
            let doneFund = jsonFundData.fundaction ;
            if(doneFund == 'true'){
                $("#acctFundReturn").html('Your Account has been credited with '+amount) ;
            }else{
                $("#acctFundReturn").html('Sorry an error occured') ;
            }
            $("#acctFundReturn").show() ;
        }) ;
    }) ;

    $("#withdrawBtn").click(function () {
        let withdrawAmt = $("#withdrawAmt").val() ;
        let url = "http://localhost/fix-a-challenge/fixasbank/api.php?guest="+userMail+"&action=2361&amount="+withdrawAmt ;
        $.get(url , {

        }).done(function(data){
            let jsonWithdrawData = JSON.parse(data) ;
            let doneWithdraw = jsonWithdrawData.withdrawAction ;
            if(doneWithdraw == '1'){
                $("#acctWithdrawReturn").html('Your Account has been debited with '+withdrawAmt) ;
            }else if(doneWithdraw == '-1'){
                $("#acctWithdrawReturn").html("Sorry you don't have that much in your account.") ;
            }else{
                $("#acctWithdrawReturn").html('Sorry an error occured') ;
            }
            $("#acctWithdrawReturn").show() ;
        }) ;
    }) ;


    $("#modelId").on('hidden.bs.modal' , function(){
        if($("#ifCreated").val() == 1){
            window.location.reload() ;
        }
    }) ;
    $("#fundAcctModal , #withdrawFundModal").on('hidden.bs.modal' , function(){
        loadUserDetails() ;
    }) ;

    $("#withdrawAmt").on('keyup' , function(){
        withdrawAmt = $(this).val() ;
        newAmt = $("#availableBalance").val() - withdrawAmt ;
        $("#remainBalance").val(newAmt) ;
    }) ;

    $("#withdrawFundModal").on('show.bs.modal' , function(){
        $("#availableBalance").val($("#balance").html()) ;
        $("#remainBalance").val($("#balance").html()) ;
    }) ;
    
    /********************************  End User Funds Account  **********************************/


$(document).ready(function () {
    loadUserDetails() ;
});