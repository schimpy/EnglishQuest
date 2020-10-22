<?php

    // Include INIT file
    require_once 'init.php';

    // Log out
    $user = new User();
    $user->logout();

    // Redirect
    Redirect::to('login.php');