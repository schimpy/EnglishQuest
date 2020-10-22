<?php
    require 'header.php';

    if(!$user->isLoggedIn()) {
        Redirect::to('index.php');
    }

    if(Input::exists('get')) {
        $unitID = Input::get('id');
        $short = Input::get('slug');

        $unit = new Unit($short, $unitID, $user);

        if(!empty($_GET['unlock'])) {
            if($_GET['unlock'] == true) {
                $unitsTable = "" . $short . "_units";
                
                $unitInfo = $unit->getDB()->get($unitsTable, array('id', '=', $unitID));
                $unitPrice = $unitInfo->first()->price;

                if($user->getCoins() >= $unitPrice) {
                    $unit->unlock();
                    Session::flash('module', 'Unit '. $unit->getUnitTitle() .' unlocked!');
                    Redirect::to('module.php?slug='. $short);
                } else {
                    Session::flash('module', 'Not enough coins!');
                    Redirect::to('module.php?slug='. $short);
                }
            } else {               
                Session::flash('module', 'No such a quest!');
                Redirect::to('module.php?slug='. $short);
            }
        } else {

            if(Session::exists('unit')) {
                echo Session::flash('unit');
            }

            $unit->render();
        }



    }   

    include 'footer.php';