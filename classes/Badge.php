<?php

class Badge {

    private $db = null;
    private $user;
    private $hasBadgesFunctionNames = array();
    private $tableName;

    public function __construct($user) {
        $this->db = Database::getInstance();
        $this->user = $user;
        $this->hasBadgesFunctionNames = $this->loadBadges();
        $this->tableName = "user_badges";
     }

    private function loadBadges() {


    }

    public function getBadgeFromAchievementFunc($achievementFunc) {


    }

    private function saveAchievement($userid, $badgeid) {


    }

    private function checkAchievements() {


    }

    private function flashNewBadgeMessage() {
        
    }


}
