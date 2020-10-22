<?php
    // Include INIT file
    require 'header.php';

    // If we have inputs
    if(Input::exists()) {

        // If inputs were not tampered with
        if(Token::check(Input::get('token'))) {

            // Validate 
            $validator = new Validator();
            $validator->check($_POST, array(

                'username' => array(
                    'name' => 'Username',
                    'required' => true,
                    'unique' => 'users',
                    'min' => 2,
                    'max' => 20
                ),

                'email' => array(
                    'name' => 'E-mail',
                    'required' => true,
                    'unique' => 'users',
                    'email' => true
                ),

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

            // If valid
            if($validator->passed()) {

                if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']))
                {
                      $secret = '6Ld3ovYUAAAAAN33s_4owHYSVqH-5ewSm-nyFSDb';
                      $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
                      $responseData = json_decode($verifyResponse);
                      if($responseData->success)
                      {
                        // Create a new user
                        $user = new User();
                        try {
                            $user->create(array(
                                'username' => Input::get('username'),
                                'password' => Hash::make(Input::get('password')),
                                'email' =>Input::get('email'),
                                'created' => date('Y-m-d H:i:s'),
                                'lvl' => '1',
                                'avatar' => Config::get('avatar/default')
                            ));

                            // Create a session message and redirect to index
                            Session::flash('home', 'Welcome <b>' . Input::get('username') . '</b>! Your account was created. You can log in.');
                            Redirect::to('index.php');

                        } catch (Exception $e) {
                            echo $e, '<br>';
                        }
                      }
                      else
                      {
                        Redirect::to('index.php?bot=yes');
                      }
                 }

            // If invalid    
            } else {
                $_GET['validationerror'] = 'yes';
            }

        }
    }
?>
    <h1>Register</h1>

        <form action="" method="post">

            <div class="field">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="<?php echo escape(Input::get('username')); ?>" class="w3-input w3-border w3-round">
            </div>

            <div class="field">
                <label for="email">E-mail</label>
                <input type="text" name="email" id="email" value="<?php echo escape(Input::get('email')); ?>" class="w3-input w3-border w3-round">
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="w3-input w3-border w3-round">
            </div>

            <div class="field">
                <label for="password_check">Password check</label>
                <input type="password" name="password_check" id="password_check" class="w3-input w3-border w3-round">
            </div>

            <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">

            <?php
                if(Input::exists('get')) {
                    if(Input::get('validationerror') == 'yes') {
                        $str = "";
                        foreach ($validator->errors() as $error) {
                            $modified = str_replace("_", " ", $error);
                            $str .= "<p>". $modified ."</p>";
                        }
                        $err = '<div class="w3-display-container w3-panel w3-card-4 w3-round w3-red">
                        <span onclick="this.parentElement.style.display=\'none\'"class="w3-button w3-small w3-hover-red w3-display-topright">&times;</span>
                        '. $str .'</div>';
                        echo $err; 
                    }                                      
                }    
            ?>
            <div class="g-recaptcha w3-padding-16" data-sitekey="6Ld3ovYUAAAAABh60BXwp0ANYKiagQjf7TB_FxNj"></div>
            <input type="submit" value="Register" class="w3-button w3-small w3-light-green w3-hover-gray w3-round w3-margin-top">

        </form>

        <p><a href="index.php">Go back</a></p>

<?php
    require 'footer.php';
