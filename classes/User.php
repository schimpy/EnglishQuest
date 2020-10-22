<?php


class User
{
    private $db;
    private $data;
    private $sessionName;
    private $cookieName;
    private $isLoggedIn;

    public function __construct($user = null) {
        $this->db = Database::getInstance();
        $this->sessionName = Config::get('sessions/session_name'); // user
        $this->cookieName = Config::get('remember/cookie_name'); // hash

        if(!$user) {
            if(Session::exists($this->sessionName)) {
                $user = Session::get($this->sessionName);

                if($this->find($user)) {
                    $this->isLoggedIn = true;
                } else {
                    $this->logout();
                }
            }
        } else  {
            $this->find($user);
        }
    }

    public function create($fields = array()) {
        if(!$this->db->insert('users', $fields)) {
            throw new Exception('Sorry, there was a problem creating your account.');
        }
    }

    public function update($fields = array(), $id = null) {
        if(!$id && $this->isLoggedIn()) {
            $id = $this->data()->id;
        }

        if(!$this->db->update('users', $id, $fields)) {
            throw new Exception('There was a problem updating the profile.');
        }
    }

    public function delete($userid) {
        $this->getDB()->delete('users', array('id', '=', $userid));
    }

    public function find($user = null) {
        if($user) {
            $field = is_numeric($user) ? 'id' : 'username';
            $data = $this->db->get('users', array($field, '=', $user));

            if($data->count()) {
                $this->data = $data->first();
                return true;
            }
        }
        return false;
    }

    public function exists() {
        return (!empty($this->data)) ? true : false;
    }

    public function login($username = null, $password = null, $remember = false) {
        if(!$username && !$password && $this->exists()) {
            if($this->isLocked($this->data()->id)) {
                Redirect::to('login.php?locked');
            }
            Session::put($this->sessionName, $this->data()->id);
        } else {
            $user = $this->find($username);
            if($user) {
                if($this->isLocked($this->data()->id)) {
                    Redirect::to('login.php?locked');
                }
                if($this->data()->password === Hash::make($password)) {
                    $this->isLoggedIn = true;
                    Session::put($this->sessionName, $this->data()->id);
                    return true;
                } else {
                    Redirect::to('login.php?badcred=yes');
                }
            }
        }
        return false;
    }

    public function logout() {
        $this->db->delete('sessions', array('userid', '=', $this->data()->id));
        $this->isLoggedIn = false;
        Session::delete($this->sessionName);
        Cookie::delete($this->cookieName);
    }

    public function isLoggedIn() {
        return $this->isLoggedIn;
    }

    public function isAdmin() {
        if(!empty($this->data())) {
            return ($this->data()->status == 2) ? true : false;
        } else {
            return false;
        }
    }

    public function isLocked($userid) {
        $this->getDB()->get('users', array('id', '=', $userid));
        $status = $this->getDB()->first()->{'status'};
        if($status == 3) {
            return true;
        } else {
            return false;
        }
    }

    public function lock($userid) {
        $this->update(array('status' => '3'), $userid);
    }

    public function unlock($userid) {
        $this->update(array('status' => '1'), $userid);
    }

    public function data() {
        return $this->data;
    }

    public function getDB() {
        return $this->db;
    }

    public function loadAllUsers($userSearch = null) {
        if(!$userSearch) {
            $this->getDB()->getAll('users');
            return $this->getDB()->results();
        } else {
            $this->getDB()->query('SELECT * FROM users WHERE username LIKE ? OR email LIKE ?', array($userSearch . '%', $userSearch . '%'));
            return $this->getDB()->results();
        }
    }

