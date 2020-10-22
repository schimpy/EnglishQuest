<?php
    require 'header.php';

    if(!$user->isLoggedIn()) {
        Redirect::to('index.php');
    }

    if(Input::exists('get')) {
        
        if(strlen(Input::get('slug')) == 3) {
            $module = new Module(Input::get('slug'), $user);

        } else {
            Redirect::to('index.php');
        }
    } else {
        Redirect::to('index.php');
    }

    if(isset($module)) {

        $module->renderModuleTitle(); 

        if(Session::exists('module')) {
            echo '<p>' . Session::flash('module') . '</p>';
        }

        $module->renderAllUnits();
    } 

    include 'footer.php';