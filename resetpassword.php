<?php
    // Include a page header
    require 'header.php';

    // Create a user instance
    $user = new User();

    // If inputs exist
    if(Input::exists()) {

        // If inputs were not tampered with
        if(Token::check(Input::get('token'))) {

            // Validate
            $validator = new Validator();
            $validator->check($_POST, array(
                'email' => array(
                    'required' => true,
                    'email' => true
                )
            ));

            // If valid
            if ($validator->passed()) {

                // Create two tokens
                $selector = bin2hex(random_bytes(8));
                $verificator = random_bytes(32);

                // URL for renewal
                $url = Config::get('domain/url') . 'createpassword.php?selector=' . $selector . '&verificator=' . bin2hex($verificator);
                $expires = date("U") + 1800;

                // If the reset was already requested
                $entry = $user->getDB()->get('pwdresets', array('email', '=', Input::get('email')));

                if (count($entry) !== 0) {

                    // Delete the entry
                    $user->getDB()->delete('pwdresets', array('email', '=', Input::get('email')));
                }

                // Make a new entry in DB and assemble the e-mail
                try {
                    $query = $user->getDB()->insert('pwdresets', array(
                        'email' => Input::get('email'),
                        'selector' => $selector,
                        'verificator' => password_hash($verificator, PASSWORD_DEFAULT),
                        'expires' => $expires
                    ));

                    $message = '<p>Hello,</p><p>We have received a password reset request.';
                    $message .= 'The link to reset your password is below. If you did not ';
                    $message .= 'make this request, you can ignore this e-mail.</p>';
                    $message .= '<p>Here is your password reset link: </br>';
                    $message .= '<a href="' . $url . '">' . $url . '</a></p>';

                    $mailer = Mailer::make()
                        ->setTo(Input::get('email'))
                        ->setFrom('noreply@english-quest.com')
                        ->setSubject('Password reset from Englih-Quest.com')
                        ->setMessage($message)
                        ->setHTML()
                        ->addGenericHeader('X-Mailer', 'PHP/' . phpversion())
                        ->setWrap(78);

                    // Attempt to send
                    $send = $mailer->send();

                    // If sent
                    if ($send) {

                        // Create a new session nessage and redirect
                        Session::flash('home', 'Please follow the instructions in the e-mail which was send to you.');
                        Redirect::to('index.php');
                    
                    // If not sent
                    } else {
                        echo "error";
                    }

                // Catch DB errors
                } catch (Exception $e) {
                    echo $this->db->error, '<br>';
                }

            // If invalid inputs
            } else {
                foreach ($validator->errors() as $error) {
                    echo $error . '<br>';
                }
            }
        }
    }
?>
    <h1>Reset your password</h1>

        <form action="" method="post">

            <div class="field">
                <label for='email'>Your e-mail</label>
                <input type="text" name="email" id="email" class="w3-input w3-border w3-round">
            </div>

            <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
            <input type="submit" value="Reset" class="w3-button w3-small w3-teal w3-hover-khaki w3-round w3-margin-top">

        </form>

        <p><a href="index.php">Go back</a></p>

<?php
    require 'footer.php';