    public function renderUserList($users) {
        $renderString = '<table class="w3-table-all w3-margin-bottom"><tr class="w3-light-green"><td></td><th>ID</th><th>Username</th><th>Actions</th></tr>';

        foreach ($users as $user) {

            $renderString .= '<tr><td></td><td>' . $user->{'id'} . '</td>';
            $renderString .= '<td>' . $user->{'username'} . '</td><td>';

            if($user->{'status'} != 2 || $user->{'id'} == Session::get(Config::get('sessions/session_name'))) {
                $renderString .= '<a href="#" onclick="callToggler(\'edit-user-' . $user->{'id'} . '\')"><i class="far fa-edit"></i></a>&nbsp;';

                if($user->{'status'} == 3) {
                    $renderString .= '<a href="admin.php?action=users&lock=' . $user->{'id'} .'"><i class="fas fa-unlock"></i></a>&nbsp;';
                } else {
                    $renderString .= '<a href="admin.php?action=users&lock=' . $user->{'id'} .'"><i class="fas fa-lock"></i></a>&nbsp;';
                }

                $renderString .= '<a href="admin.php?action=users&delete=' . $user->{'id'} .'" onClick="return confirm(\'Are you sure?\')"><i class="fas fa-trash-alt"></i></a>';

                $renderString .= '<div class="w3-container"  id="edit-user-' . $user->{'id'} . '" style="display: none;"><form action="" method="post" name="edit-user-form">';
                $renderString .= '<label for="username">Username</label><input type="text" name="username" class="w3-input w3-border w3-round" placeholder="' . $user->{'username'} . '">';
                $renderString .= '<label for="email">E-mail</label><input type="text" name="email" class="w3-input w3-border w3-round" placeholder="' . $user->{'email'} . '">';
                $renderString .= '<label for="password">Password</label><input type="password" name="password" class="w3-input w3-border w3-round">';
                $renderString .= '<label for="password-check">Password check</label><input type="password" name="password-check" class="w3-input w3-border w3-round">';
                $renderString .= '<label for="status">Status</label><select name="status" class="w3-select w3-border"><option value="0" selected>Regular user</option><option value="2">Administrator</option></select>';
                $renderString .= '<input type="hidden" name="edit-user-id" value="' . $user->{'id'} . '">';
                $renderString .= '<button type="submit" name="edit-user-submit" class="w3-button w3-small w3-light-green w3-hover-gray w3-round w3-margin-top">SAVE</button>';
                $renderString .= '</form></div></td></tr>';
                
            } else {
                $renderString .= 'No permission to modify an administrator';
            }
        }

        $renderString .= "</table>";
        return $renderString;
    }

    public function getUserIDFromEmail($email) {
        $query = $this->db->action('SELECT id', 'users', array('email', '=', $email));
        return $query->first()->id;
    }

    public function getUsernameFromID($id) {
        $this->getDB()->get('users', array('id', '=', $id));
        return $this->getDB()->first()->{'username'};
    }

    public function getUserIDFromUsername($username) {
        $query = $this->db->action('SELECT id', 'users', array('username', '=', $username));
        return $query->first()->id;
    }

