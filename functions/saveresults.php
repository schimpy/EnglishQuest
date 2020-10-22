<?php
    // Include INIT file
    require_once '../init.php';

    // Create a user instance
    $user = new User();

    if(isset($_POST['userid']))
    {
        $newXP = $user->getPoints() + $_POST['xp'];
        $newCoins = $user->getCoins() + $_POST['coins'];

        $isNewLevel = $user->calculateLevel($newXP);

        if($isNewLevel) {
            Session::flash('unit', $user->flashNewLevelMessage());
            $user->update(array('points' => $newXP, 'coins' => $newCoins, 'lvl' => $user->getNewLevel()), $_POST['userid']);
        } else {
            $user->update(array('points' => $newXP, 'coins' => $newCoins), $_POST['userid']);
        }

        // if($badgeChecker->checkAchievements()) {
        //     Session::flash('badge', $badgeChecker->flashNewBadgeMessage());
        // }        

    }