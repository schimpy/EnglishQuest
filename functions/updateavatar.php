<?php
    // Include INIT file
    require_once '../init.php';

    // Create a user instance
    $user = new User();

    $avatar = $_POST['img'];
    $userID = $user->data()->id;
    $user->update(array('status' => 1), $userID);
    $user->setAvatar($avatar, $userID);

    echo 'SUCCESS';
