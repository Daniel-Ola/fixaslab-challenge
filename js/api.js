
// http://localhost/fix-a-challenge/fixasbank/api.php?guest=example@domain.com
let userMail = $("#userMail").val() ;
    function loadUserDetails(){
        $.get("http://localhost/fix-a-challenge/fixasbank/api.php?guest="+userMail ,{

        }).done(function(data){
            let jsonData = JSON.parse(data) ;
            acctExist = jsonData.account ;
            acctBal = jsonData.balance ;
            if(acctExist == 0){ // user has no account
                $("#hasAccount").toggleClass('d-none' , 'd-block') ;
                $("#hasAccount").show() ;
                $("#navAcctCreator").show() ;
            }else{ // user has account 
                $("#hasAccount").hide() ;
                $("#navAcctCreator").hide() ;
                $("#hasBalance").toggleClass('d-none' , 'd-block') ;
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
            console.log(data);
        }) ;
    }) ;
    /********************************  End User Funds Account  **********************************/


$(document).ready(function () {
    loadUserDetails() ;
});