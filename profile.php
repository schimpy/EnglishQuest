<?php

    // Insert a page header
    require 'header.php';

    if(!$user->isLoggedIn()) {
        Redirect::to('index.php');
    }

    if(Input::exists()) {

        if(Input::get('token-email') == 'ok') {
            $user->update(array('email' => Input::get('emailChange')), $user->data()->id);
            Session::flash('profile', 'Your e-mail has been changed.');
            Redirect::to('profile.php');
        }

        if(Input::get('token-pwd') == 'ok') {
            $user->update(array('password' => Hash::make(Input::get('password_new'))), $user->data()->id);
            Session::flash('login', 'Your password has been changed. Please log in again.');
            $user->logout();
            Redirect::to('login.php');
        }
    }

    $board = new Board($user);
    $reward = new Reward($user);
?>

    <h2>Profile</h2>

    <?php
        if(Session::exists('profile')) {
            echo '<p>' . Session::flash('profile') . '</p>';
        }
    ?>

    <div class="w3-container">
        <div class="w3-bar w3-lime">
            <button class="w3-bar-item w3-button tablink w3-deep-orange" onclick="openTab(event,'info')">Info</button>
            <button class="w3-bar-item w3-button tablink" onclick="openTab(event,'avatar')">Avatar</button>
            <button class="w3-bar-item w3-button tablink" onclick="openTab(event,'progress')">Progress</button>
            <button class="w3-bar-item w3-button tablink" onclick="openTab(event,'email')">E-mail</button>
            <button class="w3-bar-item w3-button tablink" onclick="openTab(event,'password')">Password</button>
            <button class="w3-bar-item w3-button tablink" onclick="logout()">Logout</button>
        </div>

        <div id="info" class="w3-container w3-border tab"  style="display:block">
            <h4>Hello, <b><?php echo $user->getUsername(); ?></b></h4>
            <ul>
                <li>Level achieved: <b><?php echo $user->getLevel(); ?></b>&nbsp;<i class="fas fa-certificate fa-lg" title="User's level" style="color: tomato;"></i></li>
                <li>Experience points: <b><?php echo $user->getPoints(); ?></b>&nbsp;<i class="fas fa-star fa-lg" title="User's experience points" style="color: gold;"></i></li>                                
                <li>Coins earned: <b><?php echo $user->getCoins(); ?></b>&nbsp;<i class="fas fa-coins fa-lg" title="User's coins" style="color: darkorange;"></i></li>
            </ul>
        </div>

        <div id="avatar" class="w3-container w3-border tab"  style="display:none">
            <h4>Select your character</h4> 
                <a href="#" onclick="updateAvatar('default.png')">
                    <img src="img/avatar/default.png" width="80" height="80" style="padding:5px" title="Default"></a>
                <a href="#" onclick="updateAvatar('female.png')">
                    <img src="img/avatar/female.png" width="80" height="80" style="padding:5px" title="Female"></a>
                <a href="#" onclick="updateAvatar('male.png')">
                    <img src="img/avatar/male.png" width="80" height="80" style="padding:5px" title="Male"></a>

            <div id="updateAvatarModal" class="w3-modal">
                <div class="w3-modal-content">
                    <div class="w3-container">
                        <span onclick="document.getElementById('updateAvatarModal').style.display='none'" class="w3-button w3-display-topright">&times;</span>
                        <p>Your avatar was updated!</p>
                    </div>
                </div>
            </div>

            <h4>Or upload your own</h4>
                <form id="customAvaratUpload">
                    <input type="file" id="customAvatar" class="w3-input w3-border"><br>
                    <button type="submit" class="w3-button w3-lime w3-hover-deep-orange w3-margin-bottom">Upload</button>
                </form>
        </div>

        <div id="progress" class="w3-container w3-border tab" style="display:none">
            <div class="w3-row">
                <div class="w3-col s12 m12 l6">
                    <h4>Leaderboard</h4>
                        <?php $board->renderBoard(); ?>
                </div>

                <div class="w3-col s12 m12 l6">
                    <h4>Rewards</h4>
                    <?php $reward->renderRewards(); ?>
                </div>
            </div>
        </div>

        <div id="email" class="w3-container w3-border tab" style="display:none">
            
            <div id="mailchange" class="w3-panel">
                <h3 class="">Change your e-mail</h3>
                <form name="mailchange" action="" method="post">
                    <label>Your new e-mail</label>
                    <input type="text" name="emailChange" id="emailChange" class="w3-input" onkeyup="checkEmail();">
                    <div id="email-status" style="padding: 5px" class="w3-panel w3-round"></div>
                    <input type="hidden" name="token-email" value="ok">
                    <input type="submit" id="emailSubmit" value="Change" class="w3-button w3-small w3-light-green w3-hover-gray w3-round w3-margin-top" disabled>
                </form>
            </div>
        </div>

        <div id="password" class="w3-container w3-border tab" style="display:none">
            <div id="pwdchange" class="w3-panel">
                <h3 class="">Change your password</h3>
                <form name="pwdchange" action="" method="post">
                    <label>Your old password</label>
                    <input type="password" name="password_old" id="password_old" class="w3-input" value="" onkeyup="checkPassword();">
                    <label>Your new password</label>
                    <input type="password" name="password_new" id="password_new" class="w3-input" value="">
                    <label>Confirm your new password</label>
                    <input type="password" name="password_check" id="password_check" class="w3-input" value="" onkeyup="checkPassword();">
                    <div id="pwd-status" style="padding: 5px;" class="w3-panel w3-round"></div>
                    <input type="hidden" name="token-pwd" value="ok">                  
                    <input type="submit" id="pwdSubmit" value="Change" class="w3-button w3-small w3-light-green w3-hover-gray w3-round w3-margin-top">
                </form>
            </div>
        </div>

    </div>

    <script src="js/profileHandler.js"></script>

<?php
    require_once 'footer.php';
