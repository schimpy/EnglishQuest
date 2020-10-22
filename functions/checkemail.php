<?php
    // Include INIT file
    require_once '../init.php';

    // Create a user instance
    $user = new User();

    if(isset($_POST['verify_email']))
    {
        $email=$_POST['verify_email'];
    
        if($user->doesEmailExist($email) == false) {
            echo "E-mail is available!";
        } else {
            if($user->data()->email == $email) {
                echo "This e-mail is currently set!";
            } else {
                echo "E-mail already exists!";
            }
        }
    }
