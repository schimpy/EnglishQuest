<?php

class Module
{
    private $db;
    private $user;
    private $short;
    private $moduleDetails;
    private $moduleUnits = array();
    private $renderArray = array();
      
    public function __construct($short, $user) {
        $this->db = Database::getInstance();
        $this->moduleDetails = $this->loadDetails($short);
        $this->user = $user;
        $this->short = $short;
        $this->loadModuleUnits();
    }

    public function loadDetails($short) {
        $this->db->get('modules', array('short', '=', $short));
        return $this->db->first();
    }

    public function loadModuleUnits() {
        $table = $this->moduleDetails->short . '_units';
        $this->db->query("SELECT * FROM " . $table);
        $data =  $this->db->results();
        foreach(json_decode(json_encode($data), true) as $x) {
            array_push($this->moduleUnits, $x);
        }
    }

    public function getName() {
        return $this->detail->name;
    }

    public function getShort() {
        return $this->detail->short;
    }

    private function getUserID() {
        return $this->user->data()->id;
    }

    public function isUnitUnlocked($short, $id, $userid) {
        $table = "user_units";
        $this->db->query("SELECT id FROM user_units WHERE slug = ? AND unitid = ? AND userid = ?", array($short, $id, $userid));
        if($this->db->count() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function loopUnits($units) {
        foreach (json_decode(json_encode($units), true) as $unit) {
            $html = "";
            $html .= $this->renderUnitHeader($unit);
            $html .= $this->renderUnitBody($unit);
            $html .= $this->renderUnitFooter();
            array_push($this->renderArray, $html);
        }
    }

    public function renderUnitHeader($unit) {
        $color = $this->moduleDetails->color;
        return '<div class="w3-panel w3-padding-16 w3-' . $color .'"><h3>'. $unit['title'] .'</h3>';
     }

    public function renderUnitBody($unit) {
        $renderString = "";

        if ($unit['price'] == 0 && $unit['lvl'] <= $this->user->getLevel()) {
            $renderString = "<p><a href='unit.php?slug=". $this->moduleDetails->short ."&id=". $unit['id'] ."'>Go on a quest!</a></p>";
        } elseif($unit['lvl'] > $this->user->getLevel()) {
            $renderString = "<p>Unlocked from level ". $unit['lvl'] ."</p>";
        } elseif($unit['price'] != 0 && $unit['lvl'] <= $this->user->getLevel()) {  

            if($this->isUnitUnlocked($this->moduleDetails->short, $unit['id'], $this->getUserID())) {                        
                $renderString = "<p><a href='unit.php?slug=". $this->short ."&id=". $unit['id'] ."'>Go on a quest!</a></p>";
            } else {
                $renderString = "<p><a href='unit.php?slug=". $this->short ."&id=". $unit['id'] ."&unlock=true'>Purchase for ". $unit['price'] ."<i class=\"fas fa-coins fa-lg\"></i></a></p>";
            }
        }
        return $renderString;
    }

    public function renderUnitFooter() {
        return '</div>';
    }

    public function renderAllUnits() {
        $this->loopUnits($this->moduleUnits);
        foreach($this->renderArray as $unit) {
            echo $unit;
        }
    }

    public function renderModuleTitle() {
        echo '<h3>' . $this->moduleDetails->name . '</h3>';
    }
}
