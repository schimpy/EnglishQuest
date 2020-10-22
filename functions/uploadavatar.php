<?php
    require_once '../init.php';

    $user = new User();
    $targetPath = '../img/avatar/'. basename($_FILES["file"]["name"]);

    move_uploaded_file($_FILES["file"]["tmp_name"], $targetPath);

    $userID = $user->data()->id;
    $user->update(array('status' => 1), $userID);
    $user->setAvatar(basename($_FILES["file"]["name"]), $userID);

