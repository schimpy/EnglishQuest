<?php

class Reward {

    private $db = null;
    private $user;
    private $rewards = array();

    public function __construct($user) {
        $this->db = Database::getInstance();
        $this->user = $user;
    }

    public function loadRewards() {}

    public function renderRewards() {

        if(empty($this->rewards)) {
            $norwds = '<div class="w3-panel w3-round w3-teal">You have no rewards yet.<br>Go on a quest!</div>';
            $b1 = '<span class="w3-badge w3-xxlarge w3-blue" title="Blue trophy - achieve 150 points"><i class="fas fa-trophy"></i></span>';
            echo $b1;
        }

    }


}