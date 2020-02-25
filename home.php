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
                            <a class="dropdown-item" href="#">Create Account</a>
                            <a class="dropdown-item" href="#">Fund Account</a>
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
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">You do not have a FixasBank Account. Create on now.</li>
                                <li class="list-group-item"><span class="btn btn-info"> Wallet balance</span> <span class="badge badge-info float-right">0.00</span></li>
                                <li class="list-group-item">Item 3</li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </body>

    <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>

</html>