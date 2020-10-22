<?php
    // Include INIT file
    require_once 'init.php';

    // Create a user instance
    $user = new User();
    $logged = $user->isLoggedIn();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://fonts.googleapis.com/css2?family=Chelsea+Market&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="custom.css">
    <link rel="stylesheet" href="quiz.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/51e87ede39.js" crossorigin="anonymous"></script>
    <script src='https://www.google.com/recaptcha/api.js' async defer ></script>



</head>

<body>
    <main class="w3-display-container w3-mobile" style="margin: auto; width: 55%;">

            <div class="w3-row w3-margin-top w3-margin-bottom">

                <div class="w3-col l5 m8 s8 w3-margin-left">
                    <h2 style="font-size: 30px; font-family: 'Chelsea Market', cursive;">
                        <a href="index.php" style="text-decoration: none;">ENGLISH QUEST</a>
                    </h2>
                </div>

                <?php if($user->isLoggedIn()) { ?>
                    
                <div class="w3-col l6 m3 s3 w3-margin-right">
                    
                    <div class="w3-container w3-cell">
                        <a href="profile.php" style="text-decoration: none;" title="User's profile">
                            <?php echo $user->showUserAvatarInHTML(); ?>

                        </a>
                    </div>

                    <div class="w3-container w3-cell w3-hide-small w3-hide-medium w3-padding w3-round w3-lime">

                        <!-- LVL -->
                        <div class="w3-col l3 w3-left-align w3-padding">
                            <div class="w3-center">
                                <i class="fas fa-certificate fa-lg" title="User's level"></i><br>
                                <?php echo $user->getLevel(); ?>
                            </div>
                        </div>

                        <!-- XP -->
                        <div class="w3-col l3 w3-left-align w3-padding">
                            <div class="w3-center">
                                <i class="fas fa-star fa-lg" title="User's experience points"></i>
                                <?php echo $user->getPoints(); ?>
                            </div>
                        </div>

                        <!-- COINS -->
                        <div class="w3-col l3 w3-left-align w3-padding">
                            <div class="w3-center">
                                <i class="fas fa-coins fa-lg" title="User's coins"></i><br>
                                <?php echo $user->getCoins(); ?>   
                            </div>                 
                        </div>

                        <!-- LOGOUT -->
                        <div class="w3-col l2 w3-right-align w3-padding">
                            <a href="logout.php" class="w3-center">
                                <i class="fas fa-sign-out-alt fa-lg" title="Log out the English Quest"></i>
                            </a>
                        </div>

                    </div>

                </div>

                <?php } ?>
                
            </div>

            <div class="w3-margin-left w3-margin-right">




