<?php
    // Include INIT file
    require_once '../init.php';

    // Create a user instance
    $user = new User();

    if(isset($_POST['verify_password']))
    {
        $pwdOld=$_POST['verify_password'];
    
        if($user->doesPasswordMatch($pwdOld) == true) {
            echo "OK";
        } else {
            echo "Your current password is incorrect!";
        }
    }
