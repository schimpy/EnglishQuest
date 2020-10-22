<?php
    // Include a page header
    require_once 'header.php';

    // Create a user instance
    $user = new User();

    // If we have HTTP GETs
    if(Input::exists('get')) {

        // Parse GETs
        $selector = Input::get('selector');
        $verificator = Input::get('verificator');

        // Verify GETs
        if((empty($selector) || empty($verificator)) && (ctype_digit($selector) === false || ctype_digit($verificator) === false)) {
            
            // If invalid, create a session message and redirect
            Session::flash('home', 'Invalid link.');
            Redirect::to('index.php');

        // If valid
        } else {

            // If inputs exist
            if(Input::exists()) {

                // If inputs were not tampered with
                if(Token::check(Input::get('token'))) {

                    // Compute variables
                    $currentDate = date("U");
                    $entry = $user->getDB()->get('pwdresets', array('selector', '=', Input::get('selector')));
                    $resetDate = $entry->first()->expires;
                    $userEmail = $entry->first()->email;
                    $verification = password_verify(hex2bin(Input::get('verificator')), $entry->first()->verificator);

                    // If invalid link
                    if($user->getDB()->count() === 0) {
                        Session::flash('home', 'Invalid link.');
                        Redirect::to('index.php');

                    // If expired link
                    } elseif ($currentDate >= $resetDate) {
                        Session::flash('home', 'Expired link.');
                        Redirect::to('index.php');

                    // If bad verificator
                    } elseif (!$verificator) {
                        Session::flash('home', 'Invalid link.');
                        Redirect::to('index.php');

                    // If OK
                    } else {
                        
                        // Validate
                        $validator = new Validator();
                        $validator->check($_POST, array(

                            'password' => array(
                                'name' => 'Password',
                                'required' => true,
                                'min' => 8
                            ),

                            'password_check' => array(
                                'required' => true,
                                'matches' => 'password'
                            )
                        ));

                        // If valid inputs
                        if($validator->passed()) {

                            // Update a user
                            $user->update(array('password' => Hash::make(Input::get('password'))), $user->getUserIDFromEmail($userEmail));
                            
                            // Delete the request from DB
                            $user->getDB()->delete('pwdresets', array('email', '=', $userEmail));

                            // Create a session message and redirect
                            Session::flash('home', 'Your password has been changed.');
                            Redirect::to('index.php');

                        // If invalid inputs, show errors
                        } else {
                            $_GET['validationerror'] = 'yes';
                        }
                    }
                }
            }
        }
    }
?>
    <h1>Create new password</h1>

        <form action="" method="post">

            <div class="field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="w3-input w3-border w3-round">
            </div>

            <div class="field">
                <label for="password_check">Password check</label>
                <input type="password" name="password_check" id="password_check" class="w3-input w3-border w3-round">
            </div>

            <input type="hidden" name="selector" value="<?php echo $selector; ?>">
            <input type="hidden" name="verificator" value="<?php echo $verificator; ?>">
            <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">

            <?php
                if(Input::exists('get')) {
                    if(Input::get('validationerror') == 'yes') {
                        $str = "";
                        foreach ($validator->errors() as $error) {
                            $str .= "<p>". $error ."</p>";
                        }
                        echo '<div class="w3-display-container w3-panel w3-card-4 w3-round w3-red"><span onclick="this.parentElement.style.display=\'none\'"class="w3-button w3-small w3-hover-red w3-display-topright">&times;</span>'. $str .'</div>';
                    }                                      
                }    
            ?>

            <input type="submit" value="Create" class="w3-button w3-small w3-teal w3-hover-khaki w3-round w3-margin-top">

        </form>

        <p><a href="index.php">Go back</a></p>

<?php
    include('footer.php');