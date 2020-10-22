<?php
    
    // Include a page header
    require 'header.php';

    // If we have a session message registered, we show it
    if(Session::exists('login')) {
        echo '<p>' . Session::flash('login') . '</p>';
    }

    // If we have inputs
    if(Input::exists()) {

        // If inputs were not tampered with
        if(Token::check(Input::get('token'))) {

            // Check validity
            $validator = new Validator();
            $validator->check($_POST, array(
                'username' => array('required' => true),
                'password' => array('required' => true)
            ));

            // If inputs are valid
            if($validator->passed()) {

                if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']))
                {
                      $secret = '6Ld3ovYUAAAAAN33s_4owHYSVqH-5ewSm-nyFSDb';
                      $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
                      $responseData = json_decode($verifyResponse);
                      
                      if($responseData->success) {

                        // Login
                        $user = new User();
                        $login = $user->login(Input::get('username'), Input::get('password'));

                        // If successful
                        if($login) {

                            // Update lastLogin in DB
                            $user->update(array('lastlogin' => date("Y-m-d H:i:s")), $user->getUserIDFromUsername(Input::get('username')));

                            // Redirect if admin or regular user
                            if($user->isAdmin()) {
                                Redirect::to('admin.php');
                            } else {

                                // If user's first login = profil completion and tutorial
                                if($user->data()->status == 0) {
                                    Session::flash('home', '<div class="w3-panel w3-padding-16 w3-round w3-lime">Welcome in English Quest! Plese complete the registration <a href="#" onclick="document.getElementById(\'initial\').style.display=\'block\'">here</a>.</div>');
                                }

                                Redirect::to('index.php');
                            }
                        
                        // If unsuccessful
                        } else {
                            $_GET['badcred'] = 'yes';
                        }

                      } else {
                        Redirect::to('index.php?err=bot');
                      }
                 }               

            // If errors in  the form
            } else {
                $_GET['validationerror'] = 'yes';
            }
        }
    }
?>
    <h1>Login</h1>

        <form action="" method="post">

            <div class="field">
                <label for='username'>Username</label>
                <input type="text" name="username" id="username" class="w3-input w3-border w3-round">
            </div>

            <div class="field">
                <label for='password'>Password</label>
                <input type="password" name="password" id="password" class="w3-input w3-border w3-round">
            </div>

            <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">

            <?php
                if(Input::exists('get')) {
                    if(Input::get('badcred') == 'yes') {
                        $err = '<div class="w3-display-container w3-panel w3-card-4 w3-round w3-red">
                                <span onclick="this.parentElement.style.display=\'none\'"class="w3-button w3-small w3-hover-red w3-display-topright">&times;</span>
                                <p>Bad credentials</p></div>';
                        echo $err; 
                    } elseif(Input::get('validationerror') == 'yes') {
                        $str = "";
                        foreach ($validator->errors() as $error) {
                            $str .= "<p>". $error ."</p>";
                        }
                        $err = '<div class="w3-display-container w3-panel w3-card-4 w3-round w3-red">
                        <span onclick="this.parentElement.style.display=\'none\'"class="w3-button w3-small w3-hover-red w3-display-topright">&times;</span>
                        '. $str .'</div>';
                        echo $err; 
                    }                                      
                }    
            ?>
        
            <div class="g-recaptcha w3-padding-16" data-sitekey="6Ld3ovYUAAAAABh60BXwp0ANYKiagQjf7TB_FxNj"></div>
            <input type="submit" value="Login" class="w3-button w3-small w3-light-green w3-hover-grey w3-round w3-margin-top">
        </form>

        <p>Did you forget your password? <a href="resetpassword.php">Get a new one!</a></p>
        <p><a href="index.php">Go back</a></p>

<?php
    require 'footer.php';