    public function doesEmailExist($email) {
        $this->getDB()->get('users', array('email', '=', $email));
        if($this->getDB()->count() == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function doesPasswordMatch($password) {
        if($this->data()->password === Hash::make($password)) {
            return true;
        } else {
            return false;
        }
    }

    public function calculateLevel($newXP) {
        $oldLevel = $this->getLevel();
        $newLevel = floor((1+sqrt(1-4*(-$newXP/50)))/2);
        if ($newLevel > $oldLevel) {
            return true;
        } else {
             return false;
        }
    }

    public function flashNewLevelMessage() {
        $html = "";
        $html .= '<div id="newLevelModal" class="w3-modal"><div class="w3-modal-content"><div class="w3-container w3-center">';
        $html .= '<span onclick="document.getElementById(\'newLevelModal\').style.display=\'\'" class="w3-button w3-display-topright">&times;</span>';
        $html .= '<h4>Congratulations!</h4><p>You have reached new level</p>';
        $html .= '<p><span class="w3-badge w3-jumbo w3-red">'. $this->getNewLevel() .'</span></p>';
        $html .= '</div></div></div><script>document.getElementById(\'newLevelModal\').style.display=\'block\';</script>';
        return $html;
    }

    public function getID() {
        return $this->data()->id;
    }

    public function getUsername() {
        return $this->data()->username;
    }

    public function getPoints() {
        return $this->data()->points;
    }

    public function getCoins() {
        return $this->data()->coins;
    }

    public function getLevel() {
        return $this->data()->lvl;
    }

    public function getNewLevel() {
        return ($this->getLevel() + 1);
    }

    public function setPoints($points) {
        $fields = array('points', '=', $points);
        if(!$this->db->update('users', $this->data()->id, $fields)) {
            throw new Exception('There was a problem updating the profile.');
        }
    }

    public function setCoins($coins) {
        $fields = array('coins', '=', $coins);
        if(!$this->db->update('users', $this->data()->id, $fields)) {
            throw new Exception('There was a problem updating the profile.');
        }
    }

    public function setLevel($level) {
        $fields = array('level', '=', $level);
        if(!$this->db->update('users', $this->data()->id, $fields)) {
            throw new Exception('There was a problem updating the profile.');
        }
    }

    public function setAvatar($avatar, $id) {
        $this->update(array('avatar' => $avatar), $id);
    }

    public function unsetAvatar() {
        $this->update(array('avatar' => Config::get('avatar/default_avatar')), $this->data()->id);
    }

    public function getUserAvatarPath() {
        return Config::get('avatar/path') . $this->data()->avatar;
    }

    public function showUserAvatarInHTML() {
        $path = Config::get('domain/url') . $this->getUserAvatarPath();
        $html = '<img src="' . $path . '" alt="Avatar" width="55" height="55">';
        return $html;
    }

    public function loadInitialApp() {

        echo '<div class="w3-modal" id="initial">
                    <div class="w3-modal-content w3-animate-zoom">

                        <header class="w3-container w3-padding-16 w3-lime">
                            <span onclick="document.getElementById(\'initial\').style.display=\'none\'" class="w3-button w3-darkgray w3-xlarge w3-display-topright">&times;</span>
                            <h2>Welcome to English Quest!</h2>
                        </header>

                        <div class="w3-bar w3-border-bottom">
                            <button class="tablink w3-bar-item w3-button" onclick="openTab(event, \'avatar\')">Choose your character</button>
                            <button class="tablink w3-bar-item w3-button" onclick="openTab(event, \'tour\')">Take a first tour in English Quest</button>
                        </div>

                        <div id="avatar" class="w3-container tab">
                            <h4>Select your character</h4> 
                                <a href="#" onclick="updateAvatar(\'female.png\')">
                                    <img src="img/avatar/female.png" width="200" height="200" style="padding:5px"></a>
                                <a href="#" onclick="updateAvatar(\'male.png\')">
                                    <img src="img/avatar/male.png" width="200" height="200" style="padding:5px"><br></a>
                                <p>But you can change it later in your profile page as well!</p><br>
                        </div>

                        <div id="tour" class="w3-container tab">

                            <div class="w3-display-container tourSlides">
                                <img src="img/tour/tour01.png" style="width:100%">
                                    <div class="w3-display-bottom w3-large w3-container w3-padding-16 w3-black">
                                        English Quest is a web application based on principles of gamification that aims to practice English grammar. It comprises of modules which the player unlocks whit certain level.
                                    </div>
                            </div>

                            <div class="w3-display-container tourSlides">
                            <img src="img/tour/tour02.png" style="width:100%">
                                <div class="w3-display-bottom w3-large w3-container w3-padding-16 w3-black">
                                    Each module has its units that are either open to practice or must be purchased for certain amount of coins. With additional levels, the player may purchase more units from selected module.
                                </div>
                            </div>

                            <div class="w3-display-container tourSlides">
                            <img src="img/tour/tour03.png" style="width:100%">
                                <div class="w3-display-bottom w3-large w3-container w3-padding-16 w3-black">
                                    In the specific unit, the player is briefly introduced to the subject-matter, the basic grammatical rules and usage, all supported by meaningful examples.
                                </div>
                            </div>

                            <div class="w3-display-container tourSlides">
                            <img src="img/tour/tour04.png" style="width:100%">
                                <div class="w3-display-bottom w3-large w3-container w3-padding-16 w3-black">
                                    The gained knowledge might be practiced or tested in a quiz that reflects the content of the unit. There is a limited amout of time to answer each question.
                                </div>
                            </div>

                            <div class="w3-display-container tourSlides">
                            <img src="img/tour/tour05.png" style="width:100%">
                                <div class="w3-display-bottom w3-large w3-container w3-padding-16 w3-black">
                                    After completing the quiz, the player is awarded by experience points and sometimes with coins, depending on the success of the player.
                                </div>
                            </div>

                            <button class="w3-button w3-display-left w3-black" onclick="plusDivs(-1)">&#10094;</button>
                            <button class="w3-button w3-display-right w3-black" onclick="plusDivs(1)">&#10095;</button>

                        </div>

                    </div>
                </div>

                <script src="js/tourHandler.js"></script>';
    }

}