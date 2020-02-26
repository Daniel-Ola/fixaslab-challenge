<?php
require_once 'php/settings.php' ;
$userDet = $user->getUserDet($_SESSION['user_id']) ;
?>
<!DOCTYPE html>
<html>
    <head>
    <meta charset="UTF-8">
    <title> Home | FixasLab</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
</head>
    <body>
        <input type="hidden" value="<?= $userDet['email'] ?>" id="userMail">
        <nav class="navbar navbar-expand-sm navbar-dark bg-primary">
            <a class="navbar-brand" href="#">FixasLab</a>
            <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#collapsibleNavId" aria-controls="collapsibleNavId"
                aria-expanded="false" aria-label="Toggle navigation"></button>
            <div class="collapse navbar-collapse" id="collapsibleNavId">
                <ul class="navbar-nav m-auto mt-2 mt-lg-0" style="*padding-right: 100px;">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdownId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">FixasBank</a>
                        <div class="dropdown-menu" aria-labelledby="dropdownId">
                            <a class="dropdown-item" data-toggle="modal" data-target="#modelId" id="navAcctCreator" style="display: none;" href="#">Create Account</a>
                            <a class="dropdown-item" id="fundAccout" data-toggle="modal" data-target="#fundAcctModal" href="#">Fund Account</a>
                            <a class="dropdown-item" href="#">Withdraw from Account</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
                <form class="form-inline my-2 my-lg-0">
                    <h4 class="text-white">Welcome! <u><?= $userDet['fullname'] ?></u></h4>
                </form>
            </div>
        </nav>
        <main class="jumbotron">
            <div class="container">
                <div class="row">
                    <div class="col-8 offset-lg-2">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title text-center">Welcome to your homepage at FixasLab</h4>
                                <p class="card-text">Quick Links</p>
                                <?php 
                                    $detStr = implode('_-_' , $userDet) ;
                                    $encodedDet = base64_encode($detStr) ;
                                ?>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item text-center" id="preload">Loading</li>
                                <li class="list-group-item d-none" id="hasAccount">You do not have a FixasBank Account. Create one now. <span class="btn btn-info float-right" data-toggle="modal" data-target="#modelId">Create Account</span></li>
                                <li class="list-group-item d-none" id="hasBalance"><span class="btn btn-info"> Wallet balance</span> <span class="badge badge-info float-right" id="balance">0.00</span></li>
                                <!-- <li class="list-group-item d-none">Item 3</li> -->
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </main>
                                <!-- MODAL -->
                                
                                <!-- Modal -->
                                <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Create Account with FixasBank</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-info text-center d-none" id="acctcreateReturn"></div>
                                                <form method="get" action="">
                                                    <div class="form-group">
                                                      <label for="">Email</label>
                                                      <input type="email" class="form-control" name="" id="userEmail" value="<?= $userDet['email'] ?>" aria-describedby="emailHelpId" placeholder="" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                      <label for="">Fullname</label>
                                                      <input type="text" class="form-control" name="" id="fullname" value="<?= $userDet['fullname'] ?>" aria-describedby="helpId" placeholder="">
                                                    </div>
                                                    <div class="form-group">
                                                      <label for="">Password</label>
                                                      <input type="password" class="form-control" name="" id="password" placeholder="">
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" style="display: none;" id="okayBtn" data-dismiss="modal">Okay</button>
                                                <button type="button" class="btn btn-primary" id="createAcct">Create my Account</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- fund account modal -->
                                    <div id="fundAcctModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="">Enter Amount</label>
                                                            <input type="number" name="fundAmount" id="fundAmount" class="form-control" placeholder="Enter Amount" aria-describedby="helpId">
                                                        </div>
                                                        <button type="button" class="btn btn-primary" id="fundBtn">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <!-- end fund account modal -->

                                <!-- MODAL END -->



    </body>

    <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/api.js"></script>

</html>