<?php
session_start() ;
?>
<!DOCTYPE html>
<html>
    <head>
    <meta charset="UTF-8">
    <title> Register | FixasLab</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
</head>
    <body class="bg-info">
        <div class="container bg-info mt-5">
            <div class="row">
                <div class="col-6 offset-lg-3 mt-5">
                    <div class="card mt-5" style="padding: 0 15px 20px 15px;">
                        <h3 class="text-center text-info mt-4">Login</h3>
                        <?php
                            if(isset($_SESSION['logerror'])){
                        ?>
                        <div class="col-4 offset-lg-4 alert alert-info"><?= $_SESSION['logerror'] ?></div>
                        <?php
                            }
                        ?>
                        <form action="php/actionmanager.php" method="post">
                            <div class="form-group">
                            <label for="">Email</label>
                            <input type="email" class="form-control" name="email" id="" aria-describedby="emailHelpId" placeholder="Enter your Email">
                            <!-- <small id="emailHelpId" class="form-text text-muted">Help text</small> -->
                            </div>
                            <div class="form-group">
                              <label for="">Password</label>
                              <input type="password" class="form-control" name="password" id="" placeholder="Enter your password">
                            </div>
                            <button type="submit" class="btn btn-primary" name="command" value="login">Login</button>
                            <p class="text-center text-info mt-4">Already have an account? <a href="register.php">Sign up here</a></p>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </body>
</html>
<?php

unset($_SESSION['regerror']) ;
session_destroy() ;
?>