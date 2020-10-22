<?php
    // Insert a page header
    require 'header.php';

    // If we have a session message registered, we show it
    if(Session::exists('home')) {
        echo '<p>' . Session::flash('home') . '</p>';
    }

    // If user is logged in
    if($logged) {

        $user->loadInitialApp();
       
        // We load and render all modules - unlocked, purchased, available, locked
        $handler = new ModuleHandler($user->data()->id);
        $handler->loopModules($handler->loadAllModules());
        $handler->renderAllModules();
        
   
    // If user is not logged in    
    } else { 
        echo '<p class="msg">Hello! You have to <a href="login.php">log in</a> or <a href="register.php">sign in</a> to go on English Quest!</p>';
    }

    //Insert a page footer
    require 'footer.php';



